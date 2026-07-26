<?php
// src/Entity/Soutenance.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'soutenance')]
#[ORM\UniqueConstraint(name: 'unique_etudiant_soutenance', columns: ['etudiant_id'])]
class Soutenance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'soutenance')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Etudiant $etudiant = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Enseignant $president = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Enseignant $rapporteur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Enseignant $examinateur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Salle $salle = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'time')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $heure = null;

    public function getId(): ?int { return $this->id; }
    public function getEtudiant(): ?Etudiant { return $this->etudiant; }
    public function setEtudiant(?Etudiant $etudiant): static { $this->etudiant = $etudiant; return $this; }
    public function getPresident(): ?Enseignant { return $this->president; }
    public function setPresident(?Enseignant $president): static { $this->president = $president; return $this; }
    public function getRapporteur(): ?Enseignant { return $this->rapporteur; }
    public function setRapporteur(?Enseignant $rapporteur): static { $this->rapporteur = $rapporteur; return $this; }
    public function getExaminateur(): ?Enseignant { return $this->examinateur; }
    public function setExaminateur(?Enseignant $examinateur): static { $this->examinateur = $examinateur; return $this; }
    public function getSalle(): ?Salle { return $this->salle; }
    public function setSalle(?Salle $salle): static { $this->salle = $salle; return $this; }
    public function getDate(): ?\DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $date): static { $this->date = $date; return $this; }
    public function getHeure(): ?\DateTimeInterface { return $this->heure; }
    public function setHeure(\DateTimeInterface $heure): static { $this->heure = $heure; return $this; }
}