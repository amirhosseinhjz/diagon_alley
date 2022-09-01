<?php

namespace App\Service\FeatureService;

use App\Entity\Feature\Feature;
use App\Entity\Feature\ItemValue;
use App\Entity\Variant\Variant;
use App\Repository\FeatureRepository\DefineFeatureRepository;
use App\Repository\FeatureRepository\FeatureRepository;
use App\Repository\FeatureRepository\ItemValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ItemValueManagement
{
    private $em;
    private $itemValueRepository;
    private $featureRepository;
    private $defineFeatureRepository;

    public function __construct(EntityManagerInterface $em , ItemValueRepository $itemValueRepository, FeatureRepository $featureRepository, DefineFeatureRepository $defineFeatureRepository)
    {
        $this->em = $em;
        $this->itemValueRepository = $itemValueRepository;
        $this->featureRepository = $featureRepository;
        $this->defineFeatureRepository = $defineFeatureRepository;
    }
    
    public function addItemValueToVariant(array $values, Variant $variant){
        foreach($values as $featureId => $defineFeatureId) {
            $itemValue = new ItemValue();
            $itemFeature = new Feature();

            //TODO
            //have to check is featureId valid?(base on productId)

            //defineFeatureId valid?
            if (count($this->defineFeatureRepository->showFeature(array("id" => $defineFeatureId)))) {
                $temp = $this->defineFeatureRepository->showOneFeature(array("id" => $defineFeatureId));
                if ($temp->getFeature()->getId() != $featureId) return Response::HTTP_BAD_REQUEST;
                $itemValue->setValue($temp->getValue());
                $itemValue->setFeature($temp->getFeature());
            } else {
                $this->em->remove($variant);
                $this->em->flush();
                throw new \Exception("Invalid Item feature value");
            }

            $itemValue->setVariant($variant);
            $this->itemValueRepository->add($itemValue);
            $variant->addItemValue($itemValue);
        }
        $this->em->flush();
        return $variant;
    }
}