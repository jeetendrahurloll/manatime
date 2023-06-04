<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class ErrorController extends AbstractController
{
    #[Route('/error', name: 'app_error')]
    public function show($exception,Request $request): JsonResponse
    {
            //suggested to do: logger error but display only error has occured


        //echo "before var dump";
        //var_dump($exception);
        //print_r($exception);
        //echo '<pre>'; print_r($request->getContent()); echo '</pre>';
        //echo "after var dump";
        //echo "exceptionMesage".$exception->getMessage();
        //'message' => $exception->getMessage()
        //echo(get_class($exception));

        return $this->json([
            'message' =>  $exception->getMessage()       
        ]);
    }
}
