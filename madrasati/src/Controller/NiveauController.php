<?php

namespace App\Controller;

use App\Entity\Niveau;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NiveauController extends AbstractController
{
    #[Route('/niveau', name: 'app_niveau')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $niveau = new Niveau();
        $niveau->setNiveau('');
        $niveau->setPrix(1);
        $form = $this->createFormBuilder($niveau)
            ->add('niveau', TextType::class)
            ->add('prix', NumberType::class)
            ->add('save', SubmitType::class, [
                'label'=> 'Créer un niveau',
            ])
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($niveau);
            $entityManager->flush();       
            $this->addFlash('success', 'Niveau ajouté avec Succès');
        }
            return $this->renderForm('niveau/index.html.twig', [
            'controller_name' => 'NiveauController',
            'form' => $form,
            'user' => $user
        ]);
    }
    #[Route('/niveau/show', name: 'show_niveau')]
    public function show(ManagerRegistry $doctrine): Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $nivaux = $doctrine->getRepository(Niveau:: class)->findAll();
        return $this->render('niveau/show.html.twig', [
            'nivaux' => $nivaux,
            'user' => $user
        ]);
    }
}
