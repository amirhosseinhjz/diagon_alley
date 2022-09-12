<?php

namespace App\Controller\Cart;

use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Service\Cart\CartService;
use App\Trait\ControllerTrait;
use App\Entity\Cart\Cart;
use OpenApi\Attributes as OA;
use App\Utils\Swagger\Cart as CartSwagger;

#ToDo: remove all the user methods
#[Route('/api/cart', name: 'app_cart')]
class CartController extends AbstractController
{
    use ControllerTrait;
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
    #[OA\Response(
        response: 200,
        description: 'Returns the cart-Id',
        content: new OA\JsonContent(
            ref: new Model(type: Cart::class ,groups: ['Cart.create'])
        ),
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Cart')]
    public function create(): Response
    {
        try {
            $user = $this->getUser();
            $cart = $this->cartManager->createCart($user);
            $data = $this->serializer->normalize($cart, 'json', ['groups' => 'Cart.create']);
            return $this->json([
                'cart' => $data
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['Error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'expire_cart', methods: ['DELETE'])]
    #[OA\Response(
        response: 200,
        description: 'Cart expired',
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Cart')]
    public function delete(int $id): Response
    {
        try {
            $cart = $this->cartManager->getCartById($id);
            $this->checkAccess();
            $this->isGranted('CART_ACCESS', $cart);
            $this->cartManager->expireCart($cart);
            return $this->json([
                'm' => 'Cart expired successfully'
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[OA\Response(
        response: 200,
        description: 'Cart details',
        content: new OA\JsonContent(
            ref: new Model(type: Cart::class ,groups: ['Cart.read'])
        ),
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Cart')]
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

    #[Route('/item', name: 'add_item_to_cart', methods: ['POST'])]
    #[OA\RequestBody(
        description: "Add item to cart",
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: CartSwagger\AddItem::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Item added to cart',
        content: new OA\JsonContent(
            ref: new Model(type: Cart::class ,groups: ['Cart.read'])
        ),
    )]
    #[OA\Tag(name: 'Cart')]
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


    #[Route('/item', name: 'remove_item_from_cart', methods: ['DELETE'])]
    #[OA\RequestBody(
        description: "Remove item from cart",
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: CartSwagger\RemoveItem::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Item removed from cart',
        content: new OA\JsonContent(
            ref: new Model(type: Cart::class ,groups: ['Cart.read'])
        ),
    )]
    #[OA\Tag(name: 'Cart')]
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

    #[Route('/{id}/clear', name: 'clear_cart', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Cart cleared',
    )]
    #[OA\Response(
        response: 401,
        description: 'Invalid credentials',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Cart')]
    public function clearCart(int $id): Response
    {
        try {
            $cart = $this->cartManager->getCartById($id);
            $this->isGranted('CART_ACCESS', $cart);
            $this->cartManager->clearCart($cart);
            return $this->json([
                'm' => 'Cart cleared successfully'
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['Error' => $exception->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
