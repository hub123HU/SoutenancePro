<?php
// src/Form/SoutenanceType.php

namespace App\Form;

use App\Entity\Enseignant;
use App\Entity\Etudiant;
use App\Entity\Salle;
use App\Entity\Soutenance;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SoutenanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etudiant', EntityType::class, [
                'class' => Etudiant::class,
                'choice_label' => 'nomComplet',
                'label' => 'Étudiant',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('e')
                        ->leftJoin('e.soutenance', 's')
                        ->orderBy('e.nom', 'ASC');

                    if ($options['soutenance_actuelle'] instanceof Soutenance && $options['soutenance_actuelle']->getEtudiant()) {
                        $qb->where('s.id IS NULL OR e = :etudiantActuel')
                            ->setParameter('etudiantActuel', $options['soutenance_actuelle']->getEtudiant());
                    } else {
                        $qb->where('s.id IS NULL');
                    }

                    return $qb;
                },
            ])
            ->add('president', EntityType::class, [
                'class' => Enseignant::class,
                'choice_label' => 'nomComplet',
                'label' => 'Président du jury',
            ])
            ->add('rapporteur', EntityType::class, [
                'class' => Enseignant::class,
                'choice_label' => 'nomComplet',
                'label' => 'Rapporteur',
            ])
            ->add('examinateur', EntityType::class, [
                'class' => Enseignant::class,
                'choice_label' => 'nomComplet',
                'label' => 'Examinateur',
            ])
            ->add('salle', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => '__toString',
                'label' => 'Salle',
            ])
            ->add('date', DateType::class, [
                'label' => 'Date de la soutenance',
                'widget' => 'single_text',
            ])
            ->add('heure', TimeType::class, [
                'label' => 'Heure',
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Soutenance::class,
            'soutenance_actuelle' => null,
        ]);
    }
}