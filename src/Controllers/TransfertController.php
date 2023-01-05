<?php

namespace Src\Controllers;

require  '././vendor/autoload.php';

use Src\Database;
use Src\Entities\Client;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Controllers\BaseController;
use Src\Repository\ClientRepository;

class TransfertController extends BaseController
{

    public static function path()
    {
        return '/transfert';
    }

    public static function renderDoc()
    {
        $doc = [[
                'name' => 'posttransfert',
                'tittle' => 'Transfert depuis sossuke', 
                'method' => 'POST',
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                        'cli__nom',
                    ]
                ],
                'path' => self::path(),
                'description' => 'Permet de transferer un utilisateur ou un client ',
                'reponse' => 'renvoi un tableau d objet de type user ou client ',
                "Auth" => 'JWT '
            ],
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

    public static function post()
    {
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $clientRepository = new ClientRepository('client', $database, Client::class);
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body))
            return $responseHandler->handleJsonResponse('empty body', 400, 'Bad Request');

        $security = new Security();

        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;

        if (!empty($body['cli__id'])) {
            $client  = $clientRepository->transfertClient($body);
            if ($client instanceof Client) {
                $body = [
                    'data' => $client
                ];
                return $responseHandler->handleJsonResponse($body, 200, 'Opération exécutée avec succès ');
            }else {
                $body = [
                    'msg' => $client
                ];
                return $responseHandler->handleJsonResponse($body, 401, 'Un problème est survenu');
            }
        }
    }
}
