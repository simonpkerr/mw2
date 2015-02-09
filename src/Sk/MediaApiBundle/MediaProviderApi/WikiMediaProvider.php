<?php
/*
 * Original code Copyright (c) 2011 Simon Kerr
 * AmazonAPI controls access to the live amazon api or the dummy amazon api 
 * @author Simon Kerr
 * @version 1.0
 */

namespace Sk\MediaApiBundle\MediaProviderApi;

use Sk\MediaApiBundle\MediaProviderApi\WikiMediaRequest;
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

class WikiMediaProvider implements IMediaProviderStrategy {
    const FRIENDLY_NAME = 'Wikimedia';
    const PROVIDER_NAME = 'wikimedia';
    const BATCH_PROCESS_THRESHOLD = 10;
    const CACHE_TTL = 86400;
    private $apiEndPoint;                           
    private $em;
    private $params;
    private $userAgent;
    private $wikiMediaRequest;
 
    public function __construct(array $access_params, EntityManager $em, $wikimedia_request){
        $this->em = $em;            
        $this->params = array(
            'action'    =>      'query',
            'generator' =>      'categorymembers',
            'gcmlimit'  =>      50,
            'gcmtype'   =>      'file',
            'prop'      =>      'imageinfo|categories',
            'iiprop'    =>      'url'            
        );
        $this->apiEndPoint = $access_params['wikimedia_endpoint'];
        $this->userAgent = $access_params['wikimedia_user_agent'];
        $this->wikiMediaRequest = $wikimedia_request;
    }
  
    public function getCacheKey(Decade $decade, $pageNumber = 1){
        return array(
            'decade'        => $decade->getSlug(),
            'pageNumber'    => $pageNumber,
            'provider'      => self::PROVIDER_NAME
        );
    }
    
    //each api will have it's own method for returning the id of a mediaresource for caching purposes.
    public function getItemId($data){
        return (string)$data->ASIN;
    }
    
    public function getXML($data){
        return $data->asXML();
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
     * gets decade, choose random media, based on media
     * chooses random 3 years, then queries for 50 results,
     * which are cached for 2 days
     */
    public function getListings(Decade $decade, $pageNumber = 1){
        //get random media type
                
        //get random 3 years based on media type
        //$pageids = ...
        //implode($pageids, '|');
        
        $params = Utilities::removeNullEntries(array(
            'gcmpageid'     =>      $pageids
        ));
        
        $this->params = array_merge($this->params, $params);
        $response = $this->runQuery($this->params);
        
        try{
            $response = (array)$this->verifyResponse($response);
        }catch(Exception $e){
            throw $e;
        }
                
        return $response['Items'];
    }
    
    /*
     * getDetails handles calls to the live api, 
     * @param params - params to carry out the query - only contains the id of the amazon product
     */
//    public function getDetails(array $params){
//        $this->amazonParameters = array_merge(
//                $this->amazonParameters,
//                $params, 
//                array(
//               'Operation'          =>     $this->ITEM_LOOKUP,
//               //'ResponseGroup'      =>    'Images,ItemAttributes,Request,Similarities,EditorialReview',
//               'ResponseGroup'      =>      'Images,ItemAttributes,Request,Similarities,OfferSummary',
//                ));
//
//        $xml_response = $this->runQuery($this->params);
//        
//        try{
//            $verifiedResponse = $this->verifyXmlResponse($xml_response);
//        }catch(\RunTimeException $re){
//            throw $re;
//        }catch(\LengthException $le){
//            throw $le;
//        }
//        
//        //certain operations like batch processing only pass ids and do not require recommendations
//        return $verifiedResponse->Items->Item;
//        
//    }
    
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
    protected function runQuery($parameters, $uri)
    {
        return $this->wikiMediaRequest->makeRequest($this->apiEndPoint, $parameters, $this->userAgent);
    }
    

}


?>
