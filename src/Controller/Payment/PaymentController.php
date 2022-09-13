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
use App\Interface\Authentication\JWTManagementInterface;
use App\Interface\Wallet\WalletServiceInterface;

#[Route('/api/payment')]
class PaymentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
        private OrderService $orderService,
    ) {
    }

    #[Route('/walletId', name: 'app_get_wallet_id', methods: ['GET'])]
    public function getWalletId(
        JWTManagementInterface $jwtmanager,
    ) {
        try {
            $user = $jwtmanager->authenticatedUser();
            return $this->json(["id"=>$user->getWallet()->getId()]);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/userId/{walletId}', name: 'app_get_user_id', methods: ['GET'])]
    public function getUserId(
        int $walletId,
        WalletServiceInterface $walletService
    ) {
        try {
            $userId = $walletService->getUserId($walletId);
            return $this->json(["id"=>$userId]);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{walletId}/{type}/{price<\d+>}', name: 'app_charge_wallet', methods: ['GET'])]
    public function chargeWallet(
        int $walletId,
        string $type,
        int $price,
    ): Response {
        try {
            $paymentService = PaymentFactory::create("portal", $this->em, $this->validator,$this->orderService);
            
            $requestDto = $paymentService->dtoFromOrderArray(["wallet" => $walletId, "method" => "portal", "paidAmount"=>$price]);
            $paymentId = $paymentService->entityFromDto($requestDto);
            
            $array["type"] = $type;
            $array["payment"] = $paymentId;

            $response = $paymentService->pay($requestDto, $array);

            return $this->render('Payment/payment.html.twig', $response);
        } catch (\Exception $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{orderId}/{method}/{type?}', name: 'app_payment_new', methods: ['GET'])]
    public function new(
        int $orderId,
        string $method,
        ?string $type,
    ): Response {
        try {
            $paymentService = PaymentFactory::create($method, $this->em, $this->validator,$this->orderService);
            
            $requestDto = $paymentService->dtoFromOrderArray(["purchase" => $orderId, "method" => $method]);
            $paymentId = $paymentService->entityFromDto($requestDto);
            
            if(is_null($type))
                $type="saman";
            $array["type"] = $type;
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
