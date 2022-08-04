<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Services\Security;
use Src\Repository\UserRepository;
use Src\Entities\User;


Class UserController {

    public static function path(){
        return 'user';
    }

    public static function index($method){
        switch ($method) {
            case 'POST':
                return self::post();
                break;

            case 'get':
                return self::post();
                break;

            default:
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
           
        

        return $responseHandler->handleJsonResponse($body , 200 , 'ok');
    }
}