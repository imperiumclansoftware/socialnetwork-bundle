<?php

namespace ICS\SocialnetworkBundle\Form\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use ICS\MediaBundle\Service\MediaService;;
use ICS\MediaBundle\MediaBundle;
use ICS\MediaBundle\Entity\MediaFile;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Collections\Collection;

class SocialNetworkCollectionTransformer implements DataTransformerInterface
{
    private $oldValue=null;
    private $required;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em=$em;
        $this->oldValue = new ArrayCollection();
    }

    public function transform($media): ?Collection
    {
        return $media;
    }

    public function reverseTransform($files): ?Collection
    {
        $collection = $this->oldValue;
        
        

        return $collection;
    }

    /**
     * Get the value of oldValue
     */ 
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * Set the value of oldValue
     *
     * @return  self
     */ 
    public function setOldValue($oldValue)
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    /**
     * Get the value of required
     */ 
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Set the value of required
     *
     * @return  self
     */ 
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }
}