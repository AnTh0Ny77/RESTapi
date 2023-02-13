<?php

namespace Src\Controllers;

require  '././vendor/autoload.php';

use Src\Database;
use Src\Entities\User;
use Src\Entities\Client;
use Src\Entities\ShopAVendre;
use Src\Entities\ShopCmd;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Repository\LienUserClientRepository;
use Src\Repository\ClientRepository;
use Src\Repository\ShopAVendreRepository;
use Src\Repository\ShopCmdRepository;
use Src\Repository\ShopCmdLigneRepository;

class ShopCmdLigneController extends BaseController
{

    public static function path()
    {
        return '/ShopcmdLigne';
    }

    public static function renderDoc()
    {
        $doc = [
            [
                'name' => 'PostCmdMigne',
                "tittle" => 'ligne de Commandes en ligne ',
                'method' => 'POST',
                'path' => self::path(),
                'description' => 'Permet de consulter de creer une ligne cmd',
                "Auth" => 'JWT'
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
        $userRepository = new UserRepository('user', $database, User::class);
        $shopCmdRepository = new ShopCmdRepository('shop_cmd', $database, ShopCmd::class);
        $shopCmdLigneRepository = new ShopCmdLigneRepository('shop_cmd_ligne', $database, ShopCmd::class);
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client', $database, User::class);
        $security = new Security();
        //auth 
        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;
        //user
        $id_user = UserController::returnId__user($security)['uid'];
        $user = $userRepository->findOneBy(['user__id' => $id_user], true);
        $clients = $lienUserClientRepository->getUserClients($user->getUser__id());
        $user->setClients($clients);

        //recup le body :
        $body = json_decode(file_get_contents('php://input'), true);
        if (empty($body)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'le body ne peut pas etre vide'
            ], 401, 'bad request');
        }

        if (empty($body['scl__cmd_id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'l ID de commande n est pas indiqué'
            ], 401, 'bad request');
        }

        $cmd = $shopCmdRepository->findOneBy(['scm__id' => $body['scl__cmd_id'] ] , false);
        if(empty($cmd)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'la commande n existe pas'
            ], 401, 'bad request');
        }

        $verif = self::controlCmdLigne($body);

        if ($verif != false) {
            return $responseHandler->handleJsonResponse([
                'msg' => $verif
            ], 401, 'bad request');
        }

        $id = $shopCmdLigneRepository->insert($body);
        return $responseHandler->handleJsonResponse([
            'data' => $id
        ], 200, 'ok');
    }


    public static function controlCmdLigne($body){

        if (empty($body['scl__ref_id'])) return 'La référence n est pas spécifiée ';

        if (empty($body['scl__prix_unit'])) return 'Le prix n est pas spécifié  ';

        if (empty($body['scl__qte'])) return 'La quantité n est pas spécifié ';

        return false;
    }
}
