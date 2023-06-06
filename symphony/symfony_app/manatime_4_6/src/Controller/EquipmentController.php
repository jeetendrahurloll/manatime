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


Sample POST query BODY:
{
    "id":         {"OrAnd":"_OR", "EqLike":"EQUAL","Pattern":"kj*&*&*&k"},
    "name":       {"OrAnd":"_OR", "EqLike":"LIKE", "Pattern":"jkjk"},
    "category":   {"OrAnd":"_AND","EqLike":"LIKE", "Pattern":"pat"},
    "number":     {"OrAnd":"_OR", "EqLike":"LIKE", "Pattern":"pat"},
    "description":{"OrAnd":"_OR", "EqLike":"LIKE", "Pattern":"kjkj"},
    "createdAt":  {"OrAnd":"_OR", "Comparator":"greater","Date":"1984-06-05 09:15:30"},
    "updatedAt":  {"OrAnd":"_OR", "Comparator":"less","Date":"1984-06-05 09:15:30"}
}
 Json Template
{
    "id":         {"OrAnd":"OR|AND","EqLike":"EQUAL|LIKE","Pattern":"pat"},
    "name":       {"OrAnd":"OR|AND","EqLike":"EQUAL|LIKE","Pattern":"pat"},
    "category":   {"OrAnd":"OR|AND","EqLike":"EQUAL|LIKE","Pattern":"pat"},
    "number":     {"OrAnd":"OR|AND","EqLike":"EQUAL|LIKE","Pattern":"pat"},
    "description":{"OrAnd":"OR|AND","EqLike":"EQUAL|LIKE","Pattern":"pat"},
    "createdAt":  {"OrAnd":"OR|AND","Comparator":"equal|greater|less","Date":"date"},
    "updatedAt":  {"OrAnd":"OR|AND","Comparator":"equal|greater|less","Date":"date"}
}


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

        $keys = array_keys($parametersAsArray);
        if ($parametersAsArray["id"] or !empty($parametersAsArray["id"])) {

        }








        //possible keys in json argument: OrAnd,EqLike,Pattern,Comparator,Date
        $possibleKeyValues = ['OrAnd', 'EqLike', 'Pattern', 'Comparator', 'Date'];
        //for each row in search parameter json
        //Ex:  one row is   "name":{"OrAnd":"or","EqLike":"like","Pattern":"pat"},
        foreach ($parametersAsArray as $i) {



            /**for each column key in each row
            [0] => OriAnd, [1] => EqLike, [2] => pattern
             */
            $keys = array_keys($i);
            foreach ($keys as $j) {
                if (!in_array($j, $possibleKeyValues)) {
                    $messageResult = [
                        'message' => " Any parameter key must be in list of keys OrAnd,EqLike,Pattern,Date,Comparator.One key is wrongly $j"
                    ];
                    return $this->json($messageResult);
                }
            }
        }

        //possible values parameters OR,AND,EQUAL,LIKE,equal,greater,less
        foreach ($parametersAsArray as $i) {
            //Check if OrAnd is _OR _AND
            if (!in_array($i["OrAnd"], ['_OR', '_AND'])) {
                $messageResult = [
                    'message' => "OrAnd must be _OR or _AND. " . $i["OrAnd"] . "was supplied instead."

                ];
                return $this->json($messageResult);
            }

            //check if EqLike is EQUAL or LIKE
            if (array_key_exists("EqLike", $i)) {
                if (!in_array($i["EqLike"], ['EQUAL', 'LIKE'])) {
                    $messageResult = [
                        'message' => "EqLike must be EQUAL or LIKE." . $i["EqLike"] . " was supplied instead"
                    ];
                    return $this->json($messageResult);
                }
            }

            //Check if pattern is not empty
            if (array_key_exists("Pattern", $i)) {
                if (empty($i["Pattern"])) {
                    $messageResult = [
                        'message' => "One of the patterns was empty"
                    ];
                    return $this->json($messageResult);
                }
            }

            //check if Comparator is equal|greater|less
            if (array_key_exists("Comparator", $i)) {
                if (!in_array($i["Comparator"], ['equal', 'greater', 'less'])) {
                    $messageResult = [
                        'message' => "Comparator must be equal, greater, or less"
                    ];
                    return $this->json($messageResult);
                }
            }

            //check if date is a date
            if (array_key_exists("Date", $i)) {
                if (\DateTime::createFromFormat('Y-m-d H:i:s', $i["Date"]) == false) {
                    $messageResult = [
                        'message' =>  $i["Date"] . " is not a date"
                    ];
                    return $this->json($messageResult);
                }
            }
        }




        $entityManager = $doctrine->getManager();
        $repository = $entityManager->getRepository(ManatimeEquipment::class);

        $result = [];
        if ($repository) {
            // echo "repo ready";
            $result = $repository->findByMultipleFields($parametersAsArray);
            //print("<pre>".print_r($result,true)."</pre>");
            $messageResult = [
                'message' => $result
            ];
            //print("<pre>".print_r($messageResult,true)."</pre>");
            //json_encode($arr);

        }
        //print("<pre>".print_r($result,true)."</pre>");



        return $this->json($messageResult);
    }
}
