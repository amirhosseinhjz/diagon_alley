<?php

namespace App\Controller\Payment;

use App\Repository\Payment\PaymentRepository;
use App\Service\CartService\CartServiceInterface;
use App\Factory\Payment\PortalFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/payment')]
class PaymentController extends AbstractController
{

    #[Route('/{cartId}/{type}', name: 'app_payment_new', methods: ['GET'])]
    public function new(
        ValidatorInterface $validator,
        PaymentRepository $repository,
        CartServiceInterface $cartService,
        int $cartId,
        string $type
    )
    {
        try {
            $portalService = PortalFactory::create($type, $cartService);

            $requestDto = $portalService->makePaymentDTO($cartId, $type, $validator, $repository);

            $directToPayment = $portalService->payCart($requestDto);

            return $this->render('Payment/payment.html.twig', $directToPayment);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/getStatus', name: 'app_payment_get_status', methods: ['POST'])]
    public function changeStatus(
        Request $request,
        PaymentRepository $repository,
        CartServiceInterface $cartService
    ) {
        try {
            $requestToArray = $request->request->all();

            sscanf($requestToArray['ResNum'], "%d:%s", $requestToArray['ResNum'], $type);

            $portalService = PortalFactory::create($type, $cartService);
            $responce = $portalService->changeStatus($requestToArray, $repository);

            return $this->json($responce);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
