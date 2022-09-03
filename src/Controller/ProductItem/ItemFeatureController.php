<?php

namespace App\Controller\ProductItem;

use App\Service\VarientService\ItemFeatureManagement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route("/api/feature/label")]
class ItemFeatureController extends AbstractController
{
    #[Route('/define', name: 'app_feature_label_define', methods:['POST'])]
    public function define(Request $request , ItemFeatureManagement $itemFeatureManagement)
    {
        try {
            $body = $request->toArray();
            $itemFeatureManagement->addLabelsToDB($body['features']);
            return $this->json(
                ["massage" => "Features have been added!"],
                status: 200
            );
        }catch (\Exception $e){
            return $this->json($e->getMessage(),Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/read/{id}', name: 'app_feature_label_read', methods:['GET'])]
    public function read(ItemFeatureManagement $itemFeatureManagement,$id){
        try {
            $temp = $itemFeatureManagement->readFeatureLabel($id);
            return $this->json(
                $temp,
                status: 200,
                context: [AbstractNormalizer::GROUPS => 'showItemFeature']
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/update/{id}', name: 'app_feature_label_update', methods:['POST'])]
    public function update(Request $request ,ItemFeatureManagement $itemFeatureManagement,$id){
        $body = $request->toArray();
        try {
            $temp = $itemFeatureManagement->updateFeatureLabel($id,$body);
            return $this->json(
                $temp,
                status: 200,
                context: [AbstractNormalizer::GROUPS => 'showItemFeature']
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/delete/{id}', name: 'app_feature_label_delete', methods:['GET'])]
    public function delete(ItemFeatureManagement $itemFeatureManagement,$id){
        try{
            $itemFeatureManagement->deleteFeatureLabel($id);
            return $this->json(
                ["massage" => "Feature have been deleted"],
                status: 200
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/show', name: 'app_feature_label_show', methods:['GET'])]
    public function show(ItemFeatureManagement $itemFeatureManagement){
        return $this->json(
            $itemFeatureManagement->showFeatureLabel(),
            status: 200,
            context: [AbstractNormalizer::GROUPS => 'showItemFeature']
        );
    }
}
//return $this->json(
//    $varients,
//    status: 200,
//    context:[AbstractNormalizer::GROUPS => 'showVarient']
//);