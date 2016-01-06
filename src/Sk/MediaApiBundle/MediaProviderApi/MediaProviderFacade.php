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

    public function getMemoryWall($decadeSlug){
        //return test data
        //$wallData = json_decode(file_get_contents('http://mw.local/web/sample-wall-1980s.json'), true);
        //return $wallData['wallData'];

        $wallData = array();
        $wallData['errorMessages'] = array();
        $decade = null;
        if($decadeSlug == 'any'){
            $decades = $this->em->getRepository('SkMediaApiBundle:Decade')->getDecades();
            $randomKey = array_rand($decades);
            $decade = $decades[$randomKey];
        } else {
            $decade = $this->em->getRepository('SkMediaApiBundle:Decade')->getDecadeBySlug($decadeSlug);
        }

//        $decade = $this->em->getRepository('SkMediaApiBundle:Decade')->getDecadeBySlug('1980s');
        //make this something that each provider does
        //$pageNumber = rand(1, 10);
        $wallData['metaData'] = array(
            'decade' => $decade->getSlug()
            //'pageNumber' => $pageNumber
        );
        $wallData['providerData'] = array();

        foreach($this->mediaProviders as $mediaProvider){
            $items = array();
            $errorMsg = null;
            try {
                $items = $this->getRandomItems($mediaProvider, $decade);
            } catch (Exception $ex) {
                array_push($wallData['errorMessages'], $ex->getMessage());
            }

            $wallData['providerData'] = array_merge($wallData['providerData'], $items);
        }

        shuffle($wallData['providerData']);
        return $wallData;
    }

    private function getRandomItems(IMediaProviderStrategy $providerStrategy, $decade){
        $items = array();
        $cacheKey = $providerStrategy->getCacheKey($decade);
        if($this->cache->has($cacheKey)){
            $cacheElement = $this->cache->get($cacheKey);
            $items = $cacheElement->getData();
        } else {
            $response = (array)$providerStrategy->getListings($decade);
            foreach($response as $item){
                array_push($items, $providerStrategy->getItem($item));
            }
            $this->cache->set($cacheKey, $items, $providerStrategy::CACHE_TTL);
        }

        $listingsCount = count($items);
        $listingsCount = $listingsCount > 5 ? 5 : $listingsCount;
        shuffle($items);
        $randomItems = array_slice($items, 0, $listingsCount);

        return $randomItems;
    }

    public function getMemoryWallItem($provider, $id)
    {
        //remember to cache item
        if (array_key_exists($provider, $this->mediaProviders)) {
            $this->mediaProviders[$provider]->getDetails($id);
        }

    }
}
