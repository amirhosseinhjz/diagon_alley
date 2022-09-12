<?php

namespace App\Utils\Swagger\User;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Annotation as Serializer;


class User
{

    #[Assert\Length(min: 3, max: 255)]
    public ?string $name = null;

    #[Assert\NotBlank()]
    #[Assert\Email(message: "The Email '{{ value }}' is not a valid Email.")]
    public string $email;

    public ?string $lastName = null;

    #[Assert\Regex(pattern: '/^(\+989|09)\d{9}$/', message: "The number '{{ value }}' is not a valid PhoneNumber.")]
    #[Serializer\Groups(['user.userName'])]
    public string $phoneNumber;

    #[Assert\Length(min:3, max:255)]
    #[Assert\Type(type:'string')]
    public ?string $shopSlug = null;

    #[Assert\Regex(pattern: '/^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/', message: "The password is not a strong password.")]
    #[Assert\NotBlank()]
    #[Serializer\Groups(['user.pass'])]
    public string $password;

    #[Assert\Type(type: 'array')]
    #[Assert\NotBlank()]
    #[Assert\NotNull]
    #[OA\Property(
        type: "array",
        items: new OA\Items(
            type: "string"
        )
    )]
    public array $roles;

}