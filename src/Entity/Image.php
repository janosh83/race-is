<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $filename;

    #[ORM\ManyToOne(targetEntity: Answer::class, inversedBy: 'images')]
    private $answer;

    #[ORM\ManyToOne(targetEntity: JournalPost::class, inversedBy: 'images')]
    private $journalPost;

    #[ORM\ManyToOne(targetEntity: Visit::class, inversedBy: 'images')]
    private $visit;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getAnswer(): ?Answer
    {
        return $this->answer;
    }

    public function setAnswer(?Answer $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getJournalPost(): ?JournalPost
    {
        return $this->journalPost;
    }

    public function setJournalPost(?JournalPost $journalPost): self
    {
        $this->journalPost = $journalPost;

        return $this;
    }

    public function getVisit(): ?Visit
    {
        return $this->visit;
    }

    public function setVisit(?Visit $visit): self
    {
        $this->visit = $visit;

        return $this;
    }
}
