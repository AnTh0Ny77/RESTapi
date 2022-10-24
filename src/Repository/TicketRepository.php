<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
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
}