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
    public function equipmentAdd(ManagerRegistry $doctrine, Request $request, ValidatorInterface $validator, LoggerInterface $logger): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $parametersAsArray = [];
        $content = $request->getContent();
        $parametersAsArray = json_decode($content, true);

        $name = $parametersAsArray["name"];
        $category = $parametersAsArray["category"];
        $number = $parametersAsArray["number"];
        $description = $parametersAsArray["description"];
        $createdAt = $parametersAsArray["createdAt"];
        $updatedAt = $parametersAsArray["updatedAt"];
        /*
        echo("testing parameters as input array");
        echo '<pre>'; print_r($parametersAsArray); echo '</pre>';

        echo("end of testing");

        */
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
        try {


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
        } catch (\TypeError $e) {
            $logger->error('Type Exception occured in EquipmentController::equipmentAdd ' . $e->getMessage());

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
            'message' => 'Add equipment' . $manatimeEquipment->getId()
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
         * ManatimeEquipment Entity are handled locally and give a json response about the values 
         * that are acceptable.
         * Full exception message is logged in var/log/dev.log or var/log/prod.log
         * depending on APP_ENV in .env 
         * Any other errors give a "An internal error has occured in the server"
         * and are automatically routed to ErrorController::show         * 
         */

        //ORM default validation throws TypeError when value==NULL before validator has chance to check validation constraints
        try {


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
        } catch (\TypeError $e) {
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
            'message' => 'Update equipment' . $manatimeEquipment->getId()
        ]);
    }



    /**Action to search equipment */
    /**Search strategy
{
    "id":{"OrAnd":"OR|AND","EqLike":"EQUAL|LIKE","pattern":"pat"},
    "name":{"OrAnd":"OR|AND","EqLike":"EQUAL|LIKE","pattern":"pat"},
    "category":{"OrAnd":"OR|AND","EqLike":"EQUAL|LIKE","pattern":"pat"},
    "number":{"OrAnd":"OR|AND","EqLike":"EQUAL|LIKE","pattern":"pat"},
    "description":{"OrAnd":"OR|AND","EqLike":"EQUAL|LIKE","pattern":"pat"},
    "createdAt":{"OrAnd":"or","comparator":"equal|greater|less","date":"date"},
    "updatedAt":{"OrAnd":"or","comparator":"equal|greater|less","date":"date"}
}

Sample POST query BODY:


For the fields 
     * each entry will generate an addendum that will be added to the SQL query.
     * for example 
     *     "name":{"OrAnd":"OR","EqLike":"EQUAL","pattern":"pat"}
     *      will cause this addendum to be added in the sql query
     *      " OR name LIKE %pat% "
     * 
     * id field only implements "EQUAL", i.e "OR id ='pat' "and ignores the EqLike parameter
     */

    #[Route('/equipment/search', name: 'equipment_search', methods: ["POST"])]
    public function equipmentSearch(ManagerRegistry $doctrine, Request $request, LoggerInterface $logger): JsonResponse
    {

        $parametersAsArray = [];
        $content = $request->getContent();
        $parametersAsArray = json_decode($content, true);

        //print("<pre>".print_r($parametersAsArray,true)."</pre>");

        if ($parametersAsArray["id"] or !empty($parametersAsArray["id"])) {
            echo "id present   " . $parametersAsArray["id"]["OrAnd"] . "-" . $parametersAsArray["id"]["EqLike"] . "-" . $parametersAsArray["id"]["pattern"];
        }

        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(ManatimeEquipment::class);

        $result = [];
        if ($repository) {
            echo "repo ready";
            $result = $repository->findByMultipleFields();
            //print("<pre>".print_r($result,true)."</pre>");
           $messageResult= [
                'message' => $result
           ];
           //print("<pre>".print_r($messageResult,true)."</pre>");
           //json_encode($arr);

        }
        //print("<pre>".print_r($result,true)."</pre>");



        return $this->json($messageResult);
    }
}
