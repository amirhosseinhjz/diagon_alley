<?php

namespace App\Controller\Feature;

use App\Service\FeatureService\FeatureManagement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route("/api/feature")]
class FeatureController extends AbstractController
{
    #[Route('/define', name: 'app_feature_label_define', methods:['POST'])]
    public function define(Request $request , FeatureManagement $featureManagement)
    {
        try {
            $body = $request->toArray();
            $featureManagement->addLabelsToDB($body['features']);
            return $this->json(
                ["message" => "Features have been added!"],
                status: 200
            );
        }catch (\Exception $e){
            return $this->json($e->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/read/{id}', name: 'app_feature_label_read', methods:['GET'])]
    public function read(FeatureManagement $featureManagement, $id){
        try {
            $temp = $featureManagement->readFeatureLabel($id);
            return $this->json(
                $temp,
                status: 200,
                context: [AbstractNormalizer::GROUPS => 'showFeature']
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/update/{id}', name: 'app_feature_label_update', methods:['POST'])]
    public function update(Request $request , FeatureManagement $featureManagement, $id){
        $body = $request->toArray();
        try {
            $temp = $featureManagement->updateFeatureLabel($id,$body);
            return $this->json(
                $temp,
                status: 200,
                context: [AbstractNormalizer::GROUPS => 'showFeature']
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/delete/{id}', name: 'app_feature_label_delete', methods:['GET'])]
    public function delete(FeatureManagement $featureManagement, $id){
        try{
            $featureManagement->deleteFeatureLabel($id);
            return $this->json(
                ["message" => "Feature have been deleted"],
                status: 200
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/show', name: 'app_feature_label_show', methods:['GET'])]
    public function show(FeatureManagement $featureManagement){
        return $this->json(
            $featureManagement->showFeatureLabel(),
            status: 200,
            context: [AbstractNormalizer::GROUPS => 'showFeature']
        );
    }
}