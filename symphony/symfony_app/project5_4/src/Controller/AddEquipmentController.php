<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddEquipmentController extends AbstractController
{
    #[Route('/add/equipment', name: 'app_add_equipment')]
    public function index(): Response
    {
        return $this->render('add_equipment/index.html.twig', [
            'controller_name' => 'AddEquipmentController',
        ]);
    }
}
