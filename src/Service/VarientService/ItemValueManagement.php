<?php

namespace App\Service\VarientService;

use \App\Repository\ProductItem\ItemValueRepository;
use \App\Repository\ProductItem\ItemFeatureRepository;
use \App\Repository\ProductItem\DefineFeatureRepository;
use \App\Entity\ProductItem\ItemValue;
use \App\Entity\ProductItem\ItemFeature;
use App\Entity\ProductItem\Varient;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\HttpFoundation\Response;

class ItemValueManagement
{
    private $em;
    private $itemValueRepository;
    private $itemFeatureRepository;
    private $defineFeatureRepository;

    public function __construct(EntityManagerInterface $em , ItemValueRepository $itemValueRepository, ItemFeatureRepository $itemFeatureRepository, DefineFeatureRepository $defineFeatureRepository)
    {
        $this->em = $em;
        $this->itemValueRepository = $itemValueRepository;
        $this->itemFeatureRepository = $itemFeatureRepository;
        $this->defineFeatureRepository = $defineFeatureRepository;
    }
    
    public function addItemValueToVarient(array $values,Varient $varient){
        foreach($values as $featureId => $defineFeatureId) {
            $itemValue = new ItemValue();
            $itemFeature = new ItemFeature();

            //TODO
            //have to check is featureId valid?(base on productId)

            //defineFeatureId valid?
            if (count($this->defineFeatureRepository->showFeature(array("id" => $defineFeatureId)))) {
                $temp = $this->defineFeatureRepository->showOneFeature(array("id" => $defineFeatureId));
                if ($temp->getItemFeature()->getId() != $featureId) return Response::HTTP_BAD_REQUEST;
                $itemValue->setValue($temp->getValue());
                $itemValue->setItemFeature($temp->getItemFeature());
            } else {
                $this->em->remove($varient);
                $this->em->flush();
                throw new \Exception("Invalid Item feature value");
            }

            $itemValue->setVarient($varient);
            $this->itemValueRepository->add($itemValue);
            $varient->addItemValue($itemValue);
        }
        $this->em->flush();
        return $varient;
    }

    // public function readItemValueByLabelId(string $lable){
    //     $feature = $this->itemFeatureRepository->findBy(['lable' => $lable]);
    //     if($feature === null)return null;
    //     return $feature[0];
    // }
}