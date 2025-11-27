<?php

namespace App\Controller\Api;

use App\Repository\AppointementRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/appointment')]
class AppointmentApiController extends AbstractController
{
    #[Route('/available-slots', name: 'api_appointment_available_slots', methods: ['POST'])]
    public function getAvailableSlots(
        Request $request,
        AppointementRepository $appointmentRepo,
        ServiceRepository $serviceRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $date = new \DateTime($data['date']);
        $serviceIds = $data['services'] ?? [];
        
        // Calculer la durée totale des services sélectionnés
        $totalDuration = 0;
        if (!empty($serviceIds)) {
            foreach ($serviceIds as $serviceId) {
                $service = $serviceRepo->find($serviceId);
                if ($service) {
                    $totalDuration += $service->getTime();
                }
            }
        }
        
        // Si aucun service sélectionné, durée par défaut de 30 minutes
        if ($totalDuration === 0) {
            $totalDuration = 30;
        }
        
        // Récupérer tous les rendez-vous confirmés pour cette date
        $existingAppointments = $appointmentRepo->findByDateAndStatus($date, ['confirmed', 'pending']);
        
        // Générer les créneaux disponibles
        $slots = $this->generateAvailableSlots($date, $totalDuration, $existingAppointments);
        
        return $this->json([
            'slots' => $slots,
            'totalDuration' => $totalDuration
        ]);
    }
    
    #[Route('/check-conflict', name: 'api_appointment_check_conflict', methods: ['POST'])]
    public function checkConflict(
        Request $request,
        AppointementRepository $appointmentRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $startTime = new \DateTimeImmutable($data['startTime']);
        $duration = (int)$data['duration'];
        
        $endTime = $startTime->modify('+' . $duration . ' minutes');
        
        // Vérifier s'il y a des conflits
        $conflicts = $appointmentRepo->findConflicts($startTime, $endTime);
        
        return $this->json([
            'available' => empty($conflicts),
            'conflicts' => count($conflicts)
        ]);
    }
    
    #[Route('/calculate-duration', name: 'api_appointment_calculate_duration', methods: ['POST'])]
    public function calculateDuration(
        Request $request,
        ServiceRepository $serviceRepo
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $serviceIds = $data['services'] ?? [];
        
        $totalDuration = 0;
        $totalPrice = 0;
        $services = [];
        
        foreach ($serviceIds as $serviceId) {
            $service = $serviceRepo->find($serviceId);
            if ($service) {
                $totalDuration += $service->getTime();
                $totalPrice += $service->getPrice();
                $services[] = [
                    'id' => $service->getId(),
                    'name' => $service->getName(),
                    'duration' => $service->getTime(),
                    'price' => $service->getPrice()
                ];
            }
        }
        
        return $this->json([
            'totalDuration' => $totalDuration,
            'totalPrice' => $totalPrice,
            'services' => $services
        ]);
    }
    
    /**
     * Génère les créneaux disponibles pour une journée
     */
    private function generateAvailableSlots(
        \DateTime $date,
        int $duration,
        array $existingAppointments
    ): array {
        $slots = [];
        
        // Heures d'ouverture du salon (9h - 19h)
        $openingTime = clone $date;
        $openingTime->setTime(11, 0);
        
        $closingTime = clone $date;
        $closingTime->setTime(23, 59);
        
        // Pas de 30 minutes pour les créneaux
        $interval = new \DateInterval('PT30M');
        
        $currentSlot = clone $openingTime;
        
        while ($currentSlot < $closingTime) {
            $slotEnd = clone $currentSlot;
            $slotEnd->modify('+' . $duration . ' minutes');
            
            // Vérifier que le créneau ne dépasse pas l'heure de fermeture
            if ($slotEnd > $closingTime) {
                break;
            }
            
            // Vérifier s'il y a un conflit avec un rendez-vous existant
            $hasConflict = false;
            foreach ($existingAppointments as $appointment) {
                if ($this->isTimeSlotConflict(
                    $currentSlot,
                    $slotEnd,
                    $appointment->getDateHour(),
                    $appointment->getEndDateHour()
                )) {
                    $hasConflict = true;
                    break;
                }
            }
            
            // Vérifier que le créneau n'est pas dans le passé
            $now = new \DateTime();
            $isPast = $currentSlot < $now;
            
            $slots[] = [
                'time' => $currentSlot->format('H:i'),
                'datetime' => $currentSlot->format('Y-m-d H:i:s'),
                'available' => !$hasConflict && !$isPast,
                'disabled' => $hasConflict || $isPast,
                'reason' => $isPast ? 'past' : ($hasConflict ? 'conflict' : null)
            ];
            
            $currentSlot->add($interval);
        }
        
        return $slots;
    }
    
    /**
     * Vérifie si deux créneaux horaires sont en conflit
     */
    private function isTimeSlotConflict(
        \DateTime $start1,
        \DateTime $end1,
        ?\DateTimeImmutable $start2,
        ?\DateTimeImmutable $end2
    ): bool {
        if (!$start2 || !$end2) {
            return false;
        }
        
        $start2DateTime = \DateTime::createFromImmutable($start2);
        $end2DateTime = \DateTime::createFromImmutable($end2);
        
        // Les créneaux se chevauchent si:
        // - Le début du créneau 1 est avant la fin du créneau 2
        // ET
        // - La fin du créneau 1 est après le début du créneau 2
        return $start1 < $end2DateTime && $end1 > $start2DateTime;
    }
}
