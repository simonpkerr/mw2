<?php

/*
 * Original code Copyright (c) 2011 Simon Kerr
 * Decade entity for getting and setting decade data
 * @author Simon Kerr
 * @version 1.0
 */

namespace Sk\MediaApiBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Sk\MediaApiBundle\Repository\DecadeRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Decade
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", length=75)
     */
    protected $decadeName;
    
    /**
     * @ORM\Column(type="string", length=75)
     */
    protected $amazonBrowseNodeId;
    
    /**
     * @ORM\Column(type="string", length=75)
     */
    protected $sevenDigitalTag;
    
    /**
     * @ORM\Column(type="string", length=75)
     */
    protected $wikiMediaId;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $slug;
    
    public function getId()
    {
        return $this->id;
    }
   
    public function setDecadeName($decadeName)
    {
        $this->decadeName = $decadeName;
    }
    public function getDecadeName()
    {
        return $this->decadeName;
    }
    public function setAmazonBrowseNodeId($amazonBrowseNodeId)
    {
        $this->amazonBrowseNodeId = $amazonBrowseNodeId;
    }
    public function getAmazonBrowseNodeId()
    {
        return $this->amazonBrowseNodeId;
    }

    public function setSevenDigitalTag($sevenDigitalTag)
    {
        $this->sevenDigitalTag = $sevenDigitalTag;
    }
    public function getSevenDigitalTag()
    {
        return $this->sevenDigitalTag;
    }
    
    public function setWikiMediaId($wikiMediaId)
    {
        $this->wikiMediaId = $wikiMediaId;
    }
    public function getWikiMediaId()
    {
        return $this->wikiMediaId;
    }
    
    public function getSlug()
    {
        return $this->slug;
    }
}