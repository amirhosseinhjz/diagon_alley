<?php

namespace App\Controller\Brand;

use App\Service\Brand\BrandManager;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function create(Request $req): Response
    {
        try {
            $requestBody = $this->brandManager->getRequestBody($req);
            $brandArray = $this->brandManager->normalizeArray($requestBody);
            $brand = $this->brandManager->createEntityFromArray($brandArray);
            if (array_key_exists('error', $brand)) return $this->json(['message' => $brand['error']], 400);
            return $this->json(["brand" => $brand['entity']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/delete', name: 'delete', methods: ['DELETE'])]
    public function delete(Request $req): Response
    {
        try {
            $body = $this->brandManager->getRequestBody($req);
            $brand = $this->brandManager->findById($body['id']);
            if (!$brand) return $this->json(['message' => 'brand not found'], 400);
            $message = $this->brandManager->removeUnused($brand);
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
            $body = $this->brandManager->getRequestBody($req);
            $brand = $this->brandManager->findById($body['id']);
            if (!$brand) return $this->json(['message' => 'brand not found'], 400);
            $updatedBrand = $this->brandManager->updateEntity($brand, $body['updates']);
            if (array_key_exists('error', $updatedBrand)) return $this->json(['message' => $updatedBrand['error']], 400);
            $json = $this->brandManager->serialize($updatedBrand['entity'], ['brand_basic']);
            return $this->json(['brand' => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/search', name: 'search', methods: ['GET'])]
    public function search(Request $req): Response
    {
        try {
            //TODO serialize
            $query = $req->query->get('query');
            $brands = $this->brandManager->search($query);
            return $this->json(['brands' => $brands]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/{id}', name: 'brand_details', methods: ['GET'])]
    public function getBrand(int $id): Response
    {
        try {
            $brand = $this->brandManager->findById($id);
            if (!$brand) return $this->json(['message' => 'brand not found'], 400);
            $json = $this->brandManager->serialize($brand, ['brand_basic']);
            return $this->json(['brand' => $json]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
