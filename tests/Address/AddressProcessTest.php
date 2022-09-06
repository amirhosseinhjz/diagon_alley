<?php

namespace App\Tests\Payment;

use App\Entity\Address;
use App\Service\Address\AddressService;
use App\Tests\Base\BaseJsonApiTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @group Address
 */
class AddressProcessTest extends BaseJsonApiTestCase
{
    /**
     * @return string
     */
    public function testMakeProvinceFromArray()
    {

        $validator = $this->getValidator();
        $manager = $this->getManager();

        $province = (new AddressService($manager,$validator))->makeProvinceFromArray(["name"=>"Esfahan"]);
        
        self::assertInstanceOf(Address\AddressProvince::class, $province);
    }

    public function testMakeCityFromArray()
    {
        $validator = $this->getValidator();
        $manager = $this->getManager();

        //First,load fixtures
        $city = (new AddressService($manager,$validator))->makeCityFromArray(["name"=>"tehran","province"=>"Tehran"]);
        
        self::assertInstanceOf(Address\AddressProvince::class, $city);
    }

    public function testMakeAddressFromArray()
    {
        $validator = $this->getValidator();
        $manager = $this->getManager();

        $array = [
            "city"=>"Damavand",
            "postCode"=>"1234567890",
            "description"=>"Ghole street",
            "lat" => -34,
            "lng" => 64,
        ];
        
        //First,load fixtures
        $province = (new AddressService($manager,$validator))->makeAddressFromArray($array);
        
        self::assertInstanceOf(Address\AddressProvince::class, $province);
    }
    
    public function getManager(): \PHPUnit\Framework\MockObject\MockObject|EntityManagerInterface
    {
        return $this->getMockBuilder(
            EntityManagerInterface::class
        )->getMockForAbstractClass();
    }
}