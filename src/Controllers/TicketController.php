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
use Src\Repository\ClientRepository;
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
                return self::put();
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
        $clientRep = new ClientRepository('client' , $database , Client::class);
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
        $list_client = [];
        
        ////////////////////////////// traitrement des variable de recherche à inserer dans la fonction : 
        //textuelle: 
        
        $search = '';
        if (!empty($_GET['search'])) 
            $search = $_GET['search'];
        //clause in:
        $in_clause = [];
        
        //////////////recupère les clients liés au users: 
        $in_clause['mat__cli__id'] = [];
        if (empty($_GET['RECODE__PASS'])) {
            if (empty($user->getClients())) {
                return $responseHandler->handleJsonResponse([
                    'msg' =>  ' L utilisateur na pas de sociétés attribuées'
                ] , 404 , 'bad request');
            }
        }
        
        foreach ($user->getClients() as  $clients) {
           array_push($in_clause['mat__cli__id'] , $clients->getCli__id());
        }
        
        if (!empty($_GET['RECODE__PASS'])) {
                if ($_GET['RECODE__PASS'] == 'secret') {
                    $list_client = $clientRep->returnIdList();
                    foreach ( $list_client as $value) {
                        array_push($in_clause['mat__cli__id'] , $value['cli__id']);
                    }
                }
        }

        if (!empty($_GET['tkl__user_id'])) {
            $in_clause['tkl__user_id'] = [];
            foreach ($_GET['tkl__user_id'] as $key => $value) {
                array_push($in_clause['tkl__user_id'] , $value);
            }
        }
       
        if (!empty($_GET['tkl__user_id_dest'])) {
            $in_clause['tkl__user_id_dest'] = [];
            foreach ($_GET['tkl__user_id_dest'] as $key => $value) {
                array_push($in_clause['tkl__user_id_dest'] , $value);
            }
        }
        if (!empty($_GET['tk__groupe'])){
            $in_clause['tk__groupe'] = [];
            foreach ($_GET['tk__groupe'] as $key => $value) {
                array_push($in_clause['tk__groupe'] , $value);
            }
        }
        if (!empty($_GET['tk__id'])){
            $in_clause['tk__id'] = [];
            foreach ($_GET['tk__id'] as $key => $value) {
                array_push($in_clause['tk__id'] , $value);
            }
        }
        if (!empty($_GET['tk__motif'])) {
            $in_clause['tk__motif'] = [];
            foreach ($_GET['tk__motif'] as $key => $value) {
                array_push($in_clause['tk__motif'] , $value);
            }
        }
        if (!empty($_GET['tk__lu'])) {
            $in_clause['tk__lu'] = [];
            foreach ($_GET['tk__lu'] as $key => $value) {
                array_push($in_clause['tk__lu'] , $value);
            }
        }

        if (!empty($_GET['mat__id'])) {
            $in_clause['mat__id'] = [];
            foreach ($_GET['mat__id'] as $key => $value) {
                array_push($in_clause['mat__id'] , $value);
            }
        }
        
        $param = self::renderParam();
        //////////////////////////////////////////////////////////////////////////////////////////////////////////
        $request = $TicketRepository->search2($in_clause, $search , 100 ,[ "tk__lu" => "ASC","tk__id" =>"DESC"],$param);
        /////////////////////////////////////////////////////////////////////////////////////////////////////////
        
        ///////////////////////////////// format de la réponse avec toutes les infos utiles: /////////////////////
        $array_format_for_response = [];
        foreach ($request as $results){
            $ticket = $TicketRepository->findOneBy(['tk__id' => $results['tk__id']] , false);
            $lignes = $TicketLigneRepository->findBy(['tkl__tk_id' => $results['tk__id']] , 100 , ['tkl__dt' => 'ASC']);
            $array_lines = [];
            foreach ($lignes as $result) {
              
                $result['tkl__user_id'] = $userRepository->findOneBy(['user__id' => $result['tkl__user_id'] ],false);
                $result['tkl__user_id_dest'] = $userRepository->findOneBy(['user__id' => $result['tkl__user_id_dest'] ],false);
                $result['tkl__user_id_dest']['user__password'] = null;
                $result['tkl__user_id']['user__password'] = null;
                $result['champs'] = $TicketLigneChampRepository->findBy(['tklc__id' => $result['tkl__id']] , 100 , [ 'tklc__ordre' => 'ASC']);
                $result['files'] = []; 
                // if (is_dir('public/img/tickets/'. $result['tkl__id'])){
                //     $scanned_directory = array_diff(scandir('public/img/tickets/'. $result['tkl__id']), array('..', '.'));
                //     $result['files'] = $scanned_directory;
                // }
                array_push($array_lines ,  $result);
            }
            $ticket['lignes'] = $array_lines;
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


    public static function put(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $TicketRepository = new TicketRepository('ticket' , $database , Tickets::class );
        $sossuke = new Sossuke();
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
        $TicketRepositorySossuke = new TicketRepository('ticket' , $sossuke , Tickets::class );
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'le body ne peut pas etre vide'
            ] , 401 , 'bad request');
        } 

        if(empty($body['tk__id'])){
            return $responseHandler->handleJsonResponse([
                'msg' => 'l identifiant du ticket n est pas renseigné'
            ] , 401 , 'bad request');
        }

        $ticket_check = $TicketRepository->findOneBy(['tk__id' => $body['tk__id']] , true);
        if (!$ticket_check  instanceof Tickets) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'ticket inconnu'
            ] , 401 , 'bad request');
        }

        if (!empty($body['tk__lu'])) {
            if (intval($body['tk__lu']) != 3 and intval($body['tk__lu']) != 5 and intval($body['tk__lu']) != 9 ) {
                return $responseHandler->handleJsonResponse([
                    'msg' => 'la valeur de tk__lu n est pas conforme '
                ] , 401 , 'bad request');
            }
            $ticket_check->setTk__lu($body['tk__lu']);
        }

        if (!empty($body['tk__titre'])) {
            if ( strlen($body['tk__titre'] < 2 and strlen($body['tk__titre'] > 250) ) ){
                return $responseHandler->handleJsonResponse([
                    'msg' => 'le titre demandé n est pas conforme '
                ] , 401 , 'bad request');
            }
            $ticket_check->setTk__titre($body['tk__titre']);
        }

        $update = $TicketRepository->update(( array) $ticket_check);

        return $responseHandler->handleJsonResponse([
            'data' => 'le ticket a été mis a jours '
        ] , 200 , 'ok ');

    }



}