<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Entities\User;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\BaseController;
use Src\Repository\RefreshRepository;
use Src\Controllers\NotFoundController;
use Src\Repository\LienUserClientRepository;

Class UserController  extends BaseController{

    public static function path(){
        return '/user';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'postUser',
                "tittle" => 'Utilisateurs', 
                'method' => 'POST',
                'path' => self::path(),
                'description' => 'Crée un nouveau User' ,
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                            'user_mail' , 
                            'user__password', 
                            'user__nom' ,
                            'user__prenom'
                    ]
                    ],
                'reponse' =>  'renvoi un objet de type user avec token et refresh_token à conserver' ,
                "Auth" => 'PUBLIC'
                
            ] ,
            [
                'name' => 'getUser',
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet au user d obtenir les information le conçernant' ,
                'reponse' =>  'renvoi un objet de type user ' ,
                "Auth" => 'JWT'
                
            ] ,
            [
                'name' => 'putUser',
                'method' => 'PUT',
                'path' => self::path(),
                'description' => 'Permet à l utilisateur de mettre à jour les information le conçernant' ,
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                            'user__nom' ,
                            'user__prenom' , 
                            'user__service' , 
                            'user__fonction' ,
                            'user__gsm', 
                            'user__tel' 
                    ]
                    ],
                'reponse' =>  'renvoi un objet de type user avec token et refresh_token à conserver' ,
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
                return self::get($data);
                break;

            case 'PUT':
                return self::put($data);
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

	public static function post(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $userRepository = new UserRepository('user' , $database , User::class );
        $refreshRepository = new RefreshRepository($database);
        $body = json_decode(file_get_contents('php://input'), true);
        $user = $userRepository->postUser($body);

        if (!$user instanceof User) {
            return $responseHandler->handleJsonResponse([
                "msg" =>
                    $user
            ]
                , 400 , 'Bad Request');
        }
        $refresh_token = $refreshRepository->insertOne($user->getUser__id());
        $user->setRefresh_token($refresh_token);
        return $responseHandler->handleJsonResponse([
            "data" => $user->getUser__id()
        ] , 201 , 'ok');
    }

    public static function get($data){
        if (empty($data)){
            $database = new Database();
            $database->DbConnect();
           
            $responseHandler = new ResponseHandler();
            $lienUserClientRepository = new LienUserClientRepository('lien_user_client' , $database , User::class );
            $refreshRepository = new RefreshRepository($database);
            $userRepository = new UserRepository('user' , $database , User::class );
            
            $security = new Security();
            $auth = self::Auth($responseHandler,$security);
            if ($auth != null) 
                return $auth;
            
            $user = $userRepository->findOneBy(['user__id' => self::returnId__user($security)['uid']] , true);
            
            $user = $userRepository->getRole($user);
           
            $refresh_token = $refreshRepository->findOneBy(['user__id' => $user->getUser__id()] ,false );
            $user->setRefresh_token($refresh_token['refresh_token']);
           
            $clients = $lienUserClientRepository->getUserClients($user->getUser__id());
            $user->setClients($clients);
            return $responseHandler->handleJsonResponse( [ 
                "data" => $user ]  , 200 , 'ok');
        }
    }

    public static function put(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $userRepository = new UserRepository('user', $database, User::class);
        $refreshRepository = new RefreshRepository($database);
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['user__id'])) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'l id de l utilisateur nest pas renseigné'
            ], 401, 'bad request');
        }

        $user = $userRepository->findOneBy(['user__id' => $body['user__id']], false);
        if (empty($user)) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'l utilisateur nexiste pas'
            ], 401, 'bad request');
        }

        $user = $userRepository->UpdateUser($body);
        if (!$user instanceof User) {
            return $responseHandler->handleJsonResponse([ "msg" => $user],400,'Bad Request');
        }else {
            return $responseHandler->handleJsonResponse([
                "data" => "utilisateur mis a jour avec succès"
            ], 201, 'ok');
        }
        
      
    }

   

}