<?php
// src/Repository/EtudiantRepository.php

namespace App\Repository;

use App\Entity\Etudiant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EtudiantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Etudiant::class);
    }

    public function findByNom(string $search): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.nom LIKE :search')
            ->orWhere('e.prenom LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}