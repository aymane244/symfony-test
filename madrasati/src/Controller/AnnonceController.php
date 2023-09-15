<?php
namespace App\Controller;

use App\Entity\Annonce;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\AsciiSlugger;

class AnnonceController extends AbstractController
{
    #[Route('/annonce', name: 'app_annonce')]
    public function index(Request $request, ManagerRegistry $doctrine): Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $entityManager = $doctrine->getManager();
        $slugger = new AsciiSlugger();
        $annonce = new Annonce();
        $annonce->setTitre("");
        $annonce->setDescription("");
        $annonce->setDatePublication(new \DateTime(""));
        // $annonce->setImage('attachment', FileType::class);
        $form = $this->createFormBuilder($annonce)
        ->add('titre', TextType::class, [
            'attr'=> [
                'placeholder' => 'Ajouter un titre',
                'class' => 'input-group'
                ]
                ])
                ->add('description', TextareaType::class)
                ->add('date_publication', DateType::class, [
                    'widget' => 'single_text',
                    'html5' => true,
                    'format' => 'yyyy-MM-dd',
                    ])
                    ->add('image', FileType::class, [
                        'constraints' => [
                            new File([
                                'maxSize' => '5024k',
                            ])
                        ]
                    ])
                    ->add('save', SubmitType::class, [
                        'label'=> 'Créer une annonce',
                    ])
                    ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $brochureFile = $form->get('image')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $annonce->setImage($newFilename);
            }
            $entityManager->persist($annonce);
            $entityManager->flush();       
            $this->addFlash('success', 'Annonce ajouter avec Succès');
        }
        $user = $this->getUser();
        return $this->renderForm('annonce/index.html.twig', [
            'controller_name' => 'app_annonce',
            'form' => $form,
            'user' => $user
        ]);
    }
    #[Route('/annonce/show', name: 'show_annonce')]
    public function show(ManagerRegistry $doctrine): Response{
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $annonces = $doctrine->getRepository(Annonce:: class)->findAll();
        // dd($annonces);
        // dd($annonce);
        return $this->render('annonce/show.html.twig', [
            'annonces' => $annonces,
            'user' => $user
        ]);
    }
}
