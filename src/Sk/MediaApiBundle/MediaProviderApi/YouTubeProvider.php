<?php
/*
 * Copyright (c) 2015 Simon Kerr
 * Connects to YouTube api to return results for all media,
 * handles getting listings, details and batch processing of YouTube data
 * @author Simon Kerr
 * @version 1.0
 */
namespace Sk\MediaApiBundle\MediaProviderApi;
use Sk\MediaApiBundle\MediaProviderApi\Utilities;
use Sk\MediaApiBundle\Entity\MediaSelection;
use Sk\MediaApiBundle\Entity\Decade;
use Doctrine\ORM\EntityManager;
use \SimpleXMLElement;
use Sonata\Cache\CacheAdapterInterface;
use Sonata\CacheBundle\Adapter;
use Sonata\Cache\CacheElement;

class YouTubeProvider implements IMediaProviderStrategy {
    const FRIENDLY_NAME = 'YouTube';
    const PROVIDER_NAME = 'youtube';
    const BATCH_PROCESS_THRESHOLD = 24;
    const SEARCH_MAX_RESULTS = 50;
    const CACHE_TTL = 259200;
    
    private $gsYouTube;
    private $cache;
    private $defaults;
    
    //through DI, either receives the genuine search object or a fake
    /**
     * 
     * @param type $google_service_youtube
     * @param CacheAdapterInterface $cache
     * $defaults  1 (Film & animation), 10 (Music), 20 (Gaming), 24 (Entertainment), 30 (movies), 43 (Shows)
     */
    public function __construct($google_service_youtube){
        //$this->cache = $cache;
        $this->gsYouTube = $google_service_youtube;
        $this->defaults = array(
            'maxResults'        =>  self::SEARCH_MAX_RESULTS,
            'type'              =>  'video',
            'videoCategoryId'   =>  '1,10,20,24,30,43',
            'regionCode'        =>  'GB'
        );
    }
    
    public function getProviderName(){
        return self::PROVIDER_NAME;
    }
    
    public function getCacheKey(Decade $decade, $pageNumber = 1){
        return array(
            'decade'        => $decade->getSlug(),
            'provider'      => self::PROVIDER_NAME
        );
    }
    
    
//    public function getAPIEntity() {
//        return $this->apiEntity;
//    }
//
//    public function setAPIEntity(API $entity) {
//        $this->apiEntity = $entity;
//        
//    }
    
    public function setRequestObject($obj){
        $this->gsYouTube = $obj;
    }
    
    public function getItemId($data){
        return  $data->id->videoId;
    }
    
    
     public function getItemUrl($data){
        try{
            return 'https://www.youtube.com/watch?v=' . $data->id->videoId;
        } catch(\RuntimeException $re){
            return null;
        }
    }
    
    
    public function getXML($data){
        return $data->asXML();
    }
    
    public function getItemImage($data) {
        try{
            return $data->snippet->thumbnails->medium->url;
        } catch(\RuntimeException $re){
            return null;
        }
    }
    public function getItemTitle($data){
        try{
            return $data->snippet->title;
        } catch(\RuntimeException $re){
            return null;
        }
    }
    
    public function getItemDecade($data) {
        return null;
    }
    
    public function getItemDescription($data) {
        try{
            return $data->snippet->description;
        } catch (\RuntimeException $ex) {
            return null;
        }
        
    }
    
    public function getItemPrice($data){
        return null;
    }
    
    /*
     * for youtube, details are retrieved on the client,
     * but still need to be stored to drive recommendations, timeline
     * and improve memory walls
     */
    public function getDetails(array $params){
        
//        if(!isset($params['ItemId']))
//           throw new \InvalidArgumentException('No id was passed to Youtube');
//        
//        $ve = $this->gsYouTube->getVideoEntry($params['ItemId']);
//        
//        if($ve === false)
//            throw new \RuntimeException("Could not connect to YouTube");
//        
//        if(count($ve) < 1){
//            throw new \LengthException("No results were returned");
//        }
//        
//        $response = $this->constructVideoEntry(new SimpleXMLElement('<entry></entry>'), $ve);
// 
//        return $response;
        return null;
    }
    
    public function getBatch(array $ids){
//        if(count($ids) > self::BATCH_PROCESS_THRESHOLD)
//            $ids = array_slice ($ids, 0, self::BATCH_PROCESS_THRESHOLD);
//        
//        $this->ids = $ids;
//                
//        $feed = '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/"
//xmlns:batch="http://schemas.google.com/gdata/batch" xmlns:yt="http://gdata.youtube.com/schemas/2007"><batch:operation type="query" />';
//        
//        $entries = array();
//        foreach($ids as $id){
//            array_push($entries, '<entry><id>http://gdata.youtube.com/feeds/api/videos/' . $id . '</id></entry>');
//        }
//        $feed = $feed . implode('', $entries) . '</feed>';
//        try{
//            $response = $this->gsYouTube->post($feed, 'http://gdata.youtube.com/feeds/api/videos/batch');
//        }catch(\Exception $ex){
//            throw new \RuntimeException('A problem occurred connecting to YouTube');
//        }
//        
//        if($response === false)
//            throw new \RuntimeException('Could not connect to YouTube');
//        
//        if($response->getStatus() != 200)
//            throw new \RuntimeException('A problem occurred with the response');
//        
//        $response = $response->getBody();//gets the raw response as Zend_Http_Response
//        $feed = new \Zend_Gdata_YouTube_VideoFeed();
//        $feed->setMajorProtocolVersion(2);
//        try{
//            $feed->transferFromXML($response);
//        }catch(\Exception $ex){
//            throw new \RuntimeException('Could not parse response');
//        }
//        $response = $this->getSimpleXml($feed);
//
//        return $response;
        return null;
    }
    
    public function getListings(Decade $decade, $pageNumber = 1){
        try {
            $searchReponse = $this->gsYouTube->search->listSearch('id,snippet', array_merge($this->defaults, array(
                'q'     =>  urlencode($decade->getSlug()),
            )));
        } catch (\Exception $e) {
            throw $e;
        }            

        if(count($searchReponse['items']) < 1){
            throw new \LengthException("No youtube results were returned");
        }

        return $searchReponse['items'];
    }
    
    private function getVideoFeed(Decade $decade){
        //$query = $this->gsYouTube->newVideoQuery();
        
        //$query->setOrderBy('viewCount');
        //default ordering is relevance
        //$query->setMaxResults(self::BATCH_PROCESS_THRESHOLD);
        //$query->setCategory('Entertainment/' . $decade->getSlug());
        
        //$this->query = $query->getQueryUrl(2);
        
        //return $this->gsYouTube->getVideoFeed($query);    
        return null;        
    }
    
//    private function getSimpleXml($videoFeed, $debugURL = false){
//        $sxml = new SimpleXMLElement('<feed></feed>');
//        foreach($videoFeed as $i=>$videoEntry){
//            $entry = $sxml->addChild('entry');
//            $this->constructVideoEntry($entry, $videoEntry, $i);
//        }
//        
//        //debug - output the search url
//        if($debugURL){
//            $url = $sxml->addChild('url');
//            $url[0] = $this->query;
//        }
//        return $sxml;
//    }
    
//    private function constructVideoEntry(SimpleXMLElement $entry, $videoEntry, $i = null){
//        $id = $entry->addChild('id');
//        $thumbnail = $entry->addChild('thumbnail');
//        $title = $entry->addChild('title');
//        
//        if(!is_null($videoEntry->getVideoTitle())) {
//            $id[0] = $videoEntry->getVideoId();
//            $thumbnails = $videoEntry->getVideoThumbnails();
//            $tn = end($thumbnails);
//            $thumbnail[0] = $tn['url'];
//            $title[0] = $videoEntry->getVideoTitle();
//        } else {
//            if(!is_null($this->ids) && !is_null($i)){
//                $id[0] = $this->ids[$i];                        
//            } else {
//                $id[0] = '-1';
//            }
//            $thumbnail[0] = 'na';
//            $title[0] = 'Sorry, this video has been removed by YouTube';
//        }
//        
//        return $entry;
//    }
//    

   
}

?>
