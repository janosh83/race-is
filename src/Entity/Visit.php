<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VisitRepository")
 */
class Visit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $time;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Peak", inversedBy="visits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $peak;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Team", inversedBy="visited")
     * @ORM\JoinColumn(nullable=false)
     */
    private $team;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Race", inversedBy="visits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $race;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $imageFilename;

    public function __construct()
    {
        $this->time = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(\DateTimeInterface $time): self
    {
        $this->time = $time;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getPeak(): ?Peak
    {
        return $this->peak;
    }

    public function setPeak(?Peak $peak): self
    {
        $this->peak = $peak;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getRace(): ?Race
    {
        return $this->race;
    }

    public function setRace(?Race $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getImageFilename()
    {
        return $this->imageFilename;
    }

    public function setImageFilename($imageFilename)
    {
        $this->imageFilename = $imageFilename;

        return $this;
    }
}
