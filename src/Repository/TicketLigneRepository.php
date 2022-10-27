<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
use Src\Entities\TicketsLigne;
use Src\Services\ResponseHandler;

Class TicketLigneRepository  extends BaseRepository {


    public function checkTicket($tickets){
        
        $ticket = new TicketsLigne();
        
        $id_ticket = $ticket->setTkl__tk_id($tickets['tkl__tk_id']);
        if (!$id_ticket instanceof TicketsLigne) 
            return $id_ticket;

        $id_user = $ticket->setTkl__user_id($tickets['tkl__user_id']);
         if (!$id_user instanceof TicketsLigne) 
            return $id_user;

        $id_dest = $ticket->setTkl__user_id_dest($tickets['tkl__user_id_dest']);
            if (!$id_dest instanceof TicketsLigne) 
            return $id_dest;

        return $ticket;
        
    }
}