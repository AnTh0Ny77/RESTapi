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
use Src\Repository\ShopArticleRepository;
use Src\Entities\ShopArticle;

Class ShopArticleController extends BaseController {

    public static function path(){
        return '/ShopArticle';
    }

    public static function renderDoc(){
        $doc = [
           
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
        $userRepository = new UserRepository('user' , $database , User::class );
        $ShopArticleRepository = new ShopArticleRepository('shop_article' , $database , ShopArticle::class);
        $security = new Security();
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;

        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'le body ne peut pas etre vide'
            ], 401, 'bad request');
        }
        
        if (empty($body['sar__ref_constructeur'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'Ref constructeur absente'
            ], 401, 'bad request');
        }

        if (empty($body['sar__famille'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'famille absente'
            ], 401, 'bad request');
        }

        $article = $ShopArticleRepository->insert($body);

        return $responseHandler->handleJsonResponse([
            'data' => $article
        ], 200, 'ok');
    
    }

}