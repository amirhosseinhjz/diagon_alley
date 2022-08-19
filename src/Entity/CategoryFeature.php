<?php

namespace App\Entity;

use App\Repository\CategoryFeatureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryFeatureRepository::class)]
class CategoryFeature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: "Feature")]
    #[ORM\JoinColumn(name: "feature_id", referencedColumnName: "id")]
    private ?int $feature_id = null;

    #[ORM\Column]
    #[ORM\ManyToOne(targetEntity: "Category")]
    #[ORM\JoinColumn(name: "category_id", referencedColumnName: "id")]
    private ?int $category_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFeatureId(): ?int
    {
        return $this->feature_id;
    }

    public function setFeatureId(int $feature_id): self
    {
        $this->feature_id = $feature_id;

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
