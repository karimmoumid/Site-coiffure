<?php

namespace App\Repository;

use App\Entity\Appointement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Appointement>
 */
class AppointementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointement::class);
    }

    /**
     * Trouve tous les rendez-vous pour une date donnée avec un statut spécifique
     */
    public function findByDateAndStatus(\DateTime $date, array $statuses): array
    {
        $startOfDay = clone $date;
        $startOfDay->setTime(0, 0, 0);
        
        $endOfDay = clone $date;
        $endOfDay->setTime(23, 59, 59);
        
        return $this->createQueryBuilder('a')
            ->andWhere('a.date_hour >= :start')
            ->andWhere('a.date_hour <= :end')
            ->andWhere('a.status IN (:statuses)')
            ->setParameter('start', \DateTimeImmutable::createFromMutable($startOfDay))
            ->setParameter('end', \DateTimeImmutable::createFromMutable($endOfDay))
            ->setParameter('statuses', $statuses)
            ->orderBy('a.date_hour', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les rendez-vous en conflit avec une plage horaire donnée
     */
    public function findConflicts(\DateTimeImmutable $startTime, \DateTimeImmutable $endTime, ?int $excludeId = null): array
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.status IN (:statuses)')
            ->setParameter('statuses', ['confirmed', 'pending'])
            // Un conflit existe si:
            // - Le début du nouveau RDV est avant la fin d'un RDV existant
            // ET
            // - La fin du nouveau RDV est après le début d'un RDV existant
            ->andWhere('a.date_hour < :end')
            ->andWhere('a.end_date_hour > :start')
            ->setParameter('start', $startTime)
            ->setParameter('end', $endTime);
        
        if ($excludeId) {
            $qb->andWhere('a.id != :excludeId')
               ->setParameter('excludeId', $excludeId);
        }
        
        return $qb->getQuery()->getResult();
    }

    /**
     * Trouve les prochains rendez-vous confirmés
     */
    public function findUpcomingConfirmed(int $limit = 10): array
    {
        $now = new \DateTimeImmutable();
        
        return $this->createQueryBuilder('a')
            ->andWhere('a.status = :status')
            ->andWhere('a.date_hour > :now')
            ->setParameter('status', 'confirmed')
            ->setParameter('now', $now)
            ->orderBy('a.date_hour', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve les rendez-vous du jour
     */
    public function findTodayAppointments(): array
    {
        $today = new \DateTime('today');
        $tomorrow = new \DateTime('tomorrow');
        
        return $this->createQueryBuilder('a')
            ->andWhere('a.date_hour >= :today')
            ->andWhere('a.date_hour < :tomorrow')
            ->andWhere('a.status = :status')
            ->setParameter('today', \DateTimeImmutable::createFromMutable($today))
            ->setParameter('tomorrow', \DateTimeImmutable::createFromMutable($tomorrow))
            ->setParameter('status', 'confirmed')
            ->orderBy('a.date_hour', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les rendez-vous par statut pour une période donnée
     */
    public function countByStatusAndPeriod(string $status, \DateTimeImmutable $start, \DateTimeImmutable $end): int
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->andWhere('a.status = :status')
            ->andWhere('a.date_hour >= :start')
            ->andWhere('a.date_hour <= :end')
            ->setParameter('status', $status)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les créneaux occupés pour une journée
     */
    public function findOccupiedSlots(\DateTime $date): array
    {
        $startOfDay = clone $date;
        $startOfDay->setTime(0, 0, 0);
        
        $endOfDay = clone $date;
        $endOfDay->setTime(23, 59, 59);
        
        return $this->createQueryBuilder('a')
            ->select('a.date_hour as start', 'a.end_date_hour as end')
            ->andWhere('a.date_hour >= :start')
            ->andWhere('a.date_hour <= :end')
            ->andWhere('a.status IN (:statuses)')
            ->setParameter('start', \DateTimeImmutable::createFromMutable($startOfDay))
            ->setParameter('end', \DateTimeImmutable::createFromMutable($endOfDay))
            ->setParameter('statuses', ['confirmed', 'pending'])
            ->orderBy('a.date_hour', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
