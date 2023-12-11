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
use Src\Services\Security;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\ClientHtpp;
use GuzzleHttp\Promise;
use ZipArchive;

class PlanningController  extends  BaseController
{

    public static function path()
    {
        return '/planning';
    }

    public static function renderDoc()
    {
        $doc = [
             [
                'name' => 'getPlanning',
                'tittle' => ' Planning', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet d optenir le planning ',
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

    public static function get(){

        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $security = new Security();

        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;

        $config = json_decode(file_get_contents('config.json'));
        $guzzle = new \GuzzleHttp\Client(['base_uri' => $config->guzzle->host]);

        try {
            $response = $guzzle->get('/SoftRecode/apiPlanning');
        } catch (ClientException $exeption) {
            $response = $exeption->getResponse();
        }

        $data = $response->getBody()->read(12047878);
        $data = json_decode($data, true);

        if (!empty($data['data'])) {
            return $responseHandler->handleJsonResponse([
                'data' => $data['data'],
            ], 200, 'ok');
        }else{
            return $responseHandler->handleJsonResponse([
                'data' =>  [],
            ], 200, 'Pas de planning');
        }
    }


    public static function post(){
        
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $security = new Security();

        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;

        $body = json_decode(file_get_contents('php://input'), true);
        $config = json_decode(file_get_contents('config.json'));
        $guzzle = new \GuzzleHttp\Client(['base_uri' => $config->guzzle->host]);

        try {
            $response = $guzzle->post('/SoftRecode/apiPlanning' , [ 'json' =>  $body]);
        } catch (ClientException $exeption) {
            $response = $exeption->getResponse();
        }

        $data = $response->getBody()->read(12047878);
        $data = json_decode($data, true);

        return $responseHandler->handleJsonResponse([
        'data' => $data['data'],
        ], 200, 'ok');
       
    }

}