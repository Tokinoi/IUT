<?php

namespace App\Entity;

use App\Repository\RencontreRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RencontreRepository::class)]
class Rencontre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?bool $domicile_exterieur = null;

    #[ORM\Column(length: 255)]
    private ?string $hote = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_heure = null;

    #[ORM\Column(length: 255)]
    private ?string $gymnase = null;

    #[ORM\Column(length: 255)]
    private ?string $equipe_adverse = null;

    #[ORM\ManyToOne(inversedBy: 'rencontres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipe $equipe_locale = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isDomicileExterieur(): ?bool
    {
        return $this->domicile_exterieur;
    }

    public function setDomicileExterieur(bool $domicile_exterieur): self
    {
        $this->domicile_exterieur = $domicile_exterieur;

        return $this;
    }

    public function getHote(): ?string
    {
        return $this->hote;
    }

    public function setHote(string $hote): self
    {
        $this->hote = $hote;

        return $this;
    }

    public function getDateHeure(): ?\DateTimeInterface
    {
        return $this->date_heure;
    }

    public function setDateHeure(\DateTimeInterface $date_heure): self
    {
        $this->date_heure = $date_heure;

        return $this;
    }

    
    public function getGymnase(): ?string
    {
        return $this->gymnase;
    }

    public function setGymnase(string $gymnase): self
    {
        $this->gymnase = $gymnase;

        return $this;
    }

    public function getEquipeAdverse(): ?string
    {
        return $this->equipe_adverse;
    }

    public function setEquipeAdverse(string $equipe_adverse): self
    {
        $this->equipe_adverse = $equipe_adverse;

        return $this;
    }

    public function getEquipeLocale(): ?Equipe
    {
        return $this->equipe_locale;
    }

    public function setEquipeLocale(?Equipe $equipe_locale): self
    {
        $this->equipe_locale = $equipe_locale;

        return $this;
    }
}
