<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Entities\User;
use Src\Entities\Client;
use Src\Services\Security;
use Src\Entities\Commercial;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\UserController;
use Src\Repository\MaterielRepository;
use Src\Repository\CommercialRepository;
use Src\Repository\LienUserClientRepository;

Class CommercialController extends BaseController {

    public static function path(){
        return '/commercial';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'getcommercial',
                "tittle" => 'Commerciaux', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de consulter une liste de commerciaux',
                'reponse' => 'renvoi un tableau d array de commerciaux', 
                "Auth" => 'JWT'
             ]
        ];
        return $doc;
    }

	public static function index($method , $data){
        $notFound = new NotFoundController();
        switch ($method) {
            case 'POST':
                return $notFound::index();
                break;

            case 'GET':
                return self::get($data);
                break;

            case 'PUT':
                return $notFound::index();
                break;

            case 'DELETE':
                return $notFound::index();
                break;

            default:
                return $notFound::index();
                break;
        }
    }

    public static function get(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $commercialRepository = new CommercialRepository('commercial' , $database , Commercial::class );
        $security = new Security();
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;

        if(empty($_GET['com__id'])){
            return $responseHandler->handleJsonResponse([
                'msg' => 'Le parametre com__id n est pas spécifié'
            ] , 400 , 'Bad Request');
        }

        $com = $commercialRepository->findOneBy(['com__id' =>  $_GET['com__id']] , true);
        if (!$com instanceof Commercial) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'Le commercial n a pas été trouvé'
            ] , 400 , 'Bad Request');
        }

        return $responseHandler->handleJsonResponse(
            $com 
         , 200 , 'Bad Request');

    }

}