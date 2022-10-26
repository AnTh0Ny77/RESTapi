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

    public function checkTicket($tickets){
        $ticket = new Tickets();

        $ticket->setTk__motif($tickets['tk__motif']);
        if (!$ticket instanceof Tickets) 
            return $ticket;

        $ticket->setTk__titre($tickets['tk__titre']);
        if (!$ticket instanceof Tickets) 
            return $ticket;

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
}