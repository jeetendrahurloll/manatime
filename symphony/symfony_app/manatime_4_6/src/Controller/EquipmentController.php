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
use App\Service\ValidateService;

class EquipmentController extends AbstractController
{
    #[Route('/equipment', name: 'app_equipment')]
    public function index(ValidateService $validateService): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new equipment controller!',
            'path' => 'src/Controller/EquipmentController.php',
        ]);
    }

    /*Action to add new equipment 
     Sample json to post
    {
        "name":"someName",
        "category":"someCategory",
        "number":"someNumber",
        "description":"someDescription",
        "createdAt":"2023-06-14 21:30:02",
        "updatedAt":"2023-06-14 21:30:02"
    }
     */
    #[Route('/equipment/add', name: 'equipment_add', methods: ["POST"])]
    public function equipmentAdd(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator, LoggerInterface $logger): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $parametersAsArray = [];
        $content = $request->getContent();
        $parametersAsArray = json_decode($content, true);
        try {

            $name = $parametersAsArray["name"];
            $category = $parametersAsArray["category"];
            $number = $parametersAsArray["number"];
            $description = $parametersAsArray["description"];
            $createdAt = $parametersAsArray["createdAt"];
            $updatedAt = $parametersAsArray["updatedAt"];


            /**
             * Error handling strategy:
             * type errors (NULL or Blank), as defined using #[Assert\NotBlank] in 
             * ManatimeEquipment Entity are handled locally and give a json response about the values 
             * that are acceptable.
             * Full exception message is logged in var/log/dev.log or var/log/prod.log
             * depending on APP_ENV in .env 
             * Any other errors give a "An internal error has occured in the server"
             * and are automatically routed to ErrorController::show         * 
             */

            //ORM default validation throws TypeError when value==NULL before validator has chance to check validation constraints



            $manatimeEquipment = new ManatimeEquipment();
            $manatimeEquipment->setName($name);
            $manatimeEquipment->setCategory($category);
            $manatimeEquipment->setNumber($number);
            $manatimeEquipment->setDescription($description);
            $manatimeEquipment->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $createdAt));
            $manatimeEquipment->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $updatedAt));


            //Catch other validation errors besides NULL , defined in ManatimeEquipment entity validation constraints.
            $errors = $validator->validate($manatimeEquipment);
            if (count($errors) > 0) {
                $errorsString = (string) $errors;
                throw new TypeError($errorsString);
            }
        } catch (\Throwable $e) {
            $logger->error('Exception occured in EquipmentController::equipmentAdd ' . $e->getMessage());

            return $this->json([
                'message' => 'An error occurred.Some values might be blank or not according to requirements',
                'name' => 'string,not null ',
                'category' => 'string,nullable',
                'number' => 'string,not null',
                'description' => 'text,not null,empty by default',
                'createdAt' => 'datetime not null',
                'updatedAt' => 'datetime nullable'
            ]);
        }

        //Save to database
        $entityManager->persist($manatimeEquipment);
        $entityManager->flush();

        //Return response
        return $this->json([
            'message' => 'Added equipment',
            "id" => $manatimeEquipment->getId()
        ]);
    }


    /**Action to update equipment */
    #[Route('/equipment/update', name: 'equipment_update', methods: ["POST"])]
    public function equipmentUpdate(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator, LoggerInterface $logger): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $parametersAsArray = [];
        $content = $request->getContent();
        $parametersAsArray = json_decode($content, true);
        try {
            $id = $parametersAsArray["id"];
            $name = $parametersAsArray["name"];
            $category = $parametersAsArray["category"];
            $number = $parametersAsArray["number"];
            $description = $parametersAsArray["description"];
            $createdAt = $parametersAsArray["createdAt"];
            $updatedAt = $parametersAsArray["updatedAt"];

            /**
             * Error handling strategy:
             * type errors (NULL or Blank), as defined using #[Assert\NotBlank] in 
             * ManatimeEquipment Entity, are handled locally and give a json response about the values 
             * that are acceptable.
             * Full exception message is logged in var/log/dev.log or var/log/prod.log
             * depending on APP_ENV in .env 
             * Any other errors give a "An internal error has occured in the server"
             * and are automatically routed to ErrorController::show     
             * 
             * ORM default validation throws TypeError when value==NULL before validator has chance to check validation constraints
             *
             */




            $manatimeEquipment = $doctrine->getRepository(ManatimeEquipment::class)->find($id);

            if (!$manatimeEquipment) {
                throw $this->createNotFoundException(
                    'No equipment found for id ' . $id
                );
            }


            $manatimeEquipment->setName($name);
            $manatimeEquipment->setCategory($category);
            $manatimeEquipment->setNumber($number);
            $manatimeEquipment->setDescription($description);
            $manatimeEquipment->setCreatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $createdAt));
            $manatimeEquipment->setUpdatedAt(\DateTime::createFromFormat('Y-m-d H:i:s', $updatedAt));


            //Catch other validation errors besides NULL , defined in ManatimeEquipment entity validation constraints.
            $errors = $validator->validate($manatimeEquipment);
            if (count($errors) > 0) {
                $errorsString = (string) $errors;
                throw new TypeError($errorsString);
            }
        } catch (\Throwable $e) {
            $logger->error('Type Exception occured in EquipmentController::equipmentUpdate' . $e->getMessage());

            return $this->json([
                'message' => 'An error occurred.Some values might be blank or not according to requirements',
                'id' => 'int,not null',
                'name' => 'string,not null ',
                'category' => 'string,nullable',
                'number' => 'string,not null',
                'description' => 'text,not null,empty by default',
                'createdAt' => 'datetime not null',
                'updatedAt' => 'datetime nullable'
            ]);
        }

        //Save to database
        $entityManager->persist($manatimeEquipment);
        $entityManager->flush();

        //Return response
        return $this->json([
            'message' => 'Updated equipment of id ' . $manatimeEquipment->getId()
        ]);
    }


    /**
     *   Action to search equipment 
     *   Sample POST query BODY:
     *   {
     *   "id":         {"OrAnd":"_OR", "EqLike":"EQUAL","Pattern":"kjkjkjkj"},
     *   "name":       {"OrAnd":"_OR", "EqLike":"LIKE", "Pattern":"jkjk"},
     *   "category":   {"OrAnd":"_AND","EqLike":"LIKE", "Pattern":"pat"},
     *   "number":     {"OrAnd":"_OR", "EqLike":"LIKE", "Pattern":"pat"},
     *   "description":{"OrAnd":"_OR", "EqLike":"LIKE", "Pattern":"kjkj"},
     *   "createdAt":  {"OrAnd":"_OR", "Comparator":"greater","Date":"1984-06-05 09:15:30"},
     *   "updatedAt":  {"OrAnd":"_OR", "Comparator":"less","Date":"1984-06-05 09:15:30"}
     *   }

     *   For the fields ,each entry will generate an addendum that will be added to the SQL query.
     *   for example 
     *   "name":{"OrAnd":"OR","EqLike":"EQUAL","pattern":"pat"}
     *   will cause this addendum to be added in the sql query
     *   " OR name = 'pat' "      
     */

    #[Route('/equipment/search', name: 'equipment_search', methods: ["POST"])]
    public function equipmentSearch(ValidateService $validateService, ManagerRegistry $doctrine, Request $request, LoggerInterface $logger): JsonResponse
    {
        $parametersAsArray = [];
        $content = trim(preg_replace('/\s\s+/', ' ', $request->getContent()));
        $parametersAsArray = json_decode($content, true);

        if ($validateService->validateSearchJson($content)) {
            return $validateService->validateSearchJson($content);
        }

        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(ManatimeEquipment::class);

        $result = [];
        if ($repository) {
            $result = $repository->findByMultipleFields($parametersAsArray);
        }
        return $this->json($result);
    }





    #[Route('/equipment/delete/{id}', name: 'equipment_delete', methods: ["GET"])]
    public function equipmentDelete(int $id, ManagerRegistry $doctrine, Request $request, LoggerInterface $logger): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(ManatimeEquipment::class);
        $result = $repository->findOneBySomeField($id);
        if($result){
            $repository->remove($result, true);
            return new JsonResponse(['message' => $id . ' removed']);

        }
        else{
            return new JsonResponse(['message' => $id . ' not found']);

        }
        //$result = $repository->remove($result, true);
        //return new JsonResponse(['message' => $id . ' removed']);
    }


    #[Route('/equipment/route/{id}', name: 'equipment_route', methods: ["GET"])]
    public function equipmentRoute(int $id, ManagerRegistry $doctrine, Request $request, LoggerInterface $logger): JsonResponse
    {

        return new JsonResponse(['message' => $id]);
    }
}
