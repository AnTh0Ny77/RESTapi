<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Controllers\NotFoundController;
use Src\Services\Security;
use Src\Repository\UserRepository;
use Src\Entities\User;
use Src\Repository\RefreshRepository;


Class LoginController {

    public static function path(){
        return 'login';
    }

    public static function renderDoc(){
        $doc = [
           [
                'name' => 'login',
                'method' => 'POST',
                'path' => self::path(),
                'description' => 'permet à l utilisateur de se connecter ' ,
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                            'user_mail' , 
                            'user__password'
                    ]
                    ],
                'reponse' => 'renvoi un objet de type User avec un token et refresh_token à conserver' , 
                "Auth" => 'PUBLIC'
                
            ] 
        ];
        return $doc;
    }


    public static function index($method,$data){
        $notFound = new NotFoundController();
        switch ($method) {
            case 'POST':
                return self::post();
                break;

            case 'GET':
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
        $security = new Security();
        $responseHandler = new ResponseHandler();
        $userRepository = new UserRepository('User' , $database , User::class );
        $refreshRepository = new RefreshRepository($database);
        $body = json_decode(file_get_contents('php://input'), true);
        $login = $userRepository->loginUser($body);
        if (!$login instanceof User) {
            $body = [
                $data = $body ,
                $message =$login
            ];
            return $responseHandler->handleJsonResponse($body , 401 , 'Unauthorized');
        }
        $login->setToken($security->returnToken($login->getUser__id()));
        $refresh_token = $refreshRepository->insertOne($login->getUser__id());
        $login->setRefresh_token($refresh_token);
        $body = [
            $message = $login 
        ];
        return $responseHandler->handleJsonResponse($body , 200 , 'success');
    }
}