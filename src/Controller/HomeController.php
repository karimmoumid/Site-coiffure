<?php

namespace App\Controller;

use App\Repository\ServiceCategoryRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ServiceCategoryRepository $categoryRepository,
        ServiceRepository $serviceRepository
    ): Response {
        $categories = $categoryRepository->findAll();
        $featuredServices = $serviceRepository->findBy([], ['id' => 'DESC'], 6);
        
        return $this->render('home/index.html.twig', [
            'categories' => $categories,
            'featured_services' => $featuredServices,
        ]);
    }
}
