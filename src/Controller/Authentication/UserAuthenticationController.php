<?php

namespace App\Controller\Authentication;
use App\CacheEntityManager\CacheEntityManager;
use App\CacheRepository\UserRepository\CacheSellerRepository;
use App\DTO\AuthenticationDTO\LoginDTO;
use App\Entity\User\Seller;
use App\Interface\Authentication\JWTManagementInterface;
use App\Interface\Cache\CacheInterface;
use App\Repository\UserRepository\SellerRepository;
use App\Repository\UserRepository\UserRepository;
use App\Service\UserService\UserService;
use Doctrine\ORM\EntityManager;
use Exception;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

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

    #[Route('/gogo/{id}', name: 'gogo',methods: ['GET'])]
    public function update(UserService $userService, EntityManagerInterface $em, $id): Response
    {
        try{
            $seller = $em->getRepository(Seller::class)->find($id);
//            $userService->updatePhoneNumberById($id,'+989666665676');
            dd($seller);
        }catch(Exception $e){
            return $this->json(json_decode($e), Response::HTTP_OK);
        }
    }

    #[Route('/gogol/{id}', name: 'gogol',methods: ['GET'])]
    public function _update(CacheEntityManager $em, int $id, CacheInterface $cache): Response
    {
        try{
            $repo = $em->getRepository(Seller::class);
            $seller = $repo->find($id);
//            $repo->deleteAllFromCache();
//            $userService->updatePhoneNumberById($id,'+989666665676');
            dd($seller);
        }catch(Exception $e){
            return $this->json(json_decode($e), Response::HTTP_OK);
        }
    }

}
