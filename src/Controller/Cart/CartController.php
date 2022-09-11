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
            $cart = $this->cartManager->getCartById($id);
            $this->isGranted('CART_ACCESS', $cart);
            $this->cartManager->expireCart($cart);
            return $this->json([
                'm' => 'Cart expired successfully'
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'get_cart', methods: ['GET'])]
    public function show(int $id): Response
    {
        try {
            $cart = $this->cartManager->getCartById($id);
            $this->isGranted('CART_ACCESS', $cart);
            $data = $this->serializer->normalize($cart, 'json', ['groups' => 'Cart.read']);
            return $this->json([
                    'cart' => $data
                ]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/additem', name: 'add_item_to_cart', methods: ['POST'])]
    public function addItem(Request $request): Response
    {
        try {
            $cart = $this->cartManager->getCartById($request->get('cartId'));
            $this->isGranted('CART_ACCESS', $cart);
            $cart = $this->cartManager->addToCartFromRequest($request->toArray());
            $data = $this->serializer->normalize($cart, 'json', ['groups' => 'Cart.read']);
            return $this->json([
                'item' => $data
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['Error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    #[Route('/removeitem', name: 'remove_item_from_cart', methods: ['POST'])]
    public function removeItem(Request $request): Response
    {
        try {
            $cart = $this->cartManager->getCartById($request->get('cartId'));
            $this->isGranted('CART_ACCESS', $cart);
            $cart = $this->cartManager->removeFromCartFromRequest($request->toArray());
            $data = $this->serializer->normalize($cart, 'json', ['groups' => 'Cart.read']);
            return $this->json([
                'item' => $data
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['Error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/clear', name: 'clear_cart', methods: ['POST'])]
    public function clearCart(Request $request): Response
    {
        try {
            $cart = $this->cartManager->getCartById($request->get('cartId'));
            $this->isGranted('CART_ACCESS', $cart);
            $cart = $this->cartManager->clearCartFromRequest($request->toArray());
            return $this->json([
                'm' => 'Cart cleared successfully'
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['Error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
