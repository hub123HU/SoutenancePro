<?php
// src/Controller/SoutenanceController.php

namespace App\Controller;

use App\Entity\Soutenance;
use App\Form\SoutenanceType;
use App\Repository\SoutenanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/soutenance')]
class SoutenanceController extends AbstractController
{
    #[Route('/', name: 'app_soutenance_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(SoutenanceRepository $soutenanceRepository): Response
    {
        $soutenances = $this->isGranted('ROLE_ADMIN')
            ? $soutenanceRepository->findAll()
            : $soutenanceRepository->findByEnseignant($this->getUser()->getEnseignant());

        return $this->render('soutenance/index.html.twig', [
            'soutenances' => $soutenances,
            'isAdmin' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }

    #[Route('/new', name: 'app_soutenance_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $soutenance = new Soutenance();
        $form = $this->createForm(SoutenanceType::class, $soutenance, [
            'soutenance_actuelle' => $soutenance,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier les conflits
            $conflits = $this->verifierConflits($soutenance, $em);
            
            if (empty($conflits)) {
                $em->persist($soutenance);
                $em->flush();
                $this->addFlash('success', 'Soutenance programmée avec succès !');
                return $this->redirectToRoute('app_soutenance_index');
            } else {
                foreach ($conflits as $conflit) {
                    $this->addFlash('error', $conflit);
                }
            }
        }

        return $this->render('soutenance/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_soutenance_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Soutenance $soutenance, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SoutenanceType::class, $soutenance, [
            'soutenance_actuelle' => $soutenance,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conflits = $this->verifierConflits($soutenance, $em, true);
            
            if (empty($conflits)) {
                $em->flush();
                $this->addFlash('success', 'Soutenance modifiée avec succès !');
                return $this->redirectToRoute('app_soutenance_index');
            } else {
                foreach ($conflits as $conflit) {
                    $this->addFlash('error', $conflit);
                }
            }
        }

        return $this->render('soutenance/edit.html.twig', [
            'form' => $form->createView(),
            'soutenance' => $soutenance,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_soutenance_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Soutenance $soutenance, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $soutenance->getId(), $request->request->get('_token'))) {
            $em->remove($soutenance);
            $em->flush();
            $this->addFlash('success', 'Soutenance annulée avec succès !');
        }

        return $this->redirectToRoute('app_soutenance_index');
    }

    #[Route('/mes-jurys', name: 'app_soutenance_jurys', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function mesJurys(SoutenanceRepository $soutenanceRepository): Response
    {
        $enseignant = $this->getUser()->getEnseignant();

        if (!$enseignant) {
            throw $this->createNotFoundException('Enseignant non trouvé');
        }

        return $this->render('soutenance/mes_jurys.html.twig', [
            'enseignant' => $enseignant,
            'soutenances' => $soutenanceRepository->findByEnseignant($enseignant),
        ]);
    }

    #[Route('/search', name: 'app_soutenance_search', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function search(Request $request, SoutenanceRepository $soutenanceRepository): Response
    {
        $date = $request->query->get('date');
        $soutenances = $date 
            ? $soutenanceRepository->findByDate(new \DateTime($date))
            : [];

        return $this->render('soutenance/search.html.twig', [
            'soutenances' => $soutenances,
            'date' => $date,
        ]);
    }

    private function verifierConflits(Soutenance $soutenance, EntityManagerInterface $em, bool $isEdit = false): array
    {
        $conflits = [];
        $date = $soutenance->getDate();
        $heure = $soutenance->getHeure();
        $salle = $soutenance->getSalle();

        // Vérifier la salle
        $soutenancesSalle = $em->createQueryBuilder()
            ->select('s')
            ->from(Soutenance::class, 's')
            ->where('s.salle = :salle')
            ->andWhere('s.date = :date')
            ->andWhere('s.heure = :heure')
            ->setParameter('salle', $salle)
            ->setParameter('date', $date)
            ->setParameter('heure', $heure)
            ->getQuery()
            ->getResult();

        if ($isEdit) {
            $soutenancesSalle = array_filter($soutenancesSalle, function($s) use ($soutenance) {
                return $s->getId() !== $soutenance->getId();
            });
        }

        if (!empty($soutenancesSalle)) {
            $conflits[] = 'Cette salle est déjà occupée à cette date et heure.';
        }

        // Vérifier les enseignants
        $enseignants = [
            $soutenance->getPresident(),
            $soutenance->getRapporteur(),
            $soutenance->getExaminateur(),
        ];

        foreach ($enseignants as $enseignant) {
            $soutenancesEnseignant = $em->createQueryBuilder()
                ->select('s')
                ->from(Soutenance::class, 's')
                ->where('s.date = :date')
                ->andWhere('s.heure = :heure')
                ->andWhere('s.president = :enseignant OR s.rapporteur = :enseignant OR s.examinateur = :enseignant')
                ->setParameter('date', $date)
                ->setParameter('heure', $heure)
                ->setParameter('enseignant', $enseignant)
                ->getQuery()
                ->getResult();

            if ($isEdit) {
                $soutenancesEnseignant = array_filter($soutenancesEnseignant, function($s) use ($soutenance) {
                    return $s->getId() !== $soutenance->getId();
                });
            }

            if (!empty($soutenancesEnseignant)) {
                $conflits[] = "L'enseignant {$enseignant->getNomComplet()} est déjà occupé à cette date et heure.";
            }
        }

        return $conflits;
    }
}