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


Class TicketController extends BaseController {

    public static function path(){
        return '/ticket';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'getTickets',
                "tittle" => 'Ticket', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de consulter une liste de tickets, 
                 le parametre "search" peut etre précisé afin d effectuer une recherche.',
                'reponse' => 'renvoi un tableau de tableau de type ticket', 
                "Auth" => 'JWT'
             ],[
                'name' => 'postTickets',
                'method' => 'POST',
                'path' => self::path(),
                'description' => 'Permet de creer un ticket.',
                'reponse' => 'renvoi un message de succes avec l id du ticket', 
                "Auth" => 'JWT'
             ]
        ];
        return $doc;
    }

	public static function index($method , $data){
        $notFound = new NotFoundController();
        switch ($method) {
            case 'POST':
                return self::post();;
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
        $TicketRepository = new TicketRepository('ticket' , $database , Tickets::class );
        $TicketLigneRepository = new TicketLigneRepository('ticket_ligne' , $database , TicketsLigne::class );
        $TicketLigneChampRepository = new TicketLigneChampRepository('ticket_ligne_champ' , $database , TicketsLigneChamp::class );
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client' , $database , User::class );
        $userRepository = new UserRepository('user' , $database , User::class );
        $security = new Security();
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;
        $id_user = UserController::returnId__user($security)['uid'];
        $user = $userRepository->findOneBy(['user__id' => $id_user] , true);
        $clients = $lienUserClientRepository->getUserClients($user->getUser__id());
        $user->setClients($clients);
        $request = $TicketRepository->search([], 'google' , 100 ,["tk__id" => "ASC"],[]);
        $array_format_for_response = [];
        
        foreach ($request as $results){
            $ticket = $TicketRepository->findOneBy(['tk__id' => $results['tk__id']] , false);
            array_push($array_format_for_response , $ticket); 
        }

        return $responseHandler->handleJsonResponse([
            'data' => $array_format_for_response
        ] , 200 , 'bad request');
       

    }

    public static function post(){
        $database = new Database();
        $database->DbConnect();
        $sossuke = new Sossuke();
        $sossuke->DbConnect();
        $responseHandler = new ResponseHandler();
        $TicketRepository = new TicketRepository('ticket' , $database , Tickets::class );
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client' , $database , User::class );
        $userRepository = new UserRepository('user' , $database , User::class );
        $security = new Security();
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;
        $id_user = UserController::returnId__user($security)['uid'];
        $user = $userRepository->findOneBy(['user__id' => $id_user] , true);
        $clients = $lienUserClientRepository->getUserClients($user->getUser__id());
        $user->setClients($clients);

        $body = json_decode(file_get_contents('php://input'), true);
        
        if (empty($body)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'le body ne peut pas etre vide'
            ] , 401 , 'bad request');
        } 
      
        $TicketRepositorySossuke = new TicketRepository('ticket' , $sossuke , Tickets::class );
        $new_ticket = $TicketRepositorySossuke->checkTicket($body);
        if (!$new_ticket instanceof Tickets) {
            return $responseHandler->handleJsonResponse([
                'msg' => $new_ticket
            ] , 401 , 'bad request');
        }
    
        $id_new_ticket = $TicketRepositorySossuke->insert($body);
        $verify = $TicketRepositorySossuke->findOneBy(array('tk__id' => $id_new_ticket ) , true);
       
        if (!$verify instanceof Tickets) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'un problemene est survenu durant la creation du ticket dans la base de donnée sossuke'
            ] , 500 , 'internal server error');
        }
        $new_ticket->setTk__id($id_new_ticket);
        $body['tk__id'] = $id_new_ticket;
        $myRecode_ticket_id = $TicketRepository->insert($body);
        
        $new_ticket = $TicketRepository->findOneBy(array('tk__id' => $id_new_ticket) , true);
        if (!$new_ticket instanceof Tickets) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'un problemene est survenu durant la creation du ticket'
            ] , 500 , 'internal server error');
        }

        return $responseHandler->handleJsonResponse([
            'data' => [ 'tk__id' => $id_new_ticket]
        ] , 201 , 'ressource created');

    }



}