<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\ManatimeEquipment;


class EquipmentController extends AbstractController
{
    #[Route('/equipment', name: 'app_equipment')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new equipment controller!',
            'path' => 'src/Controller/EquipmentController.php',
        ]);
    }


    #[Route('/equipment/add', name: 'equipment_add')]
    public function equipmentAdd(ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        /** 
        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(1999);
        $product->setDescription('Ergonomic and stylish!');
        */

        $manatimeEquipment=new ManatimeEquipment();
        $manatimeEquipment->setName("test name");
        $manatimeEquipment->setCategory("test category");
        $manatimeEquipment->setNumber("test number");
        $manatimeEquipment->setDescription("test Description");
        $manatimeEquipment->setCreatedAt(new \DateTime('@'.strtotime('now')));
        $manatimeEquipment->setUpdatedAt(new \DateTime('@'.strtotime('now')));

        
        // tell Doctrine you want to (eventually) save the Product (no queries yet)
        $entityManager->persist($manatimeEquipment);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        //return new Response('Saved new product with id '.$product->getId());
        
        return $this->json([
            'message' => 'Add equipment'.$manatimeEquipment->getId()
        ]);
    }
}
