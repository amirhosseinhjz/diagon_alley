<?php

namespace App\Controller\Authentication;

use App\DTO\AuthenticationDTO\LoginDTO;
use App\Entity\User\Seller;
use App\Interface\Authentication\JWTManagementInterface;
use App\Repository\UserRepository\UserRepository;
use App\Service\UserService\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api',name: 'user_auth_api')]
class UserAuthenticationController extends AbstractController
{
    protected JWTManagementInterface $JWTManager;
    
    protected $passHasher;

    protected $userService;

    public function __construct(
        JWTManagementInterface $JWTManager,
        UserPasswordHasherInterface $hasher,
        UserService $userService
    )
    {
        $this->JWTManager = $JWTManager;

        $this->passHasher = $hasher;

        $this->userService = $userService;
    }

    #[Route('/user/register', name: 'app_user_register',methods: ['POST'])]
    public function create(Request $request): Response
    {
        try{
            $user = $this->userService->createFromArray($request->toArray());
            $token = $this->JWTManager->getTokenUser($user,$request);
            return new JsonResponse($token);
        }catch(\Exception $e){
            return $this->json(json_decode($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/user/logout', name: 'app_user_logout',methods: ['GET'])]
    public function logout(): Response
    {
        $this->JWTManager->invalidateToken();
        return $this->json([
            'message'=>'you logged out',
            'status'=>200
        ]);
    }

    #[Route('/user/login', name: 'app_user_login',methods: ['POST'])]
    public function login(Request $request,UserRepository $repository,ValidatorInterface $validator): Response
    {
        try{
            (new LoginDTO($request->toArray(),$validator))->doValidate();
            if ($user = $repository->findOneBy(['phoneNumber'=>$request->toArray()['username']]))
            {
                $this->JWTManager->checkIfPasswordIsValid($user,$request);
                $token = $this->JWTManager->getTokenUser($user);
                return new JsonResponse($token);
            } else {
                return $this->json([
                    'status'=>401,
                    'message'=>'Invalid credentials'
                ]);
            }
        } catch(Exception $e) {
            return $this->json(json_decode($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/user/new-password', name: 'app_user_new_password',methods: ['POST'])]
    public function newPassword(Request $request): Response
    {
        $body = $request->toArray();
        $user = $this->JWTManager->authenticatedUser();
        try{
            $userId = $this->userService->getUserBy(['phoneNumber' => $user->getUserIdentifier()])->getId();
            if(! array_key_exists('password',$body))throw new Exception("Password field is empty");
            $this->userService->updatePasswordById($userId,$body['password']);
            $this->JWTManager->invalidateToken();
            return $this->json(
                ['message'=>'password changed successfully'],
                status: 200
            );
        } catch (Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/user/new-phone-number', name: 'app_user_new_phone_number',methods: ['POST'])]
    public function newUserName(Request $request): Response
    {
        $body = $request->toArray();
        $user = $this->JWTManager->authenticatedUser();
        try{
            $userId = $this->userService->getUserBy(['phoneNumber' => $user->getUserIdentifier()])->getId();
            if(! array_key_exists('phone number',$body))throw new Exception("phone number field is empty");
            $this->userService->updatePhoneNumberById($userId,$body['phone number']);
            $this->JWTManager->invalidateToken();
            return $this->json(
                ['message'=>'phone number changed successfully'],
                status: 200
            );
        } catch (Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
