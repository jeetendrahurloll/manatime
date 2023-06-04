<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\ManatimeEquipment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TypeError;

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
    #[Route('/equipment/add', name: 'equipment_add', methods: ["POST"])]
    public function equipmentAdd(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator): JsonResponse
    {


        $data = json_decode($request->getContent(), true);
        //dd($data);
        $entityManager = $doctrine->getManager();
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }


        //ORM default validation throws TypeError when value==NULL before validator has chance to check validation constraints
        try {
            $manatimeEquipment = new ManatimeEquipment();
            $manatimeEquipment->setName("");
            $manatimeEquipment->setCategory("test catdfdfegory");
            $manatimeEquipment->setNumber("");
            $manatimeEquipment->setDescription("tesdfdft Description");
            $manatimeEquipment->setCreatedAt(new \DateTime('@' . strtotime('now')));
            $manatimeEquipment->setUpdatedAt(new \DateTime('@' . strtotime('now')));


            //Catch other validation errors besides NULL , defined in ManatimeEquipment entity validation constraints.
            $errors = $validator->validate($manatimeEquipment);
            if (count($errors) > 0) {
                $errorsString = (string) $errors; 
                throw new TypeError('Some values are incorrect or blank.');
            }
        } catch (\TypeError $e) {
            return $this->json([
                'message' => 'Add equipment failure' . $manatimeEquipment->getId()."  ".$e->getMessage()
            ]);
        }



/*
        //Catch other validation errors besides NULL 
        $errors = $validator->validate($manatimeEquipment);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return $this->json([
                'message2' => 'Add equipment failure line 97' . $errorsString
            ]);
        }
*/



        //Save to database
        $entityManager->persist($manatimeEquipment);
        $entityManager->flush();

        //Return response
        return $this->json([
            'message' => 'Add equipment' . $manatimeEquipment->getId()
        ]);
    }
}
