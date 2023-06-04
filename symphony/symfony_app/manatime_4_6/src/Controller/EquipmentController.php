<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\ManatimeEquipment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;




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

    /**Action to add new equipment */
    #[Route('/equipment/add', name: 'equipment_add',methods: ["POST"])]
    public function equipmentAdd(ManagerRegistry $doctrine,Request $request,ValidatorInterface $validator): JsonResponse
    {
        //echo "equipment add echo message"; 

        $data = json_decode($request->getContent(), true);
        //dd($data);
        $entityManager = $doctrine->getManager();

        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }
       
        
            $manatimeEquipment=new ManatimeEquipment();
            $manatimeEquipment->setName(NULL);
            $manatimeEquipment->setCategory("test catdfdfegory");
            $manatimeEquipment->setNumber("testdfdf number");
            $manatimeEquipment->setDescription("tesdfdft Description");
            $manatimeEquipment->setCreatedAt(new \DateTime('@'.strtotime('now')));
            $manatimeEquipment->setUpdatedAt(new \DateTime('@'.strtotime('now')));

        
       
        //Save to database
        $entityManager->persist($manatimeEquipment);
        $entityManager->flush();
        
        //Return response
        return $this->json([
            'message' => 'Add equipment'.$manatimeEquipment->getId()
        ]);
    }
}
