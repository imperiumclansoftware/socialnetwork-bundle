<?php
namespace ICS\SocialnetworkBundle\Entity\Instagram;

use Doctrine\Common\Collections\ArrayCollection;
use ICS\SocialnetworkBundle\Entity\SocialAccount;
use ICS\SocialnetworkBundle\Service\InstagramService;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(schema="socialnetwork")
 */
class InstagramAccount extends SocialAccount
{
	/**
	 * @ORM\Column(type="integer")
	 */
	private $instagramId;
	/**
	 * @ORM\Column(type="string")
	 */
    private $fullname;
	/**
	 * @ORM\Column(type="text")
	 */
    private $biography;
	/**
	 * @ORM\OneToMany(targetEntity=InstagramMedia::class, mappedBy="account", cascade={"persist"})
	 * @ORM\OrderBy({"date": "DESC"})
	 */
    private $timeline;
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $verified;

	public function __construct()
	{
		$this->timeline = new ArrayCollection();
	}

    public static function fromWeb($jsonObject): InstagramAccount
    {
		$result = null;

		if($jsonObject != '')
		{
			$result= new InstagramAccount();
			$result->instagramId=$jsonObject->id;
			$result->name = $jsonObject->username;
			$result->fullname = $jsonObject->full_name;
			$result->biography = $result->replace_link($jsonObject->biography);
			$result->followers = $jsonObject->edge_followed_by->count;
			$result->profilePicture = $jsonObject->profile_pic_url_hd;

			foreach($jsonObject->edge_owner_to_timeline_media->edges as $jsonMedia)
			{
				$tmpMedia = InstagramMedia::getFromWeb($jsonMedia->node);
				if(!$result->timeline->contains($tmpMedia))
				{
					$tmpMedia->setAccount($result);
					$result->timeline->add($tmpMedia);
				}
				
			}

			$result->verified = $jsonObject->is_verified == 'true';
		}
        return $result;
    }

	public function replace_link(string $text)
	{
		$text = str_replace("\n","<br/>",$text);
		
		$text = preg_replace('"@(\S+)"', '<a href="$1">@$1</a>', $text);

		return $text;
	}

	function downloadSource(InstagramService $service)
    {
		if(is_string($this->profilePicture) && $this->profilePicture != '')
		{
        	$this->profilePicture=$service->getMedia($this,$this->profilePicture,'profile.jpg');
		}

		foreach($this->timeline as $media)			
		{
			$media->downloadSource($service,$this);
		}

		
    }

	function getFullname() {
		return $this->fullname;
	}

	function setFullname($fullname) {
		$this->fullname = $fullname;
		return $this;
	}

	function getBiography() {
		return $this->biography;
	}

	function setBiography($biography) {
		$this->biography = $biography;
		return $this;
	}

	function getTimeline() {
		return $this->timeline;
	}

	function setTimeline($timeline) {
		$this->timeline = $timeline;
		return $this;
	}

	function getVerified() {
		return $this->verified;
	}

	function setVerified($verified) {
		$this->verified = $verified;
		return $this;
	}

	function getInstagramId() {
		return $this->instagramId;
	}

	function setInstagramId($instagramId) {
		$this->instagramId = $instagramId;
		return $this;
	}

}