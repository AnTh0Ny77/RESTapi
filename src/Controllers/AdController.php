<?php

namespace Src\Controllers;

require  '././vendor/autoload.php';

use ReallySimpleJWT\Validate;
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Repository\ClientRepository;
use Src\Controllers\BaseController;
use Src\Repository\UserRepository;
use Src\Repository\BaseRepository;
use Src\Entities\Client;
use Src\Entities\TicketsLigne;
use Src\Repository\TicketLigneRepository;
use Src\Services\Security;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\ClientHtpp;
use GuzzleHttp\Promise;
use ZipArchive;

class AdController  extends  BaseController
{

    public static function path()
    {
        return '/add';
    }

    public static function renderDoc()
    {
        $doc = [
             [
                'name' => 'addGet',
                'tittle' => ' récupère les pub', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet d optenir la list des publicités ',
                'reponse' => 'renvoi la variable data ou msg en cas d echec',
                "Auth" => 'JWT '
            ]
        ];
        return $doc;
    }

    public static function index($method, $data)
    {
        $notFound = new NotFoundController();
        switch ($method) {
            case 'POST':
                return $notFound::index();
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

    public static function get(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $security = new Security();
        $addrepository = new BaseRepository('promo' , $database ,  Client::class);
        $security = new Security();
        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;

            var_dump('hey');
        $list = $addrepository->findBy([1 => 1 ] , 600 , []);
        return $responseHandler->handleJsonResponse([
            'data' =>  $list,
        ], 200, "ok");
    }
}
