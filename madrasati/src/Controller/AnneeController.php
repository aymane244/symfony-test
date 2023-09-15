<?php

namespace App\Controller;

use App\Entity\AnneeScolaire;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnneeController extends AbstractController
{
    #[Route('/annee', name: 'app_annee')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $annee = new AnneeScolaire();
        $annee->setAnnee(new \DateTime(""));
        $form = $this->createFormBuilder($annee)
            ->add('annee', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
                ])
            ->add('save', SubmitType::class, [
                'label'=> 'Créer un niveau',
            ])
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($annee);
            $entityManager->flush();       
            $this->addFlash('success', 'Année ajouté avec succès');
        }
        return $this->renderForm('annee/index.html.twig', [
            'controller_name' => 'AnneeController',
            'form' => $form,
            'user' => $user
        ]);
    }
    #[Route('/annee/show', name: 'show_annee')]
    public function show(ManagerRegistry $doctrine): Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $annees = $doctrine->getRepository(AnneeScolaire:: class)->findAll();
        $date = date("Y-m-d", strtotime('+1 year'));
        return $this->render('annee/show.html.twig', [
            'annees' => $annees,
            'user' => $user,
            'date' => $date
        ]);
    }
}
