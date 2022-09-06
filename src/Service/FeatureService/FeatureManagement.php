<?php

namespace App\Service\FeatureService;

use App\Entity\Feature\Feature;
use App\Repository\FeatureRepository\FeatureRepository;
use Doctrine\ORM\EntityManagerInterface;

class FeatureManagement
{
    private $em;
    private $featureRepository;
    private $defineFeatureManagement;

    public function __construct(EntityManagerInterface $em , FeatureRepository $featureRepository , FeatureValueManagement $defineFeatureManagement)
    {
        $this->em = $em;
        $this->featureRepository = $featureRepository;
        $this->defineFeatureManagement = $defineFeatureManagement;
    }

    public function addLabelsToDB(array $features){
        foreach($features as $feature){
            if($this->featureRepository->readFeatureById($feature) === null){
                $temp = new Feature();
                $temp->setLabel($feature);
                $temp->setStatus(true);
                $this->featureRepository->add($temp,true);
            }
        }
    }

    public function readFeatureLabel($id){
        if(!$this->featureRepository->find($id) || !$this->featureRepository->find($id)->getStatus()){
            throw new \Exception("Feature not found");
        }
        return $this->featureRepository->find($id);
    }

    public function updateFeatureLabel($id , $body){
        if($body['status'] === null  || !$body['label'])throw new \Exception("Wrong data type");
        $feature = $this->readFeatureLabel($id);
        $feature->setStatus($body['status']);
        $feature->setLabel($body['label']);
        $this->em->flush();
        return $feature;
    }

    public function deleteFeatureLabel($id){
        $feature = $this->readFeatureLabel($id);
        $feature->setStatus(false);
        $this->em->flush();
        return $feature;
    }

    public function showFeatureLabel(){
        return $this->featureRepository->showFeature(array('status' => true));
    }
}