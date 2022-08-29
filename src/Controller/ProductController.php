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
            return $this->json(['product' => $product]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(Request $req, SerializerInterface $serializer): Response
    {
        try {
            [$name, $updates] = $this->productManager->getRequestBody($req);
            $product = $this->productManager->update($name, $updates);
            if (array_key_exists('error', $product)) return $this->json(['message' => $product['error']], 400);
            $json = $serializer->serialize($product['product'], 'json', ['groups' => ['product_basic']]);
            return $this->json(['product' => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, Request $req): Response
    {
        try {
            $name = $req->get('name');
            $message = $this->productManager->deleteByName($name);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/products', name: 'products', methods: ['POST'])]
    public function getProducts(ManagerRegistry $doctrine, Request $req): Response
    {
        try {
            $requestBody = $this->productManager->getRequestBody($req);
            //sort by ** make sure variants have sold number
            //TODO add to product entity: view times , created at
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{name}', name: 'get_one_product', methods: ['GET'])]
    public function getOneProduct(ManagerRegistry $doctrine, string $name, SerializerInterface $serializer): Response
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
            //TODO fix get products first
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/add-feature', name: 'add_feature', methods: ['PATCH'])]
    public function addFeature(Request $req): Response
    {
//        try {
//            [$name, $features] = $this->productManager->getRequestBody($req);
//            $message = $this->productManager->addFeature($name, $features);
//            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
//            return $this->json(['message' => 'feature added']);
//        } catch (Exception $exception) {
//            return $this->json(['message' => $exception->getMessage()], 500);
//        }
    }

    #[Route('/remove-feature', name: 'remove_feature', methods: ['PATCH'])]
    public function removeFeature(Request $req): Response
    {
//        try {
//            [$name, $features] = $this->productManager->getRequestBody($req);
//            $message = $this->productManager->removeFeature($name, $features);
//            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
//            return $this->json(['message' => 'feature removed']);
//        } catch (Exception $exception) {
//            return $this->json(['message' => $exception->getMessage()], 500);
//        }
    }

    #[Route('/toggle-activity', name: 'toggle_activity', methods: ['PATCH'])]
    public function toggleActivity(Request $req): Response
    {
        try {
            [$name, $active] = $this->productManager->getRequestBody($req);
            $message = $this->productManager->toggleActivity($name, $active);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
