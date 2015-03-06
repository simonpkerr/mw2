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

class AmazonProviderTest extends WebTestCase {

    private $access_params;
    private $decade;
    private $testASR;
    protected static $kernel;
    protected static $em;
    //private $cache;
    private $router;
    
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
        $this->testASR = $this->getMockBuilder('\\Sk\\MediaApiBundle\\MediaProviderApi\\AmazonSignedRequest')
                ->setMethods(array(
                    'aws_signed_request',
                ))
                ->getMock();
    }
    
    protected function tearDown(){
        unset($this->access_params);
        unset($this->testASR);                
    }
    
    /**
     * @expectedException Exception 
     * @expectedExceptionMessage Could not connect to Amazon 
     */
    public function testGetListingsNoConnectionThrowsException(){
       
        $this->testASR->expects($this->any())
                ->method('aws_signed_request')
                ->will($this->returnValue(False));
        
        $api = new AmazonProvider($this->access_params, $this->testASR);
        $response = $api->getListings($this->decade, 1);
        
    }
    
    /**
     * @expectedException Exception
     * @expectedExceptionMessage No results were returned
     */
    public function testGetListingsEmptyDataSetThrowsException(){
        $empty_xml_data_set = simplexml_load_file('src\Sk\MediaApiBundle\Tests\MediaProviderApi\SampleResponses\empty_xml_response.xml');
        
        $this->testASR->expects($this->any())
                ->method('aws_signed_request')
                ->will($this->returnValue($empty_xml_data_set));
        
        $api = new AmazonProvider($this->access_params, $this->testASR);
        $response = $api->getListings($this->decade, 1);
    }   
    
    public function testGetListingsValidDataSetReturnsResponse(){
        $valid_xml_data_set = simplexml_load_file('src\Sk\MediaApiBundle\Tests\MediaProviderApi\SampleResponses\valid_xml_response.xml');
        
        $this->testASR->expects($this->any())
                ->method('aws_signed_request')
                ->will($this->returnValue($valid_xml_data_set));
        
        $api = new AmazonProvider($this->access_params, $this->testASR);
        $response = $api->getListings($this->decade, 1);
        $this->assertGreaterThan(0,count($response), 'no items were returned');
    }
    
    /**
     * @expectedException RuntimeException 
     * @expectedExceptionMessage Could not connect to Amazon 
     */
//    public function testGetDetailsWithNoResponseThrowsException(){
//        $this->testASR->expects($this->any())
//                ->method('aws_signed_request')
//                ->will($this->returnValue(False));
//        
//        $api = new AmazonProvider($this->access_params, $this->testASR);
//        $response = $api->getDetails(array());
//    }
    
    /**
     * @expectedException RuntimeException 
     */
//    public function testGetDetailsWithErrorResponseThrowsException(){
//        $invalid_xml_data_set = simplexml_load_file('src\Sk\MediaApiBundle\Tests\MediaProviderApi\SampleResponses\invalidSampleAmazonDetails.xml');
//        $this->testASR->expects($this->any())
//                ->method('aws_signed_request')
//                ->will($this->returnValue($invalid_xml_data_set));
//        
//        $api = new AmazonProvider($this->access_params, $this->testASR);
//        $response = $api->getDetails(array());
//    }
    
    
    
   
}


?>
