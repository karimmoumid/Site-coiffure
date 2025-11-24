<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $small_description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $complet_description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $time = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $image = null;

    #[ORM\ManyToOne(targetEntity: ServiceCategory::class, inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ServiceCategory $service_category = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'service', cascade: ['persist', 'remove'])]
    private Collection $images;

    /**
     * @var Collection<int, Appointement>
     */
    #[ORM\ManyToMany(targetEntity: Appointement::class, mappedBy: 'services')]
    private Collection $appointements;


    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->appointements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSmallDescription(): ?string
    {
        return $this->small_description;
    }

    public function setSmallDescription(string $small_description): static
    {
        $this->small_description = $small_description;
        return $this;
    }

    public function getCompletDescription(): ?string
    {
        return $this->complet_description;
    }

    public function setCompletDescription(string $complet_description): static
    {
        $this->complet_description = $complet_description;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getTime(): ?int
    {
        // Retourner le temps en minutes (entier)
        return (int) $this->time;
    }

    public function setTime(string $time): static
    {
        $this->time = $time;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDuration(): ?int
    {
        // Alias pour getTime() pour la compatibilité
        return $this->getTime();
    }

    public function setDuration(int $duration): static
    {
        $this->time = (string) $duration;
        return $this;
    }

    public function getServiceCategory(): ?ServiceCategory
    {
        return $this->service_category;
    }

    public function setServiceCategory(?ServiceCategory $service_category): static
    {
        $this->service_category = $service_category;
        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setService($this);
        }
        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getService() === $this) {
                $image->setService(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Appointement>
     */
    public function getAppointements(): Collection
    {
        return $this->appointements;
    }

    public function addAppointement(Appointement $appointement): static
    {
        if (!$this->appointements->contains($appointement)) {
            $this->appointements->add($appointement);
            $appointement->addService($this);
        }
        return $this;
    }

    public function removeAppointement(Appointement $appointement): static
    {
        if ($this->appointements->removeElement($appointement)) {
            $appointement->removeService($this);
        }
        return $this;
    }


    /**
     * Méthode pour l'affichage dans les formulaires
     */
    public function __toString(): string
    {
        return sprintf('%s - %s€ (%d min)', 
            $this->name, 
            $this->price, 
            $this->getTime()
        );
    }
}
