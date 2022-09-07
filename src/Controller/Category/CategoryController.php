<?php

namespace App\Controller\Category;

use App\Interface\Category\CategoryManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/category', name: 'app_category_')]
class CategoryController extends AbstractController
{
    protected CategoryManagerInterface $categoryManager;

    public function __construct(CategoryManagerInterface $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    //TODO: admin auth
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $req): Response
    {
        try {
            $requestBody = $this->categoryManager->getRequestBody($req);
            $categoryArray = $this->categoryManager->normalizeArray($requestBody);
            $category = $this->categoryManager->createEntityFromArray($categoryArray);
            return $this->json(["category" => $category], 201, context: [AbstractNormalizer::GROUPS => ['category_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/', name: 'main_categories', methods: ['GET'])]
    public function getMainCategories(): Response
    {
        try {
            $mainCategories = $this->categoryManager->findMainCategories();
            return $this->json(["mainCategories" => $mainCategories], context: [AbstractNormalizer::GROUPS => ['category_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Request $req, int $id): Response
    {
        try {
            $body = $this->categoryManager->getRequestBody($req);
            $category = $this->categoryManager->findById($id);
            if (!$category) return $this->json(['message' => 'category not found'], 400);
            $updatedCategory = $this->categoryManager->updateEntity($category, $body['updates']);
            return $this->json(['category' => $updatedCategory], context: [AbstractNormalizer::GROUPS => ['category_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $category = $this->categoryManager->findById($id);
            if (!$category) return $this->json(['message' => 'category not found'], 400);
            $message = $this->categoryManager->removeUnused($category);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/{id}/activity', name: 'toggle_activity', methods: ['PATCH'])]
    public function toggleActivity(Request $req, int $id): Response
    {
        try {
            $body = $this->categoryManager->getRequestBody($req);
            $this->categoryManager->toggleActivity($id, $body['active']);
            return $this->json(['message' => 'activity updated']);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/{id}/feature', name: 'add_feature', methods: ['POST'])]
    public function addFeature(Request $req, int $id): Response
    {
        //TODO features could only be added to leaf categories
        try {
            $body = $this->categoryManager->getRequestBody($req);
            $category = $this->categoryManager->findById($id);
            if (!$category) return $this->json(['message' => 'category not found'], 400);
            $message = $this->categoryManager->addFeatures($category, $body['features']);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/{id}/feature', name: 'remove_feature', methods: ['DELETE'])]
    public function removeFeature(Request $req, int $id): Response
    {
        try {
            $body = $this->categoryManager->getRequestBody($req);
            $category = $this->categoryManager->findById($id);
            if (!$category) return $this->json(['message' => 'category not found'], 400);
            $message = $this->categoryManager->removeFeatures($category, $body['features']);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'get_category', methods: ['GET'])]
    public function getCategory(int $id): Response
    {
        try {
            $category = $this->categoryManager->findById($id);
            return $this->json(["category" => $category], context: [AbstractNormalizer::GROUPS => ['category_basic', 'category_children', 'category_parent']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{id}/parents', name: 'get_parents', methods: ['GET'])]
    public function getParents(int $id): Response
    {
        try {
            $parents = $this->categoryManager->findParentsById($id);
            return $this->json(['parents' => $parents], context: [AbstractNormalizer::GROUPS => ['category_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{id}/brands', name: 'get_brands', methods: ['GET'])]
    public function getBrands(int $id): Response
    {
        try {
            $brands = $this->categoryManager->findBrandsById($id);
            return $this->json(["brands" => $brands]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{id}/features', name: 'category_features', methods: ['GET'])]
    public function getFeatures(int $id): Response
    {
        try {
            $features = $this->categoryManager->getFeaturesById($id);
            return $this->json(['message' => $features]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
