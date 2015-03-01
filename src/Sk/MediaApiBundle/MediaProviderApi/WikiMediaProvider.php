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
use Sonata\Cache\CacheAdapterInterface;
use Sonata\CacheBundle\Adapter;
use Sonata\Cache\CacheElement;

use \Exception;

class WikiMediaProvider implements IMediaProviderStrategy {
    const FRIENDLY_NAME = 'Wikimedia';
    const PROVIDER_NAME = 'wikimedia';
    const BATCH_PROCESS_THRESHOLD = 10;
    const CACHE_TTL = 0; //86400;
    const IMAGESIZE_THRESHOLD = 300;
    private $apiEndPoint;                           
    private $em;
    private $params;
    private $userAgent;
    private $wikiMediaRequest;
    private $cache;
 
    public function __construct(array $access_params, EntityManager $em, $wikimedia_request, CacheAdapterInterface $cache){
        $this->cache = $cache;
        $this->em = $em;            
        $this->params = array(
            'action'    =>      'query',
            'generator' =>      'categorymembers',
            'gcmlimit'  =>      50,
            'gcmtype'   =>      'file',
            'prop'      =>      'imageinfo|categories',
            'iiprop'    =>      'url',
            'format'    =>      'json'
            
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
        return $data['pageid'];
    }
    
    public function getXML($data){
        return $data->asXML();
    }
    
    public function getItemImage($data){
        /*
         * when requested, create a smaller thumbnail size if the image is too big
         * cache it, then return the cached version, using the image full url as a key
         */
        try{
            $imageinfo = array_pop($data['imageinfo']);
        } catch(Exception $re){
            return null;
        }
        
        $img = $this->createThumb($imageinfo['url']);        
        return $img;
    }
    
    private function createThumb($imageUrl){
        $imgAttrs = getimagesize($imageUrl);
        $w = $imgAttrs[0];
        $h = $imgAttrs[1];
        if($w < self::IMAGESIZE_THRESHOLD && $h < self::IMAGESIZE_THRESHOLD){
            return $imageUrl;
        }
        
        /*
         * image is too big, check cache to see if image already exists
         * if it does, load the image using gd, return url
         * if not, cache image and return url of cached version
         */
        if($this->cache->has(array('imageUrl' => $imageUrl))){
            $cacheElement = $this->cache->get(array('imageUrl' => $imageUrl));
            $image = $cacheElement->getData();
            return $image;
        }
        
        $ext = $imgAttrs['mime'];
        $commands = null;
        switch($ext){
            case "image/png":
                $commands = array(
                    'imagecreatefrompng',
                    'imagepng',
                );
                break;
            case "image/jpeg":
                $commands = array(
                    'imagecreatefromjpeg',
                    'imagejpeg',
                );
                break;
            case "image/gif":
                $commands = array(
                    'imagecreatefromgif',
                    'imagegif',
                );
                break;
        }
        
        $image = call_user_func($commands[0], fopen($imageUrl, 'r'));
        
        $newWidth = $w > self::IMAGESIZE_THRESHOLD ? self::IMAGESIZE_THRESHOLD : $w;
        $newHeight = floor($h * ($newWidth / $w));
        $tmpImg = imagecreatetruecolor($newWidth, $newHeight);
        if($ext === "image/png" || $ext === "image/gif"){
            imagealphablending($tmpImg, false);
            imagesavealpha($tmpImg, true);
        }
        
        imagecopyresized($tmpImg, $image, 0, 0, 0, 0, $newWidth, $newHeight, $w, $h);
        call_user_func($commands[1], $tmpImg, $this->getUploadRootDir() . '/thumbs/' . $this->imagePath);
                
            
            
        return $imageUrl;
    }
    
    public function getItemUrl($data){
        try{
            $imageinfo = array_pop($data['imageinfo']);
            return $imageinfo['descriptionurl'];
        } catch(Exception $re){
            return null;
        }
    }
    
    public function getItemTitle($data){
        try{
            return $data['title'];
        } catch(Exception $re){
            return null;
        }
    }
    
    public function getItemDecade($data) {
        return null;
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
