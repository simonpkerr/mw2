<?php
/*
 * Copyright (c) 2011 Simon Kerr
 * Connects to YouTube api to return results for all media,
 * handles getting listings, details and batch processing of YouTube data
 * @author Simon Kerr
 * @version 1.0
 */
namespace Sk\MediaApiBundle\MediaProviderApi;
use Sk\MediaApiBundle\MediaProviderApi\Utilities;
use Sk\MediaApiBundle\Entity\MediaSelection;
use Sk\MediaApiBundle\Entity\Decade;
//use Sk\MediaApiBundle\Entity\API;
use Doctrine\ORM\EntityManager;
use \SimpleXMLElement;

class YouTubeProvider implements IMediaProviderStrategy {
    const FRIENDLY_NAME = 'YouTube';
    const PROVIDER_NAME = 'youtube';
    const BATCH_PROCESS_THRESHOLD = 24;
    const CACHE_TTL = 259200;
    
    protected $gsYouTube;
    protected $apiEntity;
    private $googleClient;
    private $query;
    private $ids;
    private $gdataKey;
    
    public function __construct($google_service_youtube){
        
        //$this->gsYouTube =  is_null($youtube_request_object) ? new \Zend_Gdata_YouTube() : $youtube_request_object;
        //$this->gdataKey = $access_params['gdata_key'];
//        $this->googleClient = new Google_Client();
//        $this->googleClient->setApplicationName("memory walls");
//        $this->googleClient->setDeveloperKey($this->gdataKey);
//        $this->googleServiceYouTube = is_null($google_service_youtube) ? new Google_Service_YouTube($this->googleClient) : $google_service_youtube;
//               
        //$this->gsYouTube->setMajorProtocolVersion(2);
       
        $this->gsYouTube = $google_service_youtube;
    }
    
    public function getProviderName(){
        return self::PROVIDER_NAME;
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
    
    public function getIdFromXML(SimpleXMLElement $xmlData){
        return (string)$xmlData->id;
    }
    
    public function getXML(SimpleXMLElement $xmlData){
        return $xmlData->asXML();
    }
    
    public function getImageUrlFromXML(SimpleXMLElement $xmlData) {
        try{
            return (string)$xmlData->thumbnail;
        } catch(\RuntimeException $re){
            return null;
        }
    }
    public function getItemTitleFromXML(SimpleXMLElement $xmlData){
        try{
            return (string)$xmlData->title;
        } catch(\RuntimeException $re){
            return null;
        }
    }
    
    public function getDecadeFromXML(SimpleXMLElement $xmlData) {
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

    public function getRandomItems(Decade $decade, $pageNumber = 1){
        $searchResponseItems = (array)$this->getListings($decade, $pageNumber);
        $listingsCount = count($searchResponseItems);
        $listingsCount = $listingsCount > 5 ? 5 : $listingsCount;
        shuffle($searchResponseItems);
        $randomItems = array_slice($searchResponseItems, 0, $listingsCount);
        
        return $randomItems;
    }
    
    public function getListings(Decade $decade, $pageNumber = 1){
        try {
            $searchReponse = $this->gsYouTube->search->listSearch('id,snippet', array(
                'q'             =>  urlencode($decade->getSlug()),
                'maxResults'    =>  self::BATCH_PROCESS_THRESHOLD,
                'pageToken'     =>  $pageNumber,
                'type'          =>  'video'
            ));
        } catch (Google_ServiceException $e) {
            return $e->getMessage();
        } catch (Google_Exception $e){
            return $e->getMessage();
        }              

        if($searchReponse === false) {
            throw new \RuntimeException("Could not connect to YouTube");
        }
        
        if(count($searchReponse['items']) < 1){
            throw new \LengthException("No youtube results were returned");
        }

        return $searchReponse['items'];
        //return $this->getSimpleXml($videoFeed);
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
    
    private function getSimpleXml($videoFeed, $debugURL = false){
        $sxml = new SimpleXMLElement('<feed></feed>');
        foreach($videoFeed as $i=>$videoEntry){
            $entry = $sxml->addChild('entry');
            $this->constructVideoEntry($entry, $videoEntry, $i);
        }
        
        //debug - output the search url
        if($debugURL){
            $url = $sxml->addChild('url');
            $url[0] = $this->query;
        }
        return $sxml;
    }
    
    private function constructVideoEntry(SimpleXMLElement $entry, $videoEntry, $i = null){
        $id = $entry->addChild('id');
        $thumbnail = $entry->addChild('thumbnail');
        $title = $entry->addChild('title');
        
        if(!is_null($videoEntry->getVideoTitle())) {
            $id[0] = $videoEntry->getVideoId();
            $thumbnails = $videoEntry->getVideoThumbnails();
            $tn = end($thumbnails);
            $thumbnail[0] = $tn['url'];
            $title[0] = $videoEntry->getVideoTitle();
        } else {
            if(!is_null($this->ids) && !is_null($i)){
                $id[0] = $this->ids[$i];                        
            } else {
                $id[0] = '-1';
            }
            $thumbnail[0] = 'na';
            $title[0] = 'Sorry, this video has been removed by YouTube';
        }
        
        return $entry;
    }
    
    /**
     * method returns a date time object against which records can be compared
     * for youtube. This enables updates to be made to out of date records
     * as there is no set time threshold for youtube, a threshold of 3 days 
     * has been chosen
     * @return type DateTime
     */
    public function getCacheTTL(){
         $date = new \DateTime("now");
         $date = $date->sub(new \DateInterval('PT72H'))->format("Y-m-d H:i:s");

         return $date;
    }

    
   
}

?>
