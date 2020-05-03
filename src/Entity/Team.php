<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TeamRepository")
 */
class Team
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
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="leader")
     * @ORM\JoinColumn(nullable=false)
     */
    private $leader;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="member")
     */
    private $member;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Race", inversedBy="signed")
     */
    private $signed;

    public function __construct()
    {
        $this->member = new ArrayCollection();
        $this->signed = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLeader(): ?User
    {
        return $this->leader;
    }

    public function setLeader(?User $leader): self
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getMember(): Collection
    {
        return $this->member;
    }

    public function addMember(User $member): self
    {
        if (!$this->member->contains($member)) {
            $this->member[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        if ($this->member->contains($member)) {
            $this->member->removeElement($member);
        }

        return $this;
    }

    /**
     * @return Collection|Race[]
     */
    public function getSigned(): Collection
    {
        return $this->signed;
    }

    public function addSigned(Race $signed): self
    {
        if (!$this->signed->contains($signed)) {
            $this->signed[] = $signed;
        }

        return $this;
    }

    public function removeSigned(Race $signed): self
    {
        if ($this->signed->contains($signed)) {
            $this->signed->removeElement($signed);
        }

        return $this;
    }
}
