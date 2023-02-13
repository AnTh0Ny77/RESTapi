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

Class ShopCmdController extends BaseController {

    public static function path(){
        return '/Shopcmd';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'PostCmd',
                "tittle" => 'Commandes en ligne ', 
                'method' => 'POST',
                'path' => self::path(),
                'description' => 'Permet de consulter de creer une cmd', 
                "Auth" => 'JWT'
             ]
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
        $userRepository = new UserRepository('user', $database, User::class);
        $shopCmdRepository = new ShopCmdRepository('shop_cmd', $database, ShopCmd::class);
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

        $verif = self::controlCmd($body);

        if ($verif != false) {
            return $responseHandler->handleJsonResponse([
                'msg' => $verif
            ], 401, 'bad request');
        }

        $body['scm__user_id'] = $user->getUser__id();

        $body['scm__dt_cmd'] = date('Y-m-d H:i:s');

        $id = $shopCmdRepository->insert($body);

        return $responseHandler->handleJsonResponse([
            'data' => $id
        ], 200, 'ok');


    }


    public static function controlCmd($body){

        if (empty($body['scm__client_id_livr'])) return 'client livré non spécifié ';
        
        if (empty($body['scm__client_id_fact'])) return 'client facturé non spécifié ';

        if (empty($body['scm__prix_port'])) return 'prix du port non spécifié ';

        return false ;
    }

}