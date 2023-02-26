<?php

namespace ICS\MediaBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use ICS\MediaBundle\Service\MediaService;;
use ICS\MediaBundle\Entity\MediaFile;

class MediaTransformer implements DataTransformerInterface
{

    private $service;
    private $outputdir='';
    private $oldValue=null;
    private $required;

    public function __construct(MediaService $service)
    {
        $this->service = $service;
    }

    public function transform($media): ?MediaFile
    {
        if($media==null && $this->required == false && $this->oldValue!=null)
        {
            return $this->oldValue;
        }

        return $media;
        
    }

    public function reverseTransform($filepath): ?MediaFile
    {
        if($filepath != null)
        {
            $type = $this->service->getMediaType($filepath->getPathname());
            
            $media = new $type($filepath->getPathname(),$this->outputdir);
            $media->setFileName($filepath->getClientOriginalName());
            return $media;
        }
        elseif($this->oldValue != null && $this->required == false)
        {
            return $this->oldValue;
        }

        return null;
    }


    /**
     * Get the value of outputdir
     */ 
    public function getOutputdir()
    {
        return $this->outputdir;
    }

    /**
     * Set the value of outputdir
     *
     * @return  self
     */ 
    public function setOutputdir($outputdir)
    {
        $this->outputdir = $outputdir;

        return $this;
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