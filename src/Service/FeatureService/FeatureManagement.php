<?php

namespace App\Service\FeatureService;

use App\Entity\Feature\Feature;
use App\Repository\FeatureRepository\FeatureRepository;
use App\Interface\Feature\FeatureManagementInterface;
use App\CacheRepository\FeatureRepository\CacheFeatureRepository;
use Doctrine\ORM\EntityManagerInterface;

class FeatureManagement implements FeatureManagementInterface
{
    private $em;
    private $featureRepository;
    private $cacheFeatureRepository;

    public function __construct(EntityManagerInterface $em , FeatureRepository $featureRepository , CacheFeatureRepository $cacheFeatureRepository)
    {
        $this->em = $em;
        $this->featureRepository = $featureRepository;
        $this->cacheFeatureRepository = $cacheFeatureRepository;
    }

    public function addLabelsToDB(array $features){
        foreach($features as $feature){
            if($this->cacheFeatureRepository->find($feature) === null){
                $temp = new Feature();
                $temp->setLabel($feature);
                $temp->setActive(true);
                $this->featureRepository->add($temp,true);
            }
        }
    }

    public function readFeatureLabel($id){
        $temp = $this->cacheFeatureRepository->find($id);
        if(!$temp || !$temp->getActive()){
            throw new \Exception("Feature not found");
        }
        return $temp;
    }

    public function updateFeatureLabel($id , $body){
        if($body['active'] === null  || !$body['label'])throw new \Exception("Wrong data type");
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
        return $this->cacheFeatureRepository->findBy(array('active' => true));
    }
}