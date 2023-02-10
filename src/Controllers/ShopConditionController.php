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
use Src\Repository\ShopConditionRepository;
use Src\Entities\ShopCondition;

Class ShopConditionController extends BaseController {

    public static function path(){
        return '/ShopConditions';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'GetShopCondition',
                "tittle" => ' Sonditions de Commandes en ligne ', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de consulter les condition de commande', 
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
        $userRepository = new UserRepository('user', $database, User::class);
        $shopConditionsRepository = new ShopCmdRepository('shop_condition', $database, ShopCondition::class);
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

        if (empty($_GET['sco__cli_id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'sco__cli_id ne peut pas etre vide '
            ], 401, 'bad request');
        }

        $condition = $shopConditionsRepository->findBy(['sco__cli_id' =>  $_GET['sco__cli_id']] ,45 , []);

        if (empty($condition)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'Aucune condition pour ce client '
            ], 401, 'bad request');
        }

        return $responseHandler->handleJsonResponse([
            'data' => $condition
        ], 200, 'ok');

    }
}