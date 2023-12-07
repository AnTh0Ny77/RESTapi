<?php

namespace Src\Controllers;

require  '././vendor/autoload.php';

use Src\Database;
use Src\Entities\User;
use Src\Entities\Confirm;
use Src\Services\Security;
use Src\Services\MailerServices;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Controllers\BaseController;
use Src\Repository\ConfirmRepository;
use Src\Entities\ShopAVendre;
use Src\Entities\Commercial;
use Src\Repository\CommercialRepository;
use Src\Repository\RefreshRepository;
use Src\Controllers\UserController;
use Src\Entities\Client;
use Src\Repository\ClientRepository;
use Src\Repository\ShopAVendreRepository;
use Src\Repository\ShopCmdRepository;
use Src\Repository\ShopCmdLigneRepository;
use GuzzleHttp\ClientHtpp;
use GuzzleHttp\Promise;
use Src\Entities\ShopCmd;
use Src\Entities\ShopCmdLigne;
use Src\Controllers\NotFoundController;
use Src\Repository\ShopArticleRepository;
use Src\Entities\ShopArticle;


class MailCmdController extends BaseController
{

    public static function path(){
        return '/mailCommande';
    }

    public static function renderDoc()
    {
        $doc = [
            [
                'name' => 'mail_commande',
                "tittle" => 'Mail de commande',
                'method' => 'POST',
                'path' => self::path(),
                'description' => 'permet d envoyer un mail de confirmation de commande',
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                        'scm__id'
                    ]
                ],
                'reponse' => 'renvoi ue reponse de succes ou dechec ',
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
                return self::post();
                break;

            case 'GET':
                return $notFound::index();
                break;

            default:
                return $notFound::index();
                break;
        }
    }

    public static  function handleResponse($response){
        
        if($response->getStatusCode() <300){
            return [
            'code' => $response->getStatusCode(),
            'data' => json_decode($response->getBody()->read(16384087),true)['data'] , 
            'http_errors' => false
            ];
        }
        
        return [
        'code' => $response->getStatusCode(),
        'msg' => json_decode($response->getBody()->read(16384),true)['msg'] , 
        'http_errors' => false
        ];
    }


    public static function post(){
        
        $database = new Database();
        $database->DbConnect();
        $security = new Security();
        $responseHandler = new ResponseHandler();
        $mailer = new MailerServices();
        $shopCmdRepository = new ShopCmdRepository('shop_cmd', $database, ShopCmd::class);
        $shopAvendreRepository = new ShopAVendreRepository('shop_avendre' , $database , ShopAVendre::class);
        $userRepository = new UserRepository('user', $database, User::class);
        $shopCmdLigneRepository = new ShopCmdLigneRepository('shop_cmd_ligne', $database, ShopCmdLigne::class);
        // $lienUserClientRepository = new LienUserClientRepository('lien_user_client', $database, User::class);
        $ShopArticleRepository = new ShopArticleRepository('shop_article' , $database , ShopArticle::class);
        $clientRepository = new ClientRepository('client' , $database , Client::class );
        $commercialRepository = new CommercialRepository('commercial' , $database , Commercial::class );
        $security = new Security();

       
        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;
    
        $id_user = UserController::returnId__user($security)['uid'];
        $user = $userRepository->findOneBy(['user__id' => $id_user] , true);
        $body = json_decode(file_get_contents('php://input'), true);
        
        if (empty($body['scm__id'])) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'scm__id semble vide ',
            ], 401, 'bad request');
        }
        
        $cmd = $shopCmdRepository->findOneBy(['scm__id' => $body['scm__id'] ] , false);
        if (empty($cmd)) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'cmd inconnue ',
            ], 401, 'bad request');
        }
      
        $ligne = $shopCmdLigneRepository->findBy(['scl__scm_id' =>  $body['scm__id'] ] , 100 , ['scl__id' => 'ASC']);
        $def_array = [];
        $sossuke_array = [];
        foreach ($ligne as $key => $value){
            $array_item = $value;
            $avendre = $shopAvendreRepository->findOneBy(['sav__id' =>  $value['scl__ref_id'] ], false);
            $article = $ShopArticleRepository->findOneBy(['sar__ref_id' => $avendre['sav__ref_id']] , false);
            $array_item['temp'] = $article;
            array_push($def_array , $array_item);
            $sossuke_line = [ ];
            $sossuke_line['scl__ref_id'] = $value['scl__ref_id'];
            $sossuke_line['scl__prix_unit'] = $value['scl__prix_unit'];
            $sossuke_line['scl__qte'] = $value['scl__qte'];
            $sossuke_line['scl__gar_mois'] = $value['scl__gar_mois'];
            $sossuke_line['scl__gar_prix'] = $value['scl__gar_prix'];
            $sossuke_line['sar__description'] = $article['sar__description'];
            $sossuke_line['sav__etat'] = $avendre['sav__etat'];
            $sossuke_line['sav__gar_std'] = $avendre['sav__gar_std'];
            $sossuke_line['sar__ref_constructeur'] = $article['sar__ref_constructeur'];
            array_push( $sossuke_array , $sossuke_line);
        }

        
        $results =  $clientRepository->findOneBy(['cli__id' => $cmd['scm__client_id_fact']] , true);
                   
                    if ($results instanceof Client) {
                        $com = $commercialRepository->findOneBy(['com__id' =>  $results->getCli__com1()] , true);
                        if (!$com instanceof Commercial) {
                            return $responseHandler->handleJsonResponse([
                                'msg' => 'Le commercial du client n a pas été trouvé'
                            ] , 400 , 'Bad Request');
                        }else{
                          
                            //////////////////////////////////////
                            //creation de la commande sur sossuke :
                            // $sossuke_commande = [];
                            // $sossuke_commande['scm__user_id'] = $results->getCli__com1();
                            // $sossuke_commande['scm__prix_port'] = $cmd['scm__prix_port'];
                            // $sossuke_commande['scm__client_id_livr'] = $cmd['scm__client_id_livr'];
                            // $sossuke_commande['scm__client_id_fact'] = $cmd['scm__client_id_fact'];
                            // $sossuke_commande['ligne'] = $sossuke_array;
                            // $sossuke_commande['secret'] = "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528";
                            
                            //envoi à l'api de sossuke/////////////////
                            $config = json_decode(file_get_contents('config.json'));
                           
                            $guzzle = new \GuzzleHttp\Client(['base_uri' => $config->guzzle->host]);
                           
                            try {

                                var_dump($response = $guzzle->post('/SoftRecode/apiCmdTransfert', [ 'json' => [
                                    "scm__user_id" => $results->getCli__com1() , 
                                    "scm__prix_port" => $cmd['scm__prix_port'], 
                                    "scm__client_id_livr" => $cmd['scm__client_id_livr'], 
                                    "scm__client_id_fact" => $cmd['scm__client_id_fact'],
                                    "ligne" =>  $sossuke_array , 
                                    "secret" => "heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528"
                                ] ]));
                                die();
                            } catch (ClientException $exeption) {
                                
                                $response = $exeption->getResponse();
                            }
                            var_dump($response); 
                            die();
                            $response = self::handleResponse($response);
                            $cmd__id  = $response["data"];
                            $shopCmdRepository->updateFromSossuke($body['scm__id'] , $cmd__id);
                            $shopCmdLigneRepository->updateFromSossuke($body['scm__id'] , $cmd__id);
                            //mis a jour de l ID de la commande et de l ID des lignes 
                            $cmd = $shopCmdRepository->findOneBy(['scm__id' => $cmd__id] , false);
                            //ENVOI DES 2 MAILS A JOUR 
                            $body_mail = $mailer->renderBody($mailer->header(), $mailer->renderBodyCommande($cmd ,$def_array), $mailer->signature()); 
                            $mailer->sendMail( $user->getUser__mail(), 'Confirmation de votre commande MyRecode',  $body_mail);
                            $mailer->sendMail( $com->getCom__email(), 'Une commande vous à été passée par '.$results->getCli__nom().'',  $body_mail);
                        }
                    } else return $responseHandler->handleJsonResponse('Aucun clients facturé trouvés' , 404 , 'Not Found');

        return $responseHandler->handleJsonResponse([
            "data" => 'l email à été transmis ',
        ], 200, 'Ok !');
        
    }
}