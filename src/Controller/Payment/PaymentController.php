<?php

namespace App\Controller\Payment;

use App\DTO\Payment\PaymentDTO;
use App\Entity\Payment\Payment;
use App\Repository\Payment\PaymentRepository;
use App\Service\Payment\PotalFactory;
// use App\Repository\Order\OaymentRepository;
use App\Service\Payment\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/payment')]
class PaymentController extends AbstractController
{
    #[Route('/{id}/{type}', name: 'app_payment_new', methods: ['POST'])]
    public function new(
        ValidatorInterface $validator,
        PaymentRepository $repository,
        int $cartId,
        string $type,
    ) {
        $requestDto = new PaymentDTO($cartId, $type, $validator);

        $portalService = PotalFactory::create($type);

        $payment = $portalService->call($requestDto, $repository);

        return $this->json($payment);
    }

    #[Route('/{id}', name: 'app_payment_check_status', methods: ['GET'])]
    public function checkIndex(PaymentRepository $repository, int $id)
    {
        $status = $repository->checkStatusById($id);

        if (!$status)
            return $this->json("Failed");
        else
            return $this->json($status->getStatus());
    }
}
