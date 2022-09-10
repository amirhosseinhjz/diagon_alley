<?php

namespace App\Controller\Cart;

use App\Interface\Cart\CartServiceInterface;
use App\Interface\Authentication\JWTManagementInterface;
use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/cart', name: 'app_cart')]
class CartController extends AbstractController
{
    protected CartServiceInterface $cartManager;
    private JWTManagementInterface $JWTManagement;

    public function __construct(CartServiceInterface $cartManagement, JWTManagementInterface $JWTManagement)
    {
        $this->cartManager = $cartManagement;
        $this->JWTManagementInterface=$JWTManagement;

    }


    #[Route('/create', name: 'create_cart', methods: ['GET'])]
    public function create(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            #ToDo: get the user correctly, access management
            $cart = $this->cartManager->createCart($this->getUser());
            return $this->json([
                'cart' => $this->cartManager->createDTOFromCart($cart)
            ],Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/{id}/delete', name: 'delete_cart', methods: ['GET'])]
    public function delete($id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $cart = $this->cartManager->getCartById($id);  #check
            $this->denyAccessUnlessGranted('_EDIT', $cart);
            $this->cartManager->removeCart($cart);
            return $this->json([
                'm' => 'removed cart'
            ]);

        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/{id}', name: 'get_cart', methods: ['GET'])]
    public function read(int $id): Response  #DTO
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $cart = $this->cartManager->getCartById($id);
            #T: check
            if ($cart) {
                $this->denyAccessUnlessGranted('_VIEW', $cart);
                return $this->json([
                    'cart' => $cart
                ], Response::HTTP_CREATED);
            } else {
                return $this->json([
                    'm' => 'cart not found'
                ], Response::HTTP_NOT_FOUND);
            }
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/{id}/finalize', name: 'finalize_cart', methods: ['GET'])]
    public function finalize($id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $cart = $this->cartManager->getCartById($id);
            $this->denyAccessUnlessGranted('_EDIT', $cart);
            $this->cartManager->updateStatus($cart, Cart::STATUS_PENDING);
            return $this->json([
                'm' => 'Status changed'
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_OK);
        }
    }


    #[Route('/{id}/close', name: 'successful_payment', methods: ['GET'])]  #t: right name?
    public function success($id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $cart = $this->cartManager->getCartById($id);
            $this->denyAccessUnlessGranted('_EDIT', $cart);
            $this->cartManager->updateStatus($cart, Cart::STATUS_SUCCESS);
            return $this->json([
                'm' => 'Status changed'
            ],Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/{id}/revert', name: 'revert_pending_cart', methods: ['GET'])]
    public function revert($id): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $cart = $this->cartManager->getCartById($id);
            $this->denyAccessUnlessGranted('_BACK', $cart);
            $this->cartManager->updateStatus($cart, Cart::STATUS_INIT);
            return $this->json([
                'm' => 'Status changed'
            ],Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #ToDo: remove item just by variant id
    #ToDo: use the update method to add/remove multiple items together

}
