<?php

namespace App\Controller;

use App\Entity\Appointement;
use App\Form\AppointmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class AppointmentController extends AbstractController
{
    #[Route('/rendez-vous', name: 'app_appointment')]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $appointment = new Appointement();
        
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($appointment);
            $entityManager->flush();

            // Envoi d'email au client
            try {
                $clientEmail = (new Email())
                    ->from('noreply@salonluxe.fr')
                    ->to($appointment->getEmail())
                    ->subject('Confirmation de votre demande de rendez-vous - Salon Luxe')
                    ->html($this->renderView('emails/appointment_client.html.twig', [
                        'appointment' => $appointment,
                    ]));
                
                $mailer->send($clientEmail);

                // Envoi d'email à l'admin
                $adminEmail = (new Email())
                    ->from('noreply@salonluxe.fr')
                    ->to('admin@salonluxe.fr')
                    ->subject('Nouvelle demande de rendez-vous')
                    ->html($this->renderView('emails/appointment_admin.html.twig', [
                        'appointment' => $appointment,
                    ]));
                
                $mailer->send($adminEmail);
            } catch (\Exception $e) {
                // Log l'erreur mais continue
            }

            $this->addFlash('success', 'Votre demande de rendez-vous a été envoyée avec succès ! Vous recevrez une confirmation par email une fois validée.');
            
            return $this->redirectToRoute('app_appointment');
        }

        return $this->render('appointment/new.html.twig', [
            'form' => $form,
        ]);
    }
}
