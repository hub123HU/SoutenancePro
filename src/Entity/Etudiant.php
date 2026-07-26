<?php
// src/Entity/Etudiant.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'etudiant')]
class Etudiant
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
    private ?string $filiere = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $themeMemoire = null;

    #[ORM\OneToOne(mappedBy: 'etudiant', cascade: ['persist', 'remove'])]
    private ?Soutenance $soutenance = null;

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): static { $this->prenom = $prenom; return $this; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }
    public function getFiliere(): ?string { return $this->filiere; }
    public function setFiliere(string $filiere): static { $this->filiere = $filiere; return $this; }
    public function getThemeMemoire(): ?string { return $this->themeMemoire; }
    public function setThemeMemoire(string $themeMemoire): static { $this->themeMemoire = $themeMemoire; return $this; }
    public function getSoutenance(): ?Soutenance { return $this->soutenance; }
    public function setSoutenance(?Soutenance $soutenance): static { $this->soutenance = $soutenance; return $this; }

    public function getNomComplet(): string { return $this->prenom . ' ' . $this->nom; }
}