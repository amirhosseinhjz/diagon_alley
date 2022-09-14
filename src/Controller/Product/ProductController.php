<?php

namespace App\Controller\Product;

use App\Interface\Product\ProductManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use OpenApi\Attributes as OA;

#[Route('/api/product', name: 'app_product_')]
#[OA\Tag(name: 'Product')]
class ProductController extends AbstractController
{
    protected ProductManagerInterface $productManager;

    public function __construct(ProductManagerInterface $productManager)
    {
        $this->productManager = $productManager;
    }

    #[IsGranted('PRODUCT_CRUD' , message: 'only admin is allowed')]
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $req): Response
    {
        try {
            $requestBody = $this->productManager->getRequestBody($req);
            $productArray = $this->productManager->normalizeArray($requestBody);
            $product = $this->productManager->createEntityFromArray($productArray);
            return $this->json(['product' => $product], 201, context: [AbstractNormalizer::GROUPS => ['product_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], $exception->getCode());
        }
    }

    #[IsGranted('PRODUCT_CRUD' , message: 'only admin is allowed')]
    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Request $req, int $id): Response
    {
        try {
            $body = $this->productManager->getRequestBody($req);
            $product = $this->productManager->findById($id);
            if (!$product) return $this->json(['message' => 'category not found'], 400);
            $updatedProduct = $this->productManager->updateEntity($product, $body['updates']);
            return $this->json(['product' => $updatedProduct], context: [AbstractNormalizer::GROUPS => ['product_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[IsGranted('PRODUCT_CRUD' , message: 'only admin is allowed')]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $req, int $id): Response
    {
        try {
            $message = $this->productManager->deleteById($id);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[IsGranted('PRODUCT_CRUD' , message: 'only admin is allowed')]
    #[Route('/{id}/feature', name: 'add_feature', methods: ['POST'])]
    public function addFeature(Request $req, int $id): Response
    {
        try {
            $body = $this->productManager->getRequestBody($req);
            $message = $this->productManager->addFeature($id, $body['features']);
            return $this->json(['message' => 'feature added']);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[IsGranted('PRODUCT_CRUD' , message: 'only admin is allowed')]
    #[Route('/{id}/feature', name: 'remove_feature', methods: ['DELETE'])]
    public function removeFeature(Request $req, int $id): Response
    {
        try {
            $body = $this->productManager->getRequestBody($req);
            $message = $this->productManager->removeFeature($id, $body['features']);
            return $this->json(['message' => 'feature removed']);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[IsGranted('PRODUCT_CRUD' , message: 'only admin is allowed')]
    #[Route('/{id}/activity', name: 'toggle_activity', methods: ['PATCH'])]
    public function toggleActivity(Request $req, int $id): Response
    {
        try {
            $body = $this->productManager->getRequestBody($req);
            $message = $this->productManager->toggleActivity($id, $body['active']);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/brand/{id}', name: 'brand_products', methods: ['GET'])]
    public function getBrandProducts(Request $req,int $id): Response
    {
        try {
            $body = $this->productManager->getRequestBody($req);
            $products = $this->productManager->findBrandProducts($id, $body['options']);
            return $this->json(['products' => $products]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/category/{id}', name: 'category_products', methods: ['GET'])]
    public function getCategoryProducts(Request $req, int $id): Response
    {
        try {
            $body = $this->productManager->getRequestBody($req);
            $products = $this->productManager->findCategoryProducts($id, $body['options']);
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
