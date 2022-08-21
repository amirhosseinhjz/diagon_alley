<?php

namespace App\Entity;

use App\Repository\BrandRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Category;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255, unique: true, nullable: false)]
    private $name = null;

    #[ORM\ManyToMany(targetEntity: "Category", inversedBy: "brands")]
    #[JoinTable(name: "brand_categories")]
    #[JoinColumn(name: "brand_id", referencedColumnName: "id")]
    #[InverseJoinColumn(name: "category_id", referencedColumnName: "id")]
    private $categories;

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
}
