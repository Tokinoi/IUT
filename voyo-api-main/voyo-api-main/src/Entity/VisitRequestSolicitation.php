<?php

namespace App\Entity;

use App\Repository\VisitRequestSolicitationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisitRequestSolicitationRepository::class)]
class VisitRequestSolicitation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $visitor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?VisitRequest $visitRequest = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isAccepted = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getVisitRequest(): ?VisitRequest
    {
        return $this->visitRequest;
    }

    public function setVisitRequest(?VisitRequest $visitRequest): static
    {
        $this->visitRequest = $visitRequest;

        return $this;
    }


    public function isIsAccepted(): ?bool
    {
        return $this->isAccepted;
    }

    public function setIsAccepted(?bool $isAccepted): static
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }
}
