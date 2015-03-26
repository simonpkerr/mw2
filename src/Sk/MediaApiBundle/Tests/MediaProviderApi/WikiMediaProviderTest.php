<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Sk\MediaApiBundle\Tests\MediaProviderApi;
use Sk\MediaApiBundle\MediaProviderApi\WikiMediaProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Sonata\CacheBundle\Adapter\ApcCache;

class WikiMediaProviderTest extends WebTestCase {
    
    private $decade;
    protected static $kernel;
    protected static $em;
    //private $cache;
    private $router;
    private $req;
    private $access_params;
    
    public static function setUpBeforeClass(){
        self::$kernel = static::createKernel();
        self::$kernel->boot();
        self::$em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    public static function tearDownAfterClass(){
        self::$kernel = null;
        self::$em = null;
    }
    
    protected function setUp() {
        $this->access_params = array(
            'wikimedia_endpoint'     => 'ep',
            'wikimedia_user_agent'    => 'ua'
        );
        $this->req = $this->getMockBuilder('\\Sk\\MediaApiBundle\\MediaProviderApi\\SimpleRequest')
                ->setMethods(array(
                    'makeRequest',
                ))
                ->getMock();
        
        $this->decade = self::$em->getRepository('SkMediaApiBundle:Decade')->getDecadeBySlug('1970s');
    }
    
    protected function tearDown(){
        unset($this->access_params);
        unset($this->req);                
    }
    
    /**
     * @expectedException Exception 
     * @expectedExceptionMessage Could not connect to WikiMedia 
     */
    public function testGetListingsIfCantConnectThrowsException(){
        $this->req->expects($this->any())
                ->method('makeRequest')
                ->will($this->returnValue(False));
        
        $provider = new WikiMediaProvider($this->access_params, $this->req);
        $response = $provider->getListings($this->decade, 1);
    }
    
    /**
     * @expectedException Exception
     * @expectedExceptionMessage Query node not found
     */
    public function testGetListingsIfNoQueryElementThrowsException(){
        $this->req->expects($this->any())
                ->method('makeRequest')
                ->will($this->returnValue('{
    "servedby": "mw1189",
    "error": {
        "code": "gcmnosuchpageid",
        "info": "There is no page with ID 0",
        "*": "See https://commons.wikimedia.org/w/api.php for API usage"
    }
}'));
        
        $provider = new WikiMediaProvider($this->access_params, $this->req);
        $response = $provider->getListings($this->decade, 1);
    }
    
    /**
     * @expectedException Exception
     * @expectedExceptionMessage No results were returned
     */
    public function testGetListingsIfNoPagesThrowsException(){
        $this->req->expects($this->any())
                ->method('makeRequest')
                ->will($this->returnValue('{
    "continue": {
        "gcmcontinue": "file|4f44444c59204841505059204d45524d4149442e4a5047|15859408",
        "continue": "gcmcontinue||"
    },
    "batchcomplete": "",
    "query": {
        "pages": {}
    }
    }'));
        
        $provider = new WikiMediaProvider($this->access_params, $this->req);
        $response = $provider->getListings($this->decade, 1);
    }
    
    
    
    
   
    
}
