<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EquipeRepository::class)]
class Equipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?string $entraineur = null;

    #[ORM\Column(length: 255)]
    private ?string $creneaux = null;

    #[ORM\Column(length: 255)]
    private ?string $url_photo = null;

    #[ORM\Column(length: 255)]
    private ?string $url_result_calendrier = null;

    #[ORM\Column(length: 2048)]
    private ?string $commentaire = null;

    #[ORM\OneToMany(mappedBy: 'equipe_locale', targetEntity: Rencontre::class)]
    private Collection $rencontres;

    public function __construct()
    {
        $this->rencontres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getEntraineur(): ?string
    {
        return $this->entraineur;
    }

    public function setEntraineur(string $entraineur): self
    {
        $this->entraineur = $entraineur;

        return $this;
    }

    public function getCreneaux(): ?string
    {
        return $this->creneaux;
    }

    public function setCreneaux(string $creneaux): self
    {
        $this->creneaux = $creneaux;

        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->url_photo;
    }

    public function setUrlPhoto(string $url_photo): self
    {
        $this->url_photo = $url_photo;

        return $this;
    }

    public function getUrlResultCalendrier(): ?string
    {
        return $this->url_result_calendrier;
    }

    public function setUrlResultCalendrier(string $url_result_calendrier): self
    {
        $this->url_result_calendrier = $url_result_calendrier;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): self
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * @return Collection<int, Rencontre>
     */
    public function getRencontres(): Collection
    {
        return $this->rencontres;
    }

    public function addRencontre(Rencontre $rencontre): self
    {
        if (!$this->rencontres->contains($rencontre)) {
            $this->rencontres->add($rencontre);
            $rencontre->setEquipeLocale($this);
        }

        return $this;
    }

    public function removeRencontre(Rencontre $rencontre): self
    {
        if ($this->rencontres->removeElement($rencontre)) {
            // set the owning side to null (unless already changed)
            if ($rencontre->getEquipeLocale() === $this) {
                $rencontre->setEquipeLocale(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getLibelle();
    }
}
