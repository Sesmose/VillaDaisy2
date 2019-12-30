<?php

namespace App\Entity;

use App\Entity\Demande;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Booking {

	/**
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue
	 * @ORM\Id
	 */
	private $id;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $beginAt;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $endAt = null;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $title;

	/**
	 * @var DateTime
	 * @ORM\Column(type="datetime")
	 */
	protected $created_at;

	/**
	 * @var DateTime
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $updated_at;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	private $description;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $googleid;

	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\Demande")
	 */
	private $Demande;

	public function getId():  ? int {
		return $this->id;
	}

	public function getBeginAt() :  ? \DateTimeInterface {
		return $this->beginAt;
	}

	public function setBeginAt(\DateTimeInterface $beginAt) : self{
		$this->beginAt = $beginAt;

		return $this;
	}

	public function getEndAt():  ? \DateTimeInterface {
		return $this->endAt;
	}

	public function setEndAt( ? \DateTimeInterface $endAt = null) : self{
		$this->endAt = $endAt;

		return $this;
	}

	public function getTitle() :  ? string {
		return $this->title;
	}

	public function setTitle(string $title) : self{
		$this->title = $title;

		return $this;
	}

	public function getCreatedAt():  ? \DateTimeInterface {
		return $this->created_at;
	}

	/**
	 * @ORM\PrePersist
	 */
	public function setCreatedAt() {
		try {
			$this->created_at = new DateTime('now', new \DateTimeZone("Europe/Paris"));
		} catch (\Exception $e) {
		}

		return $this;
	}

	public function getUpdatedAt() :  ? \DateTimeInterface {
		return $this->updated_at;
	}

	/**
	 * @ORM\PreUpdate
	 */
	public function setUpdatedAt() {
		try {
			$this->updated_at = new DateTime('now', new \DateTimeZone("Europe/Paris"));
		} catch (\Exception $e) {
		}

		return $this;
	}

	public function getDemande() :  ? Demande {
		return $this->Demande;
	}

	public function setDemande( ? Demande $Demande) : self{
		$this->Demande = $Demande;

		return $this;
	}

	public function getGoogleid() :  ? string {
		return $this->googleid;
	}

	public function setGoogleid(string $googleid) : self{
		$this->googleid = $googleid;

		return $this;
	}

	public function getDescription():  ? string {
		return $this->description;
	}

	public function setDescription( ? string $description) : self{
		$this->description = $description;

		return $this;
	}

	public function __toString() {
		return $this->title;
	}

}