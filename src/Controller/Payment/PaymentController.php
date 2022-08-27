<?php

namespace App\Controller\Payment;

use App\DTO\Payment\PaymentDTO;
use App\Repository\Payment\PaymentRepository;
// use App\Repository\Order\OaymentRepository;
use App\Service\Payment\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/payment')]
class PaymentController extends AbstractController
{
    #[Route('/{id}/{type}', name: 'app_payment_new', methods: ['POST'])]
    public function new(
        ValidatorInterface $validator,
        PaymentRepository $repository,
        // OrderRepository $orderRepository
        // int $id,
        string $type,
        ) {
        // $order = orderRepository->findById($id);
        //for testing
        $order = array("price" => 1000, "discount" => 3);
        // $type = "SAMAN";

        $paymentService = new PaymentService($repository,$type);

        // $requestDto = new PaymentDTO(json_encode($order), $type, $validator);
        $requestDto = new PaymentDTO($order, $type, $validator);

        $payment = $paymentService->portal->call($requestDto);

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
