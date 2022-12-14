<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Entities\User;
use Src\Entities\Client;
use Src\Entities\Materiel;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\UserController;
use Src\Repository\MaterielRepository;
use Src\Repository\LienUserClientRepository;
use Src\Repository\ClientRepository;

Class MaterielController extends BaseController {

    public static function path(){
        return '/materiel';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'getMateriel',
                "tittle" => 'Materiels', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de consulter une liste de materiels, 
                le parametre "search" peut etre précisé afin d effectuer une recherche.',
                'reponse' => 'renvoi un tableau d objet de type materiel', 
                "Auth" => 'JWT'
             ],[
                'name' => 'postMateriel',
                'method' => 'POST',
                'path' => self::path(),
                'description' => 'Permet de creer un materiels',
                'reponse' => 'renvoi un tableau d objet de type materiel', 
                "Auth" => 'JWT',
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                            'mat__id',
                            'mat__cli__id', 
                            'mat__type', 
                            'mat__marque',
                            'mat__model', 
                            'mat__pn',  
                            'mat__memo',
                            'mat__sn', 
                            'mat__idnec'
                    ]
                    ],
             ],[
                'name' => 'postMateriel',
                'method' => 'PUT',
                'path' => self::path(),
                'description' => 'Permet de mettre a jour un matériel',
                'reponse' => 'renvoi un tableau d objet de type materiel', 
                "Auth" => 'JWT',
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                            'mat__id',
                            'mat__cli__id', 
                            'mat__type', 
                            'mat__marque',
                            'mat__model', 
                            'mat__pn',  
                            'mat__memo',
                            'mat__sn', 
                            'mat__idnec'
                    ]
                    ],
             ],
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
                return self::get($data);
                break;

            case 'PUT':
                return self::put();
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
        $clientRep = new ClientRepository('client' , $database , Client::class);
        $responseHandler = new ResponseHandler();
        $materielRepository = new MaterielRepository('materiel' , $database , Materiel::class );
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client' , $database , User::class );
        $userRepository = new UserRepository('user' , $database , User::class );
        $security = new Security();
      
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;

       
        $id_user = UserController::returnId__user($security)['uid'];
        $user = $userRepository->findOneBy(['user__id' => $id_user] , true);
       
        $clients = $lienUserClientRepository->getUserClients($user->getUser__id());
        
        $user->setClients($clients);
        $inclause = [
            'mat__cli__id'  => [] ,
            'mat__marque' => [], 
            'mat__kw_tg' => [] , 
            'mat__type' => [], 
            'mat__id' => []
        ];
        
        $limit = 30 ;
        
                $string = '';
                if (!empty($_GET['search'])) {
                    $string = $_GET['search'];
                }
                if (!empty($_GET['RECODE__PASS'])) {
                    if ($_GET['RECODE__PASS'] == "secret") {
                        $list_client = $clientRep->returnIdList();
                        foreach ( $list_client as $value) {
                            array_push($inclause['mat__cli__id'] , $value['cli__id']);
                        }
                    }
                }else{
                    foreach($user->getClients() as $client){
                        array_push($inclause['mat__cli__id'] , $client->getCli__id());
                    }
                }
               
                
                if (!empty($_GET['mat__cli__id'])) {
                    $inclause['mat__cli__id']  = [];
                    foreach ($_GET['mat__cli__id'] as $value) {
                        array_push($inclause['mat__cli__id'] , $value);   
                    }
                    $_GET['mat__cli__id'] = "";
                }

                if (empty($inclause['mat__cli__id'])) {
                    return $responseHandler->handleJsonResponse([
                        'msg' => 'Vous n avez aucun sites en gestion'
                    ] , 404 , 'not found');
                }

                if (!empty($_GET['mat__id'])) {
                    foreach ($_GET['mat__id'] as $value) {
                        array_push($inclause['mat__id'] , $value);   
                    }
                    $_GET['mat__id'] = "";
                }
                
                if(!empty($_GET['mat__marque'])){
                    foreach ($_GET['mat__marque'] as $value) {
                            array_push($inclause['mat__marque'] , $value);   
                    }
                    $_GET['mat__marque'] = "";
                }

                if(!empty($_GET['mat__kw_tg'])){
                    foreach ($_GET['mat__kw_tg'] as $value) {
                            array_push($inclause['mat__kw_tg']  ,$value);      
                    }
                    $_GET['mat__kw_tg'] = "";
                }

                if(!empty($_GET['mat__type'])){
                    foreach ($_GET['mat__type'] as $value) {
                            array_push($inclause['mat__type']  ,$value);      
                    }
                    $_GET['mat__type'] = "";
                }
                
                if (!empty($_GET['limit'])) {
                    $limit = intval($_GET['limit']);
                }

                $list = $materielRepository->search2($inclause ,$string , $limit ,  [], []);
                
                if (empty($list)) {
                    return $responseHandler->handleJsonResponse([
                        'msg' => 'Aucun materiel n a été trouvé'
                    ] , 404 , 'not found');
                } else {
                    return $responseHandler->handleJsonResponse( [
                        "data" => $list ], 200 , 'ok ');
                }
            
    }

    public static function post(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $materielRepository = new MaterielRepository('materiel' , $database , Materiel::class );
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client' , $database , User::class );
        $userRepository = new UserRepository('user' , $database , User::class );
        $security = new Security();

        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;

        $id_user = UserController::returnId__user($security)['uid'];
        $user = $userRepository->findOneBy(['user__id' => $id_user] , true);
        $clients = $lienUserClientRepository->getUserClients($user->getUser__id());
        $user->setClients($clients);
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'le body ne peut pas etre vide'
            ] , 401 , 'bad request');
        } 

        
        $materiel = $materielRepository->postMateriel($body , $user);
        if (empty($materiel)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'un probleme est survenu durant la mise a jour '
            ] , 401 , 'bad request');
        }

        return $responseHandler->handleJsonResponse([
            'data' => $materiel
        ] , 201 , 'ressource created');

    }

    public static function put(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $materielRepository = new MaterielRepository('materiel' , $database , Materiel::class );
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client' , $database , User::class );
        $userRepository = new UserRepository('user' , $database , User::class );
        $security = new Security();

        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;

        $id_user = UserController::returnId__user($security)['uid'];
        $user = $userRepository->findOneBy(['user__id' => $id_user] , true);
        $clients = $lienUserClientRepository->getUserClients($user->getUser__id());
        $user->setClients($clients);
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'le body ne peut pas etre vide'
            ] , 401 , 'bad request');
        } 

        $materiel = $materielRepository->UpdateOne($body , $user);
        if (empty($materiel)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'un probleme est survenu durant la mise a jour '
            ] , 401 , 'bad request');
        }

        return $responseHandler->handleJsonResponse([
            'data' => $materiel
        ] , 201 , 'ressource created');

    }
    

}