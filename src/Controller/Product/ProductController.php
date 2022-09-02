<?php

namespace App\Controller\Product;

use App\Entity\Product\Product;
use App\Service\Product\ProductManager;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/product', name: 'app_product_')]
class ProductController extends AbstractController
{
    //TODO add viewCount, created_at to product

    protected ProductManager $productManager;

    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
    }

    //TODO: auth
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $req): Response
    {
        try {
            //TODO serialize
            $requestBody = $this->productManager->getRequestBody($req);
            $productArray = $this->productManager->normalizeArray($requestBody);
            $product = $this->productManager->createEntityFromArray($productArray);
            if (array_key_exists('error', $product)) return $this->json(['message' => $product['error']], 400);
            return $this->json(['product' => $product]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: auth
    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(Request $req, SerializerInterface $serializer, ManagerRegistry $doctrine): Response
    {
        try {
            [$id, $updates] = $this->productManager->getRequestBody($req);
            $product = $doctrine->getRepository(Product::class)->findOneById($id);
            if (!$product) return $this->json(['message' => 'category not found'], 400);
            $updatedProduct = $this->productManager->updateEntity($product, $updates);
            if (array_key_exists('error', $updatedProduct)) return $this->json(['message' => $updatedProduct['error']], 400);
            $json = $serializer->serialize($updatedProduct['product'], 'json', ['groups' => ['product_basic']]);
            return $this->json(['product' => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: auth
    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $req): Response
    {
        try {
            $id = $req->get('id');
            $message = $this->productManager->deleteById($id);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'get_one_product', methods: ['GET'])]
    public function getOneProduct(ManagerRegistry $doctrine, int $id, SerializerInterface $serializer): Response
    {
        try {
            //TODO update view count find best solution
            $product = $doctrine->getRepository(Product::class)->findOneByName($id);
            if (!$product) return $this->json(['message' => 'product not found']);
            $json = $serializer->serialize($product, 'json', ['groups' => ['product_basic']]);
            return $this->json(['product' => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: auth
    #[Route('/add-feature', name: 'add_feature', methods: ['PATCH'])]
    public function addFeature(Request $req): Response
    {
        try {
            [$id, $features] = $this->productManager->getRequestBody($req);
            $message = $this->productManager->addFeature($id, $features);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => 'feature added']);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: auth
    #[Route('/remove-feature', name: 'remove_feature', methods: ['PATCH'])]
    public function removeFeature(Request $req): Response
    {
        try {
            [$id, $features] = $this->productManager->getRequestBody($req);
            $message = $this->productManager->removeFeature($id, $features);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => 'feature removed']);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: auth
    #[Route('/toggle-activity', name: 'toggle_activity', methods: ['PATCH'])]
    public function toggleActivity(Request $req): Response
    {
        try {
            [$id, $active] = $this->productManager->getRequestBody($req);
            $message = $this->productManager->toggleActivity($id, $active);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/brand/{id}', name: 'brand_products', methods: ['GET'])]
    public function getBrandProducts(Request $req): Response
    {
        try {
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/category/{id}', name: 'category_products', methods: ['GET'])]
    public function getCategoryProducts(Request $req): Response
    {
        try {
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
