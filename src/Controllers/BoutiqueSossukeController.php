<?php

namespace Src\Controllers;

require  '././vendor/autoload.php';

use Src\Database;
use Src\Entities\User;
use Src\Entities\Client;
use Src\Entities\Materiel;
use Src\Services\Security;
use Src\Entities\ShopArticle;
use Src\Entities\ShopAVendre;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\UserController;
use Src\Repository\ClientRepository;
use Src\Repository\MaterielRepository;
use Src\Repository\ShopArticleRepository;
use Src\Repository\ShopAVendreRepository;
use Src\Repository\LienUserClientRepository;

class BoutiqueSossukeController extends BaseController{

    public static function path()
    {
        return '/boutiqueSossuke';
    }

    public static function renderDoc()
    {
        $doc = [];
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
        $materielRepository = new MaterielRepository('materiel', $database, Materiel::class);
        $ShopAVendreRepository = new ShopArticleRepository('shop_article' , $database, ShopArticle::class);
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client', $database, User::class);
        $userRepository = new UserRepository('user', $database, User::class);
        $security = new Security();
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'le body ne peut pas etre vide'
            ], 401, 'bad request');
        }

        if (empty($body['secret'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'opération non autorisée'
            ], 401, 'bad request');
        } elseif (!empty($body['secret']) and $body['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' => 'opération non autorisée'
            ], 401, 'bad request');
        }

        if (!empty($body['shop_avendre'])) {
            $list = $ShopAVendreRepository->findAll();
            return $responseHandler->handleJsonResponse([
                'data' => $list
            ], 200, 'ok');
        }

        if (!empty($body['sav__cli_id'])) {
            $ShopAVRepository = new ShopAVendreRepository('shop_avendre' , $database, ShopAVendre::class);
            $list = $ShopAVRepository->findby(['sav__cli_id' =>  $body['sav__cli_id'] ] , 1000 , []);
            $resulst = [];
            foreach ($list as $key => $value) {
               $article =   $ShopAVendreRepository->findOneBy(['sar__ref_id' => $value['sav__ref_id']], false);
               $value['article'] = ( array ) $article; 
               array_push($resulst , (object) $value);
            }
            return $responseHandler->handleJsonResponse([
                'data' => $resulst
            ], 200, 'ok');
        }
    }
}
