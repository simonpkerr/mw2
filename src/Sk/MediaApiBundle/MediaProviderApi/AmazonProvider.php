<?php
/*
 * Original code Copyright (c) 2011 Simon Kerr
 * AmazonAPI controls access to the live amazon api or the dummy amazon api
 * @author Simon Kerr
 * @version 1.0
 */

namespace Sk\MediaApiBundle\MediaProviderApi;

use Sk\MediaApiBundle\MediaProviderApi\AmazonSignedRequest;
use Symfony\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Sk\MediaApiBundle\Entity\Decade;
use Sk\MediaApiBundle\Entity\Genre;
use Sk\MediaApiBundle\Entity\API;
use Sk\MediaApiBundle\MediaProviderApi\Utilities;
use \SimpleXMLElement;
use Sonata\Cache\CacheAdapterInterface;
use Sonata\CacheBundle\Adapter;
use Sonata\Cache\CacheElement;
use \Exception;

class AmazonProvider implements IMediaProviderStrategy {
  const FRIENDLY_NAME = 'Amazon';
  const PROVIDER_NAME = 'amazon';
  const BATCH_PROCESS_THRESHOLD = 10;
  const PAGE_NUMBER_THRESHOLD = 10;
  const CACHE_TTL = 86400;

    //protected $apiEntity;
  private $amazonParameters;
  private $public_key;
  private $private_key;
  private $associate_tag;
  protected $asr;
  private $ITEM_SEARCH = 'ItemSearch';
  private $ITEM_LOOKUP = 'ItemLookup';
  private $cache;

  public function __construct(array $access_params, $amazon_signed_request){

    $this->public_key = $access_params['amazon_public_key'];
    $this->private_key = $access_params['amazon_private_key'];
    $this->associate_tag = $access_params['amazon_associate_tag'];

    $this->asr = $amazon_signed_request;
    $this->amazonParameters = array(
      "Operation"     => $this->ITEM_SEARCH,
      "ResponseGroup" => "Images,Small,Request",
      "Condition"     => "All",
      "MerchantId"    => "All",
      "ItemPage"      => "1",
                //"Sort"          => "salesrank", //sorting functionality to be added later
                //"Sort"          => "-releasedate", // release date oldest to newest
      "Validate"      => "True",
      );

        //$this->cache = $cache;
  }

  public function getCacheKey(Decade $decade){

    return array(
      'decade'        => $decade->getSlug(),
      'pageNumber'    => rand(1, self::PAGE_NUMBER_THRESHOLD),
      'provider'      => self::PROVIDER_NAME
      );
  }

//    public function setAPIEntity(API $entity){
//        $this->apiEntity = $entity;
//    }

//    public function getAPIEntity(){
//        return $this->apiEntity;
//    }

  public function setAmazonSignedRequest($asr){
    $this->asr = $asr;
  }

  public function getItem($data, $extendedInfo = false){
    $item = array(
      'provider'      =>  self::PROVIDER_NAME,
      'id'            =>  $this->getItemId($data),
      'title'         =>  $this->getItemTitle($data),
      'image'         =>  $this->getItemImage($data, $extendedInfo),
      'url'           =>  $this->getItemUrl($data)
    );

    if ($extendedInfo) {
      $item = array_merge($item, array(
        'price'         =>  $this->getItemPrice($data),
        'review'        =>  $this->getItemReview($data),
        'similarItems' =>  $this->getSimilarItems($data)
      ));
    }

    return $item;
  }

  private function getSimilarItems($data) {
    $similarItems = array();
    if (is_null($data->SimiliarProducts)) {
      return null;
    }

    $similarProducts = $data->SimilarProducts->SimilarProduct;

    if (!is_array($similarProducts) && !is_object($similarProducts)) {
      return null;
    }

    try {
      foreach ($similarProducts as $product) {
        array_push($similarItems, array(
          'id'    =>  (string)$product->ASIN,
          'title' =>  (string)$product->Title
        ));
      }
      return $similarItems;

    } catch (Exception $ex) {
      return null;
    }



  }

  private function getItemReview($data) {
    try {
      return strip_tags((string)$data->EditorialReviews->EditorialReview->Content);
    } catch (Exception $ex) {
      return null;
    }
  }

  private function getItemPrice($data) {
    try {
      return (string)$data->OfferSummary->LowestNewPrice->FormattedPrice;
    } catch (Exception $ex) {
      return null;
    }
  }

    //each api will have it's own method for returning the id of a mediaresource for caching purposes.
  private function getItemId($data){
    return (string)$data->ASIN;
  }

  private function getItemImage($data, $extendedInfo){
    try{
      $image = $extendedInfo ? $data->LargeImage->URL : $data->MediumImage->URL;
      return (string)$image;
    } catch(Exception $re){
      return null;
    }
  }

  private function getItemUrl($data){
    try{
      return (string)$data->DetailPageURL;
    } catch(Exception $re){
      return null;
    }
  }

  private function getItemTitle($data){
    try{
      return (string)$data->ItemAttributes->Title;
    } catch(Exception $re){
      return null;
    }
  }

  private function getItemDecade($data) {
    try{
      $title = (string)$data->ItemAttributes->Title;
      $yearParts = array();
      preg_match('/[\[|\(](\d{4})[\]|\)]/i', $title, $yearParts);
      $year = isset($yearParts[1]) ? $yearParts[1] : null;
      if(!is_null($year)){
        return substr($year, 0, 3) . '0s';
      }

      return null;
    }catch(\RuntimeException $re){
      return null;
    }
  }

    /**
     * @param \Sk\MediaApiBundle\Entity\Decade $decade
     * @param type $pageNumber
     * @return type
     * @throws Exception
     */
    public function getListings(Decade $decade){
      $browseNodeArray = array();
      array_push($browseNodeArray, $decade->getAmazonBrowseNodeId());
      $canonicalBrowseNodes = implode(',', $browseNodeArray);
      $params = Utilities::removeNullEntries(array(
            //'Keywords'       =>      $mediaSelection->getKeywords() != null ? $mediaSelection->getKeywords() : null,
        'BrowseNode'     =>      $canonicalBrowseNodes,
        'SearchIndex'    =>      'Video',
        'ItemPage'       =>      rand(1, self::PAGE_NUMBER_THRESHOLD),
        'Sort'           =>      'salesrank',
        ));

      $this->amazonParameters = array_merge($this->amazonParameters, $params);
      $xml_response = $this->queryAmazon($this->amazonParameters, "co.uk");

      try{
        $xml_response = (array)$this->verifyXmlResponse($xml_response)->Items;
      } catch(Exception $e){
        throw $e;
      }

      return $xml_response['Item'];
    }

    /*
     * getDetails handles calls to the live api,
     */
    public function getDetails($decade, $id){
      $this->amazonParameters = array_merge(
        $this->amazonParameters,
        array(
         'Operation'          =>     $this->ITEM_LOOKUP,
         //'ResponseGroup'      =>    'Images,ItemAttributes,Request,Similarities,EditorialReview',
         'ResponseGroup'      =>      'Images,ItemAttributes,Request,Similarities,OfferSummary,EditorialReview',//,BrowseNodes',
         'ItemId'             =>     $id
         ));


      $xml_response = $this->queryAmazon($this->amazonParameters, "co.uk");

      try{
        $verifiedResponse = (array)$this->verifyXmlResponse($xml_response)->Items;
      }catch(Exception $e){
        throw $e;
      }

      $item = $this->getItem($verifiedResponse['Item'], true);

      //get items from other providers using decade and name

      return $item;

    }

    /**
     * Performs a batch process of up to 10 ids to look up
     * for memory walls
     * @param array $ids
     *
     */
    public function getBatch(array $ids){
      if(count($ids) > self::BATCH_PROCESS_THRESHOLD)
        $ids = array_slice ($ids, 0, self::BATCH_PROCESS_THRESHOLD);

      $params = array(
        'ItemId'  => implode(',', $ids),
        );
      return $this->getDetails($params);
    }



    /**
     * Check if the xml received from Amazon is valid
     *
     * @param mixed $response xml response to check
     * @return bool false if the xml is invalid
     * @return mixed the xml response if it is valid
     * @return exception if we could not connect to Amazon
     */
    protected function verifyXmlResponse($response)
    {
      if ($response === False)
      {
        throw new Exception("Could not connect to Amazon");
      }
      else
      {
            //for searches
        if($response->Items->TotalResults == 0 && $this->amazonParameters['Operation'] == $this->ITEM_SEARCH){
          throw new Exception("No results were returned");
        }
            //for lookups
        if(!(bool)$response->Items->Request->IsValid && $this->amazonParameters['Operation'] == $this->ITEM_LOOKUP){
          throw new Exception("Invalid result set");
        }
        if($response->Items->Request->Errors->Error != null) {
          throw new Exception($response->Items->Request->Errors->Error->Message);
        }
        return $response;
      }
    }

    /**
     * Query Amazon with the issued parameters
     *
     * @param array $parameters parameters to query around
     * @return simpleXmlObject xml query response
     */
    protected function queryAmazon($parameters, $region = "com")
    {
      return $this->asr->aws_signed_request($region, $parameters, $this->public_key, $this->private_key, $this->associate_tag);
    }


  }


  ?>
