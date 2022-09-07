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
    public function testMakeFromArray()
    {
        $validator = $this->getValidator();
        $manager = $this->getManager();

        $province = (new AddressService($manager,$validator))->makeProvinceFromArray(["name"=>"Esfahan"]);
        self::assertInstanceOf(Address\AddressProvince::class, $province);

        $city = (new AddressService($manager,$validator))->makeCityFromArray(["name"=>"tehran","province"=>$province]);
        self::assertInstanceOf(Address\AddressCity::class, $city);

        $array = [
            "city"=>$city,
            "postCode"=>"1234567890",
            "description"=>"infinitive street",
            "lat" => -34,
            "lng" => 64,
        ];
        $province = (new AddressService($manager,$validator))->makeAddressFromArray($array);
        self::assertInstanceOf(Address\Address::class, $province);
    }

    public function getManager(): \PHPUnit\Framework\MockObject\MockObject|EntityManagerInterface
    {
        return $this->getMockBuilder(
            EntityManagerInterface::class
        )->getMockForAbstractClass();
    }
}