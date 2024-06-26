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

Class UserSitesSossukeController extends BaseController {
    public static function path(){
        return '/usersitessossuke';
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
                return self::post($data);
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

    public static function returnId__user(Security $security){
        $token = $security->getBearerToken();
        return $security->readToken($token);
    }

    public static function get(){

        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client' , $database , User::class );
        $userRepository = new UserRepository('user' , $database , User::class );
        $security = new Security();
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;
    
        $user = $userRepository->findOneBy(['user__id' => self::returnId__user($security)['uid']] , true);
        $user = $userRepository->getRole($user);
        $clients = $lienUserClientRepository->getUserClients($user->getUser__id());
        $user->setClients($clients);
        $array_user =  [] ;

        if (!empty($_GET['flm']) and $_GET['flm'] == 'ok') {
            $clients = $lienUserClientRepository->get2array($user->getUser__id());
            return $responseHandler->handleJsonResponse([ 
                "data" =>  $clients ]  , 200 , 'ok');
        }

        foreach($user->getClients() as $client){
            $array_links = $lienUserClientRepository->findBy(['luc__cli__id' => $client->getCli__id() ],1000, []);
            foreach ($array_links as $match) {
                array_push($array_user ,  $match['luc__user__id']);
            }
        }
        
        $array_user = array_unique($array_user);
        $definitve_array = [];
    

        foreach ($array_user as $users) {
            $subject = $userRepository->findOneBy(['user__id' => $users] , true);
            $clients = $lienUserClientRepository->getUserClients($users);
            // $clientsParc = $lienUserClientRepository->getUserClientsParc($users);
            $subject->setClients($clients);
            // $subject->setClientsParc($clientsParc);
            $subject = $userRepository->getRole($subject);
            array_push($definitve_array , (array ) $subject );
        }
        
        $prenom  = array_column($definitve_array, strtolower('user__prenom'));
        $nom = array_column($definitve_array, strtolower('user__nom'));
        array_multisort( $nom, SORT_STRING, $prenom, SORT_STRING, $definitve_array);
        return $responseHandler->handleJsonResponse( [ 
            "data" => $definitve_array ]  , 200 , 'ok');

    }


    public static function post(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client', $database, User::class);
        $userRepository = new UserRepository('user', $database, User::class);
        $clientRepository = new ClientRepository('client' , $database , Client::class);
        $security = new Security();
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['secret']) && $body['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Opération impossible'
            ], 404, 'bad request');
        }
        //supression des reletions tierces 
        if (!empty($body['delete'])) {
           
            return $responseHandler->handleJsonResponse([
                "data" => 'les liens ont été supprimés !',
            ], 200, 'bad request');
        }

        if (!empty($body['multiple'])) {
            
            $premier= $lienUserClientRepository->getLucOrder1($body['luc__user__id']);
            $lienUserClientRepository->delete(['luc__user__id' =>  $body['luc__user__id']]);

            $i = 0 ;
            foreach ($body['multiple'] as $key => $value) {
                $i ++ ;

                if (empty($premier)) {
                    if ($i == 1 ) {
                        $data = [
                            'luc__user__id' => $body['luc__user__id'],
                            'luc__cli__id' => $value,
                            'luc__order' => 1
                        ];
                    }else{
                        $data = [
                            'luc__user__id' => $body['luc__user__id'],
                            'luc__cli__id' => $value,
                            'luc__order' => 5
                        ];
                    }
                    
                }else{
                    if ($value == $premier['luc__cli__id']) {
                        $data = [
                            'luc__user__id' => $body['luc__user__id'],
                            'luc__cli__id' => $value,
                            'luc__order' => 1
                        ];
                    }
                    else{
                        $data = [
                            'luc__user__id' => $body['luc__user__id'],
                            'luc__cli__id' => $value,
                            'luc__order' => 5
                        ];
                    }
                }
               

                $lienUserClientRepository->insertNoPrimary($data);
            }
            return $responseHandler->handleJsonResponse([
                "data" => 'les liens ont été insérés !',
            ],
                200,
                'bad request'
            );
        }

        if(!empty($body['update'])) {

            $user = $userRepository->findOneBy(['user__id' => $body['luc__user__id'] ], true);
            // $clients = $lienUserClientRepository->getUserClients($user->getUser__id());

            //remet tout a 1
            // $request = $lienUserClientRepository->Db->Pdo->prepare("UPDATE lien_user_client 
            // SET luc__parc = 0
            // WHERE luc__user__id = ".$user->getUser__id()."");
            // $request->execute();   


            // $clause = '';
            // $totalElements = count($body['update']);
            // $index = 0;
            // foreach ($body['update'] as $value) {
            //     $index++;
            //     if ($index ===  $totalElements) {
            //         $clause .=  $value . "  ";
            //     }else {
            //         $clause .=  $value . " , ";
            //     }
               
            // }   

            
            
            foreach ($body['update'] as $value) {
            
                $insertIf = $lienUserClientRepository->insertIfNotExist($user->getUser__id() ,$value);
              
              
                if ($insertIf == false ) {
                    $request = $lienUserClientRepository->Db->Pdo->prepare("UPDATE lien_user_client 
                    SET luc__parc = 1
                    WHERE luc__user__id = ".$user->getUser__id()." AND luc__cli__id IN ( ".$value."  ) ");
                    $request->execute();
                }
               
            }   
           
            $lienUserClientRepository->DeleteUselessLinks($user->getUser__id());
                
            return $responseHandler->handleJsonResponse([
                "data" => 'mis à jour', 
            ], 200, '???');
        }

        if (empty($body['luc__user__id'])) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'user__id  n est pas renseigné', 
            ], 401, 'bad request');
        }

        $user = $userRepository->findOneBy(['user__id' =>  $body['luc__user__id']], false);

        if (empty($user)) {
            if (empty($body['luc__user__id'])) {
                return $responseHandler->handleJsonResponse([
                    "msg" => 'le user nexiste pas !',
                ], 401, 'bad request');
            }
        }
        if (empty($body['luc__cli__id'])) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'cli__id n est pas renseigné',
            ], 401, 'bad request');
        }

        $client = $clientRepository->findOneBy(['cli__id' =>  $body['luc__cli__id']], false);

        if (empty($client)) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'La société n existe pas',
            ], 401, 'bad request');
        }

        if (empty($body['luc__order'])) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'order n est pas renseigné',
            ], 401, 'bad request');
        }

        $data = [
            'luc__user__id' => $body['luc__user__id'] , 
            'luc__cli__id' => $body['luc__cli__id'] , 
            'luc__order' => $body['luc__order']
        ];

        $lienUserClientRepository->insertNoPrimary($data);

        $data = 'opération effectué avec succès';

        return $responseHandler->handleJsonResponse([
            "data" => $data
        ], 200, 'ok');
    }

}