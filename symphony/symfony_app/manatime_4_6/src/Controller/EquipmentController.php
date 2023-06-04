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
use Psr\Log\LoggerInterface;


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
    public function equipmentAdd(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator,LoggerInterface $logger): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $data = json_decode($request->getContent(), true);
        //dd($data);
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }


        /**
         * Error handling strategy:
         * type errors (NULL or Blank), as defined using #[Assert\NotBlank] in 
         * ManatimeEquipment Entity are handled locally and give a json response about the values 
         * that are acceptable.
         * Full exception message is logged in var/log/dev.log or var/log/prod.log
         * depending on APP_ENV in .env 
         * Any other errors give a "An internal error has occured in the server"
         * and are automatically routed to ErrorController::show
         * 
         */
        $logger->error('wat is happening');

        //ORM default validation throws TypeError when value==NULL before validator has chance to check validation constraints
        try {
            $manatimeEquipment = new ManatimeEquipment();
            $manatimeEquipment->setName(NULL);
            $manatimeEquipment->setCategory("test catdfdfegory");
            $manatimeEquipment->setNumber("");
            $manatimeEquipment->setDescription("tesdfdft Description");
            $manatimeEquipment->setCreatedAt(new \DateTime('@' . strtotime('now')));
            $manatimeEquipment->setUpdatedAt(new \DateTime('@' . strtotime('now')));


            //Catch other validation errors besides NULL , defined in ManatimeEquipment entity validation constraints.
            $errors = $validator->validate($manatimeEquipment);
            if (count($errors) > 0) {
                $errorsString = (string) $errors; 
                throw new TypeError($errorsString);
            }
        } catch (\TypeError $e) {
            $logger->error('Type Exception occured in EquipmentController::equipmentAdd '.$e->getMessage());
            
            return $this->json([
                'message' => 'An error occurred.Some values might be blank or not according to requirements',
                'name'=>'string,not null ',
                'category'=>'string,nullable',
                'number'=>'string,not null',
                'description'=>'text,not null,empty by default',
                'createdAt'=>'datetime not null',
                'updatedAt'=>'datetime nullable'
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
