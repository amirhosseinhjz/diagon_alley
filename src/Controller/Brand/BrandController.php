<?php

namespace App\Controller\Brand;

use App\Entity\Cart\Cart;
use App\Interface\Brand\BrandManagerInterface;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use OpenApi\Attributes as OA;
use App\Utils\Swagger\Brand\Brand as BrandSwagger;
use App\Entity\Brand\Brand;

#[Route('/api/brand', name: 'app_brand_')]
#[OA\Tag(name: 'Brand')]
class BrandController extends AbstractController
{
    protected BrandManagerInterface $brandManager;

    public function __construct(BrandManagerInterface $brandManager)
    {
        $this->brandManager = $brandManager;
    }

    #[IsGranted('BRAND_CRUD' , message: 'only admin is allowed')]
    #[Route('/', name: 'create', methods: ['POST'])]
    #[OA\RequestBody(
        description: "Add a new brand with name and description",
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: BrandSwagger::class)
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Brand created',
    )]
    #[OA\Tag(name: 'Brand')]
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
    #[OA\Response(
        response: 200,
        description: 'List of all brands',
        content: new OA\JsonContent(
            ref: new Model(type: Brand::class, groups: ['brand_basic'])
        ),
    )]
    #[OA\Tag(name: 'Brand')]
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
