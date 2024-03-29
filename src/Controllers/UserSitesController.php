<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Entities\Materiel;
use Src\Repository\BaseRepository;
use Src\Entities\User;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\BaseController;
use Src\Repository\RefreshRepository;
use Src\Controllers\NotFoundController;
use Src\Repository\ClientRepository;
use Src\Repository\LienUserClientRepository;



Class UserSitesController extends BaseController {

    public static function path(){
        return '/usersites';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'getuserSites',
                "tittle" => 'User Sites', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de consulter une liste de user liées au sites du User connecté', 
                "Auth" => 'JWT'
             ]
        ];
        return $doc;
    }

	public static function index($method , $data){
        $notFound = new NotFoundController();
        switch ($method) {
            case 'POST':
                return self::post($data);
                break;

            case 'GET':
                return self::get($data);
                break;

            case 'PUT':
                return $notFound::index();
                break;

            case 'DELETE':
                return self::delete();
                break;

            default:
                return $notFound::index();
                break;
        }
    }

    public static function returnId__user(Security $security){
        $token = $security->getBearerToken();
        return $security->readToken($token);
    }

    public static function get(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client' , $database , User::class );
        $userRepository = new UserRepository('user' , $database , User::class );
        
        $security = new Security();
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;
    
        $user = $userRepository->findOneBy(['user__id' => self::returnId__user($security)['uid']] , true);
        $user = $userRepository->getRole($user);
        $clients = $lienUserClientRepository->getUserClients($user->getUser__id());
        $user->setClients($clients);
        $array_user =  [] ;
        foreach($user->getClients() as $client){

            $array_links = $lienUserClientRepository->findBy(['luc__cli__id' => $client->getCli__id() ],1000, []);
            foreach ($array_links as $match){
                array_push($array_user ,  $match['luc__user__id']);
            }
        }
        
        $array_user = array_unique($array_user);
        $definitve_array = [];
        foreach ($array_user as $users){
            $subject = $userRepository->findOneBy(['user__id' => $users] , true);
            $clients = $lienUserClientRepository->get2array($users);
            $subject->setClients($clients);
            $subject = $userRepository->getRole($subject);
            array_push($definitve_array , (array ) $subject );
        }
        $prenom  = array_column($definitve_array, strtolower('user__prenom'));
        $nom = array_column($definitve_array, strtolower('user__nom'));
        array_multisort( $nom, SORT_STRING, $prenom, SORT_STRING, $definitve_array);
        return $responseHandler->handleJsonResponse( [ 
            "data" => $definitve_array ]  , 200 , 'ok');

    }


    public static function delete(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client', $database, User::class);
        $userRepository = new UserRepository('user', $database, User::class);
        $clientRepository = new ClientRepository('client', $database, Client::class);

        $security = new Security();
        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;

        $body = json_decode(file_get_contents('php://input'), true);
        if (empty($body['luc__user__id'])) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'user__id n est pas renseigné',
            ], 401, 'bad request');
        }

        $user = $userRepository->findOneBy(['user__id' =>  $body['luc__user__id']], false);

        if (empty($user)) {
            if (empty($body['luc__user__id'])) {
                return $responseHandler->handleJsonResponse([
                    "msg" => 'le user nexiste pas !',
                ], 401, 'bad request');
            }
        }

        $lienUserClientRepository->delete(['luc__user__id' =>  $body['luc__user__id'] ]);
        return $responseHandler->handleJsonResponse([
            "data" => 'les liens ont été supprimés !',
        ], 200, 'bad request');
    }


    public static function post()
    {
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client', $database, User::class);
        $userRepository = new UserRepository('user', $database, User::class);
        $clientRepository = new ClientRepository('client' , $database , Client::class);

        $security = new Security();
        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;

        $body = json_decode(file_get_contents('php://input'), true);
        if (empty($body['luc__user__id'])) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'user__id n est pas renseigné', 
            ], 401, 'bad request');
        }

        $user = $userRepository->findOneBy(['user__id' =>  $body['luc__user__id']], false);

        if (empty($user)) {
            if (empty($body['luc__user__id'])) {
                return $responseHandler->handleJsonResponse([
                    "msg" => 'le user nexiste pas !',
                ], 401, 'bad request');
            }
        }


        if (empty($body['luc__cli__id'])) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'cli__id n est pas renseigné',
            ], 401, 'bad request');
        }

        $client = $clientRepository->findOneBy(['cli__id' =>  $body['luc__cli__id']], false);

        if (empty($client)) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'La société n existe pas',
            ], 401, 'bad request');
        }

        if (empty($body['luc__order'])) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'order n est pas renseigné',
            ], 401, 'bad request');
        }

        if (isset($body['luc__cata'])) { $cata = $body['luc__cata'];}else{$cata = 1;}
        if (isset($body['luc__parc'])) { $parc = $body['luc__parc']; }else{$parc = 1;}

        $data = [
            'luc__user__id' => $body['luc__user__id'] , 
            'luc__cli__id' => $body['luc__cli__id'] , 
            'luc__order' => $body['luc__order'] , 
            'luc__parc' => $parc , 
            'luc__cata' => $cata
        ];

        $lienUserClientRepository->insertNoPrimary($data);

        $data = 'opération effectué avec succès';

        return $responseHandler->handleJsonResponse([
            "data" => $data
        ], 200, 'ok');
    }

}