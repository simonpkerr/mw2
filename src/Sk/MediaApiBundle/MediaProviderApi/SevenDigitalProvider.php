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

class SevenDigitalAPI implements IMediaProviderStrategy {
    const API_NAME = 'sevendigital';
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
    private $xml_request;
    private $country;
    
    public function __construct(array $access_params, $xml_request) {
        $this->country = 'GB';
        $this->methods = array(
            'search_by_tag'     =>      'release/bytag/top',
            'preview_clip'      =>      'clip'
        );
        $this->apiEndPoints = $access_params['api_end_points'];
        $this->apiKey = $access_params['seven_digital_oauth_key'];
        //previews.7digital.com
        //api.7digital.com/1.2
                
        $this->xml_request = $xml_request;
        $this->params = array(
            //'tags'                =>      defined by search
            'page'                  =>      $this->page,
            'pageSize'              =>      $this->pageSize,
            'oauth_consumer_key'    =>      $this->apiKey,
            'imageSize'             =>      self::IMAGESIZE_THRESHOLD,
            'country'               =>      $this->country
            
        );
    }
    
    /*
     * get the request by passing genre and decade tags to 7Digital
     * which results in a simplexml response which can be passed to the template
     */
    public function getRequest(array $params){
        
        ksort($params);
        $params = implode(",", $params);

        $request = $this->host . $this->method . "?tags=" . $params . "&page=". $this->page . "&pageSize=" . $this->pageSize . "&oauth_consumer_key=" . $this->apiKey;
        return getSimpleXmlResponse($request);    
    }
    
    public function getName(){
        return self::API_NAME;
    }

    public function getCacheKey(Decade $decade, $pageNumber = 1) {
        
    }

    public function getItem($data) {
        
    }

    public function getListings(Decade $decade) {
        
    }

}



