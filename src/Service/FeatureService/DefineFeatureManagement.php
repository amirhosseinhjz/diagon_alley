<?php

namespace App\Service\FeatureService;

use App\Entity\Feature\DefineFeature;
use App\Repository\FeatureRepository\DefineFeatureRepository;
use App\Repository\FeatureRepository\FeatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefineFeatureManagement
{
    private $em;
    private $defineFeatureRepository;
    private $featureRepository;
    private $serializer;

    public function __construct(EntityManagerInterface $em , DefineFeatureRepository $defineFeatureRepository , FeatureRepository $featureRepository)
    {
        $this->em = $em;
        $this->defineFeatureRepository = $defineFeatureRepository;
        $this->featureRepository = $featureRepository;
        $this->serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
    }
    
    public function defineFeature($features){
        foreach($features as $feature => $value){
            $itemfeature = $this->featureRepository->readFeatureById($feature);
            if(!$itemfeature){
                throw new \Exception("Invalid Feature ID");
            }
            $definefeature = new DefineFeature();
            $definefeature->setValue($value);
            $definefeature->setStatus(true);
            $definefeature->setFeature($itemfeature);
            $this->defineFeatureRepository->add($definefeature,true);

            $itemfeature->addDefineFeature($definefeature);
            $this->em->persist($itemfeature);
            $this->em->flush();
        }
        return true;
    }

    public function readFeatureDefinedById($id): DefineFeature{
        if(!$this->defineFeatureRepository->find($id)){
            throw new \Exception("Feature value not found");
        }
        return $this->defineFeatureRepository->find($id);
    }

    public function updateFeatureDefined($id, $value){
        $definefeature = $this->readFeatureDefinedById($id);
        $definefeature->setValue($value[$id]);
        return $this->defineFeatureRepository->add($definefeature,true);
    }

    public function showFeaturesDefined(){
        return $this->defineFeatureRepository->showFeature(['status' => 1]);
    }

    public function deleteFeatureDefined($id){
        return $this->readFeatureDefinedById($id)->setStatus(false);
    }
}