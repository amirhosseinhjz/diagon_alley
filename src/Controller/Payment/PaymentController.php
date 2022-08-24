<?php

namespace App\Controller\Payment;

use App\Entity\Payment\Payment;
use App\DTO\Payment\PaymentDTO;
use App\Form\Payment\PaymentType;
use App\Repository\Payment\PaymentRepository;
use App\Service\Payment\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/payment')]
class PaymentController extends AbstractController
{
    #[Route('/', name: 'app_payment_new', methods: ['POST'])]
    public function new(
        Request $request,
        ValidatorInterface $validator,
        PaymentService $paymentService
    ) {
        $requestDto = new PaymentDTO($request->toArray(), $validator);

        $payment = $paymentService->new($requestDto);

        return $this->json($payment);
    }

    // #[Route('/', name: 'app_payment_index', methods: ['GET'])]
    // public function index(PaymentRepository $repository)
    // {
    //     //find All
    //     $payments = $repository->findAll();

    //     return $this->json($payments);
    // }

    #[Route('/{id}', name: 'app_payment_check_status', methods: ['GET'])]
    public function checkIndex(PaymentRepository $repository, int $id)
    {
        $status = $repository->checkStatusById($id);

        if (!$status)
            return $this->json("Failed");
        else
            return $this->json($status->getStatus());
    }

    // #[Route('/', name: 'app_payment_payment_index', methods: ['GET'])]
    // public function index(PaymentRepository $paymentRepository): Response
    // {
    //     return $this->render('payment/payment/index.html.twig', [
    //         'payments' => $paymentRepository->findAll(),
    //     ]);
    // }

    // #[Route('/new', name: 'app_payment_payment_new', methods: ['GET', 'POST'])]
    // public function new(Request $request, PaymentRepository $paymentRepository): Response
    // {
    //     $payment = new Payment();
    //     $form = $this->createForm(PaymentType::class, $payment);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $paymentRepository->add($payment, true);

    //         return $this->redirectToRoute('app_payment_payment_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('payment/payment/new.html.twig', [
    //         'payment' => $payment,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_payment_payment_show', methods: ['GET'])]
    // public function show(Payment $payment): Response
    // {
    //     return $this->render('payment/payment/show.html.twig', [
    //         'payment' => $payment,
    //     ]);
    // }

    // #[Route('/{id}/edit', name: 'app_payment_payment_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Payment $payment, PaymentRepository $paymentRepository): Response
    // {
    //     $form = $this->createForm(PaymentType::class, $payment);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $paymentRepository->add($payment, true);

    //         return $this->redirectToRoute('app_payment_payment_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('payment/payment/edit.html.twig', [
    //         'payment' => $payment,
    //         'form' => $form,
    //     ]);
    // }

    // #[Route('/{id}', name: 'app_payment_payment_delete', methods: ['POST'])]
    // public function delete(Request $request, Payment $payment, PaymentRepository $paymentRepository): Response
    // {
    //     if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->request->get('_token'))) {
    //         $paymentRepository->remove($payment, true);
    //     }

    //     return $this->redirectToRoute('app_payment_payment_index', [], Response::HTTP_SEE_OTHER);
    // }
}
