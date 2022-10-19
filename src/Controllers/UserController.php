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
        $userRepository = new UserRepository('User' , $database , User::class );
        $refreshRepository = new RefreshRepository($database);
        $body = json_decode(file_get_contents('php://input'), true);

        
        $user = $userRepository->postUser($body);

        if (!$user instanceof User) {
            $body = [
                $data = $body ,
                $message = $user
            ];
            return $responseHandler->handleJsonResponse($body , 400 , 'Bad Request');
        }
        $refresh_token = $refreshRepository->insertOne($user->getUser__id());
        $user->setRefresh_token($refresh_token);
        $body = [
            $data = $user ,
            $message = 'utilisateur créé avec succès'
        ];
        
        return $responseHandler->handleJsonResponse($body , 201 , 'ok');
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
           

            $body = [
                $data = $user 
            ];
            return $responseHandler->handleJsonResponse($user  , 200 , 'ok');
        }
    }

   

}