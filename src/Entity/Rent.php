<?php

namespace App\Entity;

use App\Repository\RentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RentRepository::class)]
class Rent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\Column(nullable: false)]
    private ?\DateTime $rentDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $returnDate = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $actualReturnDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getRentDate(): ?\DateTime
    {
        return $this->rentDate;
    }

    public function setRentDate(\DateTime $rentDate): static
    {
        $this->rentDate = $rentDate;

        return $this;
    }

    public function getReturnDate(): ?\DateTime
    {
        return $this->returnDate;
    }

    public function setReturnDate(?\DateTime $returnDate): static
    {
        $this->returnDate = $returnDate;

        return $this;
    }

    public function getActualReturnDate(): ?\DateTime
    {
        return $this->actualReturnDate;
    }

    public function setActualReturnDate(?\DateTime $actualReturnDate): static
    {
        $this->actualReturnDate = $actualReturnDate;

        return $this;
    }
}
