<?php

namespace App\Entity;

use App\Repository\JournalPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JournalPostRepository::class)]
class JournalPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'journalPosts')]
    #[ORM\JoinColumn(nullable: false)]
    private $author;

    #[ORM\Column(type: 'string', length: 64)]
    private $title;

    #[ORM\Column(type: 'text')]
    private $text;

    #[ORM\Column(type: 'date')]
    private $date;

    #[ORM\ManyToOne(targetEntity: Race::class, inversedBy: 'journalPosts')]
    #[ORM\JoinColumn(nullable: false)]
    private $race;

    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: 'journalPosts')]
    #[ORM\JoinColumn(nullable: false)]
    private $team;

    #[ORM\OneToMany(mappedBy: 'journalPost', targetEntity: Image::class)]
    private $images;

    public function __construct()
    {
        $this->date = new \DateTime();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

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

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

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
            $image->setJournalPost($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getJournalPost() === $this) {
                $image->setJournalPost(null);
            }
        }

        return $this;
    }
}
