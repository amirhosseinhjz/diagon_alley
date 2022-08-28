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
    private $portalService;

    #[Route('/{cartId}/{type}', name: 'app_payment_new', methods: ['GET'])]
    public function new(
        ValidatorInterface $validator,
        PaymentRepository $repository,
        int $cartId,
        string $type,
    ) {
        $this->portalService = PotalFactory::create($type);
        
        $requestDto = $this->portalService->makePaymentDTO($cartId,$type,$validator);

        $directToPayment = $this->portalService->payCart($requestDto);

        return $this->render('Payment/payment.html.twig', $directToPayment);
    }

    #[Route('/getStatus', name: 'app_payment_get_status', methods: ['POST'])]
    public function checkStatus(Request $request)
    {
        dd($request);
        $responce = $this->portalService->checkStatus($request->request->all());
        dd($responce);
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
