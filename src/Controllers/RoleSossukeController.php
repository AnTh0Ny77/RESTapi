<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Sossuke;
use Src\Entities\User;
use Src\Entities\Client;
use Src\Entities\Materiel;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\UserController;
use Src\Entities\Tickets;
use Src\Repository\TicketRepository;
Use Src\Entities\TicketsLigne;
Use Src\Repository\TicketLigneRepository;
use Src\Entities\TicketsLigneChamp;
use Src\Repository\BaseRepository;
use Src\Repository\ClientRepository;
use Src\Repository\TicketLigneChampRepository;
use Src\Repository\LienUserClientRepository;


Class RoleSossukeController extends BaseController {

    public static function path(){
        return '/rolesossuke';
    }

    public static function renderDoc(){
       $doc = [
             [
               
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
                return self::delete();
                break;

            default:
                return $notFound::index();
                break;
        }
    }


    public static function delete(){
        $database = new Database();
        $database->DbConnect();
        $rolesQuerys = new BaseRepository('user__role', $database, User::class);
        $responseHandler = new ResponseHandler();
        $security = new Security();

        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['secret']) && $body['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Opération impossible'
            ], 404, 'bad request');
        }

        if (empty($body['user__id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'user__id doit etre renseigné'
            ], 401, 'bad request');
        }
        $delete = $rolesQuerys->deleteRole($body['user__id']);

        return $responseHandler->handleJsonResponse([
            'data' => 'Role supprimé'
        ], 200, 'ok');   

    }

    public static function post(){
        $database = new Database();
        $database->DbConnect();
        $rolesQuerys = new BaseRepository('user__role' , $database , User::class);
        $responseHandler = new ResponseHandler();
        $security = new Security();
       
        $body = json_decode(file_get_contents('php://input'), true);

        if(!empty($body['_METHOD']) and $body['_METHOD'] == 'DELETE' ) {
            return self::delete();
            die();
        }

        if (empty($body['secret']) && $body['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Opération impossible'
            ], 404, 'bad request');
        }
        
        if (empty($body['user__id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'user__id doit etre renseigné'
            ], 401, 'bad request');
        }
        if (empty($body['role'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'role doit etre renseigné'
            ], 401, 'bad request');
        }
    
        $insertData = $rolesQuerys->insertRole($body['user__id'] ,  $body['role']);

        if ($insertData != true ) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'Un problème est survenu durant l insertion en base de données'
            ], 401, 'bad request');
        }
        return $responseHandler->handleJsonResponse([
            'data' => 'Role inséré avec succès '
        ], 200, 'ok');    
    }


     

}