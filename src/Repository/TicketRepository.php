<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
use Src\Entities\Ticket;
use Src\Entities\Tickets;
use Src\Services\ResponseHandler;

Class TicketRepository  extends BaseRepository {

    public function findTickets( int $limit , array $order){

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
        foreach ($order as $key => $value){
            $orderclause .= 'ORDER BY '.$key . ' ' . $value . ' ' ;
        }
        
        $left_clause = 'LEFT JOIN tiket_ligne as tkl ON ( tkl.tkl__user_id =   or tkl.tkl__user_id_dest = ) ';
    }

    public function checkTicket($tickets){
       
        $ticket = new Tickets();
        
        $motif = $ticket->setTk__motif($tickets['tk__motif']);
        if (!$motif instanceof Tickets) 
            return $motif;

        $titre = $ticket->setTk__titre($tickets['tk__titre']);
        if (!$titre instanceof Tickets) 
            return$titre;

        if (!empty($tickets['tk__id'])) {
            $ticket->setTk__id($tickets['tk__id']);
        }

        if (!empty($tickets['tk__lu'])) {
            $ticket->setTk__lu($tickets['tk__lu']);
        }

        if (!empty($tickets['tk__indic'])) {
            $ticket->setTk__indic($tickets['tk__indic']);
        }

        if (!empty($tickets['tk__groupe'])) {
            $ticket->setTk__groupe($tickets['tk__groupe']);
        }
       
        return $ticket;
        
    }

    public function max(){
        $clause = 'SELECT MAX(tk__groupe) as max__tk__groupe FROM ticket' ;
        $request = $this->Db->Pdo->query($clause);
        return  $request->fetch(PDO::FETCH_ASSOC);
    }


    public function search(array $in ,  $clause,  int $limit , array $order  , array $parameters ){
        
        $params = [
            'self' => [
                'name' => 'ticket' , 
                'alias' => 't',
                'field' => [
                    'tk__id' => 'in' ,
                    'tk__lu' => 'in',
                    'tk__motif' => 'in',
                    'tk__titre' => 'like' , 
                    'tk__groupe' => 'in', 
                ]
            ],
            'materiel' => [
                'alias' => 'm',
                'type' => 'LEFT',
                'on' => [
                    'mat__id' => 't.tk__motif_id'
                ],
                'field' => [
                    'mat__id' => 'in' ,
                    'mat__cli__id' => 'in' ,
                    'mat__type' => 'like' , 
                    'mat__marque' => 'like', 
                    'mat__model' => 'like', 
                    'mat__pn' => 'like',
                    'mat__sn' => 'like', 
                    'mat__idnec' => 'like'
                ]
            ], 
            'lien_user_client' => [
                'alias' => 'l',
                'type' => 'LEFT',
                'on' => [
                    'luc__cli__id' => 'm.mat__cli__id'
                ],
                'field' => [
                   
                ]
            ], 
            'client' => [
                'alias' => 'c',
                'type' => 'LEFT',
                'on' => [
                    'cli__id' => 'l.luc__cli__id'
                ],
                'field' => [
                    'cli__id' => 'like' ,
                    'cli__nom' => 'like' , 
                    'cli__ville' => 'like'
                ]
            ], 'ticket_ligne' => [
                'alias' => 'y',
                'type' => 'LEFT',
                'on' => [
                    'tkl__tk_id' => 't.tk__id'
                ],
                'field' => [
                    'tkl__user_id_dest' => 'in',
                    'tkl__user_id' => 'in'
                ]
            ], 
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
                                            $where_clause .=   ' AND ' .  $value['alias'].'.'.$field  .'  LIKE "%' .$mots_filtre[$i] .'%"';
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
                                            $where_clause .=   ' AND ' .  $value['alias'].'.'.$field  .'  LIKE "%' .$mots_filtre[$i] .'%"';
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

        $clause = 'SELECT DISTINCT  t.tk__id , t.tk__lu  FROM ' . $params['self']['name'] . ' as ' . $params['self']['alias'].' '. $left_clause . ' WHERE 1 = 1 ' . $in_clause . ' ' . $where_clause . ' ' .  $orderclause  .'  ' . $limit_clause . '';
      
        $request = $this->Db->Pdo->query($clause);
        return  $request->fetchAll(PDO::FETCH_ASSOC);
    }



    public function search2(array $in ,  $clause,  int $limit , array $order  , array $parameters ){

        //////////////////////////////////////////////////////////////////////// CONFIG ///////////////////////////////////////////////////////////////////
        var_dump('hey');
        die();
        $params = [
            'start' => 'tk__titre',
            'end' => 'cli__ville',
            'self' => [
                'name' => 'ticket' , 
                'alias' => 't',
                'field' => [
                    'tk__id' => 'in' ,
                    'tk__lu' => 'in',
                    'tk__motif' => 'in',
                    'tk__titre' => 'like' , 
                    'tk__groupe' => 'in', 
                ] 
            ],
            'materiel' => [
                'alias' => 'm',
                'type' => 'LEFT',
                'on' => [
                    'mat__id' => 't.tk__motif_id'
                ],
                'field' => [
                    'mat__id' => 'in' ,
                    'mat__cli__id' => 'in' ,
                    'mat__type' => 'like' , 
                    'mat__marque' => 'like', 
                    'mat__model' => 'like', 
                    'mat__pn' => 'like',
                    'mat__sn' => 'like', 
                    'mat__idnec' => 'like'
                ]
            ], 
            'lien_user_client' => [
                'alias' => 'l',
                'type' => 'LEFT',
                'on' => [
                    'luc__cli__id' => 'm.mat__cli__id'
                ],
                'field' => [
                   
                ]
            ], 
            'client' => [
                'alias' => 'c',
                'type' => 'LEFT',
                'on' => [
                    'cli__id' => 'l.luc__cli__id'
                ],
                'field' => [
                    'cli__id' => 'like' ,
                    'cli__nom' => 'like' , 
                    'cli__ville' => 'like'
                ]
            ], 'ticket_ligne' => [
                'alias' => 'y',
                'type' => 'LEFT',
                'on' => [
                    'tkl__tk_id' => 't.tk__id'
                ],
                'field' => [
                    'tkl__user_id_dest' => 'in',
                    'tkl__user_id' => 'in'
                ]
            ], 
        ];

        ////////////////////////////////////////////////////////////////////////////////////// LIMIT //////////////////////////////////////////////////////
        $limit_clause = '';
        if (!empty($limit)) {
            $limit_clause .= ' LIMIT ' . intval($limit);
        }
        
        ///////////////////////////////////////////////////////////////////////////// LEFT ///////////////////////////////////////////////////////////////////
        $left_clause = '';
        foreach ($params as $key => $value) {
            if ($key != 'self' and $key != 'start' and $key != 'end' ) {
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
                                        if ($field == $params['start']) {
                                            $where_clause .=  ' AND ( ( ' .  $value['alias'].'.'.$field  . ' LIKE "%' .$mots_filtre[$i] .'%" )';
                                        }else {
                                            $where_clause .=  ' OR  ( ' .  $value['alias'].'.'.$field  . ' LIKE "%' .$mots_filtre[$i] .'%" ) ';
                                        }
                                        if ($field == $params['end']) {
                                            $where_clause .= ' ) ';
                                        }
                                        
                                    }else {
                                        if ($field == $params['start']) {
                                            $where_clause .=   ' AND ( ( ' .  $value['alias'].'.'.$field  .'  LIKE "%' .$mots_filtre[$i] .'%" ) ';
                                        }else {
                                            $where_clause .=   ' OR ( ' .  $value['alias'].'.'.$field  .'  LIKE "%' .$mots_filtre[$i] .'%" ) ';
                                        }
                                        if ($field == $params['end']) {
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
        // foreach ($order as $key => $value) {
        //     if ($key === array_key_last($order)){
        //         $orderclause .= ' '.$key . ' ' . $value . ' ' ;
        //     }else {
        //         $orderclause .= ' '.$key . ' ' . $value . ', ' ;
        //     }
        // }

    ///////////////////////////////////////////////////////////////////////////////// FINAL ////////////////////////////////////////////////////////////////////////
        $clause = 'SELECT DISTINCT  t.tk__id  FROM ' . $params['self']['name'] . ' as ' . $params['self']['alias'].' '. $left_clause . ' WHERE 1 = 1 ' . $in_clause . ' ' . $where_clause . ' ' .  $orderclause  .'  ' . $limit_clause . '';
       var_dump($clause);
       die();
        $request = $this->Db->Pdo->query($clause);
        return  $request->fetchAll(PDO::FETCH_ASSOC);
    }
}