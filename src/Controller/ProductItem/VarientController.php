<?php

namespace App\Controller\ProductItem;

use App\Entity\ProductItem\Varient;
use App\Repository\ProductItem\VarientRepository;
use App\Service\VarientService\VarientManagement;
use App\Service\VarientService\ItemValueManagement;
use phpDocumentor\Reflection\Types\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("/api/varient")]
class VarientController extends AbstractController
{
    #[Route('/create', name: 'app_varient_create', methods: ['POST'])]
    public function create(Request $request, VarientManagement $varientManager,ItemValueManagement $itemValueManagement , ValidatorInterface $validator): Response
    {
        $body = $request->toArray();
        $varientDto = $varientManager->arrayToDTO($body['varient']);
        try{
            $errors = $validator->validate($varientDto);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;
        
                return new Response($errorsString);
            }

            $varient = $varientManager->createVarientFromDTO($varientDto);

            $varient = $itemValueManagement->addItemValueToVarient($body['feature'],$varient);
            
            return $this->json(
                $varient,
                status: 200,
                context: [AbstractNormalizer::GROUPS => 'showVarient']
            );
        }
        catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/create/{serial}/denied', name: 'app_varient_create_serial_denied', methods: ['GET'])]
    public function denied($serial,VarientRepository $varientRepository ,VarientManagement $varientManager){
        $varientManager->deleteVarient($serial,$varientRepository);
        return $this->json(
            ["massage" => "Varient denied successfully"],
            status: 200
        );
    }

    #[Route('/create/{serial}/confirm', name: 'app_varient_create_serial_confirm', methods: ['GET'])]
    public function confirmCreate($serial,VarientRepository $varientRepository,VarientManagement $varientManager): Response
    {
        $varientManager->confirmVarient($serial,$varientRepository);
        return $this->json(
            ["massage" => "Varient confirmed successfully"],
            status: 200
        );
    }

    #[Route('/read/{serial}', name: 'app_varient_read', methods: ['GET'])]
    public function read($serial,VarientRepository $varientRepository,VarientManagement $varientManager,SerializerInterface $serializer):Response
    {
        try {
            $varient = $varientManager->readVarient($serial,$varientRepository);
            return $this->json(
                $varient,
                status: 200,
                context: [AbstractNormalizer::GROUPS => 'showVarient']
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/update/{serial}', name: 'app_varient_update', methods: ['POST'])]
    public function update($serial,Request $request,VarientRepository $varientRepository ,VarientManagement $varientManager): Response
    {
        $body = $request->toArray();
        try {
            $varientManager->updateVarient($serial,$body['quantity'],$varientRepository);
            return $this->json(
                ["massage" => "Varient updated successfully"],
                status: 200
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/delete/{serial}', name: 'app_varient_delete', methods: ['GET'])]
    public function delete($serial, VarientRepository $varientRepository ,VarientManagement $varientManager){
        try {
            $varientManager->updateVarient($serial, 0, $varientRepository);
            return $this->json(
                ["massage" => "Varient deleted successfully"],
                status: 200
            );
        } catch(\Exception $e){
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }
    #[Route('/show', name: 'app_varient_show', methods: ['GET'])]
    public function show(VarientRepository $varientRepository): Response
    {
        $filters_eq = array(Varient::STATUS_VALIDATE_SUCCESS);
        $filters_gt = array("quantity" => 0);
        $varients = $varientRepository->showVarient($filters_eq,$filters_gt);
        return $this->json(
            $varients,
            status: 200,
            context:[AbstractNormalizer::GROUPS => 'showVarient']
        );
    }

    #[Route('/create', name: 'app_varient_create_request', methods: ['GET'])]
    public function createRequest(VarientRepository $varientRepository): Response
    {
        $filters_eq = array(Varient::STATUS_VALIDATE_PENDING);
        $varients = $varientRepository->showVarient($filters_eq,array());
        return $this->json(
            $varients,
            status: 200,
            context:[AbstractNormalizer::GROUPS => 'showVarient']
        );
    }
}
