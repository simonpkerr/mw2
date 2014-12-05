<?php

namespace Sk\MediaApiBundle\MediaProviderApi;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;
use Sk\MediaApiBundle\MediaProviderApi\Utilities;
use Sk\MediaApiBundle\MediaProviderApi\XMLFileManager;
use Sk\MediaApiBundle\Entity\API;
use Sk\MediaApiBundle\Entity\MediaType;
use Sk\MediaApiBundle\Entity\Decade;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    
    public function __construct($debug_mode, EntityManager $em, Session $session, array $providers){
        $this->debugMode = $debug_mode;
        $this->em = $em;
        $this->session = $session;
        $this->mediaProviders = $providers;        
    }
    
    public function getMemoryWall(){
        $wallData = array();
        $decades = $this->em->getRepository('SkMediaApiBundle:Decade')->getDecades();
        $randomKey = array_rand($decades);
        $decade = $decades[$randomKey];
        array_push($wallData, array(
           'decade' => $decade
        ));
        
        foreach($this->mediaProviders as $mediaProvider){       
            array_push($wallData, array(
                'mediaProvider' => $mediaProvider->getProviderName(),
                'providerData'  => json_encode($mediaProvider->getListings($decade, rand(1, 10)))
            ));
        }
        
        return $wallData;
    }
}
