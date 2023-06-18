<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;


/**
 * General purpose Error response for uncaught errors.
 */
class ErrorController extends AbstractController
{
    #[Route('/error', name: 'app_error')]
    public function show($exception,Request $request,LoggerInterface $logger): JsonResponse
    {           
        $logger->error('Type Exception occured in EquipmentController::equipmentAdd '.$exception->getMessage());

        return $this->json([
            'messageErrorController' => "an error occurred in the server"      
        ]);
    }
}
