<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Sossuke;
use Src\Entities\User;
use Src\Entities\Client;
use Src\Entities\Materiel;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\UserController;
use Src\Entities\Tickets;
use Src\Repository\TicketRepository;
Use Src\Entities\TicketsLigne;
Use Src\Repository\TicketLigneRepository;
use Src\Entities\TicketLigneChamp;
use Src\Repository\TicketLigneChampRepository;
use Src\Repository\LienUserClientRepository;


Class NotificationsController extends BaseController {

    public static function path(){
        return '/notifiations';
    }

    public static function renderDoc(){
        $doc = [
            [
                'name' => 'GetCount',
                "tittle" => 'Notifications', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de récupérer les notification liées au tickets',
                'reponse' => 'renvoi le nombre demandé dans la variable data', 
                "Auth" => 'JWT'
             ]
        ];
        return $doc;
    }

	public static function index($method , $data){
        $notFound = new NotFoundController();
        switch ($method) {
            case 'POST':
                return $notFound::index();
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
        $sossuke = new Sossuke();
        $sossuke->DbConnect();
        $responseHandler = new ResponseHandler();
        $userRepository = new UserRepository('user' , $database , User::class );
        $security = new Security();
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;

        if(empty($_GET)){
            return $responseHandler->handleJsonResponse([
                'msg' => 'GET ne peut pas etre vide'
            ] , 401 , 'bad request');
        } 

        //si un id de matériel à été passé :
        if(!empty($_GET['mat__id'])){

            $TicketRepository = new TicketRepository('ticket', $database , Tickets::class);

            $tickets_list = $TicketRepository->findBy(['tk__motif_id' =>  $_GET['mat__id']] , 10000 , []);

            $total = count($tickets_list);
            $cloture = 0 ;
            $en_cours = 0 ;
            $non_lus = 0 ;
            foreach ($tickets_list as  $value) {
                
                if ($value['tk__lu'] == 2) 
                    $cloture ++ ;
                
                if ($value['tk__lu'] == 1) 
                    $en_cours ++;
                
                if ($value['tk__lu'] == 0) 
                    $en_cours ++; $non_lus++;
                
            }

            $response = [
                'total' => $total ,
                'cloture' => $cloture,
                'encours' => $en_cours,
                'nonLus' => $non_lus
            ];

            return $responseHandler->handleJsonResponse([
                'data' => $response
            ] , 200 , 'bad request');
        } 
        //si un id utilisateur à été passé :
       
    }
}