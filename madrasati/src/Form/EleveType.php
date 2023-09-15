<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Entity\Niveau;
use App\Entity\Parents;
use App\Entity\Services;
use App\Entity\AnneeScolaire;
use DateTimeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $annee = new AnneeScolaire();
        $annee->setAnnee(new \DateTime(date("d-m-Y")));
        $date = $annee->annee->format("d-m-Y");
        // dump();
        // DateTimeInterface::format();
        $builder
            ->add('fullname', TextType::class)
            ->add('dateNaissance', DateType::class)
            ->add('cne', TextType::class)
            ->add('sexe', TextType::class)
            ->add('photo', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5024k',
                    ])
                ]
            ])
            ->add('parent', EntityType::class, [
                'required' =>true,
                'label' =>'Choisir un parent',
                'class' => Parents::class,
                'choice_label' =>'fullname',
                'placeholder' => '-- Veuillez choisir un parent --',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un parent'
                    ])
                ]
            ])
            ->add('niveau', EntityType::class, [
                'required' =>true,
                'label' =>'Choisir un niveau',
                'class' => Niveau::class,
                'choice_label' =>'niveau',
                'placeholder' => '-- Veuillez choisir un niveau --',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un niveau'
                    ])
                ]
            ])
            ->add('annee_scolaire', EntityType::class, [
                'required' =>true,
                'label' =>'Choisir une année scolaire',
                'class' => AnneeScolaire::class,
                'choice_label' =>'id',
                'placeholder' => '-- Veuillez choisir une année scolaire --',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une année scolaire'
                    ])
                ]
            ])
            ->add('service', EntityType::class, [
                'label' =>'Choisir un service',
                'class' => Services::class, 
                // 'query_builder' => function (EntityRepository $repo) use ($options){
                //     $query = $repo->createQueryBuilder('s');
                //     return $query;
                // },
                'choice_label' =>'name',
                'multiple' => true,
                'expanded' => false,
                'placeholder' =>'--Choisir un service--',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Eleve::class,
        ]);
    }
}
