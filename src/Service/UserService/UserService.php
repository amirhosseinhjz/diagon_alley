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

    private const importantFields = [
        'phoneNumber',
        'password',
        'shopSlug',
        'email',
    ];

    private EntityManagerInterface $em;
    private Serializer $serializer;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $this->em = $em;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $this->validator = $validator;
    }

    public function arrayToDTO(array $array)
    {
        return $this->serializer->deserialize(json_encode($array), self::UserDTOs[$array['roles'][0]], 'json');
    }

    private function createValidDTO(array $array)
    {
        $userDTO = $this->arrayToDTO($array);
        $DTOErrors = $this->validate($userDTO);
        if (count($DTOErrors) > 0) {
            throw (new \Exception(json_encode($DTOErrors)));
        }
        return $userDTO;
    }

    public function createFromArray(array $array) : User\User
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

    public function createUserFromDTO(UserDTOs\UserDTO $dto, $flush = true): User\User
    {
        $role = $dto->roles[0];
        $userClass = self::UserRoles[$role];
        $user = new $userClass();
        foreach ($dto as $key => $value) {
            $getterName = 'get' . ucfirst($key);
            $setterName = 'set' . ucfirst($key);
            $user->$setterName($dto->$getterName());
        }
        $this->em->persist($user);
        if ($flush) {
            $this->em->flush();
        }
        return $user;
    }

    private function createDTOFromUser(User\User $user): UserDTOs\UserDTO
    {
        $role = $user->getRoles()[0];
        $userDTOClass = self::UserDTOs[$role];
        $userDTO = new $userDTOClass();
        foreach ($user as $key => $value) {
            $getterName = 'get' . ucfirst($key);
            $setterName = 'set' . ucfirst($key);
            $userDTO->$setterName($user->$getterName());
        }
        return $userDTO;
    }

    private function validate($object): array
    {
        $errors = $this->validator->validate($object);
        $errorsArray = [];
        foreach ($errors as $error) {
            $errorsArray[] = $error->getMessage();
        }
        return $errorsArray;
    }

    public function getUserById(int $id): User\User
    {
        $user = $this->em->getRepository(User\User::class)->find($id);
        if (!$user) {
            throw (new \Exception("User with id $id not found"));
        }
        return $user;
    }

    public function getUserBy(array $criteria): User\User
    {
        $user = $this->em->getRepository(User\User::class)->findOneBy($criteria);
        if (!$user) {
            throw (new \Exception("User with criteria $criteria not found"));
        }
        return $user;
    }

    public function getUsersBy(array $criteria): array
    {
        $users = $this->em->getRepository(User\User::class)->findBy($criteria);
        if (!$users) {
            throw (new \Exception("Users with criteria $criteria not found"));
        }
        return $users;
    }

    public function getUsers(): array
    {
        return $this->em->getRepository(User\User::class)->findAll();
    }

    private function updateUser(User\User $user, array $criteria): User\User
    {
        foreach (self::importantFields as $key) {
            if (array_key_exists($key, $criteria)) {
                throw (new \Exception("Action not allowed."));
            }
        }
        foreach ($criteria as $key => $value) {
                $setterName = 'set' . ucfirst($key);
                $user->$setterName($value);
            }
        $errors = $this->validate($user);
        if (count($errors) > 0) {
            throw (new \Exception(json_encode($errors)));
        }
        $this->em->flush();
        return $user;
    }

    public function updateUserById(int $id, array $array)
    {
        $user = $this->getUserById($id);
        return $this->updateUser($user, $array);
    }


    public function updateEmailById(int $id, string $email)
    {
        $user = $this->getUserById($id);
        return $this->updateEmail($user, $email);
    }

    private function updateEmail(User\User $user, string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw (new \Exception("Invalid email"));
        }
        try{
            $this->getUserBy(['email' => $email]);
            throw (new \Exception("Email already exists"));
        } catch (\Exception $e) {}
        $user->setEmail($email);
        $this->em->flush();
        return $user;
    }

    public function updatePhoneNumberById(int $id, string $phoneNumber)
    {
        $user = $this->getUserById($id);
        return $this->updatePhoneNumber($user, $phoneNumber);
    }

    private function updatePhoneNumber(User\User $user, string $phoneNumber)
    {
        if (!preg_match('/^(\+989|09)\d{9}$/', $phoneNumber)) {
            throw (new \Exception("Invalid phone number"));
        }
        try{
            $this->getUserBy(['phoneNumber' => $phoneNumber]);
            throw (new \Exception("Phone number already exists"));
        } catch (\Exception $e) {}
        $user->setPhoneNumber($phoneNumber);
        $this->em->flush();
        return $user;
    }

    public function updatePasswordById(int $id, string $password)
    {
        $user = $this->getUserById($id);
        return $this->updatePassword($user, $password);
    }

    private function updatePassword(User\User $user, string $password)
    {
        $user->setPassword($password);
        $this->em->flush();
        return $user;
    }

    public function deleteUserById($id)
    {
        $user = $this->getUserById($id);
        if ($user->getRoles()[0] === 'ROLE_ADMIN') {
            $admins = $this->getUsersBy(['roles' => ['ROLE_ADMIN']]);
            if (count($admins) <= 1) {
                throw (new \Exception("Can't delete last admin."));
            }
        }
        if (!$user->isIsActive()) {
            throw (new \Exception("User is already deleted."));
        }
        $user->setIsActive(false);
        $this->em->flush();
    }
}
