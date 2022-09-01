<?php

namespace App\Controller\Variant;

use App\Entity\Variant\Variant;
use App\Repository\VariantRepository\VariantRepository;
use App\Service\FeatureService\ItemValueManagement;
use App\Service\VariantService\VariantManagement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route("/api/variant")]
class VariantController extends AbstractController
{
    #[Route('/create', name: 'app_variant_create', methods: ['POST'])]
    public function create(Request $request, VariantManagement $variantManager, ItemValueManagement $itemValueManagement , ValidatorInterface $validator): Response
    {
        $body = $request->toArray();
        $variantDto = $variantManager->arrayToDTO($body['variant']);
        try{
            $errors = $validator->validate($variantDto);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;
        
                return new Response($errorsString);
            }

            $variant = $variantManager->createVariantFromDTO($variantDto);

            $variant = $itemValueManagement->addItemValueToVariant($body['feature'],$variant);
            
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
    public function denied($serial, VariantRepository $variantRepository , VariantManagement $variantManager){
        $variantManager->deleteVariant($serial,$variantRepository);
        return $this->json(
            ["message" => "Variant denied successfully"],
            status: 200
        );
    }

    #[Route('/create/{serial}/confirm', name: 'app_variant_create_serial_confirm', methods: ['GET'])]
    public function confirmCreate($serial, VariantRepository $variantRepository, VariantManagement $variantManager): Response
    {
        $variantManager->confirmVariant($serial,$variantRepository);
        return $this->json(
            ["message" => "Variant confirmed successfully"],
            status: 200
        );
    }

    #[Route('/read/{serial}', name: 'app_variant_read', methods: ['GET'])]
    public function read($serial, VariantRepository $variantRepository, VariantManagement $variantManager):Response
    {
        try {
            $variant = $variantManager->readVariant($serial,$variantRepository);
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
    public function update($serial, Request $request, VariantRepository $variantRepository , VariantManagement $variantManager): Response
    {
        $body = $request->toArray();
        try {
            $variantManager->updateVariant($serial,$body['quantity'],$body['price'],$variantRepository);
            return $this->json(
                ["message" => "Variant updated successfully"],
                status: 200
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/delete/{serial}', name: 'app_variant_delete', methods: ['GET'])]
    public function delete($serial, VariantRepository $variantRepository , VariantManagement $variantManager){
        try {
            $variantManager->updateVariant($serial, 0,2, $variantRepository);
            return $this->json(
                ["message" => "Variant deleted successfully"],
                status: 200
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
    #[Route('/show', name: 'app_variant_show', methods: ['GET'])]
    public function show(VariantRepository $variantRepository): Response
    {
        $filters_eq = array("status" => Variant::STATUS_VALIDATE_SUCCESS);
        $filters_gt = array("quantity" => 0);
        $variants = $variantRepository->showVariant($filters_eq,$filters_gt);
        return $this->json(
            $variants,
            status: 200,
            context:[AbstractNormalizer::GROUPS => 'showVariant']
        );
    }

    #[Route('/create', name: 'app_variant_create_request', methods: ['GET'])]
    public function createRequest(VariantRepository $variantRepository): Response
    {
        $filters_eq = array(Variant::STATUS_VALIDATE_PENDING);
        $variants = $variantRepository->showVariant($filters_eq,array());
        return $this->json(
            $variants,
            status: 200,
            context:[AbstractNormalizer::GROUPS => 'showVariant']
        );
    }
}
