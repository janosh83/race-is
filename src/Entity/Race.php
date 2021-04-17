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

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="race", orphanRemoval=true)
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity=Answer::class, mappedBy="race", orphanRemoval=true)
     */
    private $answers;

    /**
     * @ORM\OneToMany(targetEntity=JournalPost::class, mappedBy="race", orphanRemoval=true)
     */
    private $journalPosts;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startShowingPeaks;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startLoggingPeaks;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logoPath;

    /**
     * @ORM\Column(type="boolean")
     */
    private $journalEnabled;

    /**
     * @ORM\Column(type="boolean")
     */
    private $tasksEnabled;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $stopLoggingPeaks;

    public function __construct()
    {
        $this->signed = new ArrayCollection();
        $this->peaks = new ArrayCollection();
        $this->visits = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->answers = new ArrayCollection();
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

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setRace($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->contains($task)) {
            $this->tasks->removeElement($task);
            // set the owning side to null (unless already changed)
            if ($task->getRace() === $this) {
                $task->setRace(null);
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
            $answer->setRace($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
            // set the owning side to null (unless already changed)
            if ($answer->getRace() === $this) {
                $answer->setRace(null);
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
            $journalPost->setRace($this);
        }

        return $this;
    }

    public function removeJournalPost(JournalPost $journalPost): self
    {
        if ($this->journalPosts->contains($journalPost)) {
            $this->journalPosts->removeElement($journalPost);
            // set the owning side to null (unless already changed)
            if ($journalPost->getRace() === $this) {
                $journalPost->setRace(null);
            }
        }

        return $this;
    }

    public function getStartShowingPeaks(): ?\DateTimeInterface
    {
        return $this->startShowingPeaks;
    }

    public function setStartShowingPeaks(\DateTimeInterface $startShowingPeaks): self
    {
        $this->startShowingPeaks = $startShowingPeaks;

        return $this;
    }

    public function getStartLoggingPeaks(): ?\DateTimeInterface
    {
        return $this->startLoggingPeaks;
    }

    public function setStartLoggingPeaks(\DateTimeInterface $startLoggingPeaks): self
    {
        $this->startLoggingPeaks = $startLoggingPeaks;

        return $this;
    }

    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    public function setLogoPath(?string $logoPath): self
    {
        $this->logoPath = $logoPath;

        return $this;
    }

    public function getJournalEnabled(): ?bool
    {
        return $this->journalEnabled;
    }

    public function setJournalEnabled(bool $journalEnabled): self
    {
        $this->journalEnabled = $journalEnabled;

        return $this;
    }

    public function getTasksEnabled(): ?bool
    {
        return $this->tasksEnabled;
    }

    public function setTasksEnabled(bool $tasksEnabled): self
    {
        $this->tasksEnabled = $tasksEnabled;

        return $this;
    }

    public function getStopLoggingPeaks(): ?\DateTimeInterface
    {
        return $this->stopLoggingPeaks;
    }

    public function setStopLoggingPeaks(?\DateTimeInterface $stopLoggingPeaks): self
    {
        $this->stopLoggingPeaks = $stopLoggingPeaks;

        return $this;
    }
}
