<?php

namespace App\Controller\Brand;

use App\Interface\Brand\BrandManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/brand', name: 'app_brand_')]
class BrandController extends AbstractController
{
    protected BrandManagerInterface $brandManager;

    public function __construct(BrandManagerInterface $brandManager)
    {
        $this->brandManager = $brandManager;
    }

    //TODO: admin auth
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $req): Response
    {
        try {
            $requestBody = $this->brandManager->getRequestBody($req);
            $brandArray = $this->brandManager->normalizeArray($requestBody);
            $brand = $this->brandManager->createEntityFromArray($brandArray);
            return $this->json(["brand" => $brand]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    //TODO: admin auth
    #[Route('/', name: 'delete', methods: ['DELETE'])]
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
    #[Route('/', name: 'update', methods: ['PATCH'])]
    public function update(Request $req): Response
    {
        try {
            $body = $this->brandManager->getRequestBody($req);
            $brand = $this->brandManager->findById($body['id']);
            if (!$brand) return $this->json(['message' => 'brand not found'], 400);
            $updatedBrand = $this->brandManager->updateEntity($brand, $body['updates']);
            return $this->json(['brand' => $updatedBrand], context: [AbstractNormalizer::GROUPS => ['brand_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/', name: 'get_all', methods: ['GET'])]
    public function getAll(): Response
    {
        try {
            $brands = $this->brandManager->findALl();
            return $this->json(['brand' => $brands], context: [AbstractNormalizer::GROUPS => ['brand_basic']]);
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
            return $this->json(['brand' => $brand], context: [AbstractNormalizer::GROUPS => ['brand_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
