<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
use Src\Entities\Materiel;
use Src\Services\ResponseHandler;

Class MaterielRepository  extends BaseRepository {

    public function findMat(array $in ,  $clause,  int $limit , array $order ){
        
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
                        $in_clause.=   "'".$val . "'";
                    } else{
                        $in_clause.=  "'". $val . "', ";
                    }
                }
                $in_clause.= ' ) ';
            }
        }

        
        if (!empty($clause)) {
            $filtre = str_replace("-", ' ', $clause);
            $filtre = str_replace("'", ' ',$clause);
            $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
            $mots_filtre = str_word_count($filtre, 1, '0123456789');
            $array_key = [
                "mat__model" , 
                "mat__pn" , 
                "mat__type",
                "mat__marque" ,
                "mat__memo" , 
                "mat__sn",
                "mat__idnec" , 
                "mat__ident" , 
                "mat__contrat_id"
            ];
            $first = reset($array_key);
            foreach ($array_key as $key => $value) {
               
                if ($value == $first) {
                    $where_clause .=  'AND    ( ' ;
                    for ($i = 0; $i < $nb_mots_filtre; $i++){
                        if ($i == 0 ){
                            $where_clause .=  $value . ' LIKE "%' .$mots_filtre[$i] .'%"';
                        }else {
                            $where_clause .=   ' OR ' .  $value .'  LIKE "%' .$mots_filtre[$i] .'%"';
                        }
                    }
                    $where_clause .=  ' ) ';
                }else{
                    $where_clause .=  'OR  ( ' ;
                    for ($i = 0; $i < $nb_mots_filtre; $i++){
                        if ($i == 0 ){
                            $where_clause .=  $value . ' LIKE "%' .$mots_filtre[$i] .'%"';
                        }else {
                            $where_clause .= ' OR ' .  $value .'  LIKE "%' .$mots_filtre[$i] .'%"';
                        }
                    }
                    $where_clause .=  ' ) ' ;
                }
            }
        }
        
        $request = 'SELECT * FROM '.$this->Table.' WHERE 1 = 1 '.$where_clause .'  ' . $in_clause . ' '. $orderclause . $limitclause ;
       
        $request = $this->Db->Pdo->query($request);
        $request = $request->fetchAll(PDO::FETCH_ASSOC);
        return $request;

    }

    public  function postMateriel($materiel_data , $user){
        $materiel = new Materiel();

        $materiel->setMat__cli__id($materiel_data['mat__cli__id']);
        if (!$materiel instanceof Materiel)
            return $materiel;

        $materiel->setMat__type($materiel_data['mat__type']);
        if(!$materiel instanceof Materiel)
            return $materiel;

        $materiel->setMat__marque($materiel_data['mat__marque']);
        if(!$materiel instanceof Materiel)
            return $materiel;

        $materiel->setMat__model($materiel_data['mat__model']);
        if(!$materiel instanceof Materiel)
            return $materiel;

        $materiel->setMat__actif(1);
        $materiel->setMat__user_id($user->getUser__id());
        $materiel->setDate__date_maj(date('Y-m-d H:i:s'));

        $id_materiel = $this->insert($materiel_data);
        $materiel = $this->findOneBy(['mat__id' =>  $id_materiel] , true );
        return $materiel;
    }

    public function UpdateOne($materiel_data , $user){

        if (empty($materiel_data['mat__id'])) 
            return 'Le champs mat__id doit etre rendeigné';

        $verifyIfExist = $this->findOneBy(['mat__id' => $this->clean($materiel_data['mat__id'])],true);
            if(!$verifyIfExist instanceof Materiel) 
                return 'Le matériel n existe pas';

        $materiel = new Materiel();

        $materiel->setMat__id($materiel_data['mat__id']);
        if (!$materiel instanceof Materiel)
            return $materiel;
        
        $materiel->setMat__cli__id($materiel_data['mat__cli__id']);
        if (!$materiel instanceof Materiel)
            return $materiel;
        
        $materiel->setMat__type($materiel_data['mat__type']);
        if(!$materiel instanceof Materiel)
            return $materiel;
        
        $materiel->setMat__marque($materiel_data['mat__marque']);
        if(!$materiel instanceof Materiel)
            return $materiel;
        
        $materiel->setMat__model($materiel_data['mat__model']);
        if(!$materiel instanceof Materiel)
            return $materiel;

        $materiel->setDate__date_maj(date('Y-m-d H:i:s'));

        $materiel->setMat__user_id($user->getUser__id());

        $id_materiel = $this->update($materiel_data);
        $materiel = $this->findOneBy(['mat__id' =>  $id_materiel] , true );
        return $materiel;
        
    }

    public function search(array $in ,  $clause,  int $limit , array $order  , array $parameters ){
        
        $params = [
            'self' => [
                'name' => 'materiel' , 
                'alias' => 'm',
                'field' => [
                    'mat__id' => 'in' ,
                    'mat__cli__id' => 'in',
                    'mat__type' => 'in',
                    'mat__marque' => 'like' , 
                    'mat__model' => 'like', 
                    'mat__memo' => 'like', 
                    'mat__sn' => 'like',
                    'mat__idnec' => 'like',
                    'mat__ident' => 'like', 
                    "mat__contrat_id" => 'like',
                    "mat__kw_tg" => 'in'
                ]
            ]
        ];

        $limit_clause = '';
        if (!empty($limit)) {
            $limit_clause .= ' LIMIT ' . intval($limit);
        }
       

        $left_clause = '';
        foreach ($params as $key => $value) {
            if ($key != 'self' ) {
                $left_clause .=   ' ' . $value['type'] . ' JOIN '.$key.' as '.  $value['alias'] .'  ON  ( ' . $value['alias'].'.';
                foreach ($value['on'] as $keys => $entry) {
                    $left_clause .=  $keys.' = '.$entry;
                }
                $left_clause .= ' ) ';
            }
        }
        $in_clause = '';
        foreach ($params as $key => $value) {
            foreach ($value['field'] as $ref => $entry) {
                if ( $entry == 'in') {
                   
                    foreach ($in as $search => $option) {
                        
                        if (!empty($option) ) {
                            if ($search == $ref) {
                                $in_clause .= ' AND ( '.$value['alias'].'.'.$ref. ' IN ( ';
                                foreach ($in[$search] as $index =>  $input) {
                                     if ($index === array_key_last($in[$search])){
                                         $in_clause .=   '"'. $input . '" ) ';
                                     }else{
                                         $in_clause .= '"' . $input . '" , ';
                                     }
                                }  
                                $in_clause .= ' )  ';
                             }
                        }   
                    }
                }
            }
        }
        
        $where_clause = '';
       
        if (!empty($clause)) {
           
            $filtre = str_replace("-", ' ', $clause);
            $filtre = str_replace("'", ' ',$clause);
            $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
            $mots_filtre = str_word_count($filtre, 1, '0123456789');
            $first = reset($params);
            foreach ($params as $key => $value) {
                    if ($first ==  $value) {
                        if (!empty($value['field'])) {
                            foreach ($value['field'] as $field => $input) {
                                if($input == 'like'){
                                    $where_clause  .=  'AND ( ' ;
                                    for ($i = 0; $i < $nb_mots_filtre; $i++){
                                        if ($i == 0 ){
                                            $where_clause .=  $value['alias'].'.'.$field  . ' LIKE "%' .$mots_filtre[$i] .'%"';
                                        }else {
                                            $where_clause .=   ' OR ' .  $value['alias'].'.'.$field  .'  LIKE "%' .$mots_filtre[$i] .'%"';
                                        }
                                    }
                                    $where_clause .=  ' ) ';
                                }
                            }
                           
                        }
                    } else {
                        if (!empty($value['field'])) {
                            foreach ($value['field'] as $field => $input) {
                                if($input == 'like'){
                                    $where_clause  .=  ' OR  ( ' ;
                                    for ($i = 0; $i < $nb_mots_filtre; $i++){
                                        if ($i == 0 ){
                                            $where_clause .=  $value['alias'].'.'.$field  . ' LIKE "%' .$mots_filtre[$i] .'%"';
                                        }else {
                                            $where_clause .=   ' OR ' .  $value['alias'].'.'.$field  .'  LIKE "%' .$mots_filtre[$i] .'%"';
                                        }
                                    }
                                    $where_clause .=  ' ) ';
                                }
                            }
                        }
                    }
            }
        }
       
        $orderclause = '';
        if (!empty($order)) {
            $orderclause .= 'ORDER BY';
        }
        foreach ($order as $key => $value) {
            if ($key === array_key_last($order)){
                $orderclause .= ' '.$key . ' ' . $value . ' ' ;
            }else {
                $orderclause .= ' '.$key . ' ' . $value . ', ' ;
            }
           
        }

        $clause = 'SELECT DISTINCT  *  FROM ' . $params['self']['name'] . ' as ' . $params['self']['alias'].' '. $left_clause . ' WHERE 1 = 1 ' . $in_clause . ' ' . $where_clause . ' ' .  $orderclause  .'  ' . $limit_clause . '';
        var_dump($clause);
        die();
        $request = $this->Db->Pdo->query($clause);
        return  $request->fetchAll(PDO::FETCH_ASSOC);
    }


    public function search2(array $in ,  $clause,  int $limit , array $order  , array $parameters ){

        //////////////////////////////////////////////////////////////////////// CONFIG ///////////////////////////////////////////////////////////////////
        $params = [
            'self' => [
                'name' => 'materiel' , 
                'alias' => 'm',
                'field' => [
                    'mat__id' => 'in' ,
                    'mat__cli__id' => 'in',
                    'mat__type' => 'in',
                    "mat__kw_tg" => 'in' ,
                    'mat__marque' => 'double' , 
                    'mat__model' => 'like', 
                    'mat__memo' => 'like', 
                    'mat__sn' => 'like',
                    'mat__idnec' => 'like',
                    'mat__ident' => 'like', 
                    "mat__contrat_id" => 'like'
                   
                ],
                'start' => 'mat__marque',
                'end' => 'mat__contrat_id'
            ]
        ];

        ////////////////////////////////////////////////////////////////////////////////////// LIMIT //////////////////////////////////////////////////////
        $limit_clause = '';
        if (!empty($limit)) {
            $limit_clause .= ' LIMIT ' . intval($limit);
        }
       
        ///////////////////////////////////////////////////////////////////////////// LEFT ///////////////////////////////////////////////////////////////////
        $left_clause = '';
        foreach ($params as $key => $value) {
            if ($key != 'self' ) {
                $left_clause .=   ' ' . $value['type'] . ' JOIN '.$key.' as '.  $value['alias'] .'  ON  ( ' . $value['alias'].'.';
                foreach ($value['on'] as $keys => $entry) {
                    $left_clause .=  $keys.' = '.$entry;
                }
                $left_clause .= ' ) ';
            }
        }

        ////////////////////////////////////////////////////////////////////////////// IN ///////////////////////////////////////////////////////////
            $in_clause = '';
            foreach ($params as $key => $value) {
                foreach ($value['field'] as $ref => $entry) {
                    if ( $entry == 'in' or $entry == 'double') {
                        foreach ($in as $search => $option) {
                            
                            if (!empty($option) ) {
                                if ($search == $ref) {
                                    $in_clause .= ' AND ( '.$value['alias'].'.'.$ref. ' IN ( ';
                                    foreach ($in[$search] as $index =>  $input) {
                                        if ($index === array_key_last($in[$search])){
                                            $in_clause .=   '"'. $input . '" ) ';
                                        }else{
                                            $in_clause .= '"' . $input . '" , ';
                                        }
                                    }  
                                    $in_clause .= ' )  ';
                                }
                            }   
                        }
                    }
                }
            }

       ////////////////////////////////////////////////////////////////////////////// WHERE ///////////////////////////////////////////////////////////
            $where_clause = '';
            if (!empty($clause)) {
                $filtre = str_replace("-", ' ', $clause);
                $filtre = str_replace("'", ' ',$clause);
                $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
                $mots_filtre = str_word_count($filtre, 1, '0123456789');
                $first = reset($params);
                for ($i = 0; $i < $nb_mots_filtre; $i++){
                    foreach ($params as $key => $value) {
                        if (!empty($value['field'])) {
                            foreach ($value['field'] as $field => $input) {
                                if($input == 'like' or $input == 'double'){
                                    if ($i == 0 ){
                                        if ($field == $params['self']['start']) {
                                            $where_clause .=  ' AND ( ( ' .  $value['alias'].'.'.$field  . ' LIKE "%' .$mots_filtre[$i] .'%" )';
                                        }else {
                                            $where_clause .=  ' OR  ( ' .  $value['alias'].'.'.$field  . ' LIKE "%' .$mots_filtre[$i] .'%" ) ';
                                        }
                                        if ($field == $params['self']['end']) {
                                            $where_clause .= ' ) ';
                                        }
                                        
                                    }else {
                                        if ($field == $params['self']['start']) {
                                            $where_clause .=   ' AND ( ( ' .  $value['alias'].'.'.$field  .'  LIKE "%' .$mots_filtre[$i] .'%" ) ';
                                        }else {
                                            $where_clause .=   ' OR ( ' .  $value['alias'].'.'.$field  .'  LIKE "%' .$mots_filtre[$i] .'%" ) ';
                                        }
                                        if ($field == $params['self']['end']) {
                                            $where_clause .= ' ) ';
                                        }
                                       
                                    }
                                }
                            }
                        }
                    }
                }
            
        
                $orderclause = '';
                if (!empty($order)) {
                    $orderclause .= 'ORDER BY';
                }  
            }

     ////////////////////////////////////////////////////////////////////////////// ORDER ///////////////////////////////////////////////////////////
     $orderclause = " ";
        foreach ($order as $key => $value) {
            if ($key === array_key_last($order)){
                $orderclause .= ' '.$key . ' ' . $value . ' ' ;
            }else {
                $orderclause .= ' '.$key . ' ' . $value . ', ' ;
            }
        }

    ///////////////////////////////////////////////////////////////////////////////// FINAL ////////////////////////////////////////////////////////////////////////
        $clause = 'SELECT DISTINCT  *  FROM ' . $params['self']['name'] . ' as ' . $params['self']['alias'].' '. $left_clause . ' WHERE 1 = 1 ' . $in_clause . ' ' . $where_clause . ' ' .  $orderclause  .'  ' . $limit_clause . '';
       
        $request = $this->Db->Pdo->query($clause);
        return  $request->fetchAll(PDO::FETCH_ASSOC);
    }
}
