<?php

namespace Src\Controllers;

require  '././vendor/autoload.php';

use Src\Database;
use Src\Entities\Materiel;
use Src\Repository\BaseRepository;
use Src\Entities\User;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\BaseController;
use Src\Repository\RefreshRepository;
use Src\Controllers\NotFoundController;
use Src\Repository\ClientRepository;
use Src\Repository\LienUserClientRepository;



class VerifUserController extends BaseController
{

    public static function path(){
        return '/verifUser';
    }

    public static function renderDoc()
    {
        $doc = [
            [
                'name' => 'getuserSites',
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de consulter une liste de user liées au sites du User connecté',
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

    public static function returnId__user(Security $security)
    {
        $token = $security->getBearerToken();
        return $security->readToken($token);
    }

    public static function get()
    {
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client', $database, User::class);
        $userRepository = new UserRepository('user', $database, User::class);
        $security = new Security();
        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;

        if (empty($_GET['user__mail'])) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'le parametre user__mail n est pas renseigné' 
            ], 200, 'ok');
        }

        $user = $userRepository->findOneBy(['user__mail' => $_GET['user__mail']], true);

        if (!$user instanceof User) {
            return $responseHandler->handleJsonResponse([
                "data" => []
            ], 200, 'ok');
        }
        $clients = $lienUserClientRepository->getUserClients($user->getUser__id());
        $user->setClients($clients);
        $array_user =  [];
        foreach ($user->getClients() as $client) {
            $array_links = $lienUserClientRepository->findBy(['luc__cli__id' => $client->getCli__id()], 1000, []);
            foreach ($array_links as $match) {
                array_push($array_user,  $match['luc__user__id']);
            }
        }

        $array_user = array_unique($array_user);
        $definitve_array = [];

        foreach ($array_user as $users) {
            $subject = $userRepository->findOneBy(['user__id' => $users], true);
            $clients = $lienUserClientRepository->getUserClients($users);
            $subject->setClients($clients);
            $subject = $userRepository->getRole($subject);
            array_push($definitve_array, (array) $subject);
        }

        $prenom  = array_column($definitve_array, strtolower('user__prenom'));
        $nom = array_column($definitve_array, strtolower('user__nom'));
        array_multisort($nom, SORT_STRING, $prenom, SORT_STRING, $definitve_array);
        return $responseHandler->handleJsonResponse([
            "data" => $definitve_array
        ], 200, 'ok');
    }

}
