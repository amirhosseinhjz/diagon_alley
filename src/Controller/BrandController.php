<?php

namespace App\Controller;

use App\Entity\Category;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Brand;
use App\Service\BrandManager;

#[Route('/brand', name: 'app_brand_')]
class BrandController extends AbstractController
{
    protected BrandManager $brandManager;

    public function __construct(BrandManager $brandManager)
    {
        $this->brandManager = $brandManager;
    }

    //TODO: admin auth
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function create(ManagerRegistry $doctrine, Request $req): Response
    {
        try {
            $requestBody = $this->brandManager->getRequestBody($req);
            $validatedBody = $this->brandManager->validateBrandArray($requestBody);
            if (array_key_exists('error', $validatedBody)) return $this->json(['message' => $validatedBody['error']], 400);
            $brand = $this->brandManager->createEntityFromArray($validatedBody);
            $doctrine->getRepository(Brand::class)->add($brand, true);
            return $this->json(["brand" => $brand]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    //TODO remove later
    #[Route('/add/category', name: 'add_category', methods: ['PATCH'])]
    public function addCategory(Request $req): Response
    {
        try {
            [$brandName, $categoryName] = $this->brandManager->getRequestBody($req);
            $this->brandManager->addRelationWithCategory($brandName, $categoryName);
            return $this->json(['message' => "relation added"]);
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
            $this->brandManager->removeUnusedByName($name);
            return $this->json(['message' => 'removed brand']);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/remove/category', name: 'remove_category', methods: ['DELETE'])]
    public function removeCategory(ManagerRegistry $doctrine, Request $req): Response
    {
        try {
            [$brandName, $categoryName] = $this->brandManager->getRequestBody($req);
            $this->brandManager->removeRelationWithCategory($brandName, $categoryName);
            return $this->json(['message' => "relation removed"]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function update(Request $req): Response
    {
        try {
            [$name, $updates] = $this->brandManager->getRequestBody($req);
            $this->brandManager->update($name, $updates);
            return $this->json([]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{name}/description', name: 'description', methods: ['GET'])]
    public function description(ManagerRegistry $doctrine, string $name): Response
    {
        try {
            $brand = $doctrine->getRepository(Brand::class)->findOneByName($name);
            return $this->json([
                "description" => $brand->getDescription()
            ]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(ManagerRegistry $doctrine, Request $req): Response
    {
        try {
            $q = $req->get('query');
            $brands = $doctrine->getRepository(Brand::class)->findManyByQuery($q);
            return $this->json(['brands' => $brands]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/products', name: 'products', methods: ['GET'])]
    public function products(ManagerRegistry $doctrine, Request $req): Response
    {
        try {
            //TODO brand-products dont have feature filters
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
