<?php

namespace App\Controller\Cart;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Service\Cart\CartService;
use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;

#ToDo: remove all the user methods
#[Route('/api/cart', name: 'app_cart')]
class CartController extends AbstractController
{
    protected CartService $cartManager;

    public function __construct(CartService $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    #[Route('/create', name: 'create_cart', methods: ['GET'])]
    public function create(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            #ToDo: get the user correctly
            $cart = $this->getUser()->getCart();  #The user may not be but a customer
            $this->denyAccessUnlessGranted('_EDIT', $cart);
            return $this->json([
                'cart' => $cart
            ]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    //Question: do I need to take an extra step and remove all cart items?
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
    public function show(ManagerRegistry $doctrine, int $id): Response  #DTO
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $cart = $this->cartManager->getCartById($id);
            #T: check
            if ($cart) {
                $this->denyAccessUnlessGranted('_VIEW', $cart);
                return $this->json([
                    'cart' => $cart
                ]);
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
            $this->cartManager->updateStatus($cart, 'PENDING');
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
