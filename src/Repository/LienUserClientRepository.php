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

    public function getUserClientsFLM($user__id){
        $clientRepository = new ClientRepository('client' , $this->Db , Client::class );
        $clients = $this->findBy(['luc__user__id' => $user__id ], 50 , [ 'luc__order' => 'ASC'] );
        return $clients;
    }


    public function getLucOrder1($user__id){
        $clientRepository = new ClientRepository('client' , $this->Db , Client::class );
        $client = $this->findOneBy(['luc__user__id' => $user__id  , 'luc__order' => 1 ], false );
        return $client;
    }

    public function get2array($user__id){

        $clientRepository = new ClientRepository('client' , $this->Db , Client::class );
        $clients = $this->findBy(['luc__user__id' => $user__id   ], 50 , [ 'luc__order' => 'ASC'] );
        $response = [];
        foreach ($clients as  $value) {
            $temp = $clientRepository->findOneBy(['cli__id' => $value['luc__cli__id']] ,false);
           
            if (!empty($temp)) {
                $temp['luc__cata'] = $value['luc__cata'];
                $temp['luc__order'] = $value['luc__order'];
                $temp['luc__parc'] = $value['luc__parc'];
                array_push($response , $temp);
            }
        }
        return $response;
    }

    public function getUserClientsArray($user__id){
        $clientRepository = new ClientRepository('client' , $this->Db , Client::class );
        $clients = $this->findBy(['luc__user__id' => $user__id ], 50 , [ 'luc__order' => 'ASC'] );
        $responses = [];
        foreach ($clients as $key => $value) {
            $rep = $clientRepository->findOneBy(['cli__id' => $value['luc__cli__id']] ,false);
            $rep['luc__parc'] = $value['luc__parc'];
            $rep['luc__cata'] = $value['luc__cata'];
            array_push($responses , $rep);
        }
        return $responses;
    }

    public function updateLink($luc__parc , $luc__user__id , $luc__cli__id){
        $data = [
            'parc' => $luc__parc, 
            'user' => $luc__user__id , 
            'client' => $luc__cli__id
        ];
        $request = $this->Db->Pdo->prepare("UPDATE lien_user_client SET luc__parc = :parc WHERE luc__user__id = :user AND luc__cli__id = :client ");
        return $request->execute($data);
    }

    public function insertIfNotExist($user_id, $client_ids) {
        // Vérifier si des enregistrements correspondent déjà aux conditions données
        $query = $this->Db->Pdo->prepare("SELECT COUNT(*) AS count FROM lien_user_client WHERE luc__user__id = :user_id AND luc__cli__id =  :luc__cli__id");
        $query->bindParam(':user_id', $user_id);
        $query->bindParam(':luc__cli__id', $client_ids);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
       
        // Si aucun enregistrement n'est trouvé, insérer les données
        if ($result['count'] == 0) {
            $data = [
                'luc__user__id' => $user_id , 
                'luc__cli__id' => $client_ids , 
                'luc__cata' => 0 , 
                'luc__parc' => 1
            ];
            $this->insertNoPrimary($data);

            return true;
        }
        
        return false;
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

    
    public function DeleteUselessLinks($user__id) {
        $request = $this->Db->Pdo->prepare("DELETE FROM lien_user_client 
        WHERE luc__parc = 0 AND luc__cata = 0 AND luc__user__id = :user_id");
        $request->bindParam(':user_id', $user__id);
        return $request->execute();
    }
    
}