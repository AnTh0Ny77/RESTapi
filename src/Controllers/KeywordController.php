<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Entities\Materiel;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Repository\BaseRepository;


Class KeywordController extends BaseController {

    public static function path(){
        return '/keyword';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'getKeyword',
                "tittle" => 'Keyword', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de consulter une liste de keyword', 
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

    public static function get(){
        $database = new Database();
        $database->DbConnect();
        
        $responseHandler = new ResponseHandler();
        $Repository = new BaseRepository('keyword' , $database , Materiel::class );
       
        $security = new Security();
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;

        $keywordlist = $Repository->findBy([], 100000, ['kw__type' => "ASC" , 'kw__ordre' => "ASC" , "kw__lib" => "ASC"]);
        
        return $responseHandler->handleJsonResponse([
            'data' => $keywordlist
        ] , 200 , 'ok');
    }

}