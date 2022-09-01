<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Entities\User;
use Src\Entities\Client;
use Src\Entities\Confirm;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\BaseController;
use Src\Repository\ClientRepository;
use Src\Repository\ConfirmRepository;

Class ConfirmUserController  extends  BaseController {

    public static function path(){
        return 'confirm/user';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'confirmUser',
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de confirmer un utilisateur' ,
                'reponse' => 'renvoi un message de succès', 
                "Auth" => 'Public ' 
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
        $userRepository = new UserRepository('User' , $database , User::class);
        $confirmRepository = new ConfirmRepository('confirm' , $database , Confirm::class);

        if (empty($_GET))
            return $responseHandler->handleJsonResponse('Unknow Client' , 401, 'Bad Request');

        if (empty($_GET['user__email'])) 
            return $responseHandler->handleJsonResponse('Le parametre user__email est obligatoire' , 400 , 'Bad Request');

        if (empty($_GET['confirm__key'])) 
            return $responseHandler->handleJsonResponse('Le parametre confirm__key est obligatoire' , 400 , 'Bad Request');

        $user = $userRepository->findOneBy(['user__email' => $_GET['user__email'] ] , true);

        if (!$user instanceof User) 
            return $responseHandler->handleJsonResponse('Utilisateur inconnu' , 404 , 'Bad Request');

        $confirm = $confirmRepository->findOneBy(['confirm__key' => $_GET['confirm__key']] ,true);

        if(!$confirm instanceof Confirm)
            return $responseHandler->handleJsonResponse('La clef est inconnue' , 404 , 'Bad Request');

        $user->setUser__confirm(1);
        $userRepository->update( (array) $user);

        $confirm->setConfirm__used(1);
        $confirmRepository->update((array) $confirm);

        return $responseHandler->handleJsonResponse('L utilisateur à été confirmé avec succès' , 200 , 'Success');
    }

   

}