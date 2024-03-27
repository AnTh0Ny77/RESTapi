<?php

namespace Src\Repository;

require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
use Src\Services\ResponseHandler;

class ShopArticleRepository  extends BaseRepository{


    public function findAll()
    {
        $clause = 'SELECT sar__ref_id , sar__ref_constructeur , sar__description , sar__marque , k.kw__lib as famille , sar__image from shop_article
        LEFT JOIN keyword as k ON ( sar__famille = k.kw__value )  
        where 1 =1 limit 8000';
        $request = $this->Db->Pdo->query($clause);
        return  $request->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByPnCourt($pn){
        $clause = 'SELECT s.* , k.kw__lib as famille from shop_article as s
        LEFT JOIN keyword as k ON ( sar__famille = k.kw__value )  
        where sar__ref_constructeur  = '.$pn.' limit 1';
        $request = $this->Db->Pdo->query($clause);
        return  $request->fetch(PDO::FETCH_ASSOC);
    }

    public function updateSossuke(array $field){

        $array_exclusion  = [ 'token' , 'refresh_token' , 'roles'  , 'clients' , 'password' , 'user__password'] ; 
        
        $identifier =  $this->returnPrimaryKey()['COLUMN_NAME'];
            
        $setClause = 'SET ';
        $arraySetClause = [];
        $array_remplacement = [];
        foreach ($field as $key => $value){
            if ($key != $identifier and !in_array($key , $array_exclusion) ) {
                $array_remplacement[$key] = $value;
            }
        }

        foreach ($array_remplacement as $key => $value){
            if ($key != $identifier ) {
                    if ($key === array_key_last($array_remplacement)) {
                        $setClause.= ''.$key. '= ? ';
                        array_push($arraySetClause , $value);
                    }else{
                        $setClause.= ''.$key. '= ? , ';
                        array_push($arraySetClause , $value);
                    }
            }
        }
        
        $clause = 'WHERE  ( 1 = 1 AND  sar__ref_constructeur = ' . $field['sar__ref_constructeur'] . ' )';
       
        $request = $this->Db->Pdo->prepare('UPDATE '.$this->Table.' '.$setClause.' '. $clause. ' ');
        $request->execute($arraySetClause);
    }
}
