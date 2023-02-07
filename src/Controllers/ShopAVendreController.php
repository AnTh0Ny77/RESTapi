<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Entities\User;
use Src\Entities\Client;
use Src\Entities\ShopAVendre;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Repository\LienUserClientRepository;
use Src\Repository\ClientRepository;
use Src\Repository\ShopAVendreRepository;

Class ShopAVendreController extends BaseController {

    public static function path(){
        return '/ShopAVendre';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'getAVendre',
                "tittle" => 'A vendre', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de consulter une liste de materiels a vendre pour le client , 
                le parametre "search" peut etre précisé afin d effectuer une recherche.',
                'reponse' => 'renvoi un tableau d objet de type materiel a vendre', 
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

    public static function get($data){
       
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $userRepository = new UserRepository('user', $database, User::class);
        $shopAvendreRepository = new ShopAVendreRepository('shop_avendre' , $database , ShopAVendre::class);
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

        // clause IN 
        $inclause = [
            'sav__cli_id'  => [],
            'sar__famille' => [], 
            'sav__id' => []
        ];

        //recupère les clients  
        foreach ($user->getClients() as $client) {
                array_push($inclause['sav__cli_id'], $client->getCli__id());
        }
        if (empty($inclause['sav__cli_id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'Vous n avez aucun sites en gestion'
            ], 404, 'not found');
        }

        if (!empty($_GET['sav__id'])) {
            foreach ($_GET['sav__id'] as $value) {
                array_push($inclause['sav__id'], $value);
            }
            $_GET['sav__id'] = "";
        }
        // recupère si un tri par famille 
        if (!empty($_GET['sar__famille'])) {
            foreach ($_GET['sar__famille'] as $value) {
                array_push($inclause['sar__famille'], $value);
            }
            $_GET['sar__famille'] = "";
        }

        // recupère la recherche 
        $string = '';
        if (!empty($_GET['search'])) {
            $string = $_GET['search'];
        }

        //limit : 
        $limit = 30;
        if (!empty($_GET['limit'])) {
            $limit = intval($_GET['limit']);
        }

        $list = $shopAvendreRepository->search2($inclause, $string, $limit,  [], []);
       
        if (empty($list)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'Aucun materiel à vendre n a été trouvé'
            ], 404, 'not found');
        } else {
            return $responseHandler->handleJsonResponse([
                "data" => $list
            ], 200, 'ok ');
        }


        

    }


}