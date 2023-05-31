<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{    


    #[Route(['/','/index','home'], name: 'app_default')]
    public function index(): Response
    {
        //$cucon=app.request.attribute.get('_controller');
        $cucon=__CLASS__;

        return $this->render('manatimeTemplates/index.html.twig', [
            'controller_name' => $cucon,
        ]);
    }
}
