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

    #[Route('/main', name: 'main', methods: ['GET'])]
    public function main(ManagerRegistry $doctrine, SerializerInterface $serializer): Response
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
                'groups' => ['category_basic', 'category_children']
            ]);
            return $this->json(["category" => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{name}/parents', name: 'show_parents', methods: ['GET'])]
    public function showParents(string $name): Response
    {
        try {
            $parents = $this->categoryManager->findParents($name);
            return $this->json(['parents' => $parents]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{name}/brands', name: 'show_brands', methods: ['GET'])]
    //TODO replace with joins (category <=> products <=> brands )
    public function showBrands(string $name): Response
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
//            $json = $serializer->deserialize($json, Category::class, 'json');
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
            $result = $this->categoryManager->removeUnusedByName($name);
            if (array_key_exists('error', $result)) return $this->json(['message' => $result['error']], 400);
            return $this->json(['message' => $result['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(Request $req, SerializerInterface $serializer): Response
    {
        //TODO change category parent
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
    #[Route('/toggleActivity', name: 'disable', methods: ['PATCH'])]
    public function disable(ManagerRegistry $doctrine, Request $req): Response
    {
        try {
            $name = $req->get('name');
            $active = $req->get('active');
            $this->categoryManager->toggleCategoryActivityByName($name, $active);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/products', name: 'products', methods: ['GET'])]
    public function products(ManagerRegistry $doctrine, Request $req): Response
    {
        try {
            //TODO category-prodcuts
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
