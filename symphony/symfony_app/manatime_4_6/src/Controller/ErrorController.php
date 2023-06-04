<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    #[Route('/error', name: 'app_error')]
    public function show($exception): JsonResponse
    {
        //echo "exceptionMesage".$exception->getMessage();
        //'message' => $exception->getMessage()
        return $this->json([
            'message' => 'This is the error controller again!',
            'path' => 'src/Controller/ErrorController.php',
        ]);
    }
}
