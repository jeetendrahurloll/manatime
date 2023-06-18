<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;


class ValidateService
{



    /**
     * validates that json input is correct in search route.Returns null if all ok, else returns a json response to send to route.
     */
    public function validateSearchJson($content): ?JsonResponse
    {

        $parametersAsArray = [];

        $parametersAsArray = json_decode($content, true);
        //echo("param as array");
        //print_r($parametersAsArray);

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
                    //return json_encode($messageResult);
                    return new JsonResponse($messageResult);
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
                //return json_encode($messageResult);
                return new JsonResponse($messageResult);
            }

            //check if EqLike is EQUAL or LIKE
            if (array_key_exists("EqLike", $i)) {
                if (!in_array($i["EqLike"], ['EQUAL', 'LIKE'])) {
                    $messageResult = [
                        'message' => "EqLike must be EQUAL or LIKE." . $i["EqLike"] . " was supplied instead"
                    ];
                    //return json_encode($messageResult);
                    return new JsonResponse($messageResult);
                }
            }

            //Check if pattern is not empty
            if (array_key_exists("Pattern", $i)) {
                if (empty($i["Pattern"])) {
                    $messageResult = [
                        'message' => "One of the patterns was empty"
                    ];
                    //return json_encode($messageResult);
                    return new JsonResponse($messageResult);
                }
            }

            //check if Comparator is equal|greater|less
            if (array_key_exists("Comparator", $i)) {
                if (!in_array($i["Comparator"], ['equal', 'greater', 'less'])) {
                    $messageResult = [
                        'message' => "Comparator must be equal, greater, or less"
                    ];
                    //return json_encode($messageResult);
                    return new JsonResponse($messageResult);
                }
            }

            //check if date is a date
            if (array_key_exists("Date", $i)) {
                if (\DateTime::createFromFormat('Y-m-d H:i:s', $i["Date"]) == false) {
                    $messageResult = [
                        'message' =>  $i["Date"] . " is not a date"
                    ];
                    return new JsonResponse($messageResult);
                    //return json_encode($messageResult);
                }
            }
        }


        return null;
    }
}
