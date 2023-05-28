<?php
namespace ICS\SocialnetworkBundle\Entity\Instagram;

use DateTime;
use ICS\SocialnetworkBundle\Entity\Instagram\Medias\InstagramPicture;
use ICS\SocialnetworkBundle\Entity\Instagram\Medias\InstagramSidecar;
use ICS\SocialnetworkBundle\Entity\Instagram\Medias\InstagramVideo;
use ICS\SocialnetworkBundle\Service\InstagramService;
use Doctrine\ORM\Mapping as ORM;
use ICS\MediaBundle\Entity\MediaImage;

/**
 * @ORM\Entity()
 * @ORM\Table(schema="socialnetwork")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 */
abstract class InstagramMedia
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity=MediaImage::class, cascade={"persist"})
     */
    private $thumbnail;
    /**
     * @ORM\Column(type="text")
     */
    private $caption="";
    /**
     * @ORM\Column(type="integer")
     */
    private $comments=0;
    /**
     * @ORM\Column(type="integer")
     */
    private $likes=0;
    /**
     * @ORM\Column(type="datetime", nullable="true")
     */
    private $date;
    /**
     * @ORM\ManyToOne(targetEntity=InstagramAccount::class, inversedBy="timeline", cascade={"persist"})
     */
    private $account;
    /**
     * @ORM\ManyToOne(targetEntity=InstagramSidecar::class, inversedBy="elements", cascade={"persist"})
     */
    private $sidecar;

    protected function populateFromWeb($jsonData)
    {
        $this->id = $jsonData->id;
        if(property_exists($jsonData,'thumbnail_src') && is_string($jsonData->thumbnail_src))
        {
            $this->thumbnail = $jsonData->thumbnail_src;
        }

        if(property_exists($jsonData,'edge_media_to_caption'))
        {
            foreach($jsonData->edge_media_to_caption->edges as $caption)
            {
                if(property_exists($caption,'node'))
                {
                    if($this->account != null)
                    {
                        $this->caption .= $this->account->replaceLink($caption->node->text)."<br/>";
                    }
                    else
                    {
                        $this->caption .= $caption->node->text."<br/>";
                    }
                }
                
            }
        }
        if(property_exists($jsonData,'edge_media_to_comment'))
        {
            $this->comments = $jsonData->edge_media_to_comment->count;
        }

        if(property_exists($jsonData,'taken_at_timestamp'))
        {
            $this->date = new DateTime();
            $this->date->setTimestamp($jsonData->taken_at_timestamp);
        }
        if(property_exists($jsonData,'edge_liked_by'))
        {
            $this->likes = $jsonData->edge_liked_by->count;
        }
    }

    static function getFromWeb($jsonData)
    {
        switch($jsonData->__typename)
        {
            case 'GraphVideo': $result = new InstagramVideo($jsonData); break;
            case 'GraphSidecar': $result = new InstagramSidecar($jsonData); break;
            case 'GraphImage': $result = new InstagramPicture($jsonData); break;
        }
        
        return $result;
    }

    public function downloadSource(InstagramService $service,InstagramAccount $account, $directory = null)
    {
        if($this->thumbnail!=null && is_string($this->thumbnail))
        {
            $this->thumbnail=$service->getMedia($account,$this->thumbnail,$this->id.'.jpg','thumbnails');
        }
    }

	function getId() {
		return $this->id;
	}

	function setId($id) {
		$this->id = $id;
		return $this;
	}

	function getThumbnail() {
		return $this->thumbnail;
	}

	function setThumbnail($thumbnail) {
		$this->thumbnail = $thumbnail;
		return $this;
	}

	function getCaption() {
		return $this->caption;
	}

	function setCaption($caption) {
		$this->caption = $caption;
		return $this;
	}

	function getComments() {
		return $this->comments;
	}

	function setComments($comments) {
		$this->comments = $comments;
		return $this;
	}

	function getLikes() {
		return $this->likes;
	}

	function setLikes($likes) {
		$this->likes = $likes;
		return $this;
	}

	function getDate() {
		return $this->date;
	}

	function setDate($date) {
		$this->date = $date;
		return $this;
	}

	function getAccount() {
		return $this->account;
	}

	function setAccount($account) {
		$this->account = $account;
		return $this;
	}

	function getSidecar() {
		return $this->sidecar;
	}

	function setSidecar($sidecar) {
		$this->sidecar = $sidecar;
		return $this;
	}

}