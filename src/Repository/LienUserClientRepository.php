<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Repository\ClientRepository;
use Src\Entities\Client;
use Src\Services\ResponseHandler;

Class LienUserClientRepository  extends BaseRepository {

    public function getUserClients($user__id){
        $clientRepository = new ClientRepository('client' , $this->Db , Client::class );
       
        $clients = $this->findBy(['luc__user__id' => $user__id ], 50 , [ 'luc__order' => 'ASC'] );
       
        $responses = [];
        foreach ($clients as $key => $value) {
            $value = $clientRepository->findOneBy(['cli__id' => $value['luc__cli__id']] ,true);
            array_push($responses , $value);
        }
        return $responses;
    }
}