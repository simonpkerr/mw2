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
    
    public static function setUpBeforeClass(){
        self::$kernel = static::createKernel();
        self::$kernel->boot();
        self::$em = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }
    
    public static function tearDownAfterClass(){
        self::$kernel = null;
        self::$em = null;
    }
    
    public function testGetCategoryListOfInvalidPageIdReturnsThrowsException(){
        
    }
    
    public function testGetValidPageIdReturnsCategoryList(){
        
    }
    
   
    
}
