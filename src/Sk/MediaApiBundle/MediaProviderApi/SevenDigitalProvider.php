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
use \Exception;

/**
 * needs to get the releases for tags, choose random release id's, get the
 * tracks, get random track ids, then get image and preview clip
 */
class SevenDigitalProvider implements IMediaProviderStrategy {
    const PROVIDER_NAME = 'sevendigital';
    const FRIENDLY_NAME = '7Digital';
    const CACHE_TTL = 86400;
    const IMAGESIZE_THRESHOLD = 350;
    const PAGE_NUMBER_THRESHOLD = 3;
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
        $this->methods = array(
            'search_by_tag'     =>      '/release/bytag/top',
            'release_tracks'    =>      '/release/tracks',
            'preview_clip'      =>      '/clip'
        );
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

    public function getCacheKey(Decade $decade) {
        return array(
            'decade'        => $decade->getSlug(),
            'pageNumber'    => rand(1, self::PAGE_NUMBER_THRESHOLD),
            'provider'      => self::PROVIDER_NAME
        );
    }

    //use the release id to get the tracks so they can be streamed
    public function getItem($data) {
        return array(
            'provider'              =>  self::PROVIDER_NAME,
            'id'                    =>  $this->getItemDetail($data, '@id'),
            'title'                 =>  $this->getItemDetail($data, 'title'),
            'artist'                =>  $this->getItemDetail($data, 'artist/name'),
            'image'                 =>  $this->getItemDetail($data, 'image'),
            'url'                   =>  $this->getItemDetail($data, 'url'),
            'originalReleaseDate'   =>  $this->getItemDetail($data, 'year'),
            'price'                 =>  $this->getItemDetail($data, 'price/formattedPrice')
            //need a url to play the preview using the media player endpoint and the a track id
        );
    }

    private function getItemDetail(SimpleXMLElement $data, $xpath){
        try {
            return (string)$data->xpath($xpath)[0];
        } catch (Exception $ex) {
            return null;
        }
    }

    public function getListings(Decade $decade) {
        $params = Utilities::removeNullEntries(array(
            'tags'      =>      $decade->getSevenDigitalTag(),
            'page'      =>      rand(1, self::PAGE_NUMBER_THRESHOLD)
        ));

        $this->params = array_merge($this->params, $params);
        $response = $this->runQuery($this->params);

        try{
            $transformedResponse = $this->verifyResponse($response);
        }catch(Exception $e){
            throw $e;
        }

        return $transformedResponse;
    }

    protected function runQuery($parameters)
    {
        $host = $this->apiEndPoints['query_endpoint'] . $this->methods['search_by_tag'];
        return $this->simpleRequest->makeRequest($host, $parameters);
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

            return $response->xpath('taggedResults/taggedItem/release');
        }
    }

}



