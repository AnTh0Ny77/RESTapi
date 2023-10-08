<?php
namespace Src\Repository;
require  '././vendor/autoload.php';

use DateTime;
use Src\Database;
use PDO;
use Src\Repository\BaseRepository;
use Src\Repository\ClientRepository;
use Src\Repository\PromoRepository;
use Src\Repository\LienUserClientRepository;
use Src\Entities\Promo;
use Src\Entities\Client;
use Src\Services\ResponseHandler;

Class LienClientPromoRepository  extends BaseRepository {

    public function getPromoUser($user__id){
        $clientRepository = new ClientRepository('client' , $this->Db , Client::class );
        $lientUserClientRepository = new LienUserClientRepository('lien_user_client' , $this->Db, Client::class );
        $promoRepository = new PromoRepository('promo' , $this->Db, Promo::class);
        $clients = $lientUserClientRepository->getUserClients($user__id);

        $in_clause = ' ( ';

        foreach ($clients as $key =>  $value) {
            if ($key === array_key_last($clients)) {
                $in_clause .=  $value->getCli__id() . ' ) ';
            }else{
                $in_clause .=  $value->getCli__id() . ' , ';
            }
        }

        $request = "SELECT DISTINCT * FROM lien_client_promo WHERE 1 = 1 AND lcp__cli__id IN ".$in_clause." ";
        $request = $this->Db->Pdo->query($request);
        $request = $request->fetchAll(PDO::FETCH_ASSOC);
        return $request;
    }

    public function getClientAdds($ad__id){
        $request = "SELECT *  FROM lien_client_promo WHERE 1 = 1 AND lcp__ad__id  = ".$ad__id." ";
        $request = $this->Db->Pdo->query($request);
        $request = $request->fetchAll(PDO::FETCH_ASSOC);
        return $request;
    }
   

    public function getPromoClient($client__id){
        $request = "SELECT DISTINCT *  FROM lien_client_promo WHERE 1 = 1 AND lcp__cli__id  = ".$client__id." ";
        $request = $this->Db->Pdo->query($request);
        $request = $request->fetchAll(PDO::FETCH_ASSOC);
        return $request;
    }

   
}