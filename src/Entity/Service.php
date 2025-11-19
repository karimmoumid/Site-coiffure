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
    private ?string $sÃmall_description = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $complet_description = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $price = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $time = null;

    #[ORM\Column]
    private ?bool $actif = null;

    /**
     * @var Collection<int, Appointement>
     */
    #[ORM\ManyToMany(targetEntity: Appointement::class, inversedBy: 'services')]
    private Collection $appointement;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'service')]
    private Collection $images;

    #[ORM\ManyToOne(inversedBy: 'services')]
    private ?ServiceCategory $service_category = null;

    public function __construct()
    {
        $this->appointement = new ArrayCollection();
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

    public function getSÃmallDescription(): ?string
    {
        return $this->sÃmall_description;
    }

    public function setSÃmallDescription(string $sÃmall_description): static
    {
        $this->sÃmall_description = $sÃmall_description;

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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getTime(): ?string
    {
        return $this->time;
    }

    public function setTime(string $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): static
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection<int, Appointement>
     */
    public function getAppointement(): Collection
    {
        return $this->appointement;
    }

    public function addAppointement(Appointement $appointement): static
    {
        if (!$this->appointement->contains($appointement)) {
            $this->appointement->add($appointement);
        }

        return $this;
    }

    public function removeAppointement(Appointement $appointement): static
    {
        $this->appointement->removeElement($appointement);

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

    public function getServiceCategory(): ?ServiceCategory
    {
        return $this->service_category;
    }

    public function setServiceCategory(?ServiceCategory $service_category): static
    {
        $this->service_category = $service_category;

        return $this;
    }
}
