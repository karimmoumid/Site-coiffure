<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use BcMath\Number;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $lot_number = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $dlc = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProductCategory $product_category = null;

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

    public function getLotNumber(): ?int
    {
        return $this->lot_number;
    }

    public function setLotNumber(int $lot_number): static
    {
        $this->lot_number = $lot_number;
        return $this;
    }

    public function getDlc(): ?\DateTimeImmutable
    {
        return $this->dlc;
    }

    public function setDlc(\DateTimeImmutable $dlc): static
    {
        $this->dlc = $dlc;

        return $this;
    }

    public function getProductCategory(): ?ProductCategory
    {
        return $this->product_category;
    }

    public function setProductCategory(?ProductCategory $product_category): static
    {
        $this->product_category = $product_category;

        return $this;
    }
}
