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
            return $this->json(['m' => $exception->getMessage()], 500);
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
            return $this->json(['m' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{name}/parents', name: 'show_parents', methods: ['GET'])]
    public function showParents(string $name): Response
    {
        try {
            $parents = $this->categoryManager->findParents($name);
            return $this->json(['parents' => $parents]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{name}/brands', name: 'show_brands', methods: ['GET'])]
    public function showBrands(string $name): Response
    {
        try {
            $brands = $this->categoryManager->findBrandsByName($name);
            return $this->json(["category" => $brands]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $req, SerializerInterface $serializer): Response
    {
        try {
            $requestBody = $this->categoryManager->getRequestBody($req);
            $validatedBody = $this->categoryManager->validateCategoryArray($requestBody);
            if (array_key_exists('error', $validatedBody)) return $this->json(['m' => $validatedBody['error']], 400);
            $category = $this->categoryManager->createEntityFromArray($validatedBody);
            $doctrine->getRepository(Category::class)->add($category, true);
            $json = $serializer->serialize($category, 'json', ["groups" => ["category_basic"]]);
//            $json = $serializer->deserialize($json, Category::class, 'json');
            return $this->json(["category" => $json]);
        } catch (Exception $exception) {
            return $this->json(['m' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, Request $req): Response
    {
        return $this->json([]);
    }

    //TODO: admin auth
    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(ManagerRegistry $doctrine, Request $req): Response
    {
        return $this->json([]);
    }

    //TODO change category parent

    //TODO disable category and all product and children categories
}
