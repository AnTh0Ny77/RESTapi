<?php

namespace Src\Repository;

require  '././vendor/autoload.php';
use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
use Src\Services\ResponseHandler;

class ShopAVendreRepository  extends BaseRepository{

    public function search2(array $in,  $clause,  int $limit, array $order, array $parameters){
        //////////////////////////////////////////////////////////////////////// CONFIG ///////////////////////////////////////////////////////////////////
        $params = [
            'self' => [
                'name' => 'shop_avendre',
                'alias' => 's',
                'field' => [
                    'sav__cli_id' => 'in' , 
                    'sav__id' => 'in'
                ],
                'start' => 'sar__description',
                'end' => 'sar__marque'
            ],
            'shop_article' => [
                'alias' => 'a',
                'type' => 'LEFT',
                    'on' => [
                        'sar__ref_id' => 's.sav__ref_id'
                    ],
                'field' => [
                        'sar__description' => 'like' ,
                        'sar__ref_constructeur' => 'like',
                        'sar__marque' => 'like' ,
                        'sar__famille' => 'in'
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
            if ($key != 'self') {
                $left_clause .=   ' ' . $value['type'] . ' JOIN ' . $key . ' as ' .  $value['alias'] . '  ON  ( ' . $value['alias'] . '.';
                foreach ($value['on'] as $keys => $entry) {
                    $left_clause .=  $keys . ' = ' . $entry;
                }
                $left_clause .= ' ) ';
            }
        }

        ////////////////////////////////////////////////////////////////////////////// IN ///////////////////////////////////////////////////////////
        $in_clause = '';
        foreach ($params as $key => $value) {
            foreach ($value['field'] as $ref => $entry) {
                if ($entry == 'in' or $entry == 'double') {
                    foreach ($in as $search => $option) {

                        if (!empty($option)) {
                            if ($search == $ref) {
                                $in_clause .= ' AND ( ' . $value['alias'] . '.' . $ref . ' IN ( ';
                                foreach ($in[$search] as $index =>  $input) {
                                    if ($index === array_key_last($in[$search])) {
                                        $in_clause .=   '"' . $input . '" ) ';
                                    } else {
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
            $filtre = str_replace("'", ' ', $clause);
            $nb_mots_filtre = str_word_count($filtre, 0, "0123456789");
            $mots_filtre = str_word_count($filtre, 1, '0123456789');
            $first = reset($params);
            for ($i = 0; $i < $nb_mots_filtre; $i++) {
                foreach ($params as $key => $value) {
                    if (!empty($value['field'])) {
                        foreach ($value['field'] as $field => $input) {
                            if ($input == 'like' or $input == 'double') {
                                if ($i == 0) {
                                    if ($field == $params['self']['start']) {
                                        $where_clause .=  ' AND ( ( ' .  $value['alias'] . '.' . $field  . ' LIKE "%' . $mots_filtre[$i] . '%" )';
                                    } else {
                                        $where_clause .=  ' OR  ( ' .  $value['alias'] . '.' . $field  . ' LIKE "%' . $mots_filtre[$i] . '%" ) ';
                                    }
                                    if ($field == $params['self']['end']) {
                                        $where_clause .= ' ) ';
                                    }
                                } else {
                                    if ($field == $params['self']['start']) {
                                        $where_clause .=   ' AND ( ( ' .  $value['alias'] . '.' . $field  . '  LIKE "%' . $mots_filtre[$i] . '%" ) ';
                                    } else {
                                        $where_clause .=   ' OR ( ' .  $value['alias'] . '.' . $field  . '  LIKE "%' . $mots_filtre[$i] . '%" ) ';
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
            if ($key === array_key_last($order)) {
                $orderclause .= ' ' . $key . ' ' . $value . ' ';
            } else {
                $orderclause .= ' ' . $key . ' ' . $value . ', ';
            }
        }

        ///////////////////////////////////////////////////////////////////////////////// FINAL ////////////////////////////////////////////////////////////////////////
        $clause = 'SELECT s.*, a.* FROM ' . $params['self']['name'] . ' as ' . $params['self']['alias'] . ' ' . $left_clause . ' WHERE 1 = 1 ' . $in_clause . ' ' . $where_clause . ' ' .  $orderclause  . '  ' . $limit_clause . '';
      
        $request = $this->Db->Pdo->query($clause);
        return  $request->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAll(){
        $clause = 'SELECT * from shop_avendre where 1 =1 limit 8000';
        $request = $this->Db->Pdo->query($clause);
        return  $request->fetchAll(PDO::FETCH_ASSOC);
    }
}
