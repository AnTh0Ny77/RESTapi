<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Entities\User;
use Src\Entities\Client;
use Src\Entities\Materiel;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\UserController;
use Src\Repository\MaterielRepository;
use Src\Repository\LienUserClientRepository;
use Src\Repository\ClientRepository;

Class MaterielSossukeController extends BaseController {

    public static function path(){
        return '/materielSossuke';
    }

    public static function renderDoc(){
        $doc = [
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
                return self::get();
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

    public static function post(){

        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $materielRepository = new MaterielRepository('materiel', $database, Materiel::class);
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client', $database, User::class);
        $userRepository = new UserRepository('user', $database, User::class);
        $security = new Security();
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'le body ne peut pas etre vide'
            ], 401, 'bad request');
        } 

        if (empty($body['secret'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'opération non autorisée'
            ], 401, 'bad request');

        } elseif (!empty($body['secret']) and $body['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' => 'opération non autorisée'
            ], 401, 'bad request');
        }
       
        if (!empty($body['mat__id']) and isset($body['mat__actif'])) {
            
            $maj = [
                'mat__id' => $body['mat__id'], 
                'mat__actif' => $body['mat__actif']
            ];
            
            $materiel = $materielRepository->UpdateOne($maj, null);

            if (empty($materiel)) {
                return $responseHandler->handleJsonResponse([
                    'msg' => 'un probleme est survenu durant la mise a jour '
                ], 401, 'bad request');
            }

            return $responseHandler->handleJsonResponse([
                'data' => $materiel
            ], 201, 'ok');
           
        }

    }

    public static function get (){

        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $materielRepository = new MaterielRepository('materiel', $database, Materiel::class);
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client', $database, User::class);
        $userRepository = new UserRepository('user', $database, User::class);
        $security = new Security();
      
     
        if (empty($_GET['secret'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'opération non autorisée'
            ], 401, 'bad request');

        } elseif (!empty($_GET['secret']) and $_GET['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' => 'opération non autorisée'
            ], 401, 'bad request');
        }

        $mat = $materielRepository->findOneBy(["mat__sn" => $_GET['mat__sn']], false);

        return $responseHandler->handleJsonResponse([
            'data' => $mat
        ], 200, 'ok');


    }

}