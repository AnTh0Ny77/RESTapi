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
            'mat__type' => []
        ];
        $limit = 30 ;
        foreach($user->getClients() as $client){
            array_push($inclause['mat__cli__id'] , $client->getCli__id());
        }

        if (empty($inclause['mat__cli__id'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'Vous n avez aucun sites en gestion'
            ] , 404 , 'not found');
        }

        // cas de parametre spécifiés :
        if (!empty($_GET)){
            
            if (!empty($_GET['search'])) {
                if (!empty($_GET['limit'])) {
                    $limit = intval($_GET['limit']);
                }
                $order_array =  $materielRepository->getOrder($_GET);
                unset($inclause['mat__marque']);
                unset($inclause['mat__kw_tg']);
                unset($inclause['mat__type']);
                $list = $materielRepository->findMat($inclause , $_GET['search'] , $limit , $order_array);

                if (empty($list)) {
                    return $responseHandler->handleJsonResponse([
                        'msg' => 'Aucun materiel n a été trouvé'
                    ] , 404 , 'not found');
                } else {
                    return $responseHandler->handleJsonResponse($list, 200 , 'ok ');
                }

            }else{
                  //parametre cli__id est specifique :
                // $order_array =  $materielRepository->getOrder($_GET);
                
                if(!empty($_GET['mat__cli__id'])){
                    $temp = [];
                    foreach ($_GET['mat__cli__id'] as $value) {
                            foreach ($inclause['mat__cli__id'] as $cli) {
                                if ($value == $cli) {
                                        array_push($temp , $cli);
                                }
                            }
                    }
                    $inclause['mat__cli__id']  = $temp ;
                    unset($_GET['mat__cli__id']);
                }
                if(empty($inclause['mat__cli__id'])){
                    return $responseHandler->handleJsonResponse([
                        'msg' => 'Vous ne pouvez pas consulter le parc matériel des autres sites'
                    ] , 404 , 'not found');
                }
                
                if(!empty($_GET['mat__marque'])){
                    foreach ($_GET['mat__marque'] as $value) {
                            array_push($inclause['mat__marque'] , $value);   
                    }
                    unset($_GET['mat__marque']);
                }

                if(!empty($_GET['mat__kw_tg'])){
                    foreach ($_GET['mat__kw_tg'] as $value) {
                            array_push($inclause['mat__kw_tg']  ,$value);      
                    }
                    unset($_GET['mat__kw_tg']);
                }

                if(!empty($_GET['mat__type'])){
                    foreach ($_GET['mat__type'] as $value) {
                            array_push($inclause['mat__type']  ,$value);      
                    }
                    unset($_GET['mat__type']);
                }

                foreach ($inclause as $key => $value){
                    if (empty($value)) 
                        unset($inclause[$key]);
                }

                if (!empty($_GET['limit'])) {
                    $limit = intval($_GET['limit']);
                }
            
                $list = $materielRepository->findMat($inclause , [] , $limit , []);
                if (empty($list)) {
                    return $responseHandler->handleJsonResponse([
                        'msg' => 'Aucun materiel n a été trouvé'
                    ] , 404 , 'not found');
                } else {
                    return $responseHandler->handleJsonResponse($list, 200 , 'ok ');
                }
            }
        }else {
            //recherche standard dans le parc client :
            foreach ($inclause as $key => $value){
                if (empty($value)) 
                    unset($inclause[$key]);
            }
            var_dump($inclause);
            $list = $materielRepository->findMat($inclause , [] , 30 , []);
            if (empty($list)) {
                return $responseHandler->handleJsonResponse([
                    'msg' => 'Aucun materiel n a été trouvé'
                ] , 404 , 'not found');
            } else {
                return $responseHandler->handleJsonResponse($list, 200 , 'ok ');
            }
        }


    
        
    }

}