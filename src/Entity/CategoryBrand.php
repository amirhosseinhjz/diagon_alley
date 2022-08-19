<?php

namespace App\Entity;

use App\Repository\CategoryBrandRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryBrandRepository::class)]
class CategoryBrand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: "Brand")]
    #[ORM\JoinColumn(name: "brand_id", referencedColumnName: "id")]
    private ?int $brand_id = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: "Category")]
    #[ORM\JoinColumn(name: "category_id", referencedColumnName: "id")]
    private ?int $category_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrandId(): ?int
    {
        return $this->brand_id;
    }

    public function setBrandId(int $brand_id): self
    {
        $this->brand_id = $brand_id;

        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function setCategoryId(int $category_id): self
    {
        $this->category_id = $category_id;

        return $this;
    }
}
