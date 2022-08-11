<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Controllers\NotFoundController;
use Src\Services\Security;
use Src\Repository\UserRepository;
use Src\Repository\RefreshRepository;
use Src\Entities\User;


Class UserController {

    public static function path(){
        return 'user';
    }

    public static function renderDoc(){
        $doc = [
             [
                'method' => 'PUT',
                'path' => self::path(),
                'description' => 'permet à l utilisateur de se connecter ' ,
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                            'user_mail' , 
                            'user__password'
                    ]
                    ],
                'reponse' =>  'renvoi un objet de type User avec un token et refresh_token à conserver' ,
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
        
        return $responseHandler->handleJsonResponse($body , 200 , 'ok');
    }

    public static function get($data){
        if (empty($data)){
            $database = new Database();
            $database->DbConnect();
            $responseHandler = new ResponseHandler();
            $userRepository = new UserRepository('User' , $database , User::class );
            $body = [
                $message = 'La liste des utilisateurs est reservée au personnel de recode'
            ];
            return $responseHandler->handleJsonResponse($body , 401 , 'UnAuthorized');
        }
    }

   

}