<?php

namespace App\Entity;

use App\Repository\VisitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitRepository::class)]
class Visit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $time;

    #[ORM\Column(type: 'text', nullable: true)]
    private $note;

    #[ORM\ManyToOne(targetEntity: Peak::class, inversedBy: 'visits')]
    #[ORM\JoinColumn(nullable: false)]
    private $peak;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'visits')]
    #[ORM\JoinColumn(nullable: false)]
    private $team;

    #[ORM\ManyToOne(targetEntity: Race::class, inversedBy: 'visits')]
    #[ORM\JoinColumn(nullable: false)]
    private $race;

    #[ORM\OneToMany(mappedBy: 'visit', targetEntity: Image::class)]
    private $images;

    public function __construct()
    {
        $this->time = new \DateTime();
        $this->images = new ArrayCollection();
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

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setVisit($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getVisit() === $this) {
                $image->setVisit(null);
            }
        }

        return $this;
    }
}
