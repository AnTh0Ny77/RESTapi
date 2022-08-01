<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Services\ResponseHandler;

Class ClientController {

    public static function path(){
        return 'client';
    }

	public static function index(){
        $responseHandler = new ResponseHandler();
        $data = [];
        return $responseHandler->handleJsonResponse($data , 404 , 'Not Found');
    }
}