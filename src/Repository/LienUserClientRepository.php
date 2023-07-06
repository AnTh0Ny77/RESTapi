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
            // $value->luc__cata = $value['luc__parc'];
            array_push($responses , $value);
        }
        return $responses;
    }

    public function updateLink(array $luc){
        $request = $this->Db->Pdo->prepare('UPDATE lien_user_client SET luc__parc = '. $luc['luc__parc'] .' WHERE luc__user__id = '. $luc['luc__user__id'] .' AND luc__cli__id = '.  $luc['luc__cli__id'] .' ');
        $request->execute();
        return true ;
    }

    public function getUserClientsParc($user__id){
        $clientRepository = new ClientRepository('client' , $this->Db , Client::class );
        $clients = $this->findBy(['luc__user__id' => $user__id ], 50 , [ 'luc__order' => 'ASC'] );
        $responses = [];
        foreach ($clients as $key => $value) {
            if ($value['luc__parc'] == 1 ) {
                $val = $clientRepository->findOneBy(['cli__id' => $value['luc__cli__id']] ,true);
                array_push($responses , $val);
            }
        }
        return $responses;
    }
}