<?php

namespace App\Controller;

use App\DTO\Transformer\BrandTransformer;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Brand;

#[Route('/brand', name: 'app_brand_')]
class BrandController extends AbstractController
{
    //TODO: admin auth
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $req): Response
    {
        $name = trim($req->get("name"));
        $description = $req->get("description");
        if (!$name) return new Response("invalid name", 400);
        if ($description == null) $description = "";
        $brandRepo = $doctrine->getRepository(Brand::class);
        if ($brandRepo->findOneByName($name)) return new Response('name already exists', 400);
        $brand = new Brand();
        $brand->setName($name);
        $brand->setDescription($description);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($brand);
        $entityManager->flush();
        return $this->json([
            "brand" => BrandTransformer::transformFromEntity($brand)
        ]);
    }

    //TODO: admin auth
    #[Route('/add/category', name: 'add_category', methods: ['PATCH'])]
    public function addCategory(ManagerRegistry $doctrine, Request $req): Response
    {
        $brandName = $req->get("brandName");
        $categoryName = $req->get("categoryName");
        $brand = new Brand();
        $brand->setName($brandName);
//        $brand = $doctrine->getRepository(Brand::class)->findOneByName($brandName);
        $category = $doctrine->getRepository(Category::class)->findOneByName($categoryName);
        $brand->addCategory($category);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($brand);
        $entityManager->flush();
        return $this->json([
            "categories" => $brand->getCategories()
        ]);
    }

    //TODO: admin auth
    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(ManagerRegistry $doctrine, Request $req): Response
    {
        return $this->json([]);
    }

    //TODO: admin auth
    #[Route('/remove/category', name: 'remove_category', methods: ['DELETE'])]
    public function removeCategory(ManagerRegistry $doctrine, Request $req): Response
    {
        return $this->json([]);
    }

    //TODO: admin auth
    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(ManagerRegistry $doctrine, Request $req): Response
    {
        return $this->json([]);
    }

    #[Route('/{name}/description', name: 'description', methods: ['GET'])]
    public function description(ManagerRegistry $doctrine, Request $req, string $name): Response
    {
        $brand = $doctrine->getRepository(Brand::class)->findOneByName($name);
        return $this->json([
            "description" => $brand->getDescription()
        ]);
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(ManagerRegistry $doctrine, Request $req): Response
    {
        return $this->json([]);
    }
}
