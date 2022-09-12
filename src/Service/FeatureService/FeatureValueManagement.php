<?php

namespace App\Service\FeatureService;

use App\Entity\Feature\FeatureValue;
use App\Entity\Variant\Variant;
use App\Repository\FeatureRepository\FeatureValueRepository;
use App\Repository\FeatureRepository\FeatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class FeatureValueManagement
{
    private $em;
    private $featureValueRepository;
    private $featureRepository;

    public function __construct(EntityManagerInterface $em , FeatureValueRepository $featureValueRepository , FeatureRepository $featureRepository)
    {
        $this->em = $em;
        $this->featureValueRepository = $featureValueRepository;
        $this->featureRepository = $featureRepository;
    }
    
    public function defineFeatureValue($features){
        foreach($features as $feature => $value){
            $itemfeature = $this->featureRepository->readFeatureById($feature);
            if(!$itemfeature){
                throw new \Exception("Invalid Feature ID");
            }
            $featureValue = new FeatureValue();
            $featureValue->setValue($value);
            $featureValue->setStatus(true);
            $featureValue->setFeature($itemfeature);
            $this->featureValueRepository->add($featureValue,true);

            $itemfeature->addFeatureValue($featureValue);
            $this->em->persist($itemfeature);
            $this->em->flush();
        }
        return true;
    }

    public function addFeatureValueToVariant(array $values, Variant $variant){
        foreach($values as $featureId => $FeatureValueId) {
            $featureValue = new FeatureValue();

            //TODO
            //have to check is featureId valid?(base on productId)

            //FeatureValueId validation
            if (count($this->featureValueRepository->showFeature(array("id" => $FeatureValueId)))) {
                $temp = $this->featureValueRepository->showOneFeature(array("id" => $FeatureValueId));
                if ($temp->getFeature()->getId() != $featureId || !$temp->isStatus() || !$temp->getFeature()->getStatus()) throw new \Exception("Invalid Item feature value");
                $featureValue = $temp;
            } else {
                $this->em->remove($variant);
                $this->em->flush();
                throw new \Exception("Invalid Item feature value");
            }
            
            $variant->addFeatureValue($featureValue);
        }
        $this->em->flush();
        return $variant;
    }

    public function readFeatureValueById($id): FeatureValue{
        if(!$this->featureValueRepository->find($id) || !$this->featureValueRepository->find($id)->isStatus()){
            throw new \Exception("Feature value not found");
        }
        return $this->featureValueRepository->find($id);
    }

    public function updateFeatureValue($id, $value){
        $featureValue = $this->readFeatureValueById($id);
        $featureValue->setValue($value[$id]);
        return $this->featureValueRepository->add($featureValue,true);
    }

    public function showFeaturesValue(){
        return $this->featureValueRepository->showFeature(['status' => 1]);
    }

    public function deleteFeatureValue($id){
        return $this->readFeatureValueById($id)->setStatus(false);
    }
}