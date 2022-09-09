<?php

namespace App\Tests\Payment2;

use App\DTO\Payment2\PaymentDTO;
use App\Service\CartService\CartServiceInterface;
use App\Service\Payment2\SamanPortalService;
use App\Entity\Cart\Cart;
use App\Factory\Payment2\PortalFactory;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Tests\Base\BaseJsonApiTestCase;


/**
 * @group payment
 */
class PaymentProcessTest extends BaseJsonApiTestCase
{
    /**
     * @return string
     */
    public function testGetToken()
    {
        $cart = new Cart();
        $validator = $this->getValidator();
        $paymentDto = new PaymentDTO($cart, "12000", "Saman", $validator);
        $cartManager = $this->getCartManager();

        $samanPortal = new SamanPortalService($cartManager);
        $samanPortal->setInitial();

        $data = $samanPortal->getToken($paymentDto);
        self::assertNotEquals($data, "");
    }

    /**
     * @return string
     */
    public function testPortalFactory()
    {
        $cartManager = $this->getCartManager();
        $portalService = PortalFactory::create("Saman", $cartManager);

        self::assertInstanceOf(SamanPortalService::class, $portalService);
    }

    public function getCartManager(): \PHPUnit\Framework\MockObject\MockObject|CartServiceInterface
    {
        return $this->getMockBuilder(
            CartServiceInterface::class
        )->getMockForAbstractClass();
    }

    public function getValidator(): \PHPUnit\Framework\MockObject\MockObject|ValidatorInterface
    {
        return $this->getMockBuilder(
            ValidatorInterface::class
        )->getMockForAbstractClass();
    }
}
