<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Brand;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, unique: true, nullable: false)]
    private $name = null;

    #[ORM\OneToOne(targetEntity: "Category")]
    #[ORM\JoinColumn(name: "parent_id", referencedColumnName: "id")]
    private $parent;

    #[ORM\ManyToMany(targetEntity: "Brand", mappedBy: "categories")]
    private $brands;

    #[ORM\OneToMany(mappedBy: "category", targetEntity: "Product")]
    private $products;

    #[ORM\ManyToMany(targetEntity: "Feature", inversedBy: "categories")]
    #[JoinTable(name: "category_features")]
    #[JoinColumn(name: "category_id", referencedColumnName: "id")]
    #[InverseJoinColumn(name: "feature_id", referencedColumnName: "id")]
    private $features;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function setParent(string $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
