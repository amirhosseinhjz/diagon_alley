<?php

namespace App\Service\VarientService;

use \App\Repository\ProductItem\ItemFeatureRepository;
use \App\Entity\ProductItem\ItemFeature;
use \App\Entity\ProductItem\DefineFeature;
use \App\Service\VarientService\DefineFeatureManagement;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use function PHPUnit\Framework\throwException;

class ItemFeatureManagement
{
    private $em;
    private $itemFeatureRepository;
    private $defineFeatureManagement;

    public function __construct(EntityManagerInterface $em , ItemFeatureRepository $itemFeatureRepository , DefineFeatureManagement $defineFeatureManagement)
    {
        $this->em = $em;
        $this->itemFeatureRepository = $itemFeatureRepository;
        $this->defineFeatureManagement = $defineFeatureManagement;
    }

    public function addLabelsToDB(array $features){
        foreach($features as $feature){
            if($this->itemFeatureRepository->readFeatureById($feature) === null){
                $temp = new ItemFeature();
                $temp->setLabel($feature);
                $temp->setStatus(true);
                $this->itemFeatureRepository->add($temp,true);
            }
        }
    }

    public function readFeatureLabel($id){
        if(!$this->itemFeatureRepository->find($id)){
            throw new \Exception("Feature not found");
        }
        return $this->itemFeatureRepository->find($id);
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
        return $this->itemFeatureRepository->showFeature(array('status' => true));
    }
}