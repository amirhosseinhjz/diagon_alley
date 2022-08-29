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
use Symfony\Component\Serializer\SerializerInterface;

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
    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $req): Response
    {
        try {
            $name = $req->get('name');
            $message = $this->brandManager->removeUnusedByName($name);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
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
            $message = $this->brandManager->update($name, $updates);
            if (array_key_exists('error', $message)) return $this->json(['message' => $message['error']], 400);
            return $this->json(['message' => $message['message']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{name}', name: 'brand_details', methods: ['GET'])]
    public function getBrand(ManagerRegistry $doctrine, string $name, SerializerInterface $serializer): Response
    {
        try {
            $brand = $doctrine->getRepository(Brand::class)->findOneByName($name);
            if (!$brand) return $this->json(['message' => 'brand not found'], 400);
            $json = $serializer->serialize($brand, 'json', ['groups' => ['brand_basic']]);
            return $this->json(['brand' => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(ManagerRegistry $doctrine, Request $req): Response
    {
        try {
            $q = $req->query->get('query');
            $brands = $doctrine->getRepository(Brand::class)->findManyByQuery($q);
            return $this->json(['brands' => $brands]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO show brand products with filters
}
