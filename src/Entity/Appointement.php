<?php

namespace App\Entity;

use App\Repository\AppointementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AppointementRepository::class)]
class Appointement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $date_hour = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $end_date_hour = null;

    #[ORM\Column]
    private ?bool $confirmed = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $total_duration = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'pending';

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\ManyToMany(targetEntity: Service::class, inversedBy: 'appointements')]
    private Collection $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->confirmed = false;
        $this->status = 'pending';
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateHour(): ?\DateTimeImmutable
    {
        return $this->date_hour;
    }

    public function setDateHour(\DateTimeImmutable $date_hour): static
    {
        $this->date_hour = $date_hour;
        $this->updateEndDateHour();
        return $this;
    }

    public function getEndDateHour(): ?\DateTimeImmutable
    {
        return $this->end_date_hour;
    }

    public function setEndDateHour(\DateTimeImmutable $end_date_hour): static
    {
        $this->end_date_hour = $end_date_hour;
        return $this;
    }

    public function isConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): static
    {
        $this->confirmed = $confirmed;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getTotalDuration(): ?int
    {
        return $this->total_duration;
    }

    public function setTotalDuration(int $total_duration): static
    {
        $this->total_duration = $total_duration;
        $this->updateEndDateHour();
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

        public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
        public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    
    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $this->calculateTotalDuration();
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            $this->calculateTotalDuration();
        }

        return $this;
    }

    /**
     * Calcule la durée totale en fonction des services sélectionnés
     */
    public function calculateTotalDuration(): void
    {
        $total = 0;
        foreach ($this->services as $service) {
            $total += $service->getTime();
        }
        $this->total_duration = $total;
        $this->updateEndDateHour();
    }

    /**
     * Met à jour l'heure de fin en fonction de la durée totale
     */
    private function updateEndDateHour(): void
    {
        if ($this->date_hour && $this->total_duration) {
            $endTime = \DateTime::createFromImmutable($this->date_hour);
            $endTime->modify('+' . $this->total_duration . ' minutes');
            $this->end_date_hour = \DateTimeImmutable::createFromMutable($endTime);
        }
    }

    /**
     * Vérifie si ce rendez-vous est en conflit avec un autre
     */
    public function isConflictWith(Appointement $other): bool
    {
        if (!$this->date_hour || !$this->end_date_hour || 
            !$other->getDateHour() || !$other->getEndDateHour()) {
            return false;
        }

        // Vérifier si les plages horaires se chevauchent
        return !(
            $this->end_date_hour <= $other->getDateHour() ||
            $this->date_hour >= $other->getEndDateHour()
        );
    }
}
