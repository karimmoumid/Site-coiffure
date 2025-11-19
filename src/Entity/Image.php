<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $original_name = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Service $service = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?ServiceCategory $serviceCategory = null;

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

    public function getOriginalName(): ?string
    {
        return $this->original_name;
    }

    public function setOriginalName(string $original_name): static
    {
        $this->original_name = $original_name;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getServiceCategory(): ?ServiceCategory
    {
        return $this->serviceCategory;
    }

    public function setServiceCategory(?ServiceCategory $serviceCategory): static
    {
        $this->serviceCategory = $serviceCategory;

        return $this;
    }
}
