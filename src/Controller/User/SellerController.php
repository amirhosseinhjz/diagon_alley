<?php
//
//
namespace App\Controller\User;

use App\Entity\User\Seller;
use App\Form\SellerType;
use App\Repository\UserRepository\SellerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService\SellerService;

#[Route('/seller')]
class SellerController extends AbstractController
{
}
    //    #[Route('/', name: 'app_seller_index', methods: ['GET'])]
//    public function index(SellerRepository $sellerRepository): Response
//    {
//        $sellers = $sellerRepository->findAll();
//        return $this->json($sellers);
//    }
//
//    #[Route('/new', name: 'app_seller_new', methods: ['GET', 'POST'])]
//    public function new(Request $request, SellerRepository $sellerRepository, SellerService $sellerManager): Response
//    {
//        $body = $request->toArray();
//        $dto = $sellerManager->arrayToDTO($body);
//        $seller = $sellerManager->createSellerFromDTO($dto, true);
//        return $this->json($seller);
//    }
//
////    #[Route('/{id}', name: 'app_seller_show', methods: ['GET'])]
////    public function show(Seller $seller): Response
////    {
////        return $this->render('seller/show.html.twig', [
////            'seller' => $seller,
////        ]);
////    }
////
////    #[Route('/{id}/edit', name: 'app_seller_edit', methods: ['GET', 'POST'])]
////    public function edit(Request $request, Seller $seller, SellerRepository $sellerRepository): Response
////    {
////        $form = $this->createForm(SellerType::class, $seller);
////        $form->handleRequest($request);
////
////        if ($form->isSubmitted() && $form->isValid()) {
////            $sellerRepository->add($seller, true);
////
////            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
////        }
////
////        return $this->renderForm('seller/edit.html.twig', [
////            'seller' => $seller,
////            'form' => $form,
////        ]);
////    }
////
////    #[Route('/{id}', name: 'app_seller_delete', methods: ['POST'])]
////    public function delete(Request $request, Seller $seller, SellerRepository $sellerRepository): Response
////    {
////        if ($this->isCsrfTokenValid('delete' . $seller->getId(), $request->request->get('_token'))) {
////            $sellerRepository->remove($seller, true);
////        }
////
////        return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
////    }
//}
