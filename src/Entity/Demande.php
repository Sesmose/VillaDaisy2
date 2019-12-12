<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DemandeRepository")
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
	 */
	private $nom;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	private $prenom;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $email;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $telephone;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $adresse;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	private $ville;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $cp;

	/**
	 * @ORM\Column(type="datetime")
	 */
	private $created_at;

	/**
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	private $updated_at;

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

	public function getTelephone():  ? int {
		return $this->telephone;
	}

	public function setTelephone(int $telephone) : self{
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

	public function setCreatedAt() : self{
		$this->created_at = new \DateTime('now', new \DateTimeZone("Europe/Paris"));

		return $this;
	}

	public function getUpdatedAt():  ? \DateTimeInterface {
		return $this->updated_at;
	}

	public function setUpdatedAt() : self{
		$this->updated_at = new \DateTime('now', new \DateTimeZone("Europe/Paris"));

		return $this;
	}

}
