<?php
/*
 * Original code Copyright (c) 2011 Simon Kerr
 * Connects to 7Digital API to return music
 * @author Simon Kerr
 * @version 1.0
 */

namespace Sk\MediaApiBundle\MediaProviderApi;
use \SimpleXMLElement;
use \Sk\MediaApiBundle\Entity\Decade;

class SevenDigitalProvider implements IMediaProviderStrategy {
    const PROVIDER_NAME = 'sevendigital';
    const FRIENDLY_NAME = '7Digital';
    const CACHE_TTL = 86400;
    const IMAGESIZE_THRESHOLD = 350;
    private $apiEndPoints;                           
    private $params;
    protected $methods;
    protected $host;
    protected $page = 1;
    protected $pageSize = 50;
    protected $apiKey;
    protected $tags;
    private $simpleRequest;
    private $country;
    
    public function __construct(array $access_params, $simple_request) {
        $this->country = 'GB';
//        $this->methods = array(
//            'search_by_tag'     =>      '/release/bytag/top',
//            'preview_clip'      =>      '/clip'
//        );
        $this->apiEndPoints = $access_params['seven_digital_endpoints'];
        $this->apiKey = $access_params['seven_digital_oauth_key'];
        //previews.7digital.com - media player endpoint
        //api.7digital.com/1.2 - query endpoint
                
        $this->simpleRequest = $simple_request;
        $this->params = array(
            //'tags'                =>      defined by search
            'page'                  =>      $this->page,
            'pageSize'              =>      $this->pageSize,
            'oauth_consumer_key'    =>      $this->apiKey,
            'imageSize'             =>      self::IMAGESIZE_THRESHOLD,
            'country'               =>      $this->country
            
        );
    }
    
    public function getName(){
        return self::API_NAME;
    }

    public function getCacheKey(Decade $decade, $pageNumber = 1) {
        return array(
            'decade'        => $decade->getSlug(),
            'pageNumber'    => $pageNumber,
            'provider'      => self::PROVIDER_NAME
        );
    }

    //use the release id to get the tracks so they can be streamed
    public function getItem($data) {
        return array(
            'provider'              =>  self::PROVIDER_NAME,
            'id'                    =>  $data->attributes()->id,
            'title'                 =>  $this->getItemDetail($data, 'title'),
            'image'                 =>  '$this->getItemImage($imageinfo)',
            'url'                   =>  '$this->getItemUrl($imageinfo)',
            'description'           =>  '$this->getItemDescription($metadata)',
            'originalReleaseDate'   =>  'releaseDate',
            'price'                 =>  'getPrice'
            //need a url to play the preview using the media player endpoint and the a track id
        );
    }
    
    private function getItemDetail(SimpleXMLElement $data, $node){
        try {
            return (string)$data[$node];
        } catch (Exception $ex) {
            return null;    
        }
    }

    public function getListings(Decade $decade, $pageNumber = 1) {
        $params = Utilities::removeNullEntries(array(
            'tags'      =>      $decade->getSevenDigitalTag(),
            'page'      =>      1
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
    
    protected function runQuery($parameters)
    {
//        $host = $this->apiEndPoints['query_endpoint'] . $this->methods['search_by_tag'];
        return $this->simpleRequest->makeRequest($this->apiEndPoints['query_endpoint'], $parameters);
    }
    
    /**
     * 
     * @param type $response
     * @return mixed $response
     * @throws Exception
     */
    protected function verifyResponse($response)
    {
        if ($response === False)
        {
            throw new Exception("Could not connect to 7Digital");
        }
        else
        {
            $response = simplexml_load_string($response);
                        
            if($response->taggedResults->totalItems === 0){
                throw new Exception("No results were returned");
            }
            
            return (array)$response->taggedResults->taggedItem->release;
        }
    }

}



