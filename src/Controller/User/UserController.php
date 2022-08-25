<?php


namespace App\Controller\User;

use App\Entity\User\Seller;
use App\Repository\UserRepository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService\UserService;
use OpenApi\Attributes as OA;

#[Route('/user')]
class UserController extends AbstractController
{


//    #[OA\Response(
//        response: 200,
//        description: 'Returns list of users',
//        content: new OA\JsonContent(
//            type: 'array',
//            items: new OA\Items(ref: new Model(type: AlbumDto::class, groups: ['full']))
//        )
//    )]
    #[Route('/list', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $x = (new Route('/hjtj', name: 'app_user_new', methods: ['POST']))->getPath();
        dd($x);
        $users = $userRepository->findAll();
        return $this->json($users);
    }

    #[Route('/new', name: 'app_user_new_user', methods: ['POST'])]
    public function newUser(Request $request, UserService $userService): Response
    {
        try{
        return $this->json($userService->createFromArray($request->toArray()));
        }catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{int:id}', name: 'get_user_by_id', methods: ['GET'])]
    public function getUserData(UserService $userService, $id): Response
    {
        try {
            return $this->json($userService->getUserById($id));
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        //        return $this->renderForm('seller/edit.html.twig', [
//            'seller' => $seller,
//            'form' => $form,
//        ]);
//    }
//
//    #[Route('/{id}', name: 'app_seller_delete', methods: ['POST'])]
//    public function delete(Request $request, Seller $seller, SellerRepository $sellerRepository): Response
//    {
//        if ($this->isCsrfTokenValid('delete' . $seller->getId(), $request->request->get('_token'))) {
//            $sellerRepository->remove($seller, true);
//        }
//
//        return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
//    }
    }
}
