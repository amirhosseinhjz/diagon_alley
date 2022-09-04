<?php

namespace App\Controller\CartController;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

use App\Service\CartService\CartService;

use App\Entity\Cart\Cart;
use App\Entity\Cart\CartItem;

//____validation

#[Route('/cart', name: 'app_cart')]
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
            $cart = $this->cartManager->getCart($this->getUser()->getUserIdentifier());
            $this->denyAccessUnlessGranted('_EDIT', $cart);
            return $this->json([
                'cart' => $cart
            ]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], 500);
        }
    }

    //Question: do i need to take an extra step and remove all cart items?
    #[Route('/delete', name: 'delete_cart', methods: ['GET'])]
    public function delete(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $cart = $this->cartManager->getCart($this->getUser()->getUserIdentifier());  #check
            $this->denyAccessUnlessGranted('_EDIT', $cart);
            $this->cartManager->removeCart($cart);
            return $this->json([
                'm' => 'removed cart'
            ]);

        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], 500);
        }
    }


    #[Route('/{id}', name: 'get_cart', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, int $id): Response  #DTO
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $entityManager = $doctrine->getManager();
            $cartRepository = $entityManager->getRepository(Cart::class);
            $cart = $cartRepository->findOneBy(['id' => $id]); #check: is this a cart?
            #T: check
            if ($cart) {
                $this->denyAccessUnlessGranted('_VIEW', $cart);
                return $this->json([
                    'cart' => $cart
                ]);
            } else {
                return $this->json([
                    'm' => 'cart not found'
                ]);
            }
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], 500);
        }
    }

    #[Route('/finalize', name: 'finalize_cart', methods: ['GET'])]
    public function finalize(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $cart = $this->cartManager->getCart($this->getUser()->getUserIdentifier());
            $this->denyAccessUnlessGranted('_EDIT', $cart);
            $this->cartManager->updateStatus($cart, 'PENDING');
            return $this->json([
                'm' => 'Status changed'
            ]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], 500);
        }
    }

    #[Route('/finalize', name: 'successful_payment', methods: ['GET'])]
    public function success(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $cart = $this->cartManager->getCart($this->getUser()->getUserIdentifier());
            $this->denyAccessUnlessGranted('_EDIT', $cart);
            $this->cartManager->updateStatus($cart, 'SUCCESS');
            return $this->json([
                'm' => 'Status changed'
            ]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], 500);
        }
    }

    #[Route('/revert', name: 'revert_pending_cart', methods: ['GET'])]
    public function revert(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $cart = $this->cartManager->getCart($this->getUser()->getUserIdentifier());
            $this->denyAccessUnlessGranted('_BACK', $cart);
            $this->cartManager->updateStatus($cart, 'INIT');
            return $this->json([
                'm' => 'Status changed'
            ]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], 500);
        }
    }

    #[Route('/add', name: 'add_item_to_cart', methods: ['POST'])]
    public function addItem(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $array = $this->cartManager->getRequestBody($request);
            $cart = $this->cartManager->getCart($array['userid']);
            $this->denyAccessUnlessGranted('_EDIT', $cart);
            $cart = $this->cartManager->addItemToCart($array);
            return $this->json([
                'm' => "item added"
            ]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], 500);
        }
    }

    #[Route('/remove', name: 'remove_item_from_cart', methods: ['POST'])]
    public function removeItem(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        try {
            $array = $this->cartManager->getRequestBody($request);  #T: DTO
            $cart = $this->cartManager->getCart($array['userid']);
            $this->denyAccessUnlessGranted('_EDIT', $cart);
            $this->cartManager->removeItemFromCart($array);
            return $this->json([
                'm' => 'item removed'
            ]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], 500);
        }
    }

}

