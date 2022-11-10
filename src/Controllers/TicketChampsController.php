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


Class TicketChampsController extends BaseController {

    public static function path(){
        return '/ticketchamps';
    }

    public static function renderDoc(){
        $doc = [
            [
                'name' => 'postTicketsLigneChamps',
                "tittle" => 'Ticket Ligne champs', 
                'method' => 'POST',
                'path' => self::path(),
                'description' => 'Permet de creer une ligne de champ  pour une ligne de ticket.',
                'reponse' => 'renvoi un message de succes avec l id du champ', 
                "Auth" => 'JWT'
             ]
        ];
        return $doc;
    }

	public static function index($method , $data){
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
        $sossuke = new Sossuke();
        $sossuke->DbConnect();
        $responseHandler = new ResponseHandler();
        $TicketLigneRepository = new TicketLigneChampRepository('ticket_ligne_champ' , $database , TicketsLigneChamp::class );
        $TicketLigneSossukeRepository = new TicketLigneChampRepository('ticket_ligne_champ' , $database , TicketsLigneChamp::class );
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
        if(empty($body)){
            return $responseHandler->handleJsonResponse([
                'msg' => 'le body ne peut pas etre vide'
            ] , 401 , 'bad request');
        } 
        
        $check = $TicketLigneRepository->checkTicket($body);
        
        if (!$check instanceof TicketLigneChamp){
            return $responseHandler->handleJsonResponse([
                'msg' => $check
            ] , 401 , 'bad request');
        }

        $id_new_ticket_ligne = $TicketLigneSossukeRepository->insert($body);
        $TicketLigneRepository->insert($body);
        
        $verify = $TicketLigneSossukeRepository->findOneBy(array('tklc__id' => $id_new_ticket_ligne ) , true);
        if (!$verify instanceof TicketLigneChamp) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'Un probleme est survenu durant la creation dans la base de donnée sossuke'
            ] , 500 , 'bad request');
        }
    }
}