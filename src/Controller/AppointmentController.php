<?php

namespace App\Controller;

use App\Entity\Appointement;
use App\Repository\ServiceRepository;
use App\Repository\AppointementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class AppointmentController extends AbstractController
{
    #[Route('/rendez-vous', name: 'app_appointment')]
    public function new(ServiceRepository $serviceRepo): Response
    {
        return $this->render('appointment/new.html.twig');
    }
    
    #[Route('/rendez-vous/submit', name: 'app_appointment_submit', methods: ['POST'])]
    public function submit(
        Request $request,
        EntityManagerInterface $em,
        ServiceRepository $serviceRepo,
        AppointementRepository $appointmentRepo,
        MailerInterface $mailer
    ): Response {
        // Récupérer les données du formulaire
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $comment = $request->request->get('comment');
        $slotDateTime = $request->request->get('appointment_slot');
        $serviceIds = $request->request->all('services');
        
        // Validation de base
        if (!$name || !$email || !$slotDateTime || empty($serviceIds)) {
            $this->addFlash('error', 'Veuillez remplir tous les champs obligatoires.');
            return $this->redirectToRoute('app_appointment');
        }
        
        // Créer le rendez-vous
        $appointment = new Appointement();
        $appointment->setName($name);
        $appointment->setEmail($email);
        $appointment->setComment($comment);
        $appointment->setDateHour(new \DateTimeImmutable($slotDateTime));
        
        // Ajouter les services et calculer la durée totale
        $totalDuration = 0;
        $totalPrice = 0;
        $serviceNames = [];
        
        foreach ($serviceIds as $serviceId) {
            $service = $serviceRepo->find($serviceId);
            if ($service) {
                $appointment->addService($service);
                $totalDuration += $service->getTime();
                $totalPrice += $service->getPrice();
                $serviceNames[] = $service->getName();
            }
        }
        
        $appointment->setTotalDuration($totalDuration);
        
        // Calculer et définir l'heure de fin
        $endTime = \DateTime::createFromImmutable($appointment->getDateHour());
        $endTime->modify('+' . $totalDuration . ' minutes');
        $appointment->setEndDateHour(\DateTimeImmutable::createFromMutable($endTime));
        
        // Vérifier les conflits avant de sauvegarder
        $conflicts = $appointmentRepo->findConflicts(
            $appointment->getDateHour(),
            $appointment->getEndDateHour()
        );
        
        if (!empty($conflicts)) {
            $this->addFlash('error', 'Ce créneau n\'est plus disponible. Veuillez en choisir un autre.');
            return $this->redirectToRoute('app_appointment');
        }
        
        // Sauvegarder le rendez-vous
        $em->persist($appointment);
        $em->flush();
        
        // Envoyer les emails
        $this->sendConfirmationEmails($appointment, $serviceNames, $totalPrice, $mailer);
        
        $this->addFlash('success', 'Votre demande de rendez-vous a été envoyée avec succès ! Vous recevrez une confirmation par email.');
        
        return $this->redirectToRoute('app_appointment_confirmation', ['id' => $appointment->getId()]);
    }
    
    #[Route('/rendez-vous/confirmation/{id}', name: 'app_appointment_confirmation')]
    public function confirmation(int $id, AppointementRepository $repo): Response
    {
        $appointment = $repo->find($id);
        
        if (!$appointment) {
            throw $this->createNotFoundException('Rendez-vous non trouvé');
        }
        
        return $this->render('appointment/confirmation.html.twig', [
            'appointment' => $appointment,
        ]);
    }
    
    #[Route('/api/services', name: 'api_get_services')]
    public function getServices(ServiceRepository $serviceRepo): JsonResponse
    {
        $services = $serviceRepo->findAll();
        
        $data = [];
        foreach ($services as $service) {
            $data[] = [
                'id' => $service->getId(),
                'name' => $service->getName(),
                'duration' => $service->getTime(),
                'price' => $service->getPrice(),
                'description' => $service->getSmallDescription(),
                'category' => $service->getServiceCategory() ? $service->getServiceCategory()->getName() : null
            ];
        }
        
        return $this->json($data);
    }
    
    /**
     * Envoyer les emails de confirmation
     */
    private function sendConfirmationEmails(
        Appointement $appointment,
        array $serviceNames,
        float $totalPrice,
        MailerInterface $mailer
    ): void {
        try {
            // Email au client
            $clientEmail = (new Email())
                ->from('noreply@salonluxe.fr')
                ->to($appointment->getEmail())
                ->subject('Confirmation de votre demande de rendez-vous - Salon Luxe')
                ->html($this->renderView('emails/appointment_client.html.twig', [
                    'appointment' => $appointment,
                    'services' => $serviceNames,
                    'totalPrice' => $totalPrice,
                ]));
            
            $mailer->send($clientEmail);
            
            // Email à l'administrateur
            $adminEmail = (new Email())
                ->from('noreply@salonluxe.fr')
                ->to('moumidmounir@gmail.com')
                ->subject('Nouvelle demande de rendez-vous')
                ->html($this->renderView('emails/appointment_admin.html.twig', [
                    'appointment' => $appointment,
                    'services' => $serviceNames,
                    'totalPrice' => $totalPrice,
                ]));
            
            $mailer->send($adminEmail);
            
        } catch (\Exception $e) {
            // Logger l'erreur mais ne pas bloquer le processus
            dd('Erreur envoi email: '.$e->getMessage());
            // $this->logger->error('Erreur envoi email: ' . $e->getMessage());
        }
    }
}
