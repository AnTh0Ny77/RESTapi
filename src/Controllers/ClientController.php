<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Repository\ClientRepository;
use Src\Entities\Client;
use Src\Services\Security;

Class ClientController {

    public static function path(){
        return 'client';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'getClient',
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet de consulter une liste de societés en lien avec l utilisateur connecté' ,
                'reponse' => 'renvoi un tableau d objet de type client', 
                "Auth" => 'JWT'
             ],
             [
                'name' => 'postClient',
                'method' => 'POST',
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                            'cli__nom',
                            'cli__adr1', 
                            'cli__adr2',
                            'cli__cp', 
                            'cli__ville',  
                            'cli__pays',
                            'cli__tel', 
                            'cli__email'
                    ]
                    ],
                'path' => self::path(),
                'description' => 'Permet de creer un nouveau site' ,
                'reponse' => 'renvoi un tableau d objet de type client', 
                "Auth" => 'JWT + ADMIN ROLE' 
             ],
             [
                'name' => 'putClient',
                'method' => 'PUT',
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                            'cli__nom',
                            'cli__logo', 
                            'cli__adr1', 
                            'cli__adr2',
                            'cli__cp', 
                            'cli__ville',  
                            'cli__pays',
                            'cli__tel', 
                            'cli__email'
                    ]
                    ],
                'path' => self::path(),
                'description' => 'Permet de mettre à jour les information du site' ,
                'reponse' => 'renvoi un tableau d objet de type client', 
                "Auth" => 'JWT + ADMIN ROLE +  ( Le user doit etre le gestiuonnaire de ce site ) '
             ],
             [
                'name' => 'DeleteClient',
                'method' => 'DELETE',
                'path' => self::path(),
                'description' => 'Permet de désactiver un site' ,
                'reponse' => 'renvoi une confirmation que le compte à bien été désactivé', 
                "Auth" => 'JWT + ADMIN ROLE +  ( Le user doit etre le gestiuonnaire de ce site ) '
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
                return self::get($data);
                break;

            case 'DELETE':
                return self::get($data);
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
        $clientRepository = new ClientRepository('client' , $database , Client::class );
        $body = json_decode(file_get_contents('php://input'), true);
        if (empty($body)) 
            return $responseHandler->handleJsonResponse('empty body' , 400 , 'Bad Request');
        
        $security = new Security();

        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;

       
       
        $client  = $clientRepository->postClient($body);
        if (!$client instanceof Client ) {
            $body = [
                $data = $body ,
                $message = $client
            ];
            return $responseHandler->handleJsonResponse($body , 400 , 'Bad Request');
        }
        
    }


    public static function Auth( ResponseHandler $responseHandler , Security $security){
        $token = $security->getBearerToken();
        if (empty($token)) {
            $body = [
                $message = 'JWT not found '
            ];
            return $responseHandler->handleJsonResponse($body , 401 , 'Unauthorized');
        }
        $isAuth = $security->verifyToken($token);
        if ($isAuth == false) {
            $body = [
                $message = 'invalid JWT'
            ];
            return $responseHandler->handleJsonResponse($body , 498 , 'Token expired/invalid');
        }
        $isExp = $security->verifyExp($token);
        if($isExp == false){
            $body = [
                $message = 'expired JWT'
            ];
            return $responseHandler->handleJsonResponse($body , 498 , 'Token expired/invalid');
        }

        return null;
    }

    public static function get($data){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $clientRepository = new ClientRepository('client' , $database , Client::class );

        $security = new Security();

        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;

        if (empty($_GET))
            return $responseHandler->handleJsonResponse('Unknow Client' , 401, 'Bad Request');

        if (!empty($_GET['search'])){
            $params = [];
            foreach ($_GET as $key => $value){
                if ($key != 'search') {
                    
                    $params[$key] = $clientRepository->clean($value) ;

                }
            }
            $column = $clientRepository->verifyColumn($_GET);
            if (!empty($column)) {
                $body = [
                    $message = $clientRepository->verifyColumn($_GET)
                ];
                return $responseHandler->handleJsonResponse($body , 401 , 'Bad Request');
            }
            $results =  $clientRepository->searchBy($params, true);
           
            if (!empty($results)) {
                $body = [
                    $clients =  $results
                ];
                return $responseHandler->handleJsonResponse($body , 200 , 'Success');
            }else  return $responseHandler->handleJsonResponse('Aucun clients trouvés' , 404 , 'Not Found');

        }else{
            $column = $clientRepository->verifyColumn($_GET);
            if (!empty($column)) {
                $body = [
                    $message = $clientRepository->verifyColumn($_GET)
                ];
                return $responseHandler->handleJsonResponse($body , 401 , 'Bad Request');
            }
            foreach ($_GET as $key => $value){
                if ($key != 'search') {
                    $results =  $clientRepository->findOneBy([$key => $clientRepository->clean($value)] , true);
                   
                    if ($results instanceof Client) {
                        $body = [
                            $client =  $results
                        ];
                        return $responseHandler->handleJsonResponse($body , 200 , 'Success');
                    } else return $responseHandler->handleJsonResponse('Aucun clients trouvés' , 404 , 'Not Found');
                }
            }
        }
    }

    public static function put(){

    }

    public static function delete(){

    }
}