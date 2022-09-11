<?php

namespace App\Controller\Cart;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\Cart\CartService;
use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;

#ToDo: remove all the user methods
#[Route('/api/cart', name: 'app_cart')]
class CartController extends AbstractController
{
    protected CartService $cartManager;

    public function __construct(
        CartService $cartManager,
        SerializerInterface $serializer
    )
    {
        $this->cartManager = $cartManager;
        $this->serializer = $serializer;
    }

    #[Route('/create', name: 'create_cart', methods: ['GET'])]
    public function create(): Response
    {
        try {
            #ToDo: get the user correctly
            $user = $this->getUser();  #The user may not be but a customer
            $cart = $this->cartManager->createCart($user);
            $data = $this->serializer->normalize($cart, 'json', ['groups' => 'Cart.create']);
            return $this->json([
                'cart' => $data
            ]);
        } catch (Exception $exception) {
            return $this->json(['Error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    //Question: do I need to take an extra step and remove all cart items?
    #[Route('/{id}', name: 'expire_cart', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $cart = $this->cartManager->expireCart($id);
            return $this->json([
                'm' => 'Cart expired successfully'
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'get_cart', methods: ['GET'])]
    public function show(int $id): Response  #DTO
    {
        try {
            $cart = $this->cartManager->getCartById($id);
            $data = $this->serializer->normalize($cart, 'json', ['groups' => 'Cart.read']);
            return $this->json([
                    'cart' => $data
                ]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

//    #[Route('/additem', name: 'add_item_to_cart', methods: ['POST'])]
//    public function addItem(Request $request): Response
//    {
//        try {
//            $cart = $this->cartManager->addToCartFromRequest($request->toArray());
//            return $this->json([
//                'item' => $data
//            ], Response::HTTP_OK);
//        } catch (Exception $exception) {
//            return $this->json(['m' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
//        }
//    }

    #[Route('/{id}/close', name: 'successful_payment', methods: ['GET'])]  #t: right name?
    public function success($id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $cart = $this->cartManager->getCartById($id);
            $this->denyAccessUnlessGranted('_EDIT', $cart);
            $this->cartManager->updateStatus($cart, 'SUCCESS');
            return $this->json([
                'm' => 'Status changed'
            ],Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/revert', name: 'revert_pending_cart', methods: ['GET'])]
    public function revert($id): Response   #todo: what if the customer creates a new card while they have another waiting for payment
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $cart = $this->cartManager->getCartById($id);
            $this->denyAccessUnlessGranted('_BACK', $cart);
            $this->cartManager->updateStatus($cart, 'INIT');
            return $this->json([
                'm' => 'Status changed'
            ],Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/items/add', name: 'add_item_to_cart', methods: ['POST'])]
    public function addItem(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $array = $this->cartManager->getRequestBody($request);
            #ToDo: access control
            $this->cartManager->addItemToCart($array);
            return $this->json([
                'm' => "item added"
                ,Response::HTTP_OK]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/items/remove', name: 'remove_item_from_cart', methods: ['POST'])]
    public function removeItem(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $array = $this->cartManager->getRequestBody($request);
            #ToDo: access management
            $this->cartManager->removeItemFromCart($array);
            return $this->json([
                'm' => 'item removed successfully'
            ],Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #ToDo: remove item just by id

}
