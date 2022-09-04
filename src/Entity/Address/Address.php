<?php

namespace App\Entity\Address;

use App\Entity\User\User;
use App\Repository\Address\AddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AddressCity $city = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $postCode = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column(length: 15)]
    private ?string $activeStatus = null;

    #[ORM\Column]
    private ?float $lat = null;

    #[ORM\Column]
    private ?float $lng = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(?User $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getCity(): ?AddressCity
    {
        return $this->city;
    }

    public function setCity(?AddressCity $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function setPostCode(?string $postCode): self
    {
        $this->postCode = $postCode;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getActiveStatus(): ?string
    {
        return $this->activeStatus;
    }

    public function setActiveStatus(string $activeStatus): self
    {
        $this->activeStatus = $activeStatus;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }
}
