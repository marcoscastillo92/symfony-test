<?php

namespace App\Entity;

use App\Repository\ActorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ActorRepository::class)
 */
class Actor
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Name;

    /**
     * @ORM\Column(type="date")
     */
    private $Birthdate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $DeathDate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $BirthPlace;

    /**
     * @ORM\ManyToMany(targetEntity=Film::class, mappedBy="Actors")
     */
    private $films;

    public function __construct()
    {
        $this->films = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->Birthdate;
    }

    public function setBirthdate(\DateTimeInterface $Birthdate): self
    {
        $this->Birthdate = $Birthdate;

        return $this;
    }

    public function getDeathDate(): ?\DateTimeInterface
    {
        return $this->DeathDate;
    }

    public function setDeathDate(?\DateTimeInterface $DeathDate): self
    {
        $this->DeathDate = $DeathDate;

        return $this;
    }

    public function getBirthPlace(): ?string
    {
        return $this->BirthPlace;
    }

    public function setBirthPlace(?string $BirthPlace): self
    {
        $this->BirthPlace = $BirthPlace;

        return $this;
    }

    /**
     * @return Collection<int, Film>
     */
    public function getFilms(): Collection
    {
        return $this->films;
    }

    public function addFilm(Film $film): self
    {
        if (!$this->films->contains($film)) {
            $this->films[] = $film;
            $film->addActor($this);
        }

        return $this;
    }

    public function removeFilm(Film $film): self
    {
        if ($this->films->removeElement($film)) {
            $film->removeActor($this);
        }

        return $this;
    }
}
