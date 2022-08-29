<?php

namespace App\Controller\Payment;

use App\DTO\Payment\PaymentDTO;
use App\Entity\Payment\Payment;
use App\Repository\Payment\PaymentRepository;
use App\Service\Payment\PotalFactory;
use App\Service\Payment\BankPortalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use function Symfony\Component\String\u;

#[Route('/api/payment')]
class PaymentController extends AbstractController
{

    #[Route('/{cartId}/{type}', name: 'app_payment_new', methods: ['GET'])]
    public function new(
        ValidatorInterface $validator,
        PaymentRepository $repository,
        int $cartId,
        string $type,
    ) {
        try 
        {
            $portalService = PotalFactory::create($type);
            
            $requestDto = $portalService->makePaymentDTO($cartId,$type,$validator,$repository);

            $directToPayment = $portalService->payCart($requestDto);

            return $this->render('Payment/payment.html.twig', $directToPayment);
        }
        catch(\Exception $e)
        {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/getStatus', name: 'app_payment_get_status', methods: ['POST'])]
    public function changeStatus(
        Request $request, 
        PaymentRepository $repository)
    {
        try 
        {
            $requestToArray = $request->request->all();

            $type = u($requestToArray['ResNum'])->before('-');
            $requestToArray['ResNum'] = u($requestToArray['ResNum'])->after('-');

            $portalService = PotalFactory::create($type);

            $responce = $portalService->changeStatus($requestToArray,$repository);
            
            return $this->json($responce);
        }
        catch(\Exception $e)
        {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
