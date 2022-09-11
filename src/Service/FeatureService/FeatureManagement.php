<?php

namespace App\Service\FeatureService;

use App\Entity\Feature\Feature;
use App\Repository\FeatureRepository\FeatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Interface\Feature\FeatureManagementInterface;

class FeatureManagement implements FeatureManagementInterface
{
    private $em;
    private $featureRepository;

    public function __construct(EntityManagerInterface $em , FeatureRepository $featureRepository , FeatureValueManagement $defineFeatureManagement)
    {
        $this->em = $em;
        $this->featureRepository = $featureRepository;
    }

    public function addLabelsToDB(array $features){
        foreach($features as $feature){
            if($this->featureRepository->readFeatureById($feature) === null){
                $temp = new Feature();
                $temp->setLabel($feature);
                $temp->setActive(true);
                $this->featureRepository->add($temp,true);
            }
        }
    }

    public function readFeatureLabel($id){
        if(!$this->featureRepository->find($id) || !$this->featureRepository->find($id)->getActive()){
            throw new \Exception("Feature not found");
        }
        return $this->featureRepository->find($id);
    }

    public function updateFeatureLabel($id , $body){
        if($body['status'] === null  || !$body['label'])throw new \Exception("Wrong data type");
        $feature = $this->readFeatureLabel($id);
        $feature->setActive($body['active']);
        $feature->setLabel($body['label']);
        $this->em->flush();
        return $feature;
    }

    public function deleteFeatureLabel($id){
        $feature = $this->readFeatureLabel($id);
        $feature->setActive(false);
        $this->em->flush();
        return $feature;
    }

    public function showFeatureLabel(){
        return $this->featureRepository->showFeature(array('active' => true));
    }
}