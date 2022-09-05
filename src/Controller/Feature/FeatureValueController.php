<?php

namespace App\Controller\Feature;

use App\Interface\Feature\FeatureValueManagementInterface;
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
    private FeatureValueManagementInterface $featureValueManagement;

    public function __construct(FeatureValueManagementInterface $featureValueManagement)
    {
        $this->featureValueManagement = $featureValueManagement;
    }

    #[Route('/define', name: 'app_define_feature_define', methods:['POST'])]
    public function define(Request $request)
    {
        $body = $request->toArray();
        try {
            $this->featureValueManagement->defineFeatureValue($body);
            return $this->json(
                ["message" => "Feature values have been defined!"],
                status: 200
            );
        } catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/read/{id}', name: 'app_define_feature_read', methods:['GET'])]
    public function read($id){
        try {
            $temp = $this->featureValueManagement->readFeatureValueById($id);
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
    public function update(Request $request , $id){
        $body = $request->toArray();
        try {
            $this->featureValueManagement->updateFeatureValue($id,$body);
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
    public function delete($id){
        try {
            $this->featureValueManagement->deleteFeatureValue($id);
            return $this->json(
                ["message" => "Feature Value deleted successfully"],
                status: 200
            );
        }catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/show', name: 'app_define_features_show', methods:['GET'])]
    public function show(){
        $temp = $this->featureValueManagement->showFeaturesValue();
        return $this->json(
            $temp,
            status: 200,
            context:[AbstractNormalizer::GROUPS => 'showFeatureValue']
        );
    }
}
