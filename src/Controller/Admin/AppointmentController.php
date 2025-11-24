<?php

namespace App\Controller\Admin;

use App\Repository\AppointementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/appointments')]
#[IsGranted('ROLE_EMPLOYEE')]
class AppointmentController extends AbstractController
{
    #[Route('/', name: 'admin_appointments')]
    public function index(AppointementRepository $repository): Response
    {
        $appointments = $repository->findBy([], ['date_hour' => 'DESC']);
        
        return $this->render('admin/appointments/index.html.twig', [
            'appointments' => $appointments,
        ]);
    }

    #[Route('/{id}/confirm', name: 'admin_appointment_confirm')]
    public function confirm(
        int $id,
        AppointementRepository $repository,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $appointment = $repository->find($id);
        
        if (!$appointment) {
            throw $this->createNotFoundException('Rendez-vous non trouvé');
        }
        
        $appointment->setStatus('confirmed');
        $em->flush();
        
        // Envoi d'email de confirmation au client
        try {
            $email = (new Email())
                ->from('noreply@salonluxe.fr')
                ->to($appointment->getClientEmail())
                ->subject('Rendez-vous confirmé - Salon Luxe')
                ->html($this->renderView('emails/appointment_confirmed.html.twig', [
                    'appointment' => $appointment,
                ]));
            
            $mailer->send($email);
        } catch (\Exception $e) {
            // Log l'erreur
        }
        
        $this->addFlash('success', 'Rendez-vous confirmé avec succès !');
        
        return $this->redirectToRoute('admin_appointments');
    }

    #[Route('/{id}/cancel', name: 'admin_appointment_cancel')]
    public function cancel(
        int $id,
        AppointementRepository $repository,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $appointment = $repository->find($id);
        
        if (!$appointment) {
            throw $this->createNotFoundException('Rendez-vous non trouvé');
        }
        
        $appointment->setStatus('cancelled');
        $em->flush();
        
        // Envoi d'email d'annulation au client
        try {
            $email = (new Email())
                ->from('noreply@salonluxe.fr')
                ->to($appointment->getClientEmail())
                ->subject('Rendez-vous annulé - Salon Luxe')
                ->html($this->renderView('emails/appointment_cancelled.html.twig', [
                    'appointment' => $appointment,
                ]));
            
            $mailer->send($email);
        } catch (\Exception $e) {
            // Log l'erreur
        }
        
        $this->addFlash('warning', 'Rendez-vous annulé.');
        
        return $this->redirectToRoute('admin_appointments');
    }

    #[Route('/{id}/delete', name: 'admin_appointment_delete')]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        int $id,
        AppointementRepository $repository,
        EntityManagerInterface $em
    ): Response {
        $appointment = $repository->find($id);
        
        if (!$appointment) {
            throw $this->createNotFoundException('Rendez-vous non trouvé');
        }
        
        $em->remove($appointment);
        $em->flush();
        
        $this->addFlash('success', 'Rendez-vous supprimé avec succès.');
        
        return $this->redirectToRoute('admin_appointments');
    }
}
