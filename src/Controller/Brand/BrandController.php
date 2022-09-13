<?php

namespace App\Controller\Brand;

use App\Interface\Brand\BrandManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/api/brand', name: 'app_brand_')]
class BrandController extends AbstractController
{
    protected BrandManagerInterface $brandManager;

    public function __construct(BrandManagerInterface $brandManager)
    {
        $this->brandManager = $brandManager;
    }

    #[IsGranted('BRAND_CRUD' , message: 'only admin is allowed')]
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $req): Response
    {
        try {
            $requestBody = $this->brandManager->getRequestBody($req);
            $brandArray = $this->brandManager->normalizeArray($requestBody);
            $brand = $this->brandManager->createEntityFromArray($brandArray);
            return $this->json($brand, 201);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[Route('/', name: 'get_all', methods: ['GET'])]
    public function getAll(): Response
    {
        try {
            $brands = $this->brandManager->findALl();
            return $this->json($brands, context: [AbstractNormalizer::GROUPS => ['brand_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[IsGranted('BRAND_CRUD' , message: 'only admin is allowed')]
    #[Route('/{id}', name: 'update', methods: ['PATCH'])]
    public function update(Request $req, int $id): Response
    {
        try {
            $body = $this->brandManager->getRequestBody($req);
            $brand = $this->brandManager->findById($id);
            if (!$brand) return $this->json(['message' => 'brand not found'], 400);
            $updatedBrand = $this->brandManager->updateEntity($brand, $body['updates']);
            return $this->json($updatedBrand, context: [AbstractNormalizer::GROUPS => ['brand_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }

    #[IsGranted('BRAND_CRUD' , message: 'only admin is allowed')]
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        try {
            $brand = $this->brandManager->findById($id);
            if (!$brand) return $this->json(['message' => 'brand not found'], 400);
            $message = $this->brandManager->removeUnused($brand);
            return $this->json(['message' => $message['message']]);
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
            return $this->json($brands);
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
            return $this->json($brand, context: [AbstractNormalizer::GROUPS => ['brand_basic']]);
        } catch (Exception $exception) {
            return $this->json(['message' => $exception->getMessage()], 500);
        }
    }
}
