<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DemandeRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"date_debut","date_fin","email","nom","prenom"}, message="Vous avez déjà fait cette demande de réservation")
 */
class Demande {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @Assert\GreaterThan("today", message="La date demandée est antérieure à celle du jour")
	 * @ORM\Column(type="date")
	 * @Assert\NotNull
	 * @Assert\NotBlank
	 */
	private $date_debut;

	/**
	 * @Assert\Expression(
	 *     "this.getDateDebut() < this.getDateFin()",
	 *     message="La date fin ne doit pas être antérieure à la date début"
	 * )
	 * @ORM\Column(type="date")
	 */
	private $date_fin;

	/**
	 * @ORM\Column(type="string", length=100)
	 * @Assert\NotBlank
	 * @Assert\Regex(
     * pattern="/\d/",
     * match=false,
     * message="Votre nom ne peut contenir de nombres."
     * )
	 */
	private $nom;

	/**
	 * @ORM\Column(type="string", length=100)
	 * @Assert\NotBlank
	 * @Assert\Regex(
     * pattern="/\d/",
     * match=false,
     * message="Votre nom ne peut contenir de nombres."
     * )
	 */
	private $prenom;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\Email(
	 * 		message= "L'email '{{ value }}' n'est pas correct.")
	 */
	private $email;

	/**
	 * @ORM\Column(type="string")
	 * @Assert\NotBlank
	 * @Assert\Regex("/^((\+)33|0)[1-9](\d{2}){4}$/")
	 * 
	 */
	private $telephone;

	/**
	 * @ORM\Column(type="string", length=255)
	 * @Assert\NotBlank
	 */
	private $adresse;

	/**
	 * @ORM\Column(type="string", length=100)
	 * @Assert\NotBlank
	 */
	private $ville;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $cp;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $created_at;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $updated_at;

	public function getId():  ? int {
		return $this->id;
	}

	public function getDateDebut() :  ? \DateTimeInterface {
		return $this->date_debut;
	}

	public function setDateDebut(\DateTimeInterface $date_debut) : self{
		$this->date_debut = $date_debut;

		return $this;
	}

	public function getDateFin():  ? \DateTimeInterface {
		return $this->date_fin;
	}

	public function setDateFin(\DateTimeInterface $date_fin) : self{
		$this->date_fin = $date_fin;

		return $this;
	}

	public function getNom():  ? string {
		return $this->nom;
	}

	public function setNom(string $nom) : self{
		$this->nom = $nom;

		return $this;
	}

	public function getPrenom():  ? string {
		return $this->prenom;
	}

	public function setPrenom(string $prenom) : self{
		$this->prenom = $prenom;

		return $this;
	}

	public function getEmail():  ? string {
		return $this->email;
	}

	public function setEmail(string $email) : self{
		$this->email = $email;

		return $this;
	}

	public function getTelephone():  ? string {
		return $this->telephone;
	}

	public function setTelephone(string $telephone) : self{
		$this->telephone = $telephone;

		return $this;
	}

	public function getAdresse():  ? string {
		return $this->adresse;
	}

	public function setAdresse(string $adresse) : self{
		$this->adresse = $adresse;

		return $this;
	}

	public function getVille():  ? string {
		return $this->ville;
	}

	public function setVille(string $ville) : self{
		$this->ville = $ville;

		return $this;
	}

	public function getCp():  ? int {
		return $this->cp;
	}

	public function setCp(int $cp) : self{
		$this->cp = $cp;

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
			$this->created_at = new \DateTime('now', new \DateTimeZone("Europe/Paris"));
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
			$this->updated_at = new \DateTime('now', new \DateTimeZone("Europe/Paris"));
		} catch (\Exception $e) {
		}

		return $this;
	}

	public function __toString() {
		return $this->nom . ' ' . $this->prenom;
	}

}
