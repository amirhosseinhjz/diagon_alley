<?php

namespace App\Controller;

use App\Entity\Brand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Category;

#[Route('/category', name: 'app_category_')]
class CategoryController extends AbstractController
{
    #[Route('/main', name: 'main', methods: ['GET'])]
    public function main(ManagerRegistry $doctrine, Request $req): Response
    {
        $mainCategories = $doctrine->getRepository(Category::class)->findMainCategories();

        return $this->json([
            "mainCategories" => $mainCategories
        ]);
    }

    #[Route('/{name}', name: 'show', methods: ['GET'])]
    public function show(ManagerRegistry $doctrine, Request $req, string $name): Response
    {
        $category = $doctrine->getRepository(Category::class)->findOneByName($name);

        return $this->json([
            "category" => $category
        ]);
    }

//    #[Route('/{name}/children', name: 'show_children', methods: ['GET'])]
//    public function showChildren(ManagerRegistry $doctrine, Request $req, string $name): Response
//    {
//        return $this->json([]);
//    }

    #[Route('/{name}/parents', name: 'show_parents', methods: ['GET'])]
    public function showParents(ManagerRegistry $doctrine, Request $req, string $name): Response
    {
        return $this->json([]);
    }

    #[Route('/{name}/brands', name: 'show_brands', methods: ['GET'])]
    public function showBrands(ManagerRegistry $doctrine, Request $req, string $name): Response
    {
        $brands = $doctrine->getRepository(Category::class)->findBrands($name);

        return $this->json([
            "brands" => $brands
        ]);
    }

    //TODO: admin auth
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $req): Response
    {
        $entityManager = $doctrine->getManager();

        $name = trim($req->get("name"));
        $parent = $req->get("parent");
        $isLeaf = $req->get("isLeaf");
        if (!$name) return new Response("invalid name", 400);
        if ($isLeaf == null) $isLeaf = false;
        $categoryRepo = $doctrine->getRepository(Category::class);
        if ($categoryRepo->findOneByName($name)) return new Response('name already exists', 400);

        $category = new Category();
        $category->setName($name);
        $category->setIsLeaf($isLeaf);
        $category->setParent($parent);

        $entityManager->persist($category);
        $entityManager->flush();

        return $this->json([
            "category" => $category
        ]);
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
}
