<?php

namespace App\Controller\Feature;

use App\Entity\Feature\Feature as FeatureEntity;
use App\Utils\Swagger\Feature\Feature;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Interface\Feature\FeatureManagementInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;

#[Route("/api/feature" , name: 'app_feature_')]
class FeatureController extends AbstractController
{
    private FeatureManagementInterface $featureManagement;

    public function __construct(FeatureManagementInterface $featureManagement)
    {
        $this->featureManagement = $featureManagement;
    }

    #[Route('', name: 'define', methods:['POST'])]
    #[IsGranted('FEATURE_CREATE' , message: 'ONLY ADMIN CAN ADD FEATURE')]
    #[OA\Response(
        response: 200,
        description: 'Returns success message',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\RequestBody(
        description: "Define Features",
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: Feature::class)
        )
    )]
    #[OA\Tag(name: 'Feature')]
    public function define(Request $request)
    {
        try {
            $body = $request->toArray();
            $this->featureManagement->addLabelsToDB($body['features']);
            return $this->json(
                ["message" => "Features have been added!"],
            );
        }catch (\Exception $e){
            return $this->json($e->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'read', methods:['GET'] , condition: "params['id'] > 0")]
    #[IsGranted('FEATURE_SHOW' , message: 'ONLY ADMIN OR SELLER CAN ACCESS FEATURE')]
    #[OA\Response(
        response: 200,
        description: 'Returns the feature information',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: FeatureEntity::class, groups: ['showFeature']))
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Feature')]
    public function read(int $id){
        try {
            $temp = $this->featureManagement->readFeatureLabel($id);
            return $this->json(
                $temp,
                status: 200,
                context: [AbstractNormalizer::GROUPS => 'showFeature']
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'update' , methods:['PATCH']  , condition: "params['id'] > 0")]
    #[IsGranted('FEATURE_CREATE' , message: 'ONLY ADMIN CAN UPDATE FEATURE')]
    #[OA\Response(
        response: 200,
        description: 'Updates the feature and returns it',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: FeatureEntity::class, groups: ['showFeature']))
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: FeatureEntity::class , groups: ['FeatureOA'])
        )
    )]
    #[OA\Tag(name: 'Feature')]
    public function update(Request $request, int $id){
        $body = $request->toArray();
        try {
            $temp = $this->featureManagement->updateFeatureLabel($id,$body);
            return $this->json(
                $temp,
                context: [AbstractNormalizer::GROUPS => 'showFeature']
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods:['DELETE']  , condition: "params['id'] > 0")]
    #[IsGranted('FEATURE_CREATE' , message: 'ONLY ADMIN CAN DELETE FEATURE')]
    #[OA\Response(
        response: 200,
        description: 'Returns success message on deletion',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Feature')]
    public function delete(int $id){
        try{
            $this->featureManagement->deleteFeatureLabel($id);
            return $this->json(
                ["message" => "Feature have been deleted"],
                status: 200
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('', name: 'show', methods:['GET'])]
    #[IsGranted('FEATURE_SHOW' , message: 'ONLY ADMIN OR SELLER CAN ACCESS FEATURE')]
    #[OA\Response(
        response: 200,
        description: 'Returns all features',
        content: new OA\JsonContent(
        type: 'array',
        items: new OA\Items(ref: new Model(type: FeatureEntity::class, groups: ['showFeature']))
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'Feature')]
    public function show(){
        return $this->json(
            $this->featureManagement->showFeatureLabel(),
            status: 200,
            context: [AbstractNormalizer::GROUPS => 'showFeature']
        );
    }
}