<?php

namespace App\Entity;

use App\Repository\ServiceCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceCategoryRepository::class)]
class ServiceCategory
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

    #[ORM\Column(type: Types::TEXT)]
    private ?string $image = null;

    /**
     * @var Collection<int, Service>
     */
    #[ORM\OneToMany(targetEntity: Service::class, mappedBy: 'service_category')]
    private Collection $services;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'serviceCategory')]
    private Collection $images;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->images = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

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
            $service->setServiceCategory($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getServiceCategory() === $this) {
                $service->setServiceCategory(null);
            }
        }

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
            $image->setServiceCategory($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getServiceCategory() === $this) {
                $image->setServiceCategory(null);
            }
        }

        return $this;
    }
}
