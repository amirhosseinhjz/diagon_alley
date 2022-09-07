<?php

namespace App\Controller\Variant;

use App\Entity\Variant\Variant;
use App\Interface\Authentication\JWTManagementInterface;
use App\Interface\Feature\FeatureValueManagementInterface;
use App\Interface\Variant\VariantManagementInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route("/api/variant" , name: 'app_variant_')]
class VariantController extends AbstractController
{
    private FeatureValueManagementInterface $featureValueManagement;
    private VariantManagementInterface $variantManagement;
    private JWTManagementInterface $JWTManager;

    public function __construct(
        FeatureValueManagementInterface $featureValueManagement ,
        VariantManagementInterface $variantManagement,
        JWTManagementInterface $JWTManager
    )
    {
        $this->featureValueManagement = $featureValueManagement;
        $this->variantManagement = $variantManagement;
        $this->JWTManager = $JWTManager;
    }

    #[Route('',name: 'create', methods: ['POST'])]
    #[IsGranted('VARIANT_CREATE' , message: 'ONLY SELLER CAN ADD VARIANT')]
    public function create(Request $request,ValidatorInterface $validator): Response
    {
        $seller = $this->JWTManager->authenticatedUser();
        $body = $request->toArray();
        $variantDto = $this->variantManagement->arrayToDTO($body['variant']);
        try{
            $errors = $validator->validate($variantDto);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;
        
                return new Response($errorsString);
            }

            $variant = $this->variantManagement->createVariantFromDTO($variantDto,$seller);

            $variant = $this->featureValueManagement->addFeatureValueToVariant($body['feature'],$variant);
            
            return $this->json(
                $variant,
                status: 200,
                context: [AbstractNormalizer::GROUPS => 'showVariant']
            );
        }
        catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{serial}/denied', name: 'serial_denied', methods: ['DELETE'])]
    #[IsGranted('VARIANT_DENY' , message: 'ONLY ADMIN CAN ACCESS')]
    public function denied($serial){
        $this->variantManagement->deleteVariant($serial);
        return $this->json(
            ["message" => "Variant denied successfully"]
        );
    }

    #[Route('/{serial}/confirm', name: 'serial_confirm', methods: ['GET'])]
    #[IsGranted('VARIANT_CONFIRM' , message: 'ONLY ADMIN CAN ACCESS')]
    public function confirmCreate($serial): Response
    {
        $this->variantManagement->confirmVariant($serial);
        return $this->json(
            ["message" => "Variant confirmed successfully"],
        );
    }

    #[Route('/{serial}', name: 'read', methods: ['GET'])]
    public function read($serial):Response
    {
        try {
            $variant = $this->variantManagement->readVariant($serial);
            return $this->json(
                $variant,
                context: [AbstractNormalizer::GROUPS => 'showVariant']
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{serial}', name: 'update', methods: ['PATCH'])]
    public function update($serial, Request $request): Response
    {
        $body = $request->toArray();
        try {
            $this->denyAccessUnlessGranted('VARIANT_UPDATE',subject: $this->variantManagement->readVariant($serial),message: 'You are not the owner of this variant');
            $this->variantManagement->updateVariant($serial,$body['quantity'],$body['price']);
            return $this->json(
                ["message" => "Variant updated successfully"]
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{serial}', name: 'delete', methods: ['DELETE'])]
    public function delete($serial){
        try {
            $this->denyAccessUnlessGranted('VARIANT_UPDATE',subject: $this->variantManagement->readVariant($serial),message: 'You are not the owner of this variant');
            $this->variantManagement->updateVariant($serial, 0,2);
            return $this->json(
                ["message" => "Variant deleted successfully"],
                status: 200
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('', name: 'show', methods: ['GET'])]
    public function show(): Response
    {
        $filters_eq = array("status" => Variant::STATUS_VALIDATE_SUCCESS);
        $filters_gt = array("quantity" => 0);
        $variants = $this->variantManagement->showVariant($filters_eq,$filters_gt);
        return $this->json(
            $variants,
            status: 200,
            context:[AbstractNormalizer::GROUPS => 'showVariant']
        );
    }

    #[Route('/request', name: 'request', methods: ['GET'])]
    #[IsGranted('UNVERIFIED_VARIANT_SHOW' , message: 'ONLY ADMIN CAN ACCESS')]
    public function createRequest(): Response
    {
        $filters_eq = array("status" => Variant::STATUS_VALIDATE_PENDING);
        $variants = $this->variantManagement->showVariant($filters_eq,array());
        return $this->json(
            $variants,
            status: 200,
            context:[AbstractNormalizer::GROUPS => 'showVariant']
        );
    }
}
