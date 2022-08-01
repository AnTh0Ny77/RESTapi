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