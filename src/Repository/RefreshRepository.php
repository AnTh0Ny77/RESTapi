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

    public function __construct( $db ){
        $this->Table ='refresh_token';
        $this->Db = $db;
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

    public function insertOne($user_id){

        $exist = $this->findOneBy(['user__id' => $user_id] , false);
        if (!empty($exist)) {
            $this->delete(['user__id' => $user_id]);
        }
        $key = md5(microtime().rand());
        $date = date('Y-m-d H:i:s' , strtotime("+30 days"));
        $array = [
            'user__id' => $user_id , 
            'refresh_token' => $key , 
            'exp__date' =>  $date
        ];
        $this->insert($array);
        return $this->findOneBy(['user__id' => $user_id] , false)['refresh_token'];
    }
}