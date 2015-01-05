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
/**
 * MediaProviderFacade provides an interface to get data from all other media providers
 * it checks cached data and adds to or clears based on each individual providers cache
 * threshold
 *
 * @author Simon Kerr
 */
class MediaProviderFacade {
    protected $mediaProviders;
    protected $debugMode = false;
    protected $doctrine;
    protected $em;
    protected $cache;
    
    public function __construct($debug_mode, EntityManager $em, Session $session, array $providers, CacheAdapterInterface $cache){
        $this->debugMode = $debug_mode;
        $this->em = $em;
        $this->session = $session;
        $this->mediaProviders = $providers;     
        $this->cache = $cache;
    }
    
    public function getMemoryWall(){
        $wallData = array();
        $decades = $this->em->getRepository('SkMediaApiBundle:Decade')->getDecades();
        $randomKey = array_rand($decades);
        $decade = $decades[$randomKey]; //$this->em->getRepository('SkMediaApiBundle:Decade')->getDecadeBySlug('1940s'); 
        $pageNumber = rand(1, 10);
        array_push($wallData, array(
            'decade' => $decade->getSlug(),
            'pageNumber' => $pageNumber
        ));
        
        foreach($this->mediaProviders as $mediaProvider){       
            $items = array();
            $errorMsg = null;
            try {
                $items = $this->getRandomItems($mediaProvider, $decade, $pageNumber);
            } catch (Exception $ex) {
                $errorMsg = $ex->getMessage();
            }
            
            array_push($wallData, array(
                'mediaProvider' => $mediaProvider->getProviderName(),
                'providerData'  => $items,
                'errorMsg'      => $errorMsg
            ));
        }
        
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
            //$response = $providerStrategy->getResponse($response);
            foreach($response as $item){
                array_push($items, array(
                    'title'         =>  $providerStrategy->getItemTitle($item),
                    'image'         =>  $providerStrategy->getItemImage($item),
                    'url'           =>  $providerStrategy->getItemUrl($item),
                    'price'         =>  $providerStrategy->getItemPrice($item),
                    'description'   =>  $providerStrategy->getItemDescription($item)
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
