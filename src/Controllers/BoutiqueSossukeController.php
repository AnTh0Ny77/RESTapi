<?php

namespace Src\Controllers;

require  '././vendor/autoload.php';

use Src\Database;
use Src\Entities\User;
use Src\Entities\Client;
use Src\Entities\Materiel;
use Src\Services\Security;
use Src\Entities\ShopArticle;
use Src\Entities\ShopAVendre;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\UserController;
use Src\Repository\ClientRepository;
use Src\Repository\MaterielRepository;
use Src\Repository\ShopArticleRepository;
use Src\Repository\ShopAVendreRepository;
use Src\Repository\LienUserClientRepository;
use Src\Repository\ShopConditionRepository;
use Src\Entities\ShopCondition;

class BoutiqueSossukeController extends BaseController{

    public static function path()
    {
        return '/boutiqueSossuke';
    }

    public static function renderDoc()
    {
        $doc = [];
        return $doc;
    }

    public static function index($method, $data)
    {
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
        $responseHandler = new ResponseHandler();
        $materielRepository = new MaterielRepository('materiel', $database, Materiel::class);
        $ShopAVendreRepository = new ShopArticleRepository('shop_article' , $database, ShopArticle::class);
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client', $database, User::class);
        $userRepository = new UserRepository('user', $database, User::class);
        $security = new Security();
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body)) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'le body ne peut pas etre vide'
            ], 401, 'bad request');
        }

        if (empty($body['secret'])) {
            return $responseHandler->handleJsonResponse([
                'msg' => 'opération non autorisée'
            ], 401, 'bad request');
        } elseif (!empty($body['secret']) and $body['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' => 'opération non autorisée'
            ], 401, 'bad request');
        }

        // liste de tous les articles a vendre 
        if (!empty($body['shop_avendre'])) {
            $list = $ShopAVendreRepository->findAll();
            return $responseHandler->handleJsonResponse([
                'data' => $list
            ], 200, 'ok');
        }

        // liste des articles disponible pour ce client  :
        if (!empty($body['sav__cli_id']) and empty($body['sav__post']) and empty($body['sav__put'])) {
            $ShopAVRepository = new ShopAVendreRepository('shop_avendre' , $database, ShopAVendre::class);
            $list = $ShopAVRepository->findby(['sav__cli_id' =>  $body['sav__cli_id'] ] , 1000 , []);
            $resulst = [];
            foreach ($list as $key => $value) {
               $article =   $ShopAVendreRepository->findOneBy(['sar__ref_id' => $value['sav__ref_id']], false);
               $value['article'] = ( array ) $article; 
               array_push($resulst , (object) $value);
            }
            return $responseHandler->handleJsonResponse([
                'data' => $resulst
            ], 200, 'ok');
        }

        //liste des condition de commandes  
        if (!empty($body['sco__cli_id'])){
            $ShopConditions = new ShopConditionRepository('shop_condition' , $database, ShopCondition::class);
            $list = $ShopConditions->findOneby(['sco__cli_id' =>  $body['sco__cli_id'] ] , false);
            return $responseHandler->handleJsonResponse([
                'data' => $list
            ], 200, 'ok');
        }

        //ajout mis a jour des conditions de ventes  :
        if (!empty($body['sco__cli_id_r'])){
            $ShopConditions = new ShopConditionRepository('shop_condition' , $database, ShopCondition::class);
            $bodyT = [
                'sco__cli_id' => $body['sco__cli_id_r'], 
                'sco__type_port' => $body['sco__type_port'], 
                'sco__francoa' => $body['sco__francoa'] , 
                'sco__prix_port' => $body['sco__prix_port'] ,
                'sco__cli_id_fact' => $body['sco__cli_id_fact'] , 
                'sco__vue_ref' => $body['sco__vue_ref']
            ];
            $verify = $ShopConditions->findOneby(['sco__cli_id' =>  $bodyT['sco__cli_id'] ] , false);
            if (!empty($verify)) {
                $ShopConditions->update($bodyT);
            }else{
                $ShopConditions->insert($bodyT);
                var_dump($ShopConditions->insert($bodyT));
                die();
            }
            return $responseHandler->handleJsonResponse([
                'data' => true 
            ], 200, 'ok');
        }

        //ajout d un article a vendre pour unj client 
        if (!empty($body['sav__cli_id']) and !empty($body['sav__post']) and empty($body['sav__put'])) {
            $ShopAVRepository = new ShopAVendreRepository('shop_avendre' , $database, ShopAVendre::class);
            $body = [
                'sav__cli_id' => $body['sav__cli_id'], 
                'sav__ref_id' => $body['sav__ref_id'], 
                'sav__etat' => $body['sav__etat'] , 
                'sav__prix' => $body['sav__prix'] ,
                'sav__memo_recode' => $body['sav__memo_recode'] , 
                'sav__gar_std' => $body['sav__gar_std'] ,
                'sav__cli_id' => $body['sav__cli_id'], 
                'sav__dlv' => $body['sav__dlv'], 
                'sav__gar1_mois' => $body['sav__gar1_mois'] , 
                'sav__gar1_prix' => $body['sav__gar1_prix'] ,
                'sav__gar2_mois' => $body['sav__gar2_mois'] , 
                'sav__gar2_prix' => $body['sav__gar2_prix'] 
            ];
            $id  = $ShopAVRepository->insert($body);
            return $responseHandler->handleJsonResponse([
                'data' => $id
            ], 200, 'ok');
        }
        //mise à jour d un article a vendre pour un client 
        if (!empty($body['sav__cli_id']) and empty($body['sav__post']) and !empty($body['sav__put'])) {
            $ShopAVRepository = new ShopAVendreRepository('shop_avendre' , $database, ShopAVendre::class);
            $body = [
                'sav__id' => $body['sav__id'] , 
                'sav__cli_id' => $body['sav__cli_id'], 
                'sav__ref_id' => $body['sav__ref_id'], 
                'sav__etat' => $body['sav__etat'] , 
                'sav__prix' => $body['sav__prix'] ,
                'sav__memo_recode' => $body['sav__memo_recode'] , 
                'sav__gar_std' => $body['sav__gar_std'] ,
                'sav__cli_id' => $body['sav__cli_id'], 
                'sav__dlv' => $body['sav__dlv'], 
                'sav__gar1_mois' => $body['sav__gar1_mois'] , 
                'sav__gar1_prix' => $body['sav__gar1_prix'] ,
                'sav__gar2_mois' => $body['sav__gar2_mois'] , 
                'sav__gar2_prix' => $body['sav__gar2_prix'] 
            ];
            $id = $ShopAVRepository->update($body);
            return $responseHandler->handleJsonResponse([
                'data' => $id
            ], 200, 'ok');
        }
    }
}
