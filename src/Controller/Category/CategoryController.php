<?php

namespace App\Controller\Category;

use App\Service\Category\CategoryManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category', name: 'app_category_')]
class CategoryController extends AbstractController
{
    protected CategoryManager $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    //TODO: admin auth
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $req): Response
    {
        try {
            $requestBody = $this->categoryManager->getRequestBody($req);
            $categoryArray = $this->categoryManager->normalizeArray($requestBody);
            $category = $this->categoryManager->createEntityFromArray($categoryArray);
            if (array_key_exists('error', $category)) return $this->json(['message' => $category['error']], 400);
            $json = $this->categoryManager->serialize($category, ["category_basic"]);
            return $this->json(["category" => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(Request $req): Response
    {
        try {
            $body = $this->categoryManager->getRequestBody($req);
            $category = $this->categoryManager->findById($body['id']);
            if (!$category) return $this->json(['message' => 'category not found'], 400);
            $updatedCategory = $this->categoryManager->updateEntity($category, $body['updates']);
            if (array_key_exists('error', $updatedCategory)) return $this->json(['message' => $updatedCategory['error']], 400);
            $json = $this->categoryManager->serialize($category, ["category_basic"]);
            return $this->json(['category' => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $req): Response
    {
        try {
            $body = $this->categoryManager->getRequestBody($req);
            $category = $this->categoryManager->findById($body['id']);
            if (!$category) return $this->json(['message' => 'category not found'], 400);
            $message = $this->categoryManager->removeUnused($category);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/main-categories', name: 'main_categories', methods: ['GET'])]
    public function getMainCategories(): Response
    {
        try {
            $mainCategories = $this->categoryManager->findMainCategories();
            $json = $this->categoryManager->serialize($mainCategories, ['category_basic']);
            return $this->json(["mainCategories" => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/toggle-activity', name: 'toggle_activity', methods: ['PATCH'])]
    public function toggleActivity(Request $req): Response
    {
        try {
            $body = $this->categoryManager->getRequestBody($req);
            $this->categoryManager->toggleActivity($body['id'], $body['active']);
            return $this->json(['message' => 'activity updated']);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/add-feature', name: 'add_feature', methods: ['PATCH'])]
    public function addFeature(Request $req): Response
    {
        try {
            $body = $this->categoryManager->getRequestBody($req);
            $category = $this->categoryManager->findById($body['id']);
            if (!$category) return $this->json(['message' => 'category not found'], 400);
            $message = $this->categoryManager->addFeatures($category, $body['features']);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/remove-feature', name: 'remove_feature', methods: ['PATCH'])]
    public function removeFeature(Request $req): Response
    {
        try {
            $body = $this->categoryManager->getRequestBody($req);
            $category = $this->categoryManager->findById($body['id']);
            if (!$category) return $this->json(['message' => 'category not found'], 400);
            $message = $this->categoryManager->removeFeatures($category, $body['features']);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
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
            $json = $this->categoryManager->serialize($category, ['category_basic', 'category_children', 'category_parent']);
            return $this->json(["category" => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{id}/parents', name: 'get_parents', methods: ['GET'])]
    public function getParents(int $id): Response
    {
        try {
            $parents = $this->categoryManager->findParentsById($id);
            $json = $this->categoryManager->serialize($parents, ['category_basic']);
            return $this->json(['parents' => $json]);
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
