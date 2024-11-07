<?php

namespace App\Entity;

use App\Repository\VisitRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitRequestRepository::class)]
class VisitRequest
{
    public const STATE_PENDING = 'En attente';
    public const STATE_IN_PROGRESS = 'En cours';
    public const STATE_CANCELED = 'Annulée';
    public const STATE_COMPLETED = 'Terminée';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $client = null;

    #[ORM\ManyToOne]
    private ?User $visitor = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $scheduledAt = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $propertyAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $propertyCity = null;

    #[ORM\Column]
    private ?int $propertyPostalCode = null;

    #[ORM\Column]
    private ?string $price = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $report = null;

    #[ORM\Column(nullable: true)]
    private ?int $visitorRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $clientRating = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?User
    {
        return $this->client;
    }

    public function setClient(?User $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getVisitor(): ?User
    {
        return $this->visitor;
    }

    public function setVisitor(?User $visitor): static
    {
        $this->visitor = $visitor;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): static
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getScheduledAt(): ?\DateTimeInterface
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(\DateTimeInterface $scheduledAt): static
    {
        $this->scheduledAt = $scheduledAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPropertyAddress(): ?string
    {
        return $this->propertyAddress;
    }

    public function setPropertyAddress(string $propertyAddress): static
    {
        $this->propertyAddress = $propertyAddress;

        return $this;
    }

    public function getPropertyCity(): ?string
    {
        return $this->propertyCity;
    }

    public function setPropertyCity(string $propertyCity): static
    {
        $this->propertyCity = $propertyCity;

        return $this;
    }

    public function getPropertyPostalCode(): ?int
    {
        return $this->propertyPostalCode;
    }

    public function setPropertyPostalCode(int $propertyPostalCode): static
    {
        $this->propertyPostalCode = $propertyPostalCode;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getReport(): ?string
    {
        return $this->report;
    }

    public function setReport(?string $report): static
    {
        $this->report = $report;

        return $this;
    }

    public function getVisitorRating(): ?int
    {
        return $this->visitorRating;
    }

    public function setVisitorRating(?int $visitorRating): static
    {
        $this->visitorRating = $visitorRating;

        return $this;
    }

    public function getClientRating(): ?int
    {
        return $this->clientRating;
    }

    public function setClientRating(?int $clientRating): static
    {
        $this->clientRating = $clientRating;

        return $this;
    }
}
