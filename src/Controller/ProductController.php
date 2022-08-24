<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/product', name: 'app_product_')]
class ProductController extends AbstractController
{
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $req): Response
    {
        return $this->json([]);
    }

    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(ManagerRegistry $doctrine, Request $req): Response
    {
        return $this->json([]);
    }

    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, Request $req): Response
    {
        return $this->json([]);
    }

    //needs filters
    #[Route('/products', name: 'products', methods: ['GET'])]
    public function showProducts(ManagerRegistry $doctrine, Request $req): Response
    {
        return $this->json([]);
    }

    #[Route('/{name}', name: 'showOne', methods: ['GET'])]
    public function showOneProduct(ManagerRegistry $doctrine, Request $req): Response
    {
        return $this->json([]);
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(ManagerRegistry $doctrine, Request $req): Response
    {
        return $this->json([]);
    }
}
