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


Class TicketLigneController extends BaseController {

    public static function path(){
        return '/ticketligne';
    }

    public static function renderDoc(){
        $doc = [
            [
                'name' => 'postTicketsLigne',
                "tittle" => 'Ticket Ligne', 
                'method' => 'POST',
                'path' => self::path(),
                'description' => 'Permet de creer une ligne de ticket.',
                'reponse' => 'renvoi un message de succes avec l id de la ligne', 
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
        $tick = new TicketRepository('ticket' , $database , Tickets::class);
        $tickSossuke = new TicketRepository('ticket' , $sossuke , Tickets::class);
        $TicketLigneRepository = new TicketLigneRepository('ticket_ligne' , $database , TicketsLigne::class );
        $TicketLigneSossukeRepository = new TicketLigneRepository('ticket_ligne' , $sossuke , TicketsLigne::class );
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
       
        $check = $TicketLigneRepository->checkTicket($body);
        if (!$check instanceof TicketsLigne) {
            return $responseHandler->handleJsonResponse([
                'msg' => $check
            ] , 401 , 'bad request');
        }
        $body['tkl__dt'] = date('Y-m-d H:i:s');
        $tick->update(['tk__id' => $body['tkl__tk_id'] , 'tk__lu' => 3 ]);
        $tickSossuke->update(['tk__id' => $body['tkl__tk_id'] , 'tk__lu' => 3 ]);
        if ($body['tkl__motif_ligne'] === 'CLO') {
            $tick->update(['tk__id' => $body['tkl__tk_id'] , 'tk__lu' => 9 ]);
            $tickSossuke->update(['tk__id' => $body['tkl__tk_id'] , 'tk__lu' => 9 ]);
        }
        $id_new_ticket_ligne = $TicketLigneSossukeRepository->insert($body);
        $verify = $TicketLigneSossukeRepository->findOneBy(array('tkl__id' => $id_new_ticket_ligne ),true);
        if (!$verify instanceof TicketsLigne) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'Un probleme est survenu durant la creation dans la base de donnée sossuke'
            ] , 500 , 'bad request');
        }
        $body['tkl__id'] = $id_new_ticket_ligne ;
        $id_ligne_myrecode = $TicketLigneRepository->insert($body);
        $verify = $TicketLigneRepository->findOneBy(array('tkl__id' => $id_new_ticket_ligne ) , true);
        if (!$verify instanceof TicketsLigne) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'Un probleme est survenu durant la creation dans la base de donnée sossuke'
            ] , 500 , 'bad request');
        }

        return $responseHandler->handleJsonResponse([
            'data' => [ 'tkl__id' => $id_new_ticket_ligne]
        ] , 201 , 'ressource created');
    }

}