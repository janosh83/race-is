<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 63)]
    private $title;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Registration::class)]
    private $registrations;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Answer::class)]
    private $answers;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: JournalPost::class)]
    private $journalPosts;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: Visit::class)]
    private $visits;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'teams')]
    private $members;

    public function __construct()
    {
        $this->registrations = new ArrayCollection();
        $this->answers = new ArrayCollection();
        $this->journalPosts = new ArrayCollection();
        $this->visits = new ArrayCollection();
        $this->members = new ArrayCollection();
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

    /**
     * @return Collection|Registration[]
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(Registration $registration): self
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations[] = $registration;
            $registration->setTeam($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->removeElement($registration)) {
            // set the owning side to null (unless already changed)
            if ($registration->getTeam() === $this) {
                $registration->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setTeam($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->removeElement($answer)) {
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
        if ($this->journalPosts->removeElement($journalPost)) {
            // set the owning side to null (unless already changed)
            if ($journalPost->getTeam() === $this) {
                $journalPost->setTeam(null);
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
            $visit->setTeam($this);
        }

        return $this;
    }

    public function removeVisit(Visit $visit): self
    {
        if ($this->visits->removeElement($visit)) {
            // set the owning side to null (unless already changed)
            if ($visit->getTeam() === $this) {
                $visit->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        $this->members->removeElement($member);

        return $this;
    }
}
