<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Entity\Service;
use App\Form\ServiceFormType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/services')]
#[IsGranted('ROLE_ADMIN')]
class ServiceController extends AbstractController
{
    #[Route('/', name: 'admin_services')]
    public function index(ServiceRepository $repository): Response
    {
        $services = $repository->findBy([], ['id' => 'DESC']);
        
        return $this->render('admin/services/index.html.twig', [
            'services' => $services,
        ]);
    }

    #[Route('/new', name: 'admin_service_new')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $service = new Service();
        $form = $this->createForm(ServiceFormType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('services_images_directory'),
                        $newFilename
                    );
                    
                    
                    $service->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $em->persist($service);
            $em->flush();

            $this->addFlash('success', 'Service créé avec succès !');
            return $this->redirectToRoute('admin_services');
        }

        return $this->render('admin/services/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_service_edit')]
    public function edit(
        int $id,
        Request $request,
        ServiceRepository $repository,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $service = $repository->find($id);
        
        if (!$service) {
            throw $this->createNotFoundException('Service non trouvé');
        }

        $form = $this->createForm(ServiceFormType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    // Supprimer l'ancienne image si existante
        if ($service->getImage()) {
            $oldImagePath = $this->getParameter('services_images_directory') . '/' . $service->getImage();
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Déplacer le nouveau fichier
        $imageFile->move(
            $this->getParameter('services_images_directory'),
            $newFilename
        );

        // Mettre à jour la propriété image avec le nom du fichier
        $service->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $em->flush();

            $this->addFlash('success', 'Service modifié avec succès !');
            return $this->redirectToRoute('admin_services');
        }

        return $this->render('admin/services/edit.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_service_delete', methods: ['POST'])]
    public function delete(
        int $id,
        ServiceRepository $repository,
        EntityManagerInterface $em
    ): Response {
        $service = $repository->find($id);
        
        if (!$service) {
            throw $this->createNotFoundException('Service non trouvé');
        }

        // Supprimer l'image associée
         if ($service->getImages()) {
            foreach ($service->getImages() as $image) {
    $imagePath = $this->getParameter('services_images_directory') . '/' . $image->getName();
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
    $em->remove($image);
}
$image = $service->getImage();
if ($image) {
    // Récupérer le nom du fichier
    $imagePath = $this->getParameter('services_images_directory') . '/' . $image;

    // Supprimer le fichier si il existe
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}

        $em->remove($service);
        $em->flush();

        $this->addFlash('success', 'Service supprimé avec succès !');
        return $this->redirectToRoute('admin_services');
    }
}
}