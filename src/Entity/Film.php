<?php

namespace App\Entity;

use App\Command\Columns;
use App\Repository\FilmRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FilmRepository::class)
 */
class Film
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
    private $Title;

    /**
     * @ORM\Column(type="date")
     */
    private $ReleaseDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $Duration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Producer;

    /**
     * @ORM\ManyToMany(targetEntity=Actor::class, inversedBy="films")
     */
    private $Actors;

    /**
     * @ORM\ManyToMany(targetEntity=Director::class, inversedBy="films")
     */
    private $Directors;

    /**
     * @ORM\ManyToMany(targetEntity=Category::class, mappedBy="films")
     */
    private $Genres;

    public function __construct(array $data = [])
    {
        $this->Actors = new ArrayCollection();
        $this->Directors = new ArrayCollection();
        $this->Genres = new ArrayCollection();

        if (sizeof($data)) {
            $this->setTitle($data[Columns::Title])
                ->setReleaseDate($data[Columns::ReleaseDate])
                ->setDuration($data[Columns::Duration])
                ->setProducer($data[Columns::Producer]);

            foreach ($data[Columns::Genre] as $genre) {
                $this->addGenre($genre);
            }

            foreach ($data[Columns::Actors] as $actor) {
                $this->addActor($actor);
            }

            foreach ($data[Columns::Directors] as $director) {
                $this->addDirector($director);
            }
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): self
    {
        $this->Title = $Title;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->ReleaseDate;
    }

    public function setReleaseDate(\DateTimeInterface $ReleaseDate): self
    {
        $this->ReleaseDate = $ReleaseDate;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->Duration;
    }

    public function setDuration(?int $Duration): self
    {
        $this->Duration = $Duration;

        return $this;
    }

    public function getProducer(): ?string
    {
        return $this->Producer;
    }

    public function setProducer(?string $Producer): self
    {
        $this->Producer = $Producer;

        return $this;
    }

    /**
     * @return Collection<int, Actor>
     */
    public function getActors(): Collection
    {
        return $this->Actors;
    }

    public function addActor(Actor $actor): self
    {
        if (!$this->Actors->contains($actor)) {
            $this->Actors[] = $actor;
        }

        return $this;
    }

    public function removeActor(Actor $actor): self
    {
        $this->Actors->removeElement($actor);

        return $this;
    }

    /**
     * @return Collection<int, Director>
     */
    public function getDirectors(): Collection
    {
        return $this->Directors;
    }

    public function addDirector(Director $director): self
    {
        if (!$this->Directors->contains($director)) {
            $this->Directors[] = $director;
        }

        return $this;
    }

    public function removeDirector(Director $director): self
    {
        $this->Directors->removeElement($director);

        return $this;
    }

    public function __toString() {
        return $this->getTitle();
    }

    /**
     * @return Collection<int, Category>
     */
    public function getGenres(): Collection
    {
        return $this->Genres;
    }

    public function addGenre(Category $genre): self
    {
        if (!$this->Genres->contains($genre)) {
            $this->Genres[] = $genre;
            $genre->addFilm($this);
        }

        return $this;
    }

    public function removeGenre(Category $genre): self
    {
        if ($this->Genres->removeElement($genre)) {
            $genre->removeFilm($this);
        }

        return $this;
    }
}
