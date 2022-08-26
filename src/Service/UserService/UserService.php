<?php

namespace App\Service\UserService;


use App\Entity\User;
use App\DTO\UserDTOs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserService
{

    private const UserRoles = [
        'ROLE_ADMIN' => User\Admin::class,
        'ROLE_SELLER' => User\Seller::class,
        'ROLE_CUSTOMER' => User\Customer::class,
    ];

    private const UserDTOs = [
        'ROLE_ADMIN' => UserDTOs\AdminDTO::class,
        'ROLE_SELLER' => UserDTOs\SellerDTO::class,
        'ROLE_CUSTOMER' => UserDTOs\CustomerDTO::class,
    ];

    private EntityManagerInterface $em;
    private Serializer $serializer;
    private ValidatorInterface $validator;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->validator = $validator;
        $this->passwordHasher = $passwordHasher;
    }

    public function arrayToDTO(array $array)
    {
        return $this->serializer->deserialize(json_encode($array), self::UserDTOs[$array['roles'][0]], 'json');
    }

    private function createValidDTO(array $array)
    {
        $userDTO = $this->arrayToDTO($array);
        $DTOErrors = $this->validator->validate($userDTO);
        if (count($DTOErrors) > 0) {
            throw (new \Exception(json_encode($DTOErrors)));
        }
        return $userDTO;
    }


    /**
     * @param array $array
     * @return User\User
     * @throws \Exception
     */
    public function createFromArray(array $array)
    {
        $userDTO = $this->createValidDTO($array);
        $user = $this->createUserFromDTO($userDTO, false);
        $databaseErrors = $this->validate($user);
        if (count($databaseErrors) > 0) {
            throw (new \Exception(json_encode($databaseErrors)));
        }
        $this->em->flush();
        return $user;
    }

    public function createUserFromDTO(UserDTOs\UserDTO $dto, $flush = true): UserInterface
    {
        $role = $dto->roles[0];
        $userClass = self::UserRoles[$role];
        $user = new $userClass();
        foreach ($dto as $key => $value) {
            $getterName = 'get' . ucfirst($key);
            $setterName = 'set' . ucfirst($key);
            $user->$setterName($dto->$getterName());
        }
        $hashedPassword = $this->passwordHasher->hashPassword($user, $dto->getPassword());
        $user->setPassword($hashedPassword);
        $this->em->persist($user);
        if ($flush) {
            $this->em->flush();
        }
        return $user;
    }

    private function validate($dto): array
    {
        $errors = $this->validator->validate($dto);
        $errorsArray = [];
        foreach ($errors as $error) {
            $errorsArray[] = $error->getMessage();
        }
        return $errorsArray;
    }

    public function getUserById(int $id): User\User
    {
        return $this->em->getRepository(User\User::class)->find($id);
    }
}
