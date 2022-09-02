<?php

namespace App\Controller\Category;

use App\Entity\Category\Category;
use App\Service\Category\CategoryManager;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/category', name: 'app_category_')]
class CategoryController extends AbstractController
{
    protected CategoryManager $categoryManager;

    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    #[Route('/main-categories', name: 'main_categories', methods: ['GET'])]
    public function getMainCategories(ManagerRegistry $doctrine, SerializerInterface $serializer): Response
    {
        try {
            $mainCategories = $doctrine->getRepository(Category::class)->findMainCategories();
            $json = $serializer->serialize($mainCategories, 'json', ['groups' => ['category_basic']]);
            return $this->json(["mainCategories" => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'get_category', methods: ['GET'])]
    public function getCategory(ManagerRegistry $doctrine, int $id, SerializerInterface $serializer): Response
    {
        try {
            $category = $doctrine->getRepository(Category::class)->findOneById($id);
            $json = $serializer->serialize($category, 'json', [
                'groups' => ['category_basic', 'category_children', 'category_parent']
            ]);
            return $this->json(["category" => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{id}/parents', name: 'get_parents', methods: ['GET'])]
    public function getParents(int $id): Response
    {
        try {
            //TODO serialize
            $parents = $this->categoryManager->findParentsById($id);
            return $this->json(['parents' => $parents]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{id}/brands', name: 'get_brands', methods: ['GET'])]
    public function getBrands(int $id): Response
    {
        try {
            $brands = $this->categoryManager->findBrandsById($id);
            return $this->json(["category" => $brands]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(Request $req, SerializerInterface $serializer): Response
    {
        try {
            $requestBody = $this->categoryManager->getRequestBody($req);
            $categoryArray = $this->categoryManager->normalizeArray($requestBody);
            $category = $this->categoryManager->createEntityFromArray($categoryArray);
            if (array_key_exists('error', $category)) return $this->json(['message' => $category['error']], 400);
            $json = $serializer->serialize($category, 'json', ["groups" => ["category_basic"]]);
            return $this->json(["category" => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $req): Response
    {
        try {
            $id = $req->get('id');
            $message = $this->categoryManager->removeUnusedById($id);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(Request $req, SerializerInterface $serializer, ManagerRegistry $doctrine): Response
    {
        try {
            [$id, $updates] = $this->categoryManager->getRequestBody($req);
            $category = $doctrine->getRepository(Category::class)->findOneById($id);
            if (!$category) return $this->json(['message' => 'category not found'], 400);
            $updatedCategory = $this->categoryManager->updateEntity($category, $updates);
            if (array_key_exists('error', $updatedCategory)) return $this->json(['message' => $updatedCategory['error']], 400);
            $json = $serializer->serialize($updatedCategory['entity'], 'json', ['groups' => ['category_basic']]);
            return $this->json(['category' => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/toggle-activity', name: 'toggle_activity', methods: ['PATCH'])]
    public function toggleActivity(Request $req): Response
    {
        try {
            //TODO serialize
            [$id, $active] = $this->categoryManager->getRequestBody($req);
            $this->categoryManager->toggleActivity($id, $active);
            return $this->json(['message' => 'activity updated']);
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

    //TODO: admin auth
    #[Route('/add-feature', name: 'add_feature', methods: ['PATCH'])]
    public function addFeature(Request $req, ManagerRegistry $doctrine): Response
    {
        try {
            [$id, $features] = $this->categoryManager->getRequestBody($req);
            $category = $doctrine->getRepository(Category::class)->findOneById($id);
            if (!$category) return $this->json(['message' => 'category not found'], 400);
            $message = $this->categoryManager->addFeatures($category, $features);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/remove-feature', name: 'remove_feature', methods: ['PATCH'])]
    public function removeFeature(Request $req, ManagerRegistry $doctrine): Response
    {
        try {
            [$id, $features] = $this->categoryManager->getRequestBody($req);
            $category = $doctrine->getRepository(Category::class)->findOneById($id);
            if (!$category) return $this->json(['message' => 'category not found'], 400);
            $message = $this->categoryManager->removeFeatures($category, $features);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
