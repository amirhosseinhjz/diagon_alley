<?php

namespace App\Entity\Address;

use App\Entity\User\User;
use App\Repository\Address\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?AddressCity $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[Assert\Regex(pattern: '/^[0-9]{4,10}$/', message: "The postCode '{{ value }}' is not a valid postCode.")]
    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $postCode = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?bool $isActive = true;

    #[ORM\Column]
    #[Assert\GreaterThanOrEqual(-90, message: "Latitude should be Greater than -90")]
    #[Assert\LessThanOrEqual(90, message: "Latitude should be Less than 90")]
    private ?float $lat = 0;

    #[Assert\GreaterThanOrEqual(-180, message: "Longitude should be Greater than -180")]
    #[Assert\LessThanOrEqual(180, message: "Longitude should be Less than 180")]
    #[ORM\Column]
    private ?float $lng = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCity(): ?AddressCity
    {
        return $this->city;
    }

    public function setCity(?AddressCity $city): self
    {
        $this->city->removeAdderess($this);
        $city->addAddress($this);

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

    #[ORM\PreUpdate]
    #[ORM\PrePersist]
    public function setUpdateAt(): self
    {
        $this->updateAt = new \DateTimeImmutable();

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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
