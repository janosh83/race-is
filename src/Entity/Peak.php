<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PeakRepository")
 */
class Peak
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $short_id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $longitude;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Team", mappedBy="visited_peaks")
     */
    private $teams_visits;

    public function __construct()
    {
        $this->teams_visits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortId(): ?string
    {
        return $this->short_id;
    }

    public function setShortId(string $short_id): self
    {
        $this->short_id = $short_id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeamsVisits(): Collection
    {
        return $this->teams_visits;
    }

    public function addTeamsVisit(Team $teamsVisit): self
    {
        if (!$this->teams_visits->contains($teamsVisit)) {
            $this->teams_visits[] = $teamsVisit;
            $teamsVisit->addVisitedPeak($this);
        }

        return $this;
    }

    public function removeTeamsVisit(Team $teamsVisit): self
    {
        if ($this->teams_visits->contains($teamsVisit)) {
            $this->teams_visits->removeElement($teamsVisit);
            $teamsVisit->removeVisitedPeak($this);
        }

        return $this;
    }
}
