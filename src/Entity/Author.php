<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection; // Import ArrayCollection
use Doctrine\Common\Collections\Collection; // Import Collection
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuthorRepository::class)]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $nbBooks = 0;

    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'author')]
    private Collection $books; // Use Collection type for type safety

    public function __construct()
    {
        $this->books = new ArrayCollection(); // Initialize the collection
    }

    // Getters and Setters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUsername(): ?string // Ensure this method is defined correctly
    {
        return $this->username;
    }

    public function setUsername(string $username): static // Ensure this method is defined correctly
    {
        $this->username = $username;
        return $this;
    }

    public function getNbBooks(): ?int
    {
        return $this->nbBooks;
    }

    public function setNbBooks(?int $nbBooks): static // Set to nullable to allow null values
    {
        $this->nbBooks = $nbBooks;
        return $this;
    }

    public function getBooks(): Collection // Return type Collection for safety
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setAuthor($this); // Ensure the relationship is bi-directional
        }
        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // Set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return $this->username; // Return username or any other identifier
    }
}
