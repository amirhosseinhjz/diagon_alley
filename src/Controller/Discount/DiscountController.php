<?php

namespace App\Controller\Discount;

use App\Interface\Discount\DiscountServiceInterface;
use Lcobucci\JWT\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/discount', name: 'app_discount_')]
class DiscountController extends AbstractController
{
    #ToDo: voter & access management

    private Serializer $serializer;
    private DiscountServiceInterface $discountService;

    public function __construct(DiscountServiceInterface $discountService)
    {
        $this->discountService = $discountService;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    #[Route('/{id}', name: 'read' , methods: ['GET'])]
    public function read($id): Response
    {
        try {
            $discount = $this->discountService->getDiscountById($id);
            if(!empty($discount)){
                return $this->json([
                    'message' => 'Discount not found.'
                ],Response::HTTP_NOT_FOUND);
            }
            return $this->json(
                $this->serializer->normalize($this->discountService->createDTOFromDiscount($discount)),
                Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json( [
                'message' => $exception->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/create', name: 'create' , methods: ['POST'])]
    public function create(Request $request): Response
    {
        try {
            $array = $this->discountService->getRequestBody($request);
            $discount = $this->discountService->createDiscountFromArray($array);
            return $this->json([
                'message' => 'Discount created.',
                'discount'=> $this->serializer->normalize($this->discountService->createDTOFromDiscount($discount))
            ], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->json( [
                'message' => $exception->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/update', name: 'update' , methods: ['POST'])]
    public function update($id, Request $request): Response
    {
        try {
            $discount = $this->discountService->getDiscountById($id);
            if(empty($discount)){
                return $this->json(['message'=>'Discount does not exist.'], Response::HTTP_NOT_FOUND);
            }
            $dto = $this->discountService->getRequestDTO($request);
            $updatedDiscount = $this->discountService->updateDiscountFromDTO($discount,$dto);
            return $this->json([
                'discount'=> $this->serializer->normalize($this->discountService->createDTOFromDiscount($updatedDiscount)),
                'message' => 'Discount updated successfully.'
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json( [
                'message' => $exception->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/toggle_activity', name: 'toggle activity status' , methods: ['GET'])]
    public function toggleActivity($id): Response
    {
        try{
            $discount = $this->discountService->getDiscountById($id);
            if (empty($discount)){
                return $this->json(
                    ['message' => "the discount does not exist"],
                    Response::HTTP_NOT_FOUND
                );
            }
            $this->discountService->toggleActivity($discount);
            return $this->json( #check in test
                $this->serializer->normalize($this->discountService->createDTOFromDiscount($discount)),
                Response::HTTP_OK
            );
        } catch (Exception $exception) {
            return $this->json( [
                'message' => $exception->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    #ToDo: check discount Codes to be unique between active discounts (only active ones?) when creating codes and activating discounts
    #ToDo: check correctness of relational queries in cart and discount
}
