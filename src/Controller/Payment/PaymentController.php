<?php

namespace App\Controller\Payment;

use App\Factory\Payment\PaymentFactory;
use App\Factory\Portal\PortalFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/payment')]
class PaymentController extends AbstractController
{
    #[Route('/{orderId}/{method}', name: 'app_payment_new', methods: ['GET'])]
    public function new(
        Request $request,
        int $orderId,
        string $method,
    ): Response {
        try {
            $paymentService = PaymentFactory::create($method);

            $requestDto = $paymentService->dtoFromOrderArray(["purchase" => $orderId, "method" => $method]);
            $paymentId = $paymentService->entityFromDto($requestDto);

            $array = $request->toArray();
            $array["payment"] = $paymentId;

            $response = $paymentService->pay($requestDto, $array);

            if ($method == "PORTAL")
                return $this->render('Payment/payment.html.twig', $response);
            else
                return $this->json($response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/status', name: 'app_payment_get_status', methods: ['POST'])]
    public function changeStatus(
        Request $request,
    ) {
        try {
            $requestToArray = $request->request->all();

            sscanf($requestToArray['ResNum'], "%d:%s", $requestToArray['ResNum'], $type);

            $portalService = PortalFactory::create($type);
            $responce = $portalService->changeStatus($requestToArray);

            return $this->json($responce);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
