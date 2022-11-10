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
use Src\Entities\TicketsLigneChamp;
use Src\Repository\TicketLigneChampRepository;
use Src\Repository\LienUserClientRepository;


Class MaxController extends BaseController {

    public static function path(){
        return '/max';
    }

    public static function renderDoc(){
        $doc = [
            [
                'name' => 'MaxTicket',
                "tittle" => 'Ticket Max', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Indique le groupe de ticket maximum present dans sossuke.',
                'reponse' => 'renvoi un message de succes avec le numero de groupe', 
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
        $sossuke = new Sossuke();
        $sossuke->DbConnect();
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $TicketRepository = new TicketRepository('ticket' , $sossuke , Tickets::class );
        $security = new Security();
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;

        $max = $TicketRepository->max();
        return $responseHandler->handleJsonResponse([
            'data' => $max
        ] , 200 , 'ok');
        
    }


}