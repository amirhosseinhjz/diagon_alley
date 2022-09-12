<?php

namespace App\Service\Discount;

use App\Entity\Discount;
use App\Entity\Order\Purchase;
use App\Entity\User\Customer;
use App\Interface\Discount\DiscountDTO;
use App\Interface\Discount\DiscountServiceInterface;
use App\Interface\Discount\DTO;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class DiscountService implements DiscountServiceInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getRequestBody(Request $request): array
    {
        // TODO: Implement getRequestBody() method.
        return array();
    }

    public function createDiscountFromDTO(DiscountDTO $dto): Discount
    {
        // TODO: Implement createDiscountFromDTO() method.
        return new Discount();
    }

    public function createDiscountFromArray(DiscountDTO $dto): Discount
    {
        // TODO: Implement createDiscountFromArray() method.
        return new DiscountDTO();
    }

    public function createDTOFromDiscount(Discount $discount): DiscountDTO
    {
        // TODO: Implement createDTOFromDiscount() method.
        return new DiscountDTO();
    }

    public function updateDiscountFromDTO(DTO $dto): Discount
    {
        // TODO: Implement updateDiscountFromDTO() method.
        return new DiscountDTO();
    }

    public function updateDiscountFromArray(array $array): Discount
    {
        // TODO: Implement updateDiscountFromArray() method.
        return new DiscountDTO();
    }

    public function getDiscountByCode(string $code):Discount
    {
        // TODO: Implement getDiscountByCode() method.
        return new Discount();
    }

    public function getDiscountById(string $id):Discount
    {
        // TODO: Implement getDiscountById() method.
        return new Discount();
    }

    public function removeDiscountByID(int $id)
    {
        // TODO: Implement removeDiscountByID() method.
    }

    public function applyDiscountToPurchase(Discount $discount, Purchase $purchase): Purchase
    {
        // TODO: Implement applyDiscountToPurchase() method.
        return $purchase;
    }

    public function removeDiscountToPurchase(Discount $discount, Purchase $purchase): Purchase
    {
        // TODO: Implement removeDiscountToPurchase() method.
        return $purchase;
    }

    public function checkApplicability(Discount $discount, Purchase $purchase): bool
    {
        // TODO: Implement checkApplicability() method.
        return true;
    }

    public function toggleActivity(Discount $discount): Discount
    {
        // TODO: Implement toggleActivity() method.
        return $discount;
    }

    public function timesUsed(Discount $discount, Customer $customer = null): int
    {
        // TODO: Implement timesUsed() method.
        return 0;
    }
}