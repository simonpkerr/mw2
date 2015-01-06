<?php

/*
 * Original code Copyright (c) 2011 Simon Kerr
 * AmazonProvider tests
 * @author Simon Kerr
 * @version 1.0
 */

namespace Sk\MediaApiBundle\Tests\MediaProviderApi;
use Sk\MediaApiBundle\MediaProviderApi\AmazonProvider;
use Sk\MediaApiBundle\Entity\Decade;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Sonata\CacheBundle\Adapter\ApcCache;
use Sk\MediaApiBundle\MediaProviderApi\MediaProviderFacade;
use Sk\MediaApiBundle\MediaProviderApi\YouTubeProvider;

class MediaProviderFacadeTest extends WebTestCase {

    private $access_params;
    private $decade;
    private $testASR;
    protected static $kernel;
    protected static $em;
    private $cache;
    private $router;
    private $ytProvider;
    private $gsYouTube;
    
    private $aProvider;
    
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
        $this->access_params = array(
            'amazon_public_key'     => 'apk',
            'amazon_private_key'    => 'aupk',
            'amazon_associate_tag'  => 'aat',
        );
    
        $this->decade = self::$em->getRepository('SkMediaApiBundle:Decade')->getDecadeBySlug('1970s');
        $this->router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        
        $this->gsYouTube = $this->getMockBuilder('\Google_Service_YouTube')
                ->disableOriginalConstructor()
                ->getMock();
        $this->gsYouTube->search = $this->getMockBuilder('\Google_Service_YouTube_Search_Resource')
                ->disableOriginalConstructor()
                ->setMethods(array(
                    'listSearch'
                ))
                ->getMock();
        
        $this->ytProvider = new YouTubeProvider($this->gsYouTube);
        
        $this->testASR = $this->getMockBuilder('\\Sk\\MediaApiBundle\\MediaProviderApi\\AmazonSignedRequest')
                ->setMethods(array(
                    'aws_signed_request',
                ))
                ->getMock();
        
    }
    
    protected function tearDown(){
        unset($this->access_params);
        unset($this->mediaSelection);
        unset($this->testASR);                
        unset($this->cache);
    }
    
    public function testGetMemoryWallAddsErrorMessageOnException(){
        
        $this->gsYouTube->search->expects($this->any())
                ->method('listSearch')
                ->will($this->throwException(new \Exception('an error occurred')));
        
        $mpFacade = self::$kernel->getContainer()->get('sk_media_api.media_provider_api');
        $mpFacade->setProviders(array(
            $this->ytProvider
        ));
        
        $result = $mpFacade->getMemoryWall();
        $this->assertArrayHasKey('errorMsg', $result[1], 'no error message returned');
        
    }
    
    public function testGetMemoryWallAddsRandomItemsFromProviders(){
        //TODO
    }   
    
    public function testGetMemoryWallIfNoCacheKeyReturnsProviderData(){
        $file = './src/Sk/MediaApiBundle/Tests/MediaProviderApi/SampleResponses/sampleYouTubeListings.txt';
        $this->gsYouTube->search->expects($this->any())
                ->method('listSearch')
                ->will($this->returnValue(json_decode(file_get_contents($file),true)));
        
        $this->cache = $this->getMockBuilder('Sonata\\CacheBundle\\Adapter\\ApcCache')
                ->disableOriginalConstructor()
                ->setMethods(array(
                    'has',
                    'set'
                ))
                ->getMock();
        $this->cache->expects($this->any())
                ->method('has')
                ->will($this->returnValue(false));
        
        $mpFacade = self::$kernel->getContainer()->get('sk_media_api.media_provider_api');
        $mpFacade->setCache($this->cache);
        $mpFacade->setProviders(array($this->ytProvider));
        
        $result = $mpFacade->getMemoryWall();
        $this->assertGreaterThan(0, count($result[1]['providerData']), '0 items returned');
    }
    
//    public function testGetRandomItemsIfValidCacheReturnsCache(){
//        $cacheElement = new \Sonata\Cache\CacheElement(array(),array(
//            'item1' => 1,
//            'item2' => 2,
//            'item3' => 3
//        ));
//
//        $this->cache = $this->getMockBuilder('Sonata\\CacheBundle\\Adapter\\ApcCache')
//                ->disableOriginalConstructor()
//                ->setMethods(array(
//                    'has',
//                    'get'
//                ))
//                ->getMock();
//        $this->cache->expects($this->any())
//                ->method('has')
//                ->will($this->returnValue(true));
//        
//        $this->cache->expects($this->any())
//                ->method('get')
//                ->will($this->returnValue($cacheElement));                
//        
//        $api = new AmazonProvider($this->access_params, new TestAmazonSignedRequest(), $this->cache);
//        $response = $api->getRandomItems($this->decade);
//        $this->assertEquals(count($response), 3, '3 random items not returned');
//    }
    
   
    
   
}


?>
