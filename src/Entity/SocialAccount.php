<?php
namespace ICS\SocialnetworkBundle\Entity;

use ICS\MediaBundle\Entity\MediaImage;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(schema="socialnetwork")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\HasLifecycleCallbacks
 */
abstract class SocialAccount
{
	/**
	 * @ORM\Column(type="integer")
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 */
    protected $id;
	/**
	 * @ORM\Column(type="string")
	 */
    protected $name;
	/**
	 * @ORM\Column(type="integer")
	 */
    protected $followers;
	/**
	 * @ORM\ManyToOne(targetEntity=MediaImage::class, cascade={"persist","remove"})
	 */
    protected $profilePicture=null;
	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $updateDate;

	function getId() {
		return $this->id;
	}

	function setId($id) {
		$this->id = $id;
		return $this;
	}

	function getName() {
		return $this->name;
	}

	function setName($name) {
		$this->name = $name;
		return $this;
	}

	function getFollowers() {
		return $this->followers;
	}

	function setFollowers($followers) {
		$this->followers = $followers;
		return $this;
	}

	function getProfilePicture() {
		return $this->profilePicture;
	}

	function setProfilePicture($profilePicture) {
		$this->profilePicture = $profilePicture;
		return $this;
	}

	abstract static function getTypeName();

	function getUpdateDate() {
		return $this->updateDate;
	}

	function setUpdateDate($updateDate) {
		$this->updateDate = $updateDate;
		return $this;
	}

	/**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue(): void
    {
        $this->updateDate = new \DateTime();
    }

}