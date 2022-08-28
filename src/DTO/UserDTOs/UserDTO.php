<?php

namespace App\DTO\UserDTOs;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Serializer;


abstract class UserDTO
{
    private ?int $id = null;


    #[Assert\Length(min:3, max:255)]
    public ?string $name = null;

    #[Assert\NotBlank()]
    #[Assert\Email(message:"The email '{{ value }}' is not a valid email.")]
    public string $email;

    public ?string $lastName = null;

    #[Assert\Regex(pattern: '/^(\+989|09)\d{9}$/', message:"The number '{{ value }}' is not a valid PhoneNumber.")]
    public string $phoneNumber;


    #[Assert\Regex(pattern: '/^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/', message:"The password is not a strong password.")]
    #[Assert\NotBlank()]
    public string $password;

    #[Assert\Type(type:'array')]
    #[Assert\NotBlank()]
    public array $roles;

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
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
    public function getEmail(): string
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
    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }
    public function getRoles(): ?array
    {
        return $this->roles;
    }
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
}