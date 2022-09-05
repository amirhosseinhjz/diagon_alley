<?php

namespace App\Controller\Feature;

use App\Entity\Feature\FeatureValue as FeatureValueEntity;
use App\Service\FeatureService\FeatureValueManagement;
use App\Utils\Swagger\Feature\FeatureValue;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route("/api/feature/value")]
class FeatureValueController extends AbstractController
{
    #[Route('/define', name: 'app_define_feature_define', methods:['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Returns success message',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\RequestBody(
        description: "Define Features Value",
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: FeatureValue::class)
        )
    )]
    #[OA\Tag(name: 'FeatureValue')]
    public function define(Request $request , FeatureValueManagement $defineFeatureManagement)
    {
        $body = $request->toArray();
        try {
            $defineFeatureManagement->defineFeatureValue($body);
            return $this->json(
                ["message" => "Feature values have been defiend!"],
                status: 200
            );
        } catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/read/{id}', name: 'app_define_feature_read', methods:['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns FeatureValue information',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: FeatureValueEntity::class, groups: ['showFeatureValue']))
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'FeatureValue')]
    public function read(FeatureValueManagement $featureValueManagement, $id){
        try {
            $temp = $featureValueManagement->readFeatureValueById($id);
            return $this->json(
                $temp,
                status: 200,
                context:[AbstractNormalizer::GROUPS => 'showFeatureValue']
            );
        } catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/update/{id}', name: 'app_define_feature_update', methods:['POST'])]
    #[OA\Response(
        response: 200,
        description: 'Returns success message on updating',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\RequestBody(
        description: "Update Features Value",
        required: true,
        content: new OA\JsonContent(
            ref: new Model(type: FeatureValueEntity::class, groups: ['FeatureValueOA'])
        )
    )]
    #[OA\Tag(name: 'FeatureValue')]
    public function update(Request $request , FeatureValueManagement $defineFeatureManagement, $id){
        $body = $request->toArray();
        try {
            $defineFeatureManagement->updateFeatureValue($id,$body['value']);
            return $this->json(
                ["message" => "Feature Value updated successfully"],
                status: 200
            );
        }
        catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/delete/{id}', name: 'app_define_feature_delete', methods:['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns success message on deletion',
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'FeatureValue')]
    public function delete(FeatureValueManagement $defineFeatureManagement, $id){
        try {
            $defineFeatureManagement->deleteFeatureValue($id);
            return $this->json(
                ["message" => "Feature Value deleted successfully"],
                status: 200
            );
        }catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/show', name: 'app_define_features_show', methods:['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns All FeatureValue information',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: FeatureValueEntity::class, groups: ['showFeatureValue']))
        ),
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid Request',
    )]
    #[OA\Tag(name: 'FeatureValue')]
    public function show(FeatureValueManagement $defineFeatureManagement){
        $temp = $defineFeatureManagement->showFeaturesValue();
        return $this->json(
            $temp,
            status: 200,
            context:[AbstractNormalizer::GROUPS => 'showFeatureValue']
        );
    }
}
