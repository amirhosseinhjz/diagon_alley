<?php

namespace App\Controller\Address;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Address\AddressService;
use Symfony\Component\HttpFoundation\Request;

#[Route('/api/address')]
class AddressController extends AbstractController
{
    public function __construct(
        private AddressService $addressService
    ) {
    }

    #[Route('/add/province', name: 'app_add_province')]
    public function addProvince(
        Request $request,
    ): Response {
        try {
            $response = $this->addressService->createProvince($request->toArray());
            return $response;
        } catch (\Exception $e) {
            return $this->json(json_decode($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/add/city', name: 'app_add_city')]
    public function addCity(
        Request $request,
    ): Response {
        try {
            $response = $this->addressService->createCity($request->toArray());
            return $response;
        } catch (\Exception $e) {
            return $this->json(json_decode($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/add/{userId}', name: 'app_add_address')]
    public function addAddress(
        Request $request,
        int $userId,
    ): Response {
        try {
            $response = $this->addressService->addAddress($request->toArray(), $userId);
            return $response;
        } catch (\Exception $e) {
            return $this->json(json_decode($e->getMessage()), Response::HTTP_BAD_REQUEST);
        }
    }
}
