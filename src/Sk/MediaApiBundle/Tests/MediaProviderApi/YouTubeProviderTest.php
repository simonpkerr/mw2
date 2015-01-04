<?php

/*
 * Original code Copyright (c) 2015 Simon Kerr
 * AmazonAPI tests
 * @author Simon Kerr
 * @version 1.0
 */


namespace Sk\MediaApiBundle\Tests\MediaProviderApi;
use Sk\MediaApiBundle\MediaProviderApi\YouTubeProvider;
use Sk\MediaApiBundle\Entity\Decade;
use Sonata\CacheBundle\Adapter\ApcCache;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class YouTubeProviderTest extends WebTestCase {

    private $decade;
    protected static $kernel;
    protected static $em;
    private $cache;
    private $router;
    private $params;
    private $ms;
    private $gsYouTube;
    
    public static function setUpBeforeClass(){
        self::$kernel = static::createKernel();
        self::$kernel->boot();
        self::$em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    public static function tearDownAfterClass(){
        self::$kernel = null;
        self::$em = null;
    }
    
    protected function setUp(){
        $this->decade = self::$em->getRepository('SkMediaApiBundle:Decade')->getDecadeBySlug('1970s');
        $this->router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $this->cache = new ApcCache($this->router, 'token', 'prefix_', array());
        
        //$this->gsYouTube = new YouTubeProvider(new TestGoogleServiceYouTube(), $this->cache);
        $this->gsYouTube = $this->getMockBuilder('\Google_Service_YouTube')
                ->disableOriginalConstructor()
                ->setMethods(array(
                   'search.listSearch' 
                ))
                ->getMock();
        
    }
    
    public function tearDown(){
        unset($this->gsYouTube);
        unset($this->params);
        unset($this->ms);
    }
    
    /**
     * @expectedException Exception 
     **/
    public function testNoResponseThrowsRuntimeException(){
        $this->gsYouTube->expects($this->any())
                ->method('search.listSearch')
                ->will($this->throwException(new \Exception()));
        
        $yt = new YouTubeProvider($this->gsYouTube, $this->cache);
        $yt->getListings($this->decade);
    }
    
    /**
     * @expectedException LengthException 
     * @expectedExceptionMessage No results were returned
     */
    public function testEmptyResponseReturnsLengthException(){
        $emptyFile = './src/Sk/MediaApiBundle/Tests/MediaProviderApi/SampleResponses/sampleEmptyYouTubeListings.txt';
        $this->gsYouTube->expects($this->any())
                ->method('search.listSearch')
                ->will($this->returnValue(json_decode(file_get_contents($emptyFile),true)));
        
        $yt = new YouTubeProvider($this->gsYouTube, $this->cache);
        $yt->getListings($this->decade);
    }
    
    
    
    /**
     * @expectedException InvalidArgumentException 
     * @expectedExceptionMessage No id was passed to Youtube
     */
//    public function testGetYouTubeDetailsWithNoParamsThrowsException(){
//              
//        $yt = new YouTubeAPI($this->ytObj);
//        $yt->getDetails(array());
//    }
    
    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Could not connect to YouTube
     */
//    public function testGetYouTubeDetailsNoConnectionThrowsException(){
//        $ytObj = $this->getMock('\Zend_Gdata_YouTube',
//                array(
//                    'getVideoEntry'
//                ));
//
//        $ytObj->expects($this->any())
//                ->method('getVideoEntry')
//                ->will($this->returnValue(false));
//        
//        $yt = new YouTubeAPI();
//        $yt->setRequestObject($ytObj);
//        $yt->getDetails(array('ItemId' => '1'));
//    }
    
    /**
     * @expectedException LengthException 
     * @expectedExceptionMessage No results were returned
     */
//    public function testGetYouTubeDetailsNoResultsThrowsException(){
//        $ytObj = $this->getMock('\Zend_Gdata_YouTube',
//                array(
//                    'getVideoEntry'
//                ));
//
//        $ytObj->expects($this->any())
//                ->method('getVideoEntry')
//                ->will($this->returnValue(array()));
//        
//        $yt = new YouTubeAPI();
//        $yt->setRequestObject($ytObj);
//        $yt->getDetails(array('ItemId' => '1'));
//    }
    
    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Could not connect to YouTube
     */
//    public function testGetBatchJobReturnsFalseThrowsException(){
//        $ytObj = $this->getMockBuilder('\\SkNd\\MediaBundle\\MediaAPI\\TestYouTubeRequest')
//                ->setMethods(array(
//                    'post'
//                ))
//                ->getMock();
//
//        $ytObj->expects($this->any())
//                ->method('post')
//                ->will($this->returnValue(false));
//        
//        $yt = new YouTubeAPI();
//        $yt->setRequestObject($ytObj);
//        $yt->getBatch(array('ItemId' => '1'));
//    }
    
    /**
     * @expectedException RuntimeException 
     * @expectedExceptionMessage Could not parse response
     */
//    public function testGetBatchNoResultsThrowsException(){
//        $ytObj = $this->getMockBuilder('\\SkNd\\MediaBundle\\MediaAPI\\TestYouTubeRequest')
//                ->setMethods(array(
//                    'post'
//                ))
//                ->getMock();
//        
//        $mockResponse = $this->getMockBuilder('\Zend_Http_Response')
//                ->disableOriginalConstructor()             
//                ->setMethods(array(
//                    'getStatus',
//                    'getBody'
//                ))
//                ->getMock();
//        $mockResponse->expects($this->any())
//                ->method('getStatus')
//                ->will($this->returnValue(200));//indicating valid response
//        
//        $mockResponse->expects($this->any())
//                ->method('getBody')
//                ->will($this->returnValue(array()));//indicating valid response
//
//        $ytObj->expects($this->any())
//                ->method('post')
//                ->will($this->returnValue($mockResponse));
//        
//        $yt = new YouTubeAPI();
//        $yt->setRequestObject($ytObj);
//        $yt->getBatch(array('ItemId' => '1'));
//    }
//    
//    /**
//     * @expectedException RuntimeException 
//     * @expectedExceptionMessage A problem occurred with the response
//     */
//    public function testGetBatchReturnsNoSuccessStatusThrowsException(){
//        $ytObj = $this->getMockBuilder('\\SkNd\\MediaBundle\\MediaAPI\\TestYouTubeRequest')
//                ->setMethods(array(
//                    'post'
//                ))
//                ->getMock();
//        
//        $mockResponse = $this->getMockBuilder('\Zend_Http_Response')
//                ->disableOriginalConstructor()             
//                ->setMethods(array(
//                    'getStatus',
//                    'getBody'
//                ))
//                ->getMock();
//        $mockResponse->expects($this->any())
//                ->method('getStatus')
//                ->will($this->returnValue(400));//indicating bad request
//        
//        $mockResponse->expects($this->any())
//                ->method('getBody')
//                ->will($this->returnValue(array()));//indicating valid response
//
//        $ytObj->expects($this->any())
//                ->method('post')
//                ->will($this->returnValue($mockResponse));
//        
//        $yt = new YouTubeAPI();
//        $yt->setRequestObject($ytObj);
//        $yt->getBatch(array('ItemId' => '1'));
//    }
//    
//    
//    public function testGetBatchReturnsRemovedVideosAddsPlaceHolderRecord(){
//        $ytObj = $this->getMock('\Zend_Gdata_YouTube',
//                array(
//                    'getVideoEntry'
//                ));
//
//        $ve = $this->getMock('\Zend_Gdata_YouTube_VideoEntry',
//                array(
//                    'getVideoTitle'
//                ));
//        
//        $ve->expects($this->any())
//                ->method('getVideoTitle')
//                ->will($this->returnValue(null));
//        
//        $ytObj->expects($this->any())
//                ->method('getVideoEntry')
//                ->will($this->returnValue($ve));
//        
//        $yt = new YouTubeAPI();
//        $yt->setRequestObject($ytObj);
//        $response = $yt->getDetails(array('ItemId' => '1'));
//        
//        $this->assertEquals('-1', $response->id, 'id was not -1' );
//    }

}


?>
