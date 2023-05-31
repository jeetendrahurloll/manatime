<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ModifyEquipmentController extends AbstractController
{
    #[Route('/modify/equipment', name: 'app_modify_equipment')]
    public function index(): Response
    {
        return $this->render('modify_equipment/index.html.twig', [
            'controller_name' => 'ModifyEquipmentController',
        ]);
    }
}
