<?php

namespace App\Controller\Feature;

use App\Service\FeatureService\FeatureValueManagement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;

#[Route("/api/feature/value")]
class FeatureValueController extends AbstractController
{
    #[Route('/define', name: 'app_define_feature_define', methods:['POST'])]
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
    public function update(Request $request , FeatureValueManagement $defineFeatureManagement, $id){
        $body = $request->toArray();
        try {
            $defineFeatureManagement->updateFeatureValue($id,$body);
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
    public function show(FeatureValueManagement $defineFeatureManagement){
        $temp = $defineFeatureManagement->showFeaturesValue();
        return $this->json(
            $temp,
            status: 200,
            context:[AbstractNormalizer::GROUPS => 'showFeatureValue']
        );
    }
}
