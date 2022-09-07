<?php

namespace App\Controller\Address;

use App\Interface\Authentication\JWTManagementInterface;
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
            return  $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/add/city', name: 'app_add_city')]
    public function addCity(
        Request $request,
    ): Response {
        try {
            $response = $this->addressService->createCity($request->toArray());
            return $this->json($response);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/add', name: 'app_add_address')]
    public function addAddress(
        Request $request,
        JWTManagementInterface $jwtmanager,
    ): Response {
        try {
            $user = $jwtmanager->authenticatedUser();
            $array = $request->toArray();
            $array["user"]=$user;
            $response = $this->addressService->addAddress($array);
            return $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
