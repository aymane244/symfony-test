<?php

namespace App\Controller;

use App\Entity\Services;
use App\Form\ServicesType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServiceController extends AbstractController
{
    #[Route('/service', name: 'app_service')]
    public function index(Request $request, ManagerRegistry $doctrine): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $entityManager = $doctrine->getManager();
        $service = new Services();
        $form = $this->createForm(ServicesType::class, $service);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($service);
            $entityManager->flush();       
            $this->addFlash('success', 'Service ajouté avec Succès');
        }
        return $this->render('service/index.html.twig', [
            'serviceForm' => $form->createView(),
            'form' => $form,
            'user' => $user
        ]);
    }
    #[Route('/service/show', name: 'show_service')]
    public function show(ManagerRegistry $doctrine): Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $services = $doctrine->getRepository(Services:: class)->findAll();
        return $this->render('service/show.html.twig', [
            'services' => $services,
            'user' => $user
        ]);
    }
}
