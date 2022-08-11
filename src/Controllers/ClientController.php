<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Services\Security;

Class ClientController {

    public static function path(){
        return 'client';
    }

    public static function renderDoc(){
        $doc = [
             [
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de consulter une liste de societés en lien avec l utilisateur connecté' ,
                'reponse' => 'renvoi un tableau d objet de type client', 
                "Auth" => 'JWT'
            ] 
        ];
        return $doc;
    }

	public static function index(){
        $database = new Database();
        $security = new Security();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $token = $security->returnToken(15);
        $data = [
            "token" => $token
        ];
        return $responseHandler->handleJsonResponse($data , 200 , 'ok');
    }
}