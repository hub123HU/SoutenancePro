<?php
// src/Controller/EnseignantController.php

namespace App\Controller;

use App\Entity\Enseignant;
use App\Entity\User;
use App\Form\EnseignantRegistrationType;
use App\Form\EnseignantType;
use App\Repository\EnseignantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/enseignant')]
#[IsGranted('ROLE_ADMIN')]
class EnseignantController extends AbstractController
{
    #[Route('/', name: 'app_enseignant_index', methods: ['GET'])]
    public function index(EnseignantRepository $enseignantRepository): Response
    {
        return $this->render('enseignant/index.html.twig', [
            'enseignants' => $enseignantRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_enseignant_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $enseignant = new Enseignant();
        $form = $this->createForm(EnseignantRegistrationType::class, $enseignant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Créer un compte utilisateur associé
            $user = new User();
            $user->setEmail($enseignant->getEmail());
            $user->setNom($enseignant->getNom());
            $user->setPrenom($enseignant->getPrenom());
            $user->setRoles(['ROLE_ENSEIGNANT']);
            $user->setPassword(
                $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData())
            );

            $enseignant->setUser($user);

            $em->persist($user);
            $em->persist($enseignant);
            $em->flush();

            $this->addFlash('success', 'Enseignant ajouté avec succès !');
            return $this->redirectToRoute('app_enseignant_index');
        }

        return $this->render('enseignant/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_enseignant_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Enseignant $enseignant, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EnseignantType::class, $enseignant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Enseignant modifié avec succès !');
            return $this->redirectToRoute('app_enseignant_index');
        }

        return $this->render('enseignant/edit.html.twig', [
            'form' => $form->createView(),
            'enseignant' => $enseignant,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_enseignant_delete', methods: ['POST'])]
    public function delete(Request $request, Enseignant $enseignant, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $enseignant->getId(), $request->request->get('_token'))) {
            $user = $enseignant->getUser();
            if ($user) {
                $em->remove($user);
            }
            $em->remove($enseignant);
            $em->flush();
            $this->addFlash('success', 'Enseignant supprimé avec succès !');
        }

        return $this->redirectToRoute('app_enseignant_index');
    }
}