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


Class RoleController extends BaseController {

    public static function path(){
        return '/role';
    }

    public static function renderDoc(){
       $doc = [
             [
                'name' => 'postTickets',
                'tittle' => 'Roles' ,
                'method' => 'POST',
                'path' => self::path(),
                'description' => 'Permet  dattribuer un role à un utilisateur',
                'reponse' => 'renvoi un message de succes', 
                "Auth" => 'JWT'
             ]
        ];
        return $doc;
    }

	public static function index($method , $data){
        $notFound = new NotFoundController();
        switch ($method) {
            case 'POST':
                return self::post();;
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
        $rolesQuerys = new BaseRepository('user__role' , $database , User::class);
        $responseHandler = new ResponseHandler();
        $security = new Security();
        $auth = self::Auth($responseHandler, $security);

        if ($auth != null)
            return $auth;

        $body = json_decode(file_get_contents('php://input'), true);
       
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
        $insertData = $rolesQuerys->insertNoPrimary(['ur__user_id' => $body['user__id'] , 'ur__role' => $body['role']]);
        if ($insertData != true ) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'Un problème est survenu durant l insertion en base de données'
            ], 401, 'bad request');
        }

        return $responseHandler->handleJsonResponse([
            'msg' => 'Role inséré avec succès '
        ], 401, 'bad request');    
    }

}