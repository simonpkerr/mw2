<?php
/*
 * Original code Copyright (c) 2011 Simon Kerr
 * TestYouTubeRequest simulates calls to the live youtube api
 * checking for cached versions of details or listings
 * @author Simon Kerr
 * @version 1.0
 */

namespace Sk\MediaApiBundle\Tests\MediaProviderApi;
class TestGoogleServiceYouTube extends \Google_Service_YouTube {
    private $headers;
    public $search;
    
    public function __construct(){
        $this->headers = 'HTTP/1.1 200 OK
X-gdata-user-country: GB
Content-type: application/atom+xml; charset=UTF-8; type=feed
Gdata-version: 2.1
Date: Tue, 05 Feb 2013 07:44:26 GMT
Expires: Tue, 05 Feb 2013 07:44:26 GMT
Cache-control: private, max-age=0
X-content-type-options: nosniff
X-frame-options: SAMEORIGIN
X-xss-protection: 1; mode=block
Server: GSE
Connection: close';
//Transfer-encoding: chunked
        $this->search = new TestSearchResponse();
    }
}

class TestSearchResponse {
    public function listSearch($part, $optParams = array()){
        $file = 'src\Sk\MediaApiBundle\Tests\MediaProviderApi\SampleResponses\sampleYouTubeListings.txt';
        if(isset($optParams['fileName'])){
             $file = $optParams['fileName'];
        }
        
        return json_decode(file_get_contents($file), true);
    }
    
//    public function setMajorProtocolVersion($version){
//        return 0;
//    }
//    
//    public function newVideoQuery(){
//        return array();
//    }
//    
//    public function getVideoFeed($queryUrl){
//        $feed = simplexml_load_file('src\Sk\MediaApiBundle\Tests\MediaProviderApi\SampleResponses\sampleYouTubeListings.xml');
//        return $feed;
//    }
    
//    public function post($data){
//        $body = simplexml_load_file('src\Sk\MediaApiBundle\Tests\MediaProviderApi\SampleResponses\ytBatchResponse.xml');
//        
//        $response = new \Zend_Http_Response(200, \Zend_Http_Response::extractHeaders($this->headers), $body->asXML(), '1.1', '200');
//        return $response;
//    }
    
//    public function getVideoEntry($id){
//        $data = simplexml_load_file('src\Sk\MediaApiBundle\Tests\MediaProviderApi\SampleResponses\validYouTubeDetails.xml');
//        $ve = new \Zend_Gdata_YouTube_VideoEntry();
//        $ve->transferFromXML($data->asXML());
//        
//        return $ve;
//    }
    
}

?>
