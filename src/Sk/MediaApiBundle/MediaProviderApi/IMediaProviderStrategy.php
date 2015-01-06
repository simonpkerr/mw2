<?php
/*
 * Original code Copyright (c) 2011 Simon Kerr
 *
 * Interface for setting strategy of the api to use by the MediaProviderApi service
 * 7Digital, Amazon, YouTube and GDataImages implement this interface.
 * @author Simon Kerr
 * @version 1.0
 */

namespace Sk\MediaApiBundle\MediaProviderApi;
use Sk\MediaApiBundle\Entity\Decade;
use \SimpleXMLElement;

interface IMediaProviderStrategy {
    
    public function getCacheKey(Decade $decade, $pageNumber = 1);
    //public function getAPIEntity();
    //public function setAPIEntity(API $entity);
    //public function getRandomItems(Decade $decade, $pageNumber = 1);
    public function getListings(Decade $decade);
    //public function getDetails(array $params);
    public function getProviderName();
    //public function getBatch(array $ids);
    public function getItemId($data);
    public function getXML($data);
    public function getItemImage($data);
    public function getItemTitle($data);
    public function getItemDecade($data);
    public function getItemDescription($data);
    public function getItemUrl($data);
   
    
}

?>
