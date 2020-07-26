<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=63)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Team", mappedBy="leader")
     */
    private $leader;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Team", mappedBy="member")
     */
    private $member;

    /**
     * @ORM\OneToMany(targetEntity=JournalPost::class, mappedBy="author", orphanRemoval=true)
     */
    private $journalPost;

    public function __construct()
    {
        $this->leader = new ArrayCollection();
        $this->member = new ArrayCollection();
        $this->journalPost = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    /**
     * @return Collection|Team[]
     */
    public function getLeader(): Collection
    {
        return $this->leader;
    }

    public function addLeader(Team $leader): self
    {
        if (!$this->leader->contains($leader)) {
            $this->leader[] = $leader;
            $leader->setLeader($this);
        }

        return $this;
    }

    public function removeLeader(Team $leader): self
    {
        if ($this->leader->contains($leader)) {
            $this->leader->removeElement($leader);
            // set the owning side to null (unless already changed)
            if ($leader->getLeader() === $this) {
                $leader->setLeader(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getMember(): Collection
    {
        return $this->member;
    }

    public function addMember(Team $member): self
    {
        if (!$this->member->contains($member)) {
            $this->member[] = $member;
            $member->addMember($this);
        }

        return $this;
    }

    public function removeMember(Team $member): self
    {
        if ($this->member->contains($member)) {
            $this->member->removeElement($member);
            $member->removeMember($this);
        }

        return $this;
    }

    /**
     * @return Collection|JournalPost[]
     */
    public function getJournalPost(): Collection
    {
        return $this->journalPost;
    }

    public function addJournalPost(JournalPost $journalPost): self
    {
        if (!$this->journalPost->contains($journalPost)) {
            $this->journalPost[] = $journalPost;
            $journalPost->setAuthor($this);
        }

        return $this;
    }

    public function removeJournalPost(JournalPost $journalPost): self
    {
        if ($this->journalPost->contains($journalPost)) {
            $this->journalPost->removeElement($journalPost);
            // set the owning side to null (unless already changed)
            if ($journalPost->getAuthor() === $this) {
                $journalPost->setAuthor(null);
            }
        }

        return $this;
    }
}
