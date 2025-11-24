<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Entity\ServiceCategory;
use App\Repository\ServiceCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/service-categories')]
#[IsGranted('ROLE_ADMIN')]
class ServiceCategoryController extends AbstractController
{
    #[Route('/', name: 'admin_service_categories')]
    public function index(ServiceCategoryRepository $repository): Response
    {
        $categories = $repository->findAll();
        
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'admin_service_category_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        if ($request->isMethod('POST')) {
            $category = new ServiceCategory();
            $category->setName($request->request->get('name'));
            $category->setCompletDescription($request->request->get('completDescription'));
            $category->setSmallDescription($request->request->get('smallDescription'));
            
            // Gestion de l'upload d'image
            $imageFile = $request->files->get('image');
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('categories_images_directory'),
                        $newFilename
                    );
                    
                    $category->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }
            
            $em->persist($category);
            $em->flush();
            
            $this->addFlash('success', 'Catégorie créée avec succès !');
            return $this->redirectToRoute('admin_service_categories');
        }
        
        return $this->render('admin/categories/index.html.twig');
    }

    #[Route('/{id}/edit', name: 'admin_service_category_edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        ServiceCategoryRepository $repository,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ): Response {
        $category = $repository->find($id);
        
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }
        
        if ($request->isMethod('POST')) {
            $category->setName($request->request->get('name'));
            $category->setCompletDescription($request->request->get('completDescription'));
            $category->setSmallDescription($request->request->get('smallDescription'));
            
            // Gestion de l'upload d'image
            $imageFile = $request->files->get('image');
           if ($imageFile) {
    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
    $safeFilename = $slugger->slug($originalFilename);
    $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

    try {
        // Supprimer l'ancienne image si existante
        if ($category->getImage()) {
            $oldImagePath = $this->getParameter('categories_images_directory') . '/' . $category->getImage();
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Déplacer le nouveau fichier
        $imageFile->move(
            $this->getParameter('categories_images_directory'),
            $newFilename
        );

        // Mettre à jour la propriété image avec le nom du fichier
        $category->setImage($newFilename);

    } catch (FileException $e) {
        $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
    }
}

            
            $em->flush();
            
            $this->addFlash('success', 'Catégorie modifiée avec succès !');
            return $this->redirectToRoute('admin_service_categories');
        }
        
        return $this->render('admin/categories/edit.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_service_category_delete', methods: ['POST'])]
    public function delete(
        int $id,
        ServiceCategoryRepository $repository,
        EntityManagerInterface $em
    ): Response {
        $category = $repository->find($id);
        
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }
        
        // Vérifier qu'il n'y a pas de services associés
        if (count($category->getServices()) > 0) {
            $this->addFlash('error', 'Impossible de supprimer une catégorie contenant des services.');
            return $this->redirectToRoute('admin_service_categories');
        }
        
        // Supprimer l'image associée
        if ($category->getImages()) {
            foreach ($category->getImages() as $image) {
    $imagePath = $this->getParameter('categories_images_directory') . '/' . $image->getName();
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
    $em->remove($image);
}

        }

        $image = $category->getImage();
if ($image) {
    // Récupérer le nom du fichier
    $imagePath = $this->getParameter('categories_images_directory') . '/' . $image;

    // Supprimer le fichier si il existe
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }
}
        
        $em->remove($category);
        $em->flush();
        
        $this->addFlash('success', 'Catégorie supprimée avec succès !');
        return $this->redirectToRoute('admin_service_categories');
    }
}
