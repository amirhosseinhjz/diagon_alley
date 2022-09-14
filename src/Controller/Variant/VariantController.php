<?php

namespace App\Controller\Variant;

use App\Entity\Product\Product;
use App\Entity\User\User;
use App\Entity\Variant\Variant as VariantEntity;
use App\Utils\Swagger\Variant\Variant;
use App\Interface\Authentication\JWTManagementInterface;
use App\Interface\Feature\FeatureValueManagementInterface;
use App\Interface\Variant\VariantManagementInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
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
    #[OA\Response(
        response: 200,
        description: 'Returns Variant',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: VariantEntity::class, groups: ['showVariant']))
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\RequestBody(
        description: "Define New Variant",
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Variant::class)
        )
    )]
    #[OA\Tag(name: 'Variant')]
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

    #[Route('/{serial}/validation', name: 'serial_denied', methods: ['DELETE'])]
    #[IsGranted('VARIANT_DENY' , message: 'ONLY ADMIN CAN ACCESS')]
    #[OA\Response(
        response: 200,
        description: 'Delete Denied Variant',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Variant')]
    public function denied($serial){
        $this->variantManagement->deleteVariant($serial);
        return $this->json(
            ["message" => "Variant denied successfully"]
        );
    }

    #[Route('/{serial}/validation', name: 'serial_confirm', methods: ['GET'])]
    #[IsGranted('VARIANT_CONFIRM' , message: 'ONLY ADMIN CAN ACCESS')]
    #[OA\Response(
        response: 200,
        description: 'Returns success message on variant confirmation',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Variant')]
    public function confirmCreate($serial): Response
    {
        $this->variantManagement->confirmVariant($serial);
        return $this->json(
            ["message" => "Variant confirmed successfully"],
        );
    }

    #[Route('/{valid}', name: 'show', requirements: ['valid' => '[0,1]'], defaults: ['valid' => 1], methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: '1 := Returns all valid variant information
                      0 := Returns all pending variant information', 
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: VariantEntity::class, groups: ['showVariant']))
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Variant')]
    /**
     * @var User $seller
     */
    public function show(bool $valid): Response
    {
        if(!$valid)$this->denyAccessUnlessGranted('VARIANT_SHOW', subject: $valid , message: 'Access Denied For Customers');
        $seller = $this->JWTManager->authenticatedUser();
        if($seller && array_search('ROLE_SELLER',$seller->getRoles()) !== false && !$valid) {
            $variants = $this->variantManagement->findInValidVariantsBySeller($seller->getId());
        }
        else $variants = $this->variantManagement->findVariantsByValidation($valid);
        return $this->json(
            $variants,
            context:[AbstractNormalizer::GROUPS => 'showVariant']
        );
    }

    #[Route('/{serial}', name: 'read', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns variant information',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: VariantEntity::class, groups: ['showVariant']))
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Variant')]
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
    #[OA\Response(
        response: 200,
        description: 'Returns success message on updating variant',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\RequestBody(
        description: "Set new price and quantity",
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: VariantEntity::class , groups: ['VariantOAUpdate'])
        )
    )]
    #[OA\Tag(name: 'Variant')]
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
    #[OA\Response(
        response: 200,
        description: 'Delete Variant (set quantity to zero)',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Variant')]
    public function delete($serial){
        try {
            $this->denyAccessUnlessGranted('VARIANT_UPDATE',subject: $this->variantManagement->readVariant($serial),message: 'You are not the owner of this variant');
            $this->variantManagement->updateVariant($serial, 0,1);
            return $this->json(
                ["message" => "Variant deleted successfully"],
                status: 200
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/show/{productId}', name: 'show_product_variants', methods: ['GET'])]
    #[OA\RequestBody(
        description: "Show variants of product",
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns Variant',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: VariantEntity::class, groups: ['showVariant']))
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Variant')]
    public function showVariantOfProduct(int $productId){
        $variants = $this->variantManagement->findVariantsByProduct($productId);
        return $this->json(
            $variants,
            status: 200,
            context: [AbstractNormalizer::GROUPS => 'showVariant']
        );
    }
}
