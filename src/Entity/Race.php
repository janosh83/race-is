<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RaceRepository")
 */
class Race
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=63)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Team", mappedBy="signed")
     */
    private $signed;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Peak", mappedBy="race")
     */
    private $peaks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Visit", mappedBy="race", orphanRemoval=true)
     */
    private $visits;

    public function __construct()
    {
        $this->signed = new ArrayCollection();
        $this->peaks = new ArrayCollection();
        $this->visits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return Collection|Team[]
     */
    public function getSigned(): Collection
    {
        return $this->signed;
    }

    public function addSigned(Team $signed): self
    {
        if (!$this->signed->contains($signed)) {
            $this->signed[] = $signed;
            $signed->addSigned($this);
        }

        return $this;
    }

    public function removeSigned(Team $signed): self
    {
        if ($this->signed->contains($signed)) {
            $this->signed->removeElement($signed);
            $signed->removeSigned($this);
        }

        return $this;
    }

    /**
     * @return Collection|Peak[]
     */
    public function getPeaks(): Collection
    {
        return $this->peaks;
    }

    public function addPeak(Peak $peak): self
    {
        if (!$this->peaks->contains($peak)) {
            $this->peaks[] = $peak;
            $peak->setRace($this);
        }

        return $this;
    }

    public function removePeak(Peak $peak): self
    {
        if ($this->peaks->contains($peak)) {
            $this->peaks->removeElement($peak);
            // set the owning side to null (unless already changed)
            if ($peak->getRace() === $this) {
                $peak->setRace(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Visit[]
     */
    public function getVisits(): Collection
    {
        return $this->visits;
    }

    public function addVisit(Visit $visit): self
    {
        if (!$this->visits->contains($visit)) {
            $this->visits[] = $visit;
            $visit->setRace($this);
        }

        return $this;
    }

    public function removeVisit(Visit $visit): self
    {
        if ($this->visits->contains($visit)) {
            $this->visits->removeElement($visit);
            // set the owning side to null (unless already changed)
            if ($visit->getRace() === $this) {
                $visit->setRace(null);
            }
        }

        return $this;
    }
}