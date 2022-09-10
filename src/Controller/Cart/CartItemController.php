<?php

namespace App\Controller\Cart;

use App\Interface\Cart\CartItemServiceInterface;
use App\Interface\Authentication\JWTManagementInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

#[Route('/cart/item', name: 'app_cart_item')]
class CartItemController extends AbstractController
{
    private CartItemServiceInterface $cartItemService;
    private JWTManagementInterface $JWTManagement;


    public function __construct(CartItemServiceInterface $cartItemService, JWTManagementInterface $JWTManagement){
        $this->cartItemService = $cartItemService;
        $this->JWTManagementInterface=$JWTManagement;
    }

    #[Route('/read/{id}', name: 'read_cart_item')]
    public function read(int $id): Response
    {
        try {
            $item = $this->cartItemService->getCartItemById($id);
            if($item === null){
                return $this->json(['m'=>'item does not exist'],Response::HTTP_NOT_FOUND);
            }
            return $this->json([ #check
                $this->cartItemService->createDTOFromCartItem($item)
            ], Response::HTTP_CREATED);
        }catch (Exception $exception){
            return $this->json(['m'=> $exception->getMessage()],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/remove/{itemId}', name: 'remove', methods: ['GET'])]
    public function remove($itemId): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $item= $this->cartItemService->getCartItemById($itemId);
            $this->denyAccessUnlessGranted($item);
            $this->cartItemService->removeCartItem($item);
            return $this->json([
                'm' => 'item removed successfully'
            ],Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/create', name: 'add_item_to_cart', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $array = $this->cartItemService->getRequestBody($request);
            #ToDo: access control
            #ToDo: check if the item already exists in the cart
            $item = $this->cartItemService->createCartItemFromArray($array);
            #ToDo: manage relation with cart, variant in creating from DTO
            return $this->json([
                'm' => "item added"
                ,Response::HTTP_OK]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/update', name: 'add_item_to_cart', methods: ['POST'])]
    public function update(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $array = $this->cartItemService->getRequestBody($request);
            #ToDo: access control
            $item = $this->cartItemService->createCartItemFromArray($array);
            #ToDo: manage relation with cart, variant in creating from DTO
            return $this->json([
                'm' => "item added"
                ,Response::HTTP_OK]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}