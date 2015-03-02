<?php
/*
 * Original code Copyright (c) 2015 Simon Kerr
 * WikiMediaProvider controls access to wikimedia, gets listings, details or batch 
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

use \Exception;

class WikiMediaProvider implements IMediaProviderStrategy {
    const FRIENDLY_NAME = 'Wikimedia';
    const PROVIDER_NAME = 'wikimedia';
    const BATCH_PROCESS_THRESHOLD = 50;
    const CACHE_TTL = 0; //86400;
    const IMAGESIZE_THRESHOLD = 300;
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
            'gcmlimit'  =>      self::BATCH_PROCESS_THRESHOLD,
            'gcmtype'   =>      'file',
            'prop'      =>      'imageinfo|categories',         //get imageinfo and categories
            'iiprop'    =>      'url',                          //get the image url used on the page
            'format'    =>      'json',
            'iiurlwidth'=>      self::IMAGESIZE_THRESHOLD,      //specify a thumbnail url to return
            'clshow'    =>      '!hidden'                       //don't show hidden categories
            
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
    private function getItemId($data){
        return $data['pageid'];
    }
    
    private function getItemImage($data){
        try{
            $imageinfo = array_pop($data['imageinfo']);
            return $imageinfo['url'];
        } catch(Exception $re){
            return null;
        }
        
    }
    
    private function getItemUrl($data){
        try{
            $imageinfo = array_pop($data['imageinfo']);
            return $imageinfo['descriptionurl'];
        } catch(Exception $re){
            return null;
        }
    }
    
    private function getItemTitle($data){
        try{
            return $data['title'];
        } catch(Exception $re){
            return null;
        }
    }
    
    private function getItemThumbnail($data){
        try {
            $imageinfo = array_pop($data['imageinfo']);
            return $imageinfo['thumburl'];
            
        } catch (Exception $ex) {
            return null;
        }
    }
    
    private function getItemCategories($data){
        try{
            $categories = array_pop($data['categories']);
            return (array)$categories['title'];
        } catch (Exception $ex) {
            return null;
        }
    }
    
    public function getItem($data){
        return array(
            'provider'      =>  self::PROVIDER_NAME,
            'id'            =>  $this->getItemId($data),
            'title'         =>  $this->getItemTitle($data),
            'image'         =>  $this->getItemImage($data),
            'thumbnail'     =>  $this->getItemThumbnail($data),
            'url'           =>  $this->getItemUrl($data),
            'categories'    =>  $this->getItemCategories($data)
        );
    }
   
    /**
     * @param \Sk\MediaApiBundle\Entity\Decade $decade
     * @param type $pageNumber
     * @return type
     * @throws Exception
     * gets decade, choose random media, based on media
     * chooses random 3 years, then queries for 50 results,
     * which are cached for 2 days
     */
    public function getListings(Decade $decade, $pageNumber = 1){
        $decadeIds = explode("|", $decade->getWikiMediaId());
	shuffle($decadeIds);
        $decadeIds = array_splice($decadeIds, 0, 4);
	$pageIds = implode("|", $decadeIds);         

        $params = Utilities::removeNullEntries(array(
            'gcmpageid'     =>      $pageIds
        ));
        
        $this->params = array_merge($this->params, $params);
        $response = $this->runQuery($this->params);
        
        try{
            $response = $this->verifyResponse($response);
        }catch(Exception $e){
            throw $e;
        }
                
        return $response;
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
//    public function getBatch(array $ids){
//        if(count($ids) > self::BATCH_PROCESS_THRESHOLD)
//            $ids = array_slice ($ids, 0, self::BATCH_PROCESS_THRESHOLD);
//            
//        $params = array(
//            'ItemId'  => implode(',', $ids),
//        );
//        return $this->getDetails($params);
//    }
   
    /**
     * Check if the json received from WikiMedia is valid
     * 
     * @param mixed $response json response to check
     * @return bool false if the response is invalid
     * @return mixed the response if it is valid
     * @return exception if could not connect to WikiMedia
     */
    protected function verifyResponse($response)
    {
        if ($response === False)
        {
            throw new Exception("Could not connect to WikiMedia");
        }
        else
        {
            $response = json_decode($response, true, 25);
            if($response['query'] === null) {
                throw new Exception("Query node not found");
            }
            
            if(count($response['query']['pages']) === 0){
                throw new Exception("No results were returned");
            }
            
            return $response['query']['pages'];
        }
    }
    
    /**
     * Query WikiMedia with the issued parameters
     * 
     * @param array $parameters parameters to query around
     * @return json data response
     */
    protected function runQuery($parameters)
    {
        return $this->wikiMediaRequest->makeRequest($this->apiEndPoint, $parameters, $this->userAgent);
    }
    

}


?>
