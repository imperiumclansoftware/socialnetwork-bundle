<?php
namespace ICS\SocialnetworkBundle\Entity\Instagram\Medias;

use Doctrine\Common\Collections\ArrayCollection;
use ICS\SocialnetworkBundle\Entity\Instagram\InstagramAccount;
use ICS\SocialnetworkBundle\Entity\Instagram\InstagramMedia;
use ICS\SocialnetworkBundle\Service\InstagramService;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(schema="socialnetwork")
 */
class InstagramSidecar extends InstagramMedia
{
    /**
     * @ORM\OneToMany(targetEntity=InstagramMedia::class, mappedBy="sidecar", cascade={"persist"})
     */
    private $elements;

    private $tmpUrls = [];

    public function __construct($jsonData)
    {
        parent::populateFromWeb($jsonData);
        $this->elements = new ArrayCollection();

        foreach($jsonData->edge_sidecar_to_children->edges as $media)
        {
            $this->tmpUrls[] = InstagramMedia::getFromWeb($media->node,$this->getId());
        }
        
    }

    function downloadSource(InstagramService $service,InstagramAccount $account, $directory = null)
    {
        parent::downloadSource($service,$account);
       
        foreach($this->tmpUrls as $key=>$url)
        {
            $url->downloadSource($service,$account,$this->getId());
            $url->setSidecar($this);
            $this->elements->add($url);   
        }

        
    }

	function getElements() {
		return $this->elements;
	}

	function setElements($elements) {
		$this->elements = $elements;
		return $this;
	}

}