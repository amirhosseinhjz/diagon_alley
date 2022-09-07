<?php

namespace App\Controller\Product;

use App\Interface\Product\ProductManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/product', name: 'app_product_')]
class ProductController extends AbstractController
{
    protected ProductManagerInterface $productManager;

    public function __construct(ProductManagerInterface $productManager)
    {
        $this->productManager = $productManager;
    }

    //TODO: auth
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $req): Response
    {
        try {
            $requestBody = $this->productManager->getRequestBody($req);
            $productArray = $this->productManager->normalizeArray($requestBody);
            $product = $this->productManager->createEntityFromArray($productArray);
            return $this->json(['product' => $product], context: [AbstractNormalizer::GROUPS => ['product_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], $exception->getCode());
        }
    }

    //TODO: auth
    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(Request $req): Response
    {
        try {
            $body = $this->productManager->getRequestBody($req);
            $product = $this->productManager->findById($body['id']);
            if (!$product) return $this->json(['message' => 'category not found'], 400);
            $updatedProduct = $this->productManager->updateEntity($product, $body['updates']);
            return $this->json(['product' => $updatedProduct], context: [AbstractNormalizer::GROUPS => ['product_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: auth
    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $req): Response
    {
        try {
            $body = $this->productManager->getRequestBody($req);
            $message = $this->productManager->deleteById($body['id']);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: auth
    #[Route('/add-feature', name: 'add_feature', methods: ['PATCH'])]
    public function addFeature(Request $req): Response
    {
        try {
            $body = $this->productManager->getRequestBody($req);
            $message = $this->productManager->addFeature($body['id'], $body['features']);
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
            $body = $this->productManager->getRequestBody($req);
            $message = $this->productManager->removeFeature($body['id'], $body['features']);
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
            $body = $this->productManager->getRequestBody($req);
            $message = $this->productManager->toggleActivity($body['id'], $body['active']);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/brand/{id}', name: 'brand_products', methods: ['GET'])]
    public function getBrandProducts(Request $req): Response
    {
        try {
            $body = $this->productManager->getRequestBody($req);
            $products = $this->productManager->findBrandProducts($body['options']);
            return $this->json(['products' => $products]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/category/{id}', name: 'category_products', methods: ['GET'])]
    public function getCategoryProducts(Request $req): Response
    {
        try {
            $body = $this->productManager->getRequestBody($req);
            $products = $this->productManager->findCategoryProducts($body['options']);
            return $this->json(['products' => $products]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'get_one_product', methods: ['GET'])]
    public function getOneProduct(int $id): Response
    {
        try {
            //TODO update view count find best solution
            $product = $this->productManager->findById($id);
            if (!$product) return $this->json(['message' => 'product not found']);
            return $this->json(['product' => $product], context: [AbstractNormalizer::GROUPS => ['product_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
