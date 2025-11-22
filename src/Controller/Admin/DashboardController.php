<?php

namespace App\Controller\Admin;

use App\Repository\AppointementRepository;
use App\Repository\ProductRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'admin_dashboard')]
    public function index(
        AppointementRepository $appointmentRepo,
        ServiceRepository $serviceRepo,
        ProductRepository $productRepo,
        UserRepository $userRepo
    ): Response {
        // Statistiques
        $stats = [
            'pending_appointments' => count($appointmentRepo->findBy(['status' => 'pending'])),
            'confirmed_appointments' => count($appointmentRepo->findBy(['status' => 'confirmed'])),
            'total_services' => count($serviceRepo->findAll()),
            'total_products' => count($productRepo->findAll()),
            'total_users' => count($userRepo->findAll()),
        ];
        
        // Derniers rendez-vous en attente
        $pendingAppointments = $appointmentRepo->findBy(
            ['status' => 'pending'],
            ['createdAt' => 'DESC'],
            5
        );
        
        // Prochains rendez-vous confirmés
        $upcomingAppointments = $appointmentRepo->findBy(
            ['status' => 'confirmed'],
            ['appointmentDate' => 'ASC'],
            5
        );
        
        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'pending_appointments' => $pendingAppointments,
            'upcoming_appointments' => $upcomingAppointments,
        ]);
    }
}
