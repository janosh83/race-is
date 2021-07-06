<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity=Registration::class, mappedBy="category")
     */
    private $registration;

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

    public function getRegistration(): ?Registration
    {
        return $this->registration;
    }

    public function setRegistration(Registration $registration): self
    {
        // set the owning side of the relation if necessary
        if ($registration->getCategory() !== $this) {
            $registration->setCategory($this);
        }

        $this->registration = $registration;

        return $this;
    }
}
