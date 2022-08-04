<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Controllers\NotFoundController;
use Src\Services\Security;
use Src\Repository\UserRepository;
use Src\Entities\User;


Class UserController {

    public static function path(){
        return 'user';
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
        $body = json_decode(file_get_contents('php://input'), true);

        
        $user = $userRepository->postUser($body);

        if (!$user instanceof User) {
            $body = [
                $data = $body ,
                $message = $user
            ];
            return $responseHandler->handleJsonResponse($body , 400 , 'Bad Request');
        }
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

       //retourne l'utilisateur demandé si le token est correct : 
        
    }

   

}