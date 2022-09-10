<?php

namespace App\Controller\Variant;

use App\Entity\Variant\Variant;
use App\Interface\Feature\FeatureValueManagementInterface;
use App\Interface\Variant\VariantManagementInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api/variant")]
class VariantController extends AbstractController
{
    private FeatureValueManagementInterface $featureValueManagement;
    private VariantManagementInterface $variantManagement;

    public function __construct(FeatureValueManagementInterface $featureValueManagement , VariantManagementInterface $variantManagement)
    {
        $this->featureValueManagement = $featureValueManagement;
        $this->variantManagement = $variantManagement;
    }

    #[Route('/create', name: 'app_variant_create', methods: ['POST'])]
    public function create(Request $request,ValidatorInterface $validator): Response
    {
        $body = $request->toArray();
        $variantDto = $this->variantManagement->arrayToDTO($body['variant']);
        try{
            $errors = $validator->validate($variantDto);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;
        
                return new Response($errorsString);
            }

            $variant = $this->variantManagement->createVariantFromDTO($variantDto);

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

    #[Route('/create/{serial}/denied', name: 'app_variant_create_serial_denied', methods: ['GET'])]
    public function denied($serial){
        $this->variantManagement->deleteVariant($serial);
        return $this->json(
            ["message" => "Variant denied successfully"],
            status: 200
        );
    }

    #[Route('/create/{serial}/confirm', name: 'app_variant_create_serial_confirm', methods: ['GET'])]
    public function confirmCreate($serial): Response
    {
        $this->variantManagement->confirmVariant($serial);
        return $this->json(
            ["message" => "Variant confirmed successfully"],
            status: 200
        );
    }

    #[Route('/read/{serial}', name: 'app_variant_read', methods: ['GET'])]
    public function read($serial):Response
    {
        try {
            $variant = $this->variantManagement->readVariant($serial);
            return $this->json(
                $variant,
                status: 200,
                context: [AbstractNormalizer::GROUPS => 'showVariant']
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/update/{serial}', name: 'app_variant_update', methods: ['POST'])]
    public function update($serial, Request $request): Response
    {
        $body = $request->toArray();
        try {
            $this->variantManagement->updateVariant($serial,$body['quantity'],$body['price']);
            return $this->json(
                ["message" => "Variant updated successfully"],
                status: 200
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/delete/{serial}', name: 'app_variant_delete', methods: ['GET'])]
    public function delete($serial){
        try {
            $this->variantManagement->updateVariant($serial, 0,2);
            return $this->json(
                ["message" => "Variant deleted successfully"],
                status: 200
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/show', name: 'app_variant_show', methods: ['GET'])]
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

    #[Route('/create', name: 'app_variant_create_request', methods: ['GET'])]
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
