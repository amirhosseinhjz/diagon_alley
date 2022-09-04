<?php

namespace App\Entity\User;

use App\Entity\Address\Address;
use App\Repository\UserRepository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: "type", type: "string" )]
#[ORM\DiscriminatorMap(['seller'=>'Seller','admin'=>'Admin','customer'=>'Customer'])]
#[UniqueEntity(fields: ["email"], message: "This email is already in use")]
#[UniqueEntity(fields: ["phoneNumber"], message: "This phoneNumber is already in use")]
abstract class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const PAYLOAD_KEY_FOR_USERNAME = 'phoneNumber';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastName = null;

    #[Assert\Email(message:"The email '{{ value }}' is not a valid email.")]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[Assert\Regex(pattern: '/^(\+989|09)\d{9}$/', message:"The number '{{ value }}' is not a valid PhoneNumber.")]
    #[ORM\Column(length: 13, unique: true)]
    private ?string $phoneNumber = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $tokenValidateAfter = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'userId', targetEntity: Address::class)]
    private Collection $addresses;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getTokenValidateAfter(): ?\DateTimeInterface
    {
        return $this->tokenValidateAfter;
    }

    public function setTokenValidateAfter(?\DateTimeInterface $tokenValidateAfter): self
    {
        $this->tokenValidateAfter = $tokenValidateAfter;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses->add($address);
            $address->setUserId($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getUserId() === $this) {
                $address->setUserId(null);
            }
        }

        return $this;
    }
}
