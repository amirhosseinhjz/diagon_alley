<?php


namespace App\Controller\User;

use App\Entity\User\Seller;
use App\Repository\UserRepository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService\UserService;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->json($users);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserService $userService): Response
    {
//        try{
//        return $this->json($userService->createFromArray($request->toArray()));
//        }catch(\Exception $e){
//            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
//        }
//        $cls = $request::class;
        $S= 'Seller';
        $x = new $S();
//        $x->setShopSlug('test');
        return $this->json($S);
    }

    #[Route('/{id}', name: 'app_user_new', methods: ['GET'])]
    public function getUserData(UserService $userService, $id): Response
    {
        return $this->json($userService->getUserById($id));
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
