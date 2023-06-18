<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;


class WhereClauseBuilder
{
    //Generates a section of SQL for where clauses Ex: "WHERE id=1 OR name="somename" AND category LIKE %pattern%.....
    public function buildWhereClause($parametersAsArray): string
    {
        /*
        $orAnd=$parametersAsArray[$keys[0]]['OrAnd'];           //SQL OR or AAND
        $column=$keys[0];                                       //id,name,category....   
        $sqlOperator=$parametersAsArray[$keys[0]]['EqLike'];    //EQUAL or LIKE
        $pattern=$parametersAsArray[$keys[0]]['Pattern'];        //input pattern
        */
        $whereClause = '';                                       //a string that comprises all the SQL WHERE statements
        $sqlOperatorsMap = [                                     //this is used to convert json/PHP friendly operators into sql friendly operators
            '_OR' => 'OR',
            '_AND' => 'AND',
            'EQUAL' => '=',
            'LIKE' => 'LIKE',
            'equal' => '=',
            'greater' => '>',
            'less' => '<'
        ];


        //Generates WHERE clause for non-dates, such as id,name,category,etc
        $whereClauseFragment1 = "";                                 //where clause for columns
        $first = true;
        foreach (['id', 'name', 'category', 'number', 'description'] as $column) {
            if (!array_key_exists($column, $parametersAsArray)) {
                continue;
            }

            //$column                                                //id,name,category....   
            $orAnd = $parametersAsArray[$column]['OrAnd'];           //SQL OR or AAND
            $mappedOrAnd = $sqlOperatorsMap[$orAnd];
            $sqlOperator = $parametersAsArray[$column]['EqLike'];    //EQUAL or LIKE
            $mappedSqlOperator = $sqlOperatorsMap[$sqlOperator];
            $pattern = $parametersAsArray[$column]['Pattern'];        //input pattern
            $sqlWildcard = '%';

            if ($sqlOperator == 'EQUAL') {
                $sqlWildcard = '';
            } else {
                $sqlWildcard = '%';
            }

            //remove first OR or AND stament in where clause            
            if ($first) {
                $mappedOrAnd = "WHERE";
                $first = false;
            }

            $whereClauseFragment1 = $whereClauseFragment1 . ' ' . $mappedOrAnd . '  ' . $column . '  ' . $mappedSqlOperator . '  \'' . $sqlWildcard . $pattern . $sqlWildcard . '\'';
        }






        //Generates WHERE clause for dates
        $whereClauseFragment2 = "";                                         //where clause for dates
        foreach (['created_at', 'updated_at'] as $column) {
            if (!array_key_exists($column, $parametersAsArray)) {
                continue;
            }
            $orAnd = $parametersAsArray[$column]['OrAnd'];                  //SQL OR or AAND
            $mappedOrAnd = $sqlOperatorsMap[$orAnd];
            $sqlOperator = $parametersAsArray[$column]['Comparator'];       //EQUAL or LIKE
            $mappedSqlOperator = $sqlOperatorsMap[$sqlOperator];
            $date = $parametersAsArray[$column]['Date'];                    //input pattern
            $sqlWildcard = '%';

            if ($first) {
                $mappedOrAnd = "WHERE";
                $first = false;
            }
            $whereClauseFragment2 = $whereClauseFragment2 . ' ' . $mappedOrAnd . '  ' . $column . '  ' . $mappedSqlOperator . ' \'' . $date . '\' ';
        }

        $whereClause = $whereClauseFragment1 . $whereClauseFragment2; //concatenate both fragment of where clauses into a final where clause

        return $whereClause;
    }
}
