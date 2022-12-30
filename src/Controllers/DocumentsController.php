<?php

namespace Src\Controllers;

require  '././vendor/autoload.php';

use ZipArchive;
use Src\Database;
use GuzzleHttp\Promise;
use Src\Entities\Client;
use GuzzleHttp\ClientHtpp;
use Src\Services\Security;
use ReallySimpleJWT\Validate;
use Src\Entities\TicketsLigne;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\BaseController;
use Src\Repository\ClientRepository;
use GuzzleHttp\Exception\ClientException;
use Src\Repository\TicketLigneRepository;

class DocumentsController  extends  BaseController
{

    public static function path()
    {
        return '/documents';
    }

    public static function renderDoc()
    {
        $doc = [
            [
                'name' => 'getDocuments',
                "tittle" => 'Documents',
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de visualiser les documents ',
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
        $security = new Security();
        // $auth = self::Auth($responseHandler, $security);

        // if ($auth != null)
        //     return $auth;
        
        if (empty($_GET['cmd__id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' la cmd nest pas précisée'
            ], 401, 'bad request');
        }
        if (empty($_GET['cmd__etat'])) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  'le type de document n est pas précisé'
            ], 401, 'bad request');
        }
        
        $config = json_decode(file_get_contents('config.json'));
        var_dump('ras le cul ' );
        die();
        $guzzle = new \GuzzleHttp\Client(['base_uri' => $config->guzzle->host , 'curl' => array(CURLOPT_SSL_VERIFYPEER => false)]);
       
        switch ($_GET['cmd__etat']) {
            case 'LST':
                if (!empty($_GET['cli__id'])) {
                    try {
                      
                        $response = $guzzle->get('/SoftRecode/apiList',
                         ['query' =>  [ 
                            'cli__id' =>  $_GET['cli__id']
                            ]
                        ]);
                    } catch (ClientException $exeption) {
                        $response = $exeption->getResponse();
                    }

                    if ($response->getStatusCode() < 300) {
                        
                        return $responseHandler->handleJsonResponse([
                            'data' =>  json_decode($response->getBody()->read(1638408), true),
                        ], 200, 'ok');
                    }else{
                        return $responseHandler->handleJsonResponse([
                            'msg' =>  json_decode($response->getBody()->read(163840), true),
                        ], 401, 'ok');
                    }
                }else{
                    return $responseHandler->handleJsonResponse([
                        'msg' =>  'le parametre cli__id doit etre indiqué ',
                    ], 401, 'ok');
                }
                break;
            case 'VLD':
                try {
                    $response = $guzzle->get('/SoftRecode/apiFacture', ['stream' => true , 'query' => ['cmd__id' =>  $_GET['cmd__id']]  ]);
                } catch (ClientException $exeption) {
                    $response = $exeption->getResponse();
                }
                $data = $response->getBody()->getContents();
                header('Content-Type: application/pdf');
                echo $data;
                break;
            case 'IMP':
                try {
                    $response = $guzzle->get('/SoftRecode/apiBL', ['stream' => true, 'query' => ['cmd__id' =>  $_GET['cmd__id']]]);
                } catch (ClientException $exeption) {
                    $response = $exeption->getResponse();
                }
                $data = $response->getBody()->getContents();
                
                header('Content-Type: application/pdf');
                echo $data;
                break;
            default:
                try {
                    $response = $guzzle->get('/SoftRecode/apiDevis', ['stream' => true, 'query' => ['cmd__id' =>  $_GET['cmd__id']]]);
                } catch (ClientException $exeption) {
                    $response = $exeption->getResponse();
                }
                $data = $response->getBody()->getContents();
                header('Content-Type: application/pdf');
                echo $data;
                break;
        }
    }

   
}
