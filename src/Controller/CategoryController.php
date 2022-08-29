<?php

namespace App\Controller;

use App\Service\CategoryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Category;
use Exception;

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

    #[Route('/{name}', name: 'show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, string $name, SerializerInterface $serializer): Response
    {
        try {
            $category = $doctrine->getRepository(Category::class)->findOneByName($name);
            $json = $serializer->serialize($category, 'json', [
                'groups' => ['category_basic', 'category_children', 'category_parent']
            ]);
            return $this->json(["category" => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{name}/parents', name: 'get_parents', methods: ['GET'])]
    public function getParents(string $name): Response
    {
        try {
            $parents = $this->categoryManager->findParents($name);
            return $this->json(['parents' => $parents]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{name}/brands', name: 'get_brands', methods: ['GET'])]
    public function getBrands(string $name): Response
    {
        try {
            $brands = $this->categoryManager->findBrandsByName($name);
            return $this->json(["category" => $brands]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $req, SerializerInterface $serializer): Response
    {
        try {
            $requestBody = $this->categoryManager->getRequestBody($req);
            $validatedBody = $this->categoryManager->validateCategoryArray($requestBody);
            if (array_key_exists('error', $validatedBody)) return $this->json(['message' => $validatedBody['error']], 400);
            $category = $this->categoryManager->createEntityFromArray($validatedBody);
            $doctrine->getRepository(Category::class)->add($category, true);
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
            $name = $req->get('name');
            $message = $this->categoryManager->removeUnusedByName($name);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(Request $req, SerializerInterface $serializer): Response
    {
        try {
            [$name, $updates] = $this->categoryManager->getRequestBody($req);
            $updatedCategory = $this->categoryManager->update($name, $updates);
            if (array_key_exists('error', $updatedCategory)) return $this->json(['message' => $updatedCategory['error']], 400);
            $json = $serializer->serialize($updatedCategory, 'json', ['groups' => ['category_basic']]);
            return $this->json(['category' => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/toggle-activity', name: 'disable', methods: ['PATCH'])]
    public function disable(Request $req): Response
    {
        try {
            $name = $req->get('name');
            $active = $req->get('active');
            $this->categoryManager->toggleCategoryActivityByName($name, $active);
            return $this->json(['message' => 'activity updated']);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{name}/features', name: 'category_features', methods: ['GET'])]
    public function getFeatures(string $name): Response
    {
        try {
            $features = $this->categoryManager->getFeaturesByName($name);
            return $this->json(['message' => $features]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/add-feature', name: 'add_feature', methods: ['PATCH'])]
    public function addFeature(Request $req): Response
    {
        try {
            $requestBody = $this->categoryManager->getRequestBody($req);
            $message = $this->categoryManager->addFeatures($requestBody);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/remove-feature', name: 'remove_feature', methods: ['PATCH'])]
    public function removeFeature(Request $req): Response
    {
        try {
            $requestBody = $this->categoryManager->getRequestBody($req);
            $message = $this->categoryManager->removeFeatures($requestBody);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO show brand products with filters
}
