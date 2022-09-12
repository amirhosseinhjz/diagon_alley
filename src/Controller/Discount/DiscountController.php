<?php

namespace App\Controller\Discount;

use App\Interface\Discount\DiscountServiceInterface;
use http\Env\Request;
use Lcobucci\JWT\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#ToDo: serialization ,
#[Route('/discount', name: 'app_discount_')]
class DiscountController extends AbstractController
{

    private DiscountServiceInterface $discountService;

    public function __construct(DiscountServiceInterface $discountService)
    {
        $this->discountService = $discountService;
    }

    #[Route('/{id}', name: 'read' , methods: ['GET'])]
    public function read($id): Response  #ToDo: should I use the code instead?
    {
        try {
            $discount = $this->discountService->getDiscountById($id);;
            if($discount === null){
                return $this->json([
                    'm' => 'Discount not found.'
                ],Response::HTTP_NOT_FOUND);
            }
            return $this->json([
                'result'=> $this->discountService->createDTOFromDiscount($discount)
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json( [
                'm' => $exception->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    #[Route('/create', name: 'create' , methods: ['POST'])]
    public function create(Request $request): Response #ToDo: check
    {
        try {
            $array = $this->discountService->getRequestBody($request);
            $discount = $this->discountService->createDiscountFromArray($array);
            return $this->json([
                'm' => 'Discount created.'
            ], Response::HTTP_CREATED);
        } catch (Exception $exception) {
            return $this->json( [
                'm' => $exception->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/update', name: 'update' , methods: ['POST'])]
    public function update(Request $request): Response
    {
        try { #ToDo: handle notfound exception
            $array = $this->discountService->getRequestBody($request);
            $updatedDiscount = $this->discountService->updateDiscountFromArray($array);
            return $this->json([
                'm' => 'Discount updated successfully.'
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json( [
                'm' => $exception->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #ToDo: change method to delete
    #[Route('/{id}/delete', name: 'delete' , methods: ['GET'])]
    public function delete($id): Response
    {
        try {
            $this->discountService->removeDiscountByID($id);
            return $this->json([
                'm' => 'Discount deleted successfully.'
            ], Response::HTTP_OK);
        } catch (Exception $exception) {
            return $this->json( [
                'm' => $exception->getMessage(),
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #ToDo: check discount Codes to be unique between active discounts (only active ones?)
    #ToDo: create advanced filters
}
