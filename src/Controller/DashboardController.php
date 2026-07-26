<?php
// src/Controller/DashboardController.php

namespace App\Controller;

use App\Repository\EnseignantRepository;
use App\Repository\EtudiantRepository;
use App\Repository\SalleRepository;
use App\Repository\SoutenanceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(
        EtudiantRepository $etudiantRepository,
        EnseignantRepository $enseignantRepository,
        SalleRepository $salleRepository,
        SoutenanceRepository $soutenanceRepository
    ): Response {
        $user = $this->getUser();

        if ($this->isGranted('ROLE_ADMIN')) {
            $stats = [
                'etudiants' => $etudiantRepository->count([]),
                'enseignants' => $enseignantRepository->count([]),
                'salles' => $salleRepository->count([]),
                'soutenances' => $soutenanceRepository->count([]),
            ];

            return $this->render('dashboard/admin.html.twig', ['stats' => $stats]);
        }

        // Dashboard enseignant
        $enseignant = $user->getEnseignant();

        if (!$enseignant) {
            throw $this->createNotFoundException('Enseignant non trouvé');
        }

        $soutenances = $soutenanceRepository->findByEnseignant($enseignant);
        $etudiants = [];

        foreach ($soutenances as $soutenance) {
            if ($soutenance->getEtudiant()) {
                $etudiants[] = $soutenance->getEtudiant();
            }
        }

        return $this->render('dashboard/enseignant.html.twig', [
            'enseignant' => $enseignant,
            'soutenances' => $soutenances,
            'etudiants' => array_unique($etudiants, SORT_REGULAR),
        ]);
    }
}