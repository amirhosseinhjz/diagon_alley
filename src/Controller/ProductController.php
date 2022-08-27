<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;
use Exception;
use App\Service\ProductManager;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/product', name: 'app_product_')]
class ProductController extends AbstractController
{
    protected ProductManager $productManager;

    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
    }

    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $req): Response
    {
        try {
            $requestBody = $this->productManager->getRequestBody($req);
            $validatedBody = $this->productManager->validateProductArray($requestBody);
            if (array_key_exists('error', $validatedBody)) return $this->json(['message' => $validatedBody['error']], 400);
            $product = $this->productManager->createEntityFromArray($validatedBody);
            $doctrine->getRepository(Product::class)->add($product, true);
            //serialize probably
            return $this->json(['product' => $product]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(ManagerRegistry $doctrine, Request $req): Response
    {
        try {
            //TODO type must be in array(physical digital)
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, Request $req): Response
    {
        try {
            //TODO delete product and all variants
            $name = $req->get('name');
            $this->productManager->deleteByName($name);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //needs filters
    #[Route('/products', name: 'products', methods: ['POST'])]
    public function showProducts(ManagerRegistry $doctrine, Request $req): Response
    {
        //TODO fix brand and category products first
        try {
            //category
            //brands
            //pagination
            //sort by
            //price range
            //quantity > 0
            //return name and price
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{name}', name: 'showOne', methods: ['GET'])]
    public function showOneProduct(ManagerRegistry $doctrine, string $name, SerializerInterface $serializer): Response
    {
        try {
            $product = $doctrine->getRepository(Product::class)->findOneByName($name);
            $json = $serializer->serialize($product, 'json', ['groups' => ['product_basic']]);
            return $this->json(['product' => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(ManagerRegistry $doctrine, Request $req): Response
    {
        try {

        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
