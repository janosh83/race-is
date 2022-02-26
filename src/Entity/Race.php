<?php

namespace App\Entity;

use App\Repository\RaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RaceRepository::class)]
class Race
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 63)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'race', targetEntity: Peak::class)]
    private $peaks;

    #[ORM\OneToMany(mappedBy: 'race', targetEntity: Registration::class)]
    private $registrations;

    #[ORM\OneToMany(mappedBy: 'race', targetEntity: Task::class)]
    private $tasks;

    #[ORM\OneToMany(mappedBy: 'race', targetEntity: Answer::class)]
    private $answers;

    #[ORM\OneToMany(mappedBy: 'race', targetEntity: JournalPost::class)]
    private $journalPosts;

    #[ORM\OneToMany(mappedBy: 'race', targetEntity: Visit::class)]
    private $visits;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $startShowingPeaks;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $startLoggingVisits;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $logoPath;

    #[ORM\Column(type: 'boolean')]
    private $journalEmabled;

    #[ORM\Column(type: 'boolean')]
    private $tasksEnabled;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $stopLoggingVisists;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'races')]
    private $categories;

    public function __construct()
    {
        $this->peaks = new ArrayCollection();
        $this->registrations = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->answers = new ArrayCollection();
        $this->journalPosts = new ArrayCollection();
        $this->visits = new ArrayCollection();
        $this->categories = new ArrayCollection();
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
        if ($this->peaks->removeElement($peak)) {
            // set the owning side to null (unless already changed)
            if ($peak->getRace() === $this) {
                $peak->setRace(null);
            }
        }

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
            $registration->setRace($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->removeElement($registration)) {
            // set the owning side to null (unless already changed)
            if ($registration->getRace() === $this) {
                $registration->setRace(null);
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
        if ($this->tasks->removeElement($task)) {
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
        if ($this->answers->removeElement($answer)) {
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
        if ($this->journalPosts->removeElement($journalPost)) {
            // set the owning side to null (unless already changed)
            if ($journalPost->getRace() === $this) {
                $journalPost->setRace(null);
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
        if ($this->visits->removeElement($visit)) {
            // set the owning side to null (unless already changed)
            if ($visit->getRace() === $this) {
                $visit->setRace(null);
            }
        }

        return $this;
    }

    public function getStartShowingPeaks(): ?\DateTimeInterface
    {
        return $this->startShowingPeaks;
    }

    public function setStartShowingPeaks(?\DateTimeInterface $startShowingPeaks): self
    {
        $this->startShowingPeaks = $startShowingPeaks;

        return $this;
    }

    public function getStartLoggingVisits(): ?\DateTimeInterface
    {
        return $this->startLoggingVisits;
    }

    public function setStartLoggingVisits(?\DateTimeInterface $startLoggingVisits): self
    {
        $this->startLoggingVisits = $startLoggingVisits;

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

    public function getJournalEmabled(): ?bool
    {
        return $this->journalEmabled;
    }

    public function setJournalEmabled(bool $journalEmabled): self
    {
        $this->journalEmabled = $journalEmabled;

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

    public function getStopLoggingVisists(): ?\DateTimeInterface
    {
        return $this->stopLoggingVisists;
    }

    public function setStopLoggingVisists(?\DateTimeInterface $stopLoggingVisists): self
    {
        $this->stopLoggingVisists = $stopLoggingVisists;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
