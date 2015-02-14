<?php

namespace Sk\MediaApiBundle\MediaProviderApi;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use Sk\MediaApiBundle\MediaProviderApi\Utilities;
use Sk\MediaApiBundle\Entity\MediaType;
use Sk\MediaApiBundle\Entity\Decade;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\Cache\CacheAdapterInterface;
use Sonata\CacheBundle\Adapter;
use Sonata\Cache\CacheElement;
use \RuntimeException;
use \Exception;
/**
 * MediaProviderFacade provides an interface to get data from all other media providers
 * it checks cached data and adds to or clears based on each individual providers cache
 * threshold
 *
 * @author Simon Kerr
 */
class MediaProviderFacade {
    protected $mediaProviders;
    protected $debugMode;
    protected $doctrine;
    protected $em;
    protected $cache;
    
    public function __construct(EntityManager $em, array $providers, CacheAdapterInterface $cache, $debug_mode = false){
        $this->debugMode = $debug_mode;
        $this->em = $em;
        //$this->session = $session;
        $this->mediaProviders = $providers;     
        $this->cache = $cache;
    }
    
    public function setProviders(array $providers){
        $this->mediaProviders = $providers;
    }
    
    public function setCache(CacheAdapterInterface $cache){
        $this->cache = $cache;
    }    
    
    public function getMemoryWall(){
        $wallData = array();
        $decades = $this->em->getRepository('SkMediaApiBundle:Decade')->getDecades();
        $randomKey = array_rand($decades);
        //$decade = $decades[$randomKey]; 
        $decade = $this->em->getRepository('SkMediaApiBundle:Decade')->getDecadeBySlug('1980s'); 
        $pageNumber = rand(1, 10);
        $wallData['metaData'] = array(
            'decade' => $decade->getSlug(),
            'pageNumber' => $pageNumber
        );
        $wallData['providerData'] = array();
        
        foreach($this->mediaProviders as $mediaProvider){       
            $items = array();
            $errorMsg = null;
            try {
                $items = $this->getRandomItems($mediaProvider, $decade, $pageNumber);
            } catch (Exception $ex) {
                array_push($wallData['errorMessages'], $ex->getMessage());
            }
              
            $wallData['providerData'] = array_merge($wallData['providerData'], $items);
            
//            array_push($wallData['providers'], array(
//                'mediaProvider' => $mediaProvider->getProviderName(),
//                'providerData'  => $items,
//                'errorMsg'      => $errorMsg
//            ));
        }
        
        shuffle($wallData['providerData']);
        return $wallData;
    }
    
    private function getRandomItems($providerStrategy, $decade, $pageNumber){
        $items = array();
        $cacheKey = $providerStrategy->getCacheKey($decade, $pageNumber);
        if($this->cache->has($cacheKey)){
            $cacheElement = $this->cache->get($cacheKey);
            $items = $cacheElement->getData();
        } else {
            $response = (array)$providerStrategy->getListings($decade, $pageNumber);
            foreach($response as $item){
                //could just provide data object to api, each directive knows how to consume data
                array_push($items, array(
                    'provider'      =>  $providerStrategy::PROVIDER_NAME,
                    'id'            =>  $providerStrategy->getItemId($item),
                    'title'         =>  $providerStrategy->getItemTitle($item),
                    'image'         =>  $providerStrategy->getItemImage($item),
                    'url'           =>  $providerStrategy->getItemUrl($item),
//                    'price'         =>  $providerStrategy->getItemPrice($item),
                    'description'   =>  $providerStrategy->getItemDescription($item)
                    //get additional data such as categories for wikimedia
                ));
            }
            $this->cache->set($cacheKey, $items, $providerStrategy::CACHE_TTL);
        }        
        
        $listingsCount = count($items);
        $listingsCount = $listingsCount > 5 ? 5 : $listingsCount;
        shuffle($items);
        $randomItems = array_slice($items, 0, $listingsCount);
        
        return $randomItems;
    }
}
