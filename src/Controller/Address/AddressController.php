<?php

namespace App\Controller\Address;

use App\Interface\Authentication\JWTManagementInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Address\AddressService;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;
use App\Utils\Swagger\Address\Address;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;


#[Route('/api/address')]
class AddressController extends AbstractController
{
    private Serializer $serializer;

    public function __construct(
        private AddressService $addressService,
    ) {
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Province has been added.',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Address::class,groups: ['address.pro'])
        )
    )]
    #[OA\Tag(name: 'Address')]
    #[Route('/province', name: 'app_add_province',methods: 'POST')]
    #[IsGranted('ADDRESS_ADMIN' , message: 'ONLY ADMIN CAN ADD PROVINCE')]
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

    #[OA\Tag(name: 'Address')]
    #[Route('/province', name: 'app_read_province',methods: 'GET')]
    public function readProvince(
    ): Response {
        try {
            $response = $this->addressService->readProvince();
            return  $this->json($response,
            context:[AbstractNormalizer::GROUPS => 'province']
        );
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Province has been updated.',
    )]
    #[OA\Response(
        response: 400,
        description: 'This province does not exist',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Address::class,groups: ['address.pro'])
        )
    )]
    #[IsGranted('ADDRESS_ADMIN' , message: 'ONLY ADMIN CAN UPDATE PROVINCE')]
    #[OA\Tag(name: 'Address')]
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

    #[OA\Response(
        response: 200,
        description: 'Province has been deleted.',
    )]
    #[OA\Response(
        response: 400,
        description: 'bad request',
    )]
    #[OA\Tag(name: 'Address')]
    #[IsGranted('ADDRESS_ADMIN' , message: 'ONLY ADMIN CAN UPDATE PROVINCE')]
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

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'City has been added.',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Address::class,groups: ['address.city'])
        )
    )]
    #[OA\Tag(name: 'Address')]
    #[IsGranted('ADDRESS_ADMIN' , message: 'ONLY ADMIN CAN ADD PROVINCE')]
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

    #[OA\Tag(name: 'Address')]
    #[Route('/city', name: 'app_read_city',methods: 'GET')]
    public function readCity(
    ): Response {
        try {
            $response = $this->addressService->readCity();
            return  $this->json($response,
            context:[AbstractNormalizer::GROUPS => 'city']
        );
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'City has been updated.',
    )]
    #[OA\Response(
        response: 400,
        description: 'This city does not exist',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Address::class,groups: ['address.city'])
        )
    )]
    #[IsGranted('ADDRESS_ADMIN' , message: 'ONLY ADMIN CAN UPDATE PROVINCE')]
    #[OA\Tag(name: 'Address')]
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

    #[OA\Response(
        response: 200,
        description: 'City has been deleted.',
    )]
    #[OA\Response(
        response: 400,
        description: 'bad request',
    )]
    #[IsGranted('ADDRESS_ADMIN' , message: 'ONLY ADMIN CAN UPDATE PROVINCE')]
    #[OA\Tag(name: 'Address')]
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
    
    
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Address has been added.',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Address::class,groups: ['address'])
        )
    )]
    #[OA\Tag(name: 'Address')]
    #[Route('/', name: 'app_add_address',methods: ['POST'])]
    public function addAddress(
        Request $request,
        JWTManagementInterface $jwtManager,
    ): Response {
        try {
            $user = $jwtManager->authenticatedUser();
            $array = $request->toArray();
            $array["user"]=$user;
            $response = $this->addressService->addAddress($array);
            return $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[OA\Tag(name: 'Address')]
    #[IsGranted('ADDRESS_ADMIN' , message: 'ONLY ADMIN CAN READ ALL ADDRESS')]
    #[Route('/', name: 'app_read_address',methods: 'GET')]
    public function readAddress(
    ): Response {
        try {
            $response = $this->addressService->readAddress();
            return  $this->json($response,
            context:[AbstractNormalizer::GROUPS => 'address']
        );
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[OA\Tag(name: 'Address')]
    #[Route('/my', name: 'app_read_address_By_User',methods: 'GET')]
    public function readAddressByUser(
        JWTManagementInterface $jwtManager,
    ): Response {
        try {
            $user = $jwtManager->authenticatedUser();
            $response = $this->addressService->readAddressByUser($user);
            return  $this->json($response,
            context:[AbstractNormalizer::GROUPS => 'address']
        );
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Address has been updated.',
    )]
    #[OA\Response(
        response: 400,
        description: 'This Address does not exist',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Address::class,groups: ['address'])
        )
    )]
    #[OA\Tag(name: 'Address')]
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

    #[OA\Response(
        response: 200,
        description: 'Address has been deleted.',
    )]
    #[OA\Response(
        response: 400,
        description: 'bad request',
    )]
    #[OA\Tag(name: 'Address')]
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
