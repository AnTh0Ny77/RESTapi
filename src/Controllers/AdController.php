<?php

namespace Src\Controllers;

require  '././vendor/autoload.php';

use ReallySimpleJWT\Validate;
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Repository\ClientRepository;
use Src\Controllers\BaseController;
use Src\Repository\UserRepository;
use Src\Repository\BaseRepository;
use Src\Repository\LienClientPromoRepository;
use Src\Entities\Promo;
use Src\Entities\Client;
use Src\Entities\TicketsLigne;
use Src\Repository\TicketLigneRepository;
use Src\Services\Security;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\ClientHtpp;
use GuzzleHttp\Promise;

class AdController  extends  BaseController
{

    public static function path()
    {
        return '/add';
    }

    public static function renderDoc()
    {
        $doc = [
             [
                'name' => 'addGet',
                'tittle' => ' récupère les pub', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet d optenir la list des publicités ',
                'reponse' => 'renvoi la variable data ou msg en cas d echec',
                "Auth" => 'JWT '
            ]
        ];
        return $doc;
    }

    public static function index($method, $data)
    {
        $notFound = new NotFoundController();
        switch ($method) {
            case 'POST':
                return self::post();
                break;

            case 'GET':
                return self::get();
                break;

            case 'PUT':
                return $notFound::index();
                break;

            case 'DELETE':
                return $notFound::index();
                break;

            default:
                return $notFound::index();
                break;
        }
    }

    public static function get(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $security = new Security();
        $clientRepository = new ClientRepository('client' , $database , Client::class );
        $addrepository = new BaseRepository('promo' , $database ,  Client::class);
        $lienClientpromo = new  LienClientPromoRepository('lien_client_promo' , $database , Client::class);
        $security = new Security();
        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;


        if (!empty($_GET['cli__id'])) {
            $list = $lienClientpromo->getPromoClient($_GET['cli__id']);
            $data = [];
            foreach ($list as $key => $value){
                $temp = $addrepository->findOneBy(['ad__id' => $value['lcp__ad__id']] , false );
                array_push($data,$temp);
            }
            return $responseHandler->handleJsonResponse([
                'data' =>  $data,
            ], 200, "ok");
        }

        if (!empty($_GET['all']) and  $_GET['all'] == "vgvhnoza7875z85acc114cz5"){
            $list = $addrepository->getAllAdd();
            $definitive_array = [];
            foreach ($list as  $value) {
                $relations = $lienClientpromo->getClientAdds($value['ad__id']);
                $array_client = [];
                foreach ($relations as $client) {
                    $results = $clientRepository->findOneBy(['cli__id' => $client['lcp__cli__id']] , false);
                    array_push($array_client , $results);
                }

                $temp = [
                    'relation' => $array_client , 
                    'ad__titre' => $value['ad__titre'],
                    'ad__lien' => $value['ad__lien'],
                    'ad__txt' => $value['ad__txt'] , 
                    'ad__img' => $value['ad__img']

                ];
                array_push($definitive_array , $temp);
                
            }
            return $responseHandler->handleJsonResponse([
                'data' =>   $definitive_array,
            ], 200, "ok");
        }

        $list = $addrepository->findRandom();
        return $responseHandler->handleJsonResponse([
            'data' =>  $list,
        ], 200, "ok");
    }


    public static function post(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $security = new Security();
        $addrepository = new BaseRepository('promo' , $database ,  Client::class);
        $lienClientpromo = new  LienClientPromoRepository('lien_client_promo' , $database , Client::class);
        $security = new Security();
        
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['secret']) && $body['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Opération impossible'
            ], 404, 'bad request');
        }

        if (!empty($body['ad__titre'])) {
            
            $new_add = [
                'ad__titre' => $body['ad__titre'] , 
                'ad__lien' => $body['ad__lien'] , 
                'ad__txt' => $body['ad__txt'] , 
                'ad__img' => $body['ad__img']
            ];

            $id = $addrepository->insert($new_add);

            if (!empty($body['relation'])) {
                foreach ($body['relation'] as $value){
                    $relation = [
                        'lcp__cli__id' => $value , 
                        'lcp__ad__id' => $id
                    ];
                    $lienClientpromo->insert($relation);
                }
            }
        }   
            
        
        return $responseHandler->handleJsonResponse([
            'data' =>  'OKKKKK',
        ], 200, "ok");
    }
}
