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
        return '/notifications';
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
                
                if ($value['tk__lu'] == 9) 
                    $cloture ++ ;
                
                if ($value['tk__lu'] == 5) 
                    $en_cours ++;
                
                if ($value['tk__lu'] == 3) {
                    $en_cours ++; $non_lus++;
                }
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
        if (!empty($_GET['user__id'])){
            
            $lienUserClientRepository = new LienUserClientRepository('lien_user_client' , $database , User::class);
            $TicketLigneRepository = new TicketLigneRepository('ticket_ligne' , $database , TicketsLigne::class );
            $TicketRepository = new TicketRepository('ticket' , $database , Tickets::class );
            $id_user = UserController::returnId__user($security)['uid'];
            $user = $userRepository->findOneBy(['user__id' => $id_user] , true);
            $clients = $lienUserClientRepository->getUserClients($user->getUser__id());
            $user->setClients($clients);
            if (empty($user->getClients())) {
                return $responseHandler->handleJsonResponse([
                    'msg' =>  ' L utilisateur na pas de sociétés attribuées'
                ] , 404 , 'bad request');
            }
            $in_clause = [];
            $in_clause['mat__cli__id'] = [];
            foreach ($user->getClients() as  $client){
                array_push($in_clause['mat__cli__id'] , $client->cli__id);
            }
            
            $request = $TicketRepository->search($in_clause, null , 100 ,[ "tk__lu" => "ASC" , "tk__id" => "DESC"],[]);
            $array_format_for_response = [];
            foreach ($request as $results){
                $ticket = $TicketRepository->findOneBy(['tk__id' => $results['tk__id']] , false);
                $lignes = $TicketLigneRepository->findBy(['tkl__tk_id' => $results['tk__id']] , 100 , ['tkl__dt' => 'ASC']);
                $array_lines = [];
                foreach ($lignes as $key  => $result) {
                    if ($key === array_key_last($lignes)) {
                        $ticket['encours'] = $userRepository->findOneBy(['user__id' => $result['tkl__user_id_dest'] ],false);
                    }
                    array_push($array_lines ,  $result);
                }
                $ticket['lignes'] = $array_lines;
                array_push($array_format_for_response , $ticket); 
            }
         
            $response = [
                'total' => 0,
                'cloture' => 0 ,
                'encours' => 0 ,
                'nonlus' => 0
            ];
            foreach ($array_format_for_response as $key => $value) {
               
                switch ($value['tk__lu']) {
                    case 3:
                        $response['total'] ++ ;
                        $response['encours'] ++ ;
                        if (intval($_GET['user__id']) == $user->getUser__id() ) {
                            $response['nonlus'] ++ ;
                        }
                        break;
                    case 5:
                        $response['total'] ++ ;
                        $response['encours'] ++ ;
                        break;
                    case 9:
                        $response['total'] ++ ;
                        $response['cloture'] ++ ;
                        break;
                }
               
            }
            return $responseHandler->handleJsonResponse([
                'data' =>  $response
            ] , 200 , 'ok');

        }
       
    }
}