<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Entities\User;
use Src\Services\ResponseHandler;

Class RefreshRepository {
    public string $Table;
    public Database $Db;

    public function __construct(string $table , $db ){
        $this->Table = $table;
        $this->Db = $db;
    }

    public function insertOne(){

    }

    public function findOneBy(array $array , bool $auto ){
        $clause = '';
        $data = [];
        foreach ($array as $key => $value) {
            $clause .=  "AND " . $key. " = '" .$value. "' ";
            array_push($data ,  $value);
        }
        $request = "SELECT * FROM ".$this->Table." WHERE 1 = 1 ".$clause ."";
        $request = $this->Db->Pdo->query($request);
        $request = $request->fetch(PDO::FETCH_ASSOC);
       
        if($request != false)
            return $request;
        
        return null;
    }


    public function Update(){

    }
}