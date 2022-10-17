<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
use Src\Services\ResponseHandler;

Class MaterielRepository  extends BaseRepository {

    public function findMat(array $in , array $clause,  int $limit , array $order){

        $limitclause = '';
        switch ($limit) {
            case 0:
            case null:
                $limitclause = '';
                break;
            
            default:
                $limitclause = 'LIMIT ' .  $limit;
                break;
        }
        $orderclause = '';
        foreach ($order as $key => $value) {
            $orderclause .= 'ORDER BY '.$key . ' ' . $value . ' ' ;
        }

        $in_clause = '';
        $where_clause = '';

        if (!empty($in)) {
            foreach ($in as $key => $array_of_type) {
                $in_clause.= 'AND ' . $key . ' IN (  ';
                foreach ($array_of_type as $iteration => $val) {
                    if ( $iteration === array_key_last($array_of_type)){ 
                        $in_clause.=   $val . '';
                    } else{
                        $in_clause.=   $val . ', ';
                    }
                }
                $in_clause.= ' )';
            }
        }
        if (!empty($clause)) {
           foreach ($clause as $key => $value) {
            $where_clause .=  'AND ' . $key . ' = ' .$value.'';
            }
        }

        $request = 'SELECT * FROM '.$this->Table.' WHERE 1 = 1 '.$where_clause .' ' . $in_clause . ' '. $orderclause . $limitclause ;

        return $request;

    }

}