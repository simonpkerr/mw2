<?php
/*
 * Original code Copyright (c) 2011 Simon Kerr
 * AmazonAPI controls access to the live amazon api or the dummy amazon api 
 * @author Simon Kerr
 * @version 1.0
 */

namespace Sk\MediaApiBundle\MediaProviderApi;

use Sk\MediaApiBundle\MediaProviderApi\AmazonSignedRequest;
use Symfony\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Sk\MediaApiBundle\Entity\Decade;
use Sk\MediaApiBundle\Entity\Genre;
use Sk\MediaApiBundle\Entity\API;
use Sk\MediaApiBundle\MediaProviderApi\Utilities;
use \SimpleXMLElement;
use Sonata\Cache\CacheAdapterInterface;
use Sonata\CacheBundle\Adapter;
use Sonata\Cache\CacheElement;
use \Exception;

class AmazonProvider implements IMediaProviderStrategy {
    const FRIENDLY_NAME = 'Amazon';
    const PROVIDER_NAME = 'amazon';
    const BATCH_PROCESS_THRESHOLD = 10;
    const CACHE_TTL = 86400;
    
    //protected $apiEntity;
    private $amazonParameters;
    private $public_key;                           
    private $private_key;
    private $associate_tag;
    protected $asr;
    private $ITEM_SEARCH = 'ItemSearch';
    private $ITEM_LOOKUP = 'ItemLookup';
    private $cache;
 
    public function __construct(array $access_params, $amazon_signed_request){
            
        $this->public_key = $access_params['amazon_public_key'];
        $this->private_key = $access_params['amazon_private_key'];
        $this->associate_tag = $access_params['amazon_associate_tag'];
        
        $this->asr = $amazon_signed_request;     
        $this->amazonParameters = array(
                "Operation"     => $this->ITEM_SEARCH,
                "ResponseGroup" => "Images,Small,Request",
                "Condition"     => "All",
                "MerchantId"    => "All",
                "ItemPage"      => "1",
                //"Sort"          => "salesrank", //sorting functionality to be added later
                //"Sort"          => "-releasedate", // release date oldest to newest
                "Validate"      => "True",
         );
        
        //$this->cache = $cache;
    }
    
    public function getProviderName(){
        return self::PROVIDER_NAME;
    }
    
    public function getCacheKey(Decade $decade, $pageNumber = 1){
        return array(
            'decade'        => $decade->getSlug(),
            'pageNumber'    => $pageNumber,
            'provider'      => self::PROVIDER_NAME
        );
    }
    
//    public function setAPIEntity(API $entity){
//        $this->apiEntity = $entity;
//    }
    
//    public function getAPIEntity(){
//        return $this->apiEntity;
//    }
    
    public function setAmazonSignedRequest($asr){
        $this->asr = $asr;
    }
    
    //each api will have it's own method for returning the id of a mediaresource for caching purposes.
    public function getItemId($data){
        return (string)$data->ASIN;
    }
    
    public function getXML($data){
        return $data->asXML();
    }
    
    public function getItemPrice($data){
        try {
            return (string)$data->ItemAttributes->ListPrice->FormattedPrice;
        } catch (Exception $ex) {
            return null;
        }
    }
    
    public function getItemImage($data){
        try{
            return (string)$data->MediumImage->URL;
        } catch(Exception $re){
            return null;
        }
    }
    
    public function getItemUrl($data){
        try{
            return (string)$data->DetailPageURL;
        } catch(Exception $re){
            return null;
        }
    }
    
    public function getItemTitle($data){
        try{
            return (string)$data->ItemAttributes->Title;
        } catch(Exception $re){
            return null;
        }
    }
    
    public function getItemDecade($data) {
        try{
            $title = (string)$data->ItemAttributes->Title;
            $yearParts = array();
            preg_match('/[\[|\(](\d{4})[\]|\)]/i', $title, $yearParts);
            $year = isset($yearParts[1]) ? $yearParts[1] : null;
            if(!is_null($year)){
                return substr($year, 0, 3) . '0s';
            }
            
            return null;
        }catch(\RuntimeException $re){
            return null;
        }
    }
    
    public function getItemDescription($data) {
//        try{
//            return null;
//        } catch (Exception $ex) {
//            return null;
//        }
        return null;
        
    }
    
   
    /**
     * @param \Sk\MediaApiBundle\Entity\Decade $decade
     * @param type $pageNumber
     * @return type
     * @throws \Sk\MediaApiBundle\MediaProviderApi\Exception
     */
    public function getListings(Decade $decade, $pageNumber = 1){
        $browseNodeArray = array(); 
        array_push($browseNodeArray, $decade->getAmazonBrowseNodeId());
        $canonicalBrowseNodes = implode(',', $browseNodeArray);
        $params = Utilities::removeNullEntries(array(
            //'Keywords'       =>      $mediaSelection->getKeywords() != null ? $mediaSelection->getKeywords() : null,
            'BrowseNode'     =>      $canonicalBrowseNodes,
            'SearchIndex'    =>      'Video',
            'ItemPage'       =>      $pageNumber,
            'Sort'           =>      'salesrank',
        ));
        
        $this->amazonParameters = array_merge($this->amazonParameters, $params);
        $xml_response = $this->queryAmazon($this->amazonParameters, "co.uk");
        
        try{
            $xml_response = (array)$this->verifyXmlResponse($xml_response)->Items;
        }catch(Exception $e){
            throw $e;
        }
                
        return $xml_response['Item'];
    }
    
    /*
     * getDetails handles calls to the live api, 
     * @param params - params to carry out the query - only contains the id of the amazon product
     */
    public function getDetails(array $params){
        $this->amazonParameters = array_merge(
                $this->amazonParameters,
                $params, 
                array(
               'Operation'          =>     $this->ITEM_LOOKUP,
               //'ResponseGroup'      =>    'Images,ItemAttributes,Request,Similarities,EditorialReview',
               'ResponseGroup'      =>      'Images,ItemAttributes,Request,Similarities,OfferSummary',
                ));

        
        $xml_response = $this->queryAmazon($this->amazonParameters, "co.uk");
        
        try{
            $verifiedResponse = $this->verifyXmlResponse($xml_response);
        }catch(\RunTimeException $re){
            throw $re;
        }catch(\LengthException $le){
            throw $le;
        }
        
        //certain operations like batch processing only pass ids and do not require recommendations
        return $verifiedResponse->Items->Item;
        
    }
    
    /**
     * Performs a batch process of up to 10 ids to look up 
     * for memory walls
     * @param array $ids 
     * 
     */
    public function getBatch(array $ids){
        if(count($ids) > self::BATCH_PROCESS_THRESHOLD)
            $ids = array_slice ($ids, 0, self::BATCH_PROCESS_THRESHOLD);
            
        $params = array(
            'ItemId'  => implode(',', $ids),
        );
        return $this->getDetails($params);
    }
    
   
   
    /**
     * Check if the xml received from Amazon is valid
     * 
     * @param mixed $response xml response to check
     * @return bool false if the xml is invalid
     * @return mixed the xml response if it is valid
     * @return exception if we could not connect to Amazon
     */
    protected function verifyXmlResponse($response)
    {
        if ($response === False)
        {
            throw new Exception("Could not connect to Amazon");
        }
        else
        {
            //for searches
            if($response->Items->TotalResults == 0 && $this->amazonParameters['Operation'] == $this->ITEM_SEARCH){
                throw new Exception("No results were returned");
            }
            //for lookups
            if(!$response->Items->Request->IsValid && $this->amazonParameters['Operation'] == $this->ITEM_LOOKUP){
                throw new Exception("Invalid result set");
            }
            if($response->Items->Request->Errors->Error != null) {
                throw new Exception($response->Items->Request->Errors->Error->Message);
            }
            return $response;
        }
    }
    
    /**
     * Query Amazon with the issued parameters
     * 
     * @param array $parameters parameters to query around
     * @return simpleXmlObject xml query response
     */
    protected function queryAmazon($parameters, $region = "com")
    {
        return $this->asr->aws_signed_request($region, $parameters, $this->public_key, $this->private_key, $this->associate_tag);
    }
    

}


?>
