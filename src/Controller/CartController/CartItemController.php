<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart/item', name: 'app_cart_item')]
class CartItemController extends AbstractController
{
    #[Route('/read/{id}', name: 'read_cart_item')]
    public function index(int $id): Response
    {   #todo: fetch and return the item properties
        return $this->json([
            'id'=> $id
        ]);
    }
}
