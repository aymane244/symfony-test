<?php

namespace App\Controller;

use App\Entity\Parents;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParentController extends AbstractController
{
    #[Route('/parent', name: 'app_parent')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $parent = new Parents();
        $parent->setFullname('');
        $parent->setCin('');
        $parent->setAddress('');
        $parent->setNTel('');
        $parent->setEmail('');
        $parent->setDate(new \DateTime(""));
        $form = $this->createFormBuilder($parent)
            ->add('fullname', TextType::class)
            ->add('cin', TextType::class)
            ->add('address', TextType::class)
            ->add('n_tel', TextType::class)
            ->add('email', TextType::class)
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'format' => 'yyyy-MM-dd',
                ])
            ->add('save', SubmitType::class, [
                'label'=> 'Créer un parent',
            ])
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($parent);
            $entityManager->flush();       
            $this->addFlash('success', 'Parent ajouté avec Succès');
        }
        return $this->renderForm('parent/index.html.twig', [
            'controller_name' => 'ParentController',
            'form' => $form,
            'user' => $user
        ]);
    }
    #[Route('/parent/show', name: 'show_parent')]
    public function show(ManagerRegistry $doctrine): Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $parents = $doctrine->getRepository(Parents:: class)->findAll();
        return $this->render('parent/show.html.twig', [
            'parents' => $parents,
            'user' => $user
        ]);
    }
}
