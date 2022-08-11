<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Repository\RefreshRepository;
use Src\Services\Security;

Class RefreshController {

    public static function path(){
        return 'refresh';
    }

    public static function renderDoc(){
        $doc = [
              [
                'method' => 'DELETE',
                'path' => self::path(),
                'description' => 'permet à l utilisateur de se connecter ' ,
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                            'user_mail' , 
                            'user__password'
                    ]
                    ],
                'reponse' =>  'renvoi un objet de type User avec un token et refresh_token à conserver',
                "Auth" => 'PUBLIC'
                
            ] 
        ];
        return $doc;
    }


    public static function index($method,$data){
        $notFound = new NotFoundController();
        switch ($method) {
            case 'POST':
                return self::refresh();
                break;

            case 'GET':
                return $notFound::index();
                break;

            default:
                return $notFound::index();
                break;
        }
    }


	public static function refresh(){
        $database = new Database();
        $security = new Security();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $security = new Security();
        $refreshRepository = new RefreshRepository($database);
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['refresh_token'])) {
            $body = [
                $data = $body ,
                $message = 'le refresh token n a pas été trouvé dans la requete'
            ];
            return $responseHandler->handleJsonResponse($body , 400 , 'Bad Request');
        }

        $refresh_token = $refreshRepository->findOneBy(['refresh_token' => $body['refresh_token'] ] , false);
        if (empty($refresh_token)) {
            $body = [
                $data = $body ,
                $message = 'refresh token est invalide'
            ];
            return $responseHandler->handleJsonResponse($body , 400 , 'Bad Request');
        }
        $token  = $security->returnToken($refresh_token['user__id']);

        $data = [
            "token" => $token
        ];
        return $responseHandler->handleJsonResponse($data , 200 , 'ok');
    }
}