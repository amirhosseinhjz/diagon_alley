<?php

namespace App\Controller\Payment;

use App\Factory\Payment\PaymentFactory;
use App\Factory\Portal\PortalFactory;
use App\Service\OrderService\OrderService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/payment')]
class PaymentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private OrderService $orderService,
    ) {
    }

    #[Route('/{orderId}/{method}', name: 'app_payment_new', methods: ['POST'])]
    public function new(
        Request $request,
        int $orderId,
        string $method,
    ): Response {
        try {
            $paymentService = PaymentFactory::create($method, $this->em, $this->validator,$this->orderService);
            
            $requestDto = $paymentService->dtoFromOrderArray(["purchase" => $orderId, "method" => $method]);
            $paymentId = $paymentService->entityFromDto($requestDto);
            
            $array = $request->toArray();
            $array["payment"] = $paymentId;

            $response = $paymentService->pay($requestDto, $array);

            if ($method == "portal")
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

            $portalService = PortalFactory::create($type,$this->em,$this->orderService);
            $responce = $portalService->changeStatus($requestToArray);

            return $this->json($responce);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
}
