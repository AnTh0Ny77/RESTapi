<?php
namespace Src\Repository;
require  '././vendor/autoload.php';
require  '././src/Entities/User.php';
use Src\Database;
use PDO;
use Src\Entities\User;
use ReflectionClass;
use Src\Services\ResponseHandler;

Class BaseRepository {


    public string $Table;
    public  $Class;
    public Database $Db;

    public function __construct(string $table , $db , $class){
        $this->Table = $table;
        $this->Db = $db;
        $this->Class = $class;
    }

    public function findBy(array $array , int $limit , array $order){
        $limitclause = '';
        switch ($limit) {
            case 0:
            case null:
                $limitclause = '';
                break;
            
            default:
                $limitclause = 'LIMIT' .  $limit;
                break;
        }
        $orderclause = '';
        foreach ($order as $key => $value) {
            $orderclause .= 'ORDER BY '.$key . ' ' . $value ;
        }
        
        $clause = '';
       
        foreach ($array as $key => $value) {
            $clause .=  'AND ' . $key . ' = ' .$value.'';
        }
        $request = 'SELECT * FROM '.$this->Table.' WHERE 1 = 1 '.$clause .' ' . $orderclause . $limitclause ;
        $request = $this->Db->Pdo->prepare($request);
        return  $request->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findOneBy(array $array ){
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
            return $this->auto_mapping($request, $this->Class);
        
        return null;
    }


    public function auto_mapping($array , $class){
        $object = new $class();
        foreach($array as $key => $value){
            $setName = 'set' . ucfirst($key);
            $object->$setName($value);
        }
        return $object;
    }

    public function insert(array $array){
        $column = '( ';
        $value = '( ';
        foreach ($array as $key => $val) {
            if ($key === array_key_last($array)){
                $column .= $key.' ';
                $value .=  ':'.$key.'';
            }else {
                $column .= $key.', ';
                $value .=  ':'.$key.', ';
            }
        }
        $column .= ') ';
        $value .=  ') ';
        $request = "INSERT INTO " .$this->Table." ";
        $request .= $column . ' VALUES ' . $value ; 
       
        $request = $this->Db->Pdo->prepare($request);
        foreach ($array as $key => $val) {
            $value =  ':'.$key.'';
            $request->bindValue($value, $val);
        }
        
        $request->execute();
        $user__id = $this->Db->Pdo->lastInsertId();
        return $user__id;
    }

    
    
    public function updateFields(){

    }

    
}