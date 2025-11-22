<?php

namespace App\Controller\Employee;

use App\Repository\AppointementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employee')]
#[IsGranted('ROLE_EMPLOYEE')]
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'employee_dashboard')]
    public function index(AppointementRepository $appointmentRepo): Response
    {
        $today = new \DateTime('today');
        
        // Statistiques
        $stats = [
            'today_appointments' => count(array_filter(
                $appointmentRepo->findBy(['status' => 'confirmed']),
                fn($a) => $a->getAppointmentDate() == $today
            )),
            'pending_appointments' => count($appointmentRepo->findBy(['status' => 'pending'])),
            'upcoming_this_week' => count(array_filter(
                $appointmentRepo->findBy(['status' => 'confirmed']),
                fn($a) => $a->getAppointmentDate() >= $today && 
                          $a->getAppointmentDate() <= (clone $today)->modify('+7 days')
            )),
        ];
        
        // Prochains rendez-vous
        $upcomingAppointments = array_filter(
            $appointmentRepo->findBy(['status' => 'confirmed'], ['appointmentDate' => 'ASC', 'appointmentTime' => 'ASC']),
            fn($a) => $a->getAppointmentDate() >= $today
        );
        $upcomingAppointments = array_slice($upcomingAppointments, 0, 10);
        
        // Rendez-vous en attente
        $pendingAppointments = $appointmentRepo->findBy(
            ['status' => 'pending'],
            ['createdAt' => 'DESC'],
            5
        );
        
        return $this->render('employee/dashboard.html.twig', [
            'stats' => $stats,
            'upcoming_appointments' => $upcomingAppointments,
            'pending_appointments' => $pendingAppointments,
        ]);
    }
}
