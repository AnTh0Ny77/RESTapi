<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
use Src\Entities\TicketsLigne;
use Src\Entities\TicketLigneChamp;
use Src\Services\ResponseHandler;

Class TicketLigneChampRepository  extends BaseRepository {


    public function checkTicket($tickets){
       
        $ticket = new TicketLigneChamp();
       
        $id_ticket = $ticket->setTklc__id($tickets['tklc__id']);
        if (!$id_ticket instanceof TicketLigneChamp) 
            return $id_ticket;

        $id_ticket = $ticket->setTklc__nom_champ($tickets['tklc__nom_champ']);
        if (!$id_ticket instanceof TicketLigneChamp) 
            return $id_ticket;

        return $ticket;
        
    }
}