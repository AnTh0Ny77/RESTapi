<?php

namespace Src\Controllers;

require  '././vendor/autoload.php';

use ReallySimpleJWT\Validate;
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Repository\ClientRepository;
use Src\Controllers\BaseController;
use Src\Repository\UserRepository;
use Src\Entities\Client;
use Src\Entities\TicketsLigne;
use Src\Repository\TicketLigneRepository;
use Src\Repository\LienUserClientRepository;
use Src\Repository\MaterielRepository;
use Src\Repository\TicketRepository;
use Src\Services\Security;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\ClientHtpp;
use GuzzleHttp\Promise;
use ZipArchive;

class ListSocieteController  extends  BaseController{
    public static function path(){
        return '/sossuke';
    }

    public static function renderDoc(){
        $doc = [ [] ];
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
                return $notFound::index();
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

    public static function post(){
      
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $security = new Security();
        $Client = new ClientRepository('client' , $database , Client::class );
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client', $database, User::class);
        $userRepository = new UserRepository('user', $database, User::class);
        $materielRepository = new MaterielRepository('materiel' , $database , Materiel::class );
        $TicketRepository = new TicketRepository('ticket' , $database , Tickets::class );
        
        $security = new Security();
        // $auth = self::Auth($responseHandler, $security);
        // if ($auth != null)
        //     return $auth;

        $body = json_decode(file_get_contents('php://input'), true);
      
        if (empty($body['secret']) && $body['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Opération impossible'
            ], 404, 'bad request');
        }
        if (empty($body['one'])) {
            $list = $Client->findBy([1 => 1 ], 1000 , []);
            $final__array = [];
            foreach($list as $client){
                $userList = $lienUserClientRepository->findBy(['luc__cli__id' =>  $client['cli__id']], 1500 , []);
                $user_final = [];
                foreach ($userList as $key => $value){
                     $use = $userRepository->findOneBy(['user__id' => $value['luc__user__id']] , false );
                     array_push($user_final,$use);
                }
                $client['users'] = $user_final;
                $param = self::renderParam();
                $client['tickets'] = $TicketRepository->search2(['mat__cli__id' =>  [ $client['cli__id'] ] ],'', 100 ,["tk__lu"=>"ASC","tk__id"=>"DESC"],$param);
                $client['materiels'] = $materielRepository->search2([ 'mat__cli__id'  => [$client['cli__id']]  ],'' , 2500 ,  [], []);
                array_push($final__array , $client);
            }
            return $responseHandler->handleJsonResponse([
                'data' => $final__array,
            ], 200,'ok');
        }else {
            $list = $Client->findBy(['cli__id' => $body['one'] ], 1500 , []);
            if (!empty($list)) {
                $final__array = [];
                foreach ($list as $client) {
                    $userList = $lienUserClientRepository->findBy(['luc__cli__id' =>  $client['cli__id']], 1500, []);
                    $user_final = [];
                    foreach ($userList as $key => $value) {
                        $use = $userRepository->findOneBy(['user__id' => $value['luc__user__id']], false);
                        $role = $userRepository->getRoleArray($use);
                        $clients = $lienUserClientRepository->getUserClients($use['user__id']);
                        $use['clients'] = $clients;
                        $use['roles'] = $role;
                        array_push($user_final, $use);
                    }
                    $client['users'] = $user_final;
                    $param = self::renderParam();
                    $client['tickets'] = $TicketRepository->search2(['mat__cli__id' =>  [$client['cli__id']]], '', 100, ["tk__lu" => "ASC", "tk__id" => "DESC"], $param);
                    $client['materiels'] = $materielRepository->search2(['mat__cli__id'  => [$client['cli__id']]], '', 2500,  [], []);
                    array_push($final__array, $client);
                }
                return $responseHandler->handleJsonResponse([
                    'data' => $final__array[0],
                ], 200, 'ok');
            }
            return $responseHandler->handleJsonResponse([
                'data' => [],
            ], 200,'ok');
        }
    }

    public static function renderParam(){
        return  [
            'start' => 'tk__titre',
            'end' => 'cli__ville',
            'self' => [
                'name' => 'ticket' , 
                'alias' => 't',
                'field' => [
                    'tk__id' => 'in' ,
                    'tk__lu' => 'in',
                    'tk__motif' => 'in',
                    'tk__titre' => 'like' , 
                    'tk__groupe' => 'in', 
                ] 
            ],
            'materiel' => [
                'alias' => 'm',
                'type' => 'LEFT',
                'on' => [
                    'mat__id' => 't.tk__motif_id'
                ],
                'field' => [
                    'mat__id' => 'in' ,
                    'mat__cli__id' => 'in' ,
                    'mat__type' => 'like' , 
                    'mat__marque' => 'like', 
                    'mat__model' => 'like', 
                    'mat__pn' => 'like',
                    'mat__sn' => 'like', 
                    'mat__idnec' => 'like'
                ]
            ], 
            'lien_user_client' => [
                'alias' => 'l',
                'type' => 'LEFT',
                'on' => [
                    'luc__cli__id' => 'm.mat__cli__id'
                ],
                'field' => [
                   
                ]
            ], 
            'client' => [
                'alias' => 'c',
                'type' => 'LEFT',
                'on' => [
                    'cli__id' => 'l.luc__cli__id'
                ],
                'field' => [
                    'cli__id' => 'like' ,
                    'cli__nom' => 'like' , 
                    'cli__ville' => 'like'
                ]
            ], 'ticket_ligne' => [
                'alias' => 'y',
                'type' => 'LEFT',
                'on' => [
                    'tkl__tk_id' => 't.tk__id'
                ],
                'field' => [
                    'tkl__user_id_dest' => 'in',
                    'tkl__user_id' => 'in'
                ]
            ], 
        ];
   
}
}
