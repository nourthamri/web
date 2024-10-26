<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id] // Primary key
    #[ORM\GeneratedValue] // Auto increment
    #[ORM\Column]
    private ?int $ref = null; // This is the primary key

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $publicationDate = null;

    #[ORM\Column(length: 255)]
    private ?string $category = null; // Category field

    #[ORM\Column]
    private ?bool $enabled = null; // Field indicating if the book is enabled or not

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Author $author = null; // Relation to Author entity

    // Getters and Setters

    public function getRef(): ?int
    {
        return $this->ref;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): static
    {
        $this->publicationDate = $publicationDate;

        return $this;
    }

    public function getCategory(): ?string // Getter for category
    {
        return $this->category;
    }

    public function setCategory(string $category): static // Setter for category
    {
        $this->category = $category;

        return $this;
    }

    public function isEnabled(): ?bool // Check if the book is enabled
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static // Setter for enabled status
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getAuthor(): ?Author // Getter for author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): static // Setter for author
    {
        $this->author = $author;

        return $this;
    }
}
