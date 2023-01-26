<?php

namespace Src\Controllers;

require  '././vendor/autoload.php';

use ReallySimpleJWT\Validate;
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Repository\ClientRepository;
use Src\Controllers\BaseController;
use Src\Repository\UserRepository;
use Src\Entities\Client;
use Src\Entities\TicketsLigne;
use Src\Repository\TicketLigneRepository;
use Src\Repository\LienUserClientRepository;
use Src\Repository\MaterielRepository;
use Src\Repository\TicketRepository;
use Src\Services\Security;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\ClientHtpp;
use GuzzleHttp\Promise;
use ZipArchive;

class VerifyMailController  extends  BaseController{
    public static function path(){
        return '/verifymail';
    }

    public static function renderDoc(){
        $doc = [ [] ];
        return $doc;
    }

    public static function index($method, $data)
    {
        $notFound = new NotFoundController();
        switch ($method) {
            case 'POST':
                return self::post();
                break;

            case 'GET':
                return $notFound::index();
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
        $security = new Security();
        $userRepository = new UserRepository('user', $database, User::class);
        $security = new Security();
        $body = json_decode(file_get_contents('php://input'), true);
      
        if (empty($body['secret']) && $body['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Opération impossible'
            ], 404, 'bad request');
        }
        if (!empty($body['user__mail'])) {
            $response = $userRepository->findOneBy(['user__mail' =>  $body['user__mail'] ] , false);
            if (!empty($response)) {
                return $responseHandler->handleJsonResponse([
                    'data' => $response,
                ], 200,'ok');
            }else{
                return $responseHandler->handleJsonResponse([
                    'msg' =>'libre',
                ], 400,'ok');
            } 
        }else {
            return $responseHandler->handleJsonResponse([
                'msg' => 'libre',
            ], 400,'ok');
        }
    }
}
