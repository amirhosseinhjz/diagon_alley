<?php

namespace App\Controller\Cart;

use App\Interface\Cart\CartItemServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('/cart/item', name: 'app_cart_item')]
class CartItemController extends AbstractController
{

    private CartItemServiceInterface $cartItemService;

    public function __construct(CartItemServiceInterface $cartItemService){
        $this->cartItemService = $cartItemService;
    }

    #[Route('/read/{id}', name: 'read_cart_item')]
    public function index(int $id): Response
    {
        try {
            $item = $this->cartItemService->getCartItemById($id);
            if($item === null){
                return $this->json(['m'=>'item does not exist'],Response::HTTP_NOT_FOUND);
            }
            return $this->json([ #check
                'result' => $this->cartItemService->createDTOFromCartItem($item)
            ], Response::HTTP_OK);
        }catch (Exception $exception){
            return $this->json(['m'=> $exception->getMessage()],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}