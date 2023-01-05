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

class ListFilesController  extends  BaseController
{

    public static function path()
    {
        return '/listFiles';
    }

    public static function renderDoc()
    {
        $doc = [
             [
                'name' => 'getFilesListTickets',
                'tittle' => ' Fichiers Liste', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet d optenir la list des tickets ',
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
        $tiketLigne = new TicketLigneRepository('ticket_ligne', $database, TicketsLigne::class);
        $security = new Security();
        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;

        if (empty($_GET['tkl__id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' La ligne de ticket n est pas précisée'
            ], 404, 'bad request');
        }

        $config = json_decode(file_get_contents('config.json'));
        $guzzle = new \GuzzleHttp\Client(['base_uri' => $config->guzzle->host]);
        try {
            $response = $guzzle->get('/SoftRecode/apiListDocuments', ['stream' => true, 'query' => ['tkl__id' =>  $_GET['tkl__id']]]);
        } catch (ClientException $exeption) {
            $response = $exeption->getResponse();
        }

        if ($response->getStatusCode() > 299 ) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  'Une erreur est survenue dans l api listFilesController condition n 103  ',
            ], 401, 'Bad request');
        }
        return $responseHandler->handleJsonResponse([
            'data' =>  'Une erreur est survenue dans l api listFilesController condition n 103  ',
        ], 200, json_decode($response->getBody()->read(125922)));
    }
}
