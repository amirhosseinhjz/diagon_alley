<?php

namespace App\Service\FeatureService;

use App\Entity\Feature\FeatureValue;
use App\Entity\Variant\Variant;
use App\CacheRepository\FeatureRepository\CacheFeatureValueRepository;
use App\Interface\Feature\FeatureManagementInterface;
use App\Repository\FeatureRepository\FeatureValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Interface\Feature\FeatureValueManagementInterface;

class FeatureValueManagement implements FeatureValueManagementInterface
{
    private $em;
    private $featureValueRepository;
    private $featureManagement;
    private $cacheFeatureValueRepository;

    public function __construct(EntityManagerInterface $em , FeatureValueRepository $featureValueRepository , FeatureManagementInterface $featureManagement , CacheFeatureValueRepository $cacheFeatureValueRepository)
    {
        $this->em = $em;
        $this->featureValueRepository = $featureValueRepository;
        $this->featureManagement = $featureManagement;
        $this->cacheFeatureValueRepository = $cacheFeatureValueRepository;
    }
    
    public function defineFeatureValue($features){
        foreach($features as $feature => $value){
            $itemfeature = $this->featureManagement->readFeatureLabel($feature,false);
            if(!$itemfeature){
                throw new \Exception("Invalid Feature ID");
            }
            $featureValue = new FeatureValue();
            $featureValue->setValue($value);
            $featureValue->setActive(true);
            $featureValue->setFeature($itemfeature);
            $this->featureValueRepository->add($featureValue,true);

            $itemfeature->addFeatureValue($featureValue);
            $this->em->persist($itemfeature);
            $this->em->flush();
        }
        return true;
    }

    public function addFeatureValueToVariant(array $values, Variant $variant){
        $product = $variant->getProduct();
        foreach($values as $featureId => $FeatureValueId) {
            $featureValue = new FeatureValue();

            //FeatureValueId validation
            $featureValue = $this->cacheFeatureValueRepository->findOneBy(array("id" => $FeatureValueId),null,false);
            if ($featureValue) {
                if ($featureValue->getFeature()->getId() != $featureId || !$featureValue->isActive() || !$featureValue->getFeature()->getActive()){
                    throw new \Exception("Invalid Item feature value");
                }
            } else {
                $this->em->remove($variant);
                $this->em->flush();
                throw new \Exception("Invalid Item feature value for this feature with id : {$featureId} ");
            }

            //have to check is featureId valid(base on productId)
            if(!$product->containFeatureValue($featureValue)){
                throw new \Exception("Invalid Item feature value for product");
            }
            
            $variant->addFeatureValue($featureValue);
        }
        $this->em->flush();
        return $variant;
    }

    public function readFeatureValueById($id,$cache = true): FeatureValue{
        $featureValue = $this->cacheFeatureValueRepository->find($id , $cache);
        if(!$featureValue || !$featureValue->isActive()){
            throw new \Exception("Feature value not found");
        }
        return $featureValue;
    }

    public function updateFeatureValue($id, $value){
        $featureValue = $this->readFeatureValueById($id,false);
        if(!array_key_exists('value',$value))throw new \Exception("Invalid data input");
        $featureValue->setValue($value['value']);
        return $this->featureValueRepository->add($featureValue,true);
    }

    public function showFeaturesValue(){
        return $this->cacheFeatureValueRepository->findBy(['active' => 1]);
    }

    public function deleteFeatureValue($id){
        $temp =  $this->readFeatureValueById($id,false)->setActive(false);
        $this->em->flush();
        return $temp;
    }
}