<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Repository\MaterielRepository;
use Src\Repository\UserRepository;
use Src\Repository\LienUserClientRepository;
use Src\Entities\User;
use Src\Controllers\UserController;
use Src\Entities\Client;
use Src\Services\Security;

Class MaterielController extends BaseController {

    public static function path(){
        return '/materiel';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'getMateriel',
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de consulter une liste de materiels, 
                le parametre "search" peut etre précisé afin d effectuer une recherche.',
                'reponse' => 'renvoi un tableau d objet de type client', 
                "Auth" => 'JWT'
             ],
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
                return self::get($data);
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
        $materielRepository = new MaterielRepository('materiel' , $database , Client::class );
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client' , $database , User::class );
        $userRepository = new UserRepository('User' , $database , User::class );
        $security = new Security();
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;
        $id_user = UserController::returnId__user($security)['uid'];
        $user = $userRepository->findOneBy(['user__id' => $id_user] , true);
        $clients = $lienUserClientRepository->getUserClients($user->getUser__id());
        $user->setClients($clients);
        $inclause = [
            'mat__cli__id'  => []
        ];
        foreach($user->getClients() as $client){
            array_push($inclause['mat__cli__id'] , $client->getCli__id());
        }

        var_dump($materielRepository->findMat($inclause , [] , 5 , []));
        // cas de parametre spécifiés :
        if (empty($_GET)){

        }


    
        
    }

}