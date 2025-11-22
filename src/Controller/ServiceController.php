<?php

namespace App\Controller;

use App\Repository\ServiceCategoryRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/services')]
class ServiceController extends AbstractController
{
    #[Route('/', name: 'app_services')]
    public function index(ServiceCategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        
        return $this->render('service/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/category/{id}', name: 'app_service_category')]
    public function showCategory(
        int $id,
        ServiceCategoryRepository $categoryRepository
    ): Response {
        $category = $categoryRepository->find($id);
        
        if (!$category) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }
        
        return $this->render('service/category.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/detail/{id}', name: 'app_service_show')]
    public function show(int $id, ServiceRepository $serviceRepository): Response
    {
        $service = $serviceRepository->find($id);
        
        if (!$service) {
            throw $this->createNotFoundException('Service non trouvé');
        }
        
        return $this->render('service/show.html.twig', [
            'service' => $service,
        ]);
    }
}
