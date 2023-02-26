<?php
namespace ICS\SocialnetworkBundle\Entity\Instagram\Medias;

use ICS\MediaBundle\Entity\MediaVideo;
use ICS\SocialnetworkBundle\Entity\Instagram\InstagramAccount;
use ICS\SocialnetworkBundle\Entity\Instagram\InstagramMedia;
use ICS\SocialnetworkBundle\Service\InstagramService;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(schema="socialnetwork")
 */
class InstagramVideo extends InstagramMedia
{
	/**
	 * @ORM\ManyToOne(targetEntity=MediaVideo::class, cascade={"persist"})
	 */
    private $video;
	/**
	 * @ORM\Column(type="integer")
	 */
    private $view;

    public function __construct($jsonData)
    {
        parent::populateFromWeb($jsonData);
        $this->video = $jsonData->video_url;
        $this->view = $jsonData->video_view_count;
    }

    public function downloadSource(InstagramService $service,InstagramAccount $account, $directory = null)
    {
        parent::downloadSource($service,$account);
		if(is_string($this->video))
		{
        	$this->video=$service->getMedia($account,$this->video,$this->getId().'.mp4', $directory);
		}
    }

	function getVideo() {
		return $this->video;
	}

	function setVideo($video) {
		$this->video = $video;
		return $this;
	}

	function getView() {
		return $this->view;
	}

	function setView($view) {
		$this->view = $view;
		return $this;
	}

}