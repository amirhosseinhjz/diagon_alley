<?php

namespace App\Controller\ProductItem;

use App\Service\VarientService\DefineFeatureManagement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/api/feature/value")]
class DefineFeatureController extends AbstractController
{
    #[Route('/define', name: 'app_define_feature_define', methods:['POST'])]
    public function define(Request $request , DefineFeatureManagement $defineFeatureManagement)
    {
        $body = $request->toArray();
        try {
            $defineFeatureManagement->defineFeature($body);
            return $this->json([
                "massage" => "Feature values have been defiend!",
                "status" => 200
            ]);
        } catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/read/{id}', name: 'app_define_feature_read', methods:['GET'])]
    public function read(DefineFeatureManagement $defineFeatureManagement,$id){
        try {
            $temp = $defineFeatureManagement->readFeatureDefined($id);
            return $this->json([
                "data" => $temp,
                "status" => 200
            ]);
        } catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/update/{id}', name: 'app_define_feature_update', methods:['POST'])]
    public function upd(Request $request ,DefineFeatureManagement $defineFeatureManagement,$id){
        $body = $request->toArray();
        try {
            $defineFeatureManagement->updFeatureDefined($id,$body);
            return $this->json([
                "massage" => "Feature Value updated successfully",
                "status" => 200
            ]);
        }
        catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/delete/{id}', name: 'app_define_feature_delete', methods:['GET'])]
    public function delete(DefineFeatureManagement $defineFeatureManagement,$id){
        try {
            $defineFeatureManagement->deleteFeatureDefined($id);
            return $this->json([
                "massage" => "Feature Value deleted successfully",
                "status" => 200
            ]);
        }catch (\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/show', name: 'app_define_features_show', methods:['GET'])]
    public function show(DefineFeatureManagement $defineFeatureManagement){
        $temp = $defineFeatureManagement->showFeaturesDefined();
        return $this->json($temp,context:['groups' => 'show']);
    }
}
