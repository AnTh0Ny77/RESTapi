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

    public function verifyColumn(array $array){
        $object = new $this->Class();
        foreach ($array as $key => $value) { 
            if ($key != 'search') {
                if (!property_exists($object , $key )) {
                    return 'Le champ '.$key.' n existe pas  ';
                }
            }
        }
        return null;
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
       
        if($request != false){
            if ($auto == true ) 
                return $this->auto_mapping($request, $this->Class);
            if ($auto == false) 
                return $request;
        }
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

    public function clean($string){
        return trim(preg_replace('/[^A-Za-z0-9\-\ÀÁÂÄÈÉèËÊÎéêëïúöôûâàÓÔÙÚÿ@.]/', '', $string)); 
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

    public function delete(array $array){
        $clause = '';
        $data = [];
        foreach ($array as $key => $value) {
            $clause .=  "AND " . $key. " = '" .$value. "' ";
            array_push($data ,  $value);
        }
        $request = "DELETE FROM ".$this->Table." WHERE 1 = 1 ".$clause ."";
        $request = $this->Db->Pdo->prepare($request);
        $request = $request->execute();
        if($request != false)
            return $request;
        return null;
    }

    public function searchBy(array $array){
        $clause = '';
        $first = reset( $array );
        foreach ($array as $key => $value) {
            if ($value == $first) {
                $clause .=  'AND  ( ' . $key . ' LIKE "%' .$value.'%"';
            }else{
                $clause .=  'OR ' . $key . ' LIKE "%' .$value.'%"';
            }
        }
        $clause .= ' )';
        $request = 'SELECT * FROM '.$this->Table.' WHERE 1 = 1 '.$clause .' ' ;
        $request = $this->Db->Pdo->query($request);
        $results =   $request->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $key => $value) {
            $value = $this->auto_mapping($value, $this->Class);
        }
        return $results;
    }

    public function update(array $field , array $where){
        
        $this->verifyColumn($field);
        
        $clause = '';
        foreach ($where as $key => $value) {
            $clause .=  "AND " . $key. " = '" .$value. "' ";
        }
        foreach ($field as $key => $value) {
            # code...
        }

    }

}