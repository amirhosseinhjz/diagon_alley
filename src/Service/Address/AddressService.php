<?php

namespace App\Service\Address;

use App\Entity\Address;
use App\Trait\AddressTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddressService
{
    use AddressTrait;

    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
    ) {
    }

    public function createProvince(array $array)
    {
        $province = $this->makeProvinceFromArray($array);

        $this->em->persist($province);
        $this->em->flush();

        return ["status"=>200,"message"=>"Province has been added."];
    }

    public function createCity(array $array)
    {
        $province = $this->em->getRepository(Address\AddressProvince::class)->findOneByName($array["province"]);
        if (is_null($province))
            throw (new \Exception("This province is not exist."));

        $array["province"] = $province;
        $city = $this->makeCityFromArray($array);

        $this->em->persist($city);
        $this->em->flush();

        $province->addAddressCity($city);
        return ["status"=>200,"message"=>"City has been added."];
    }

    public function addAddress(array $array)
    {
        $city = $this->em->getRepository(Address\AddressCity::class)->findOneByName($array["city"]);
        if (is_null($city))
            throw (new \Exception("This city is not exist."));
        $array["city"] = $city;
        
        $address = $this->makeAddressFromArray($array);

        $this->em->persist($address);
        $this->em->flush();
        
        $array["user"]->addAddress($address);
        return ["status"=>200,"message"=>"Address has been added."];
    }

    public function makeProvinceFromArray(array $array): Address\AddressProvince
    {
        $province = new Address\AddressProvince();
        foreach ($array as $key => $value) {
            $setterName = 'set' . ucfirst($key);
            $province->$setterName($value);
        }

        $databaseErrors = $this->validate($province);
        if (count($databaseErrors) > 0) {
            throw (new \Exception(json_encode($databaseErrors)));
        }

        return $province;
    }

    public function makeCityFromArray(array $array): Address\AddressCity
    {
        $city = new Address\AddressCity();

        foreach ($array as $key => $value) {
            $setterName = 'set' . ucfirst($key);
            $city->$setterName($value);
        }

        $databaseErrors = $this->validate($city);
        if (count($databaseErrors) > 0) {
            throw (new \Exception(json_encode($databaseErrors)));
        }

        return $city;
    }

    public function makeAddressFromArray(array $array): Address\Address
    {
        $address = new Address\Address();

        foreach ($array as $key => $value) {
            $setterName = 'set' . ucfirst($key);
            $address->$setterName($value);
        }

        $databaseErrors = $this->validate($address);

        if (count($databaseErrors) > 0) {
            throw (new \Exception(json_encode($databaseErrors)));
        }

        return $address;
    }

    private function validate($object): array
    {
        $errors = $this->validator->validate($object);
        $errorsArray = [];
        foreach ($errors as $error) {
            $errorsArray[] = $error->getMessage();
        }
        return $errorsArray;
    }

    public function findDistance(Address\Address $adr1, Address\Address $adr2): int
    {
        return $this->findDistanceOfTwoPoints(
            $adr1->getLat(),
            $adr1->getLng(),
            $adr2->getLat(),
            $adr2->getLng()
        );
    }
}
