<?php
// src/Repository/SoutenanceRepository.php

namespace App\Repository;

use App\Entity\Enseignant;
use App\Entity\Soutenance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SoutenanceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Soutenance::class);
    }

    public function findByEnseignant(Enseignant $enseignant): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.president = :enseignant')
            ->orWhere('s.rapporteur = :enseignant')
            ->orWhere('s.examinateur = :enseignant')
            ->setParameter('enseignant', $enseignant)
            ->orderBy('s.date', 'ASC')
            ->addOrderBy('s.heure', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByDate(\DateTimeInterface $date): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.date = :date')
            ->setParameter('date', $date)
            ->orderBy('s.heure', 'ASC')
            ->getQuery()
            ->getResult();
    }
}