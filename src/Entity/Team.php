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
    private $title;

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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Visit", mappedBy="team", orphanRemoval=true)
     */
    private $visited;

    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="team", orphanRemoval=true)
     */
    private $answered;

    /**
     * @ORM\OneToMany(targetEntity=JournalPost::class, mappedBy="team", orphanRemoval=true)
     */
    private $journalPosts;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $about;

    public function __construct()
    {
        $this->member = new ArrayCollection();
        $this->signed = new ArrayCollection();
        $this->visited = new ArrayCollection();
        $this->answered = new ArrayCollection();
        $this->journalPosts = new ArrayCollection();
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

    /**
     * @return Collection|Visit[]
     */
    public function getVisited(): Collection
    {
        return $this->visited;
    }

    public function addVisited(Visit $visited): self
    {
        if (!$this->visited->contains($visited)) {
            $this->visited[] = $visited;
            $visited->setTeam($this);
        }

        return $this;
    }

    public function removeVisited(Visit $visited): self
    {
        if ($this->visited->contains($visited)) {
            $this->visited->removeElement($visited);
            // set the owning side to null (unless already changed)
            if ($visited->getTeam() === $this) {
                $visited->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswered(): Collection
    {
        return $this->answered;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answered->contains($answer)) {
            $this->answered[] = $answer;
            $answer->setTeam($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answered->contains($answer)) {
            $this->answered->removeElement($answer);
            // set the owning side to null (unless already changed)
            if ($answer->getTeam() === $this) {
                $answer->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|JournalPost[]
     */
    public function getJournalPosts(): Collection
    {
        return $this->journalPosts;
    }

    public function addJournalPost(JournalPost $journalPost): self
    {
        if (!$this->journalPosts->contains($journalPost)) {
            $this->journalPosts[] = $journalPost;
            $journalPost->setTeam($this);
        }

        return $this;
    }

    public function removeJournalPost(JournalPost $journalPost): self
    {
        if ($this->journalPosts->contains($journalPost)) {
            $this->journalPosts->removeElement($journalPost);
            // set the owning side to null (unless already changed)
            if ($journalPost->getTeam() === $this) {
                $journalPost->setTeam(null);
            }
        }

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(string $about): self
    {
        $this->about = $about;

        return $this;
    }
}
