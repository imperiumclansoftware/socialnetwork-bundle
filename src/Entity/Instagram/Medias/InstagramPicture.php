<?php
namespace ICS\SocialnetworkBundle\Entity\Instagram\Medias;

use ICS\MediaBundle\Entity\MediaImage;
use ICS\SocialnetworkBundle\Entity\Instagram\InstagramAccount;
use ICS\SocialnetworkBundle\Entity\Instagram\InstagramMedia;
use ICS\SocialnetworkBundle\Service\InstagramService;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(schema="socialnetwork")
 */
class InstagramPicture extends InstagramMedia
{
    /**
     * @ORM\ManyToOne(targetEntity=MediaImage::class, cascade={"persist"})
     */
    private $picture;
    

    public function __construct($jsonData)
    {
        parent::populateFromWeb($jsonData);
        $this->picture = $jsonData->display_url;
    }

    function downloadSource(InstagramService $service,InstagramAccount $account, $directory = null)
    {
        parent::downloadSource($service,$account);
        if(is_string($this->picture))
        {
            $this->picture=$service->getMedia($account,$this->picture,$this->getId().'.jpg',$directory);
        }
    }

	function getPicture() {
		return $this->picture;
	}

	function setPicture($picture) {
		$this->picture = $picture;
		return $this;
	}

}