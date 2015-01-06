<?php

/*
 * Original code Copyright (c) 2011 Simon Kerr
 * MediaType gets and sets the current media type
 * checking for cached versions of details 
 * @author Simon Kerr
 * @version 1.0
 */
namespace Sk\MediaApiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Sk\MediaApiBundle\Repository\MediaTypeRepository")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class MediaType
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
    protected $mediaName;
    
    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $amazonBrowseNodeId;
    
    //protected $genres;
    
    /**
     * @Gedmo\Slug(fields={"mediaName"})
     * @ORM\Column(type="string", length=100)
     */
    protected $slug;
    
        
    public function __construct(){
        //$this->genres = new ArrayCollection();
    }
    
//    public function getGenres(){
//        return $this->genres;
//    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setMediaName($mediaName)
    {
        $this->mediaName = $mediaName;
    }

    public function getMediaName()
    {
        return $this->mediaName;
    }

    public function setAmazonBrowseNodeId($amazonBrowseNodeId)
    {
        $this->amazonBrowseNodeId = $amazonBrowseNodeId;
    }

    public function getAmazonBrowseNodeId()
    {
        return $this->amazonBrowseNodeId;
    }

//    public function addGenre(Genre $genres)
//    {
//        $this->genres[] = $genres;
//    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function getSlug()
    {
        return $this->slug;
    }
    

}