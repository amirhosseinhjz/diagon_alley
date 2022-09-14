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

    #[Route('/province', name: 'app_add_province',methods: 'POST')]
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

    #[Route('/province', name: 'app_read_province',methods: 'GET')]
    public function readProvince(
    ): Response {
        try {
            $response = $this->addressService->readProvince();
            return  $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/province/{id}', name: 'app_update_province',methods: 'PATCH')]
    public function updateProvince(
        int $id,
        Request $request
    ): Response {
        try {
            $response = $this->addressService->updateProvince($id,$request->toArray());
            return  $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/province/{id}', name: 'app_delete_province',methods: ['DELETE'])]
    public function deleteProvince(
        int $id
    ): Response {
        try {
            $response = $this->addressService->deleteProvince($id);
            return  $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/city', name: 'app_add_city',methods: ['POST'])]
    public function addCity(
        Request $request,
    ): Response {
        try {
            $response = $this->addressService->createCity($request->toArray());
            return $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/city', name: 'app_read_city',methods: 'GET')]
    public function readCity(
    ): Response {
        try {
            $response = $this->addressService->readCity();
            return  $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/city/{id}', name: 'app_update_city',methods: 'PATCH')]
    public function updateCity(
        int $id,
        Request $request
    ): Response {
        try {
            $response = $this->addressService->updateCity($id,$request->toArray());
            return  $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/', name: 'app_add_address',methods: ['POST'])]
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

    #[Route('/city/{id}', name: 'app_delete_city',methods: ['DELETE'])]
    public function deleteCity(
        int $id
    ): Response {
        try {
            $response = $this->addressService->deleteCity($id);
            return  $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/', name: 'app_read_address',methods: 'GET')]
    public function readAddress(
    ): Response {
        try {
            $response = $this->addressService->readAddress();
            return  $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_update_address',methods: 'PATCH')]
    public function updateAddress(
        int $id,
        Request $request
    ): Response {
        try {
            $response = $this->addressService->updateAddress($id,$request->toArray());
            return  $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_delete_address',methods: ['DELETE'])]
    public function deleteAddress(
        int $id
    ): Response {
        try {
            $response = $this->addressService->deleteAddress($id);
            return  $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
