<?php
// src/Entity/Enseignant.php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'enseignant')]
class Enseignant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $nom = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    private ?string $specialite = null;

    #[ORM\OneToOne(inversedBy: 'enseignant')]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'president', targetEntity: Soutenance::class)]
    private Collection $soutenancesPresidees;

    #[ORM\OneToMany(mappedBy: 'rapporteur', targetEntity: Soutenance::class)]
    private Collection $soutenancesRapportees;

    #[ORM\OneToMany(mappedBy: 'examinateur', targetEntity: Soutenance::class)]
    private Collection $soutenancesExaminees;

    public function __construct()
    {
        $this->soutenancesPresidees = new ArrayCollection();
        $this->soutenancesRapportees = new ArrayCollection();
        $this->soutenancesExaminees = new ArrayCollection();
    }

    // Getters et Setters
    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): static { $this->prenom = $prenom; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }
    public function getSpecialite(): ?string { return $this->specialite; }
    public function setSpecialite(string $specialite): static { $this->specialite = $specialite; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getNomComplet(): string { return $this->prenom . ' ' . $this->nom; }

    public function getSoutenancesPresidees(): Collection { return $this->soutenancesPresidees; }
    public function getSoutenancesRapportees(): Collection { return $this->soutenancesRapportees; }
    public function getSoutenancesExaminees(): Collection { return $this->soutenancesExaminees; }

    public function getToutesSoutenances(): array
    {
        return array_merge(
            $this->soutenancesPresidees->toArray(),
            $this->soutenancesRapportees->toArray(),
            $this->soutenancesExaminees->toArray()
        );
    }
}