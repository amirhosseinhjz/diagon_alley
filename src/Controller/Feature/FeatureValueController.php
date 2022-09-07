<?php

namespace App\Controller\Feature;

use App\Interface\Feature\FeatureValueManagementInterface;
use App\Service\FeatureService\FeatureValueManagement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use OpenApi\Attributes as OA;

#[Route("/api/feature/value" , name: 'app_feature_value_')]
class FeatureValueController extends AbstractController
{
    private FeatureValueManagementInterface $featureValueManagement;

    public function __construct(FeatureValueManagementInterface $featureValueManagement)
    {
        $this->featureValueManagement = $featureValueManagement;
    }

    #[Route('', name: 'create', methods:['POST'])]
    #[IsGranted('FEATURE_VALUE_CREATE' , message: 'ONLY ADMIN CAN ADD FEATURE VALUE')]
    public function define(Request $request)
    {
        $body = $request->toArray();
        try {
            $this->featureValueManagement->defineFeatureValue($body);
            return $this->json(
                ["message" => "Feature values have been defined!"],
            );
        } catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'read', methods:['GET'] , condition: "params['id'] > 0")]
    #[IsGranted('FEATURE_VALUE_SHOW' , message: 'ONLY ADMIN OR SELLER CAN ACCESS FEATURE VALUE')]
    public function read(int $id){
        try {
            $temp = $this->featureValueManagement->readFeatureValueById($id);
            return $this->json(
                $temp,
                context:[AbstractNormalizer::GROUPS => 'showFeatureValue']
            );
        } catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'update', methods:['PATCH'] , condition: "params['id'] > 0")]
    #[IsGranted('FEATURE_VALUE_CREATE' , message: 'ONLY ADMIN CAN UPDATE FEATURE VALUE')]
    public function update(Request $request , int $id){
        $body = $request->toArray();
        try {
            $this->featureValueManagement->updateFeatureValue($id,$body);
            return $this->json(
                ["message" => "Feature Value updated successfully"],
            );
        }
        catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'delete', methods:['DELETE'] , condition: "params['id'] > 0")]
    #[IsGranted('FEATURE_VALUE_CREATE' , message: 'ONLY ADMIN CAN DELETE FEATURE VALUE')]
    public function delete(int $id){
        try {
            $this->featureValueManagement->deleteFeatureValue($id);
            return $this->json(
                ["message" => "Feature Value deleted successfully"],
            );
        }catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('', name: 'show', methods:['GET'])]
    #[IsGranted('FEATURE_VALUE_SHOW' , message: 'ONLY ADMIN OR SELLER CAN ACCESS FEATURE VALUE')]
    public function show(){
        $temp = $this->featureValueManagement->showFeaturesValue();
        return $this->json(
            $temp,
            context:[AbstractNormalizer::GROUPS => 'showFeatureValue']
        );
    }
}
