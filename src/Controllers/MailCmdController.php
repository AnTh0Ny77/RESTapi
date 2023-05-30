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
use Src\Repository\RefreshRepository;
use Src\Repository\ClientRepository;
use Src\Repository\ShopAVendreRepository;
use Src\Repository\ShopCmdRepository;
use Src\Repository\ShopCmdLigneRepository;
use Src\Entities\ShopCmd;
use Src\Controllers\NotFoundController;
use Src\Repository\ShopArticleRepository;
use Src\Entities\ShopArticle;


class MailCmdController extends BaseController
{

    public static function path()
    {
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
                'description' => 'permet d envoyer un mail deconfirmation de commande',
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


    public static function post(){
        
        $database = new Database();
        $database->DbConnect();
        $security = new Security();
        $responseHandler = new ResponseHandler();
        $mailer = new MailerServices();
        $shopCmdRepository = new ShopCmdRepository('shop_cmd', $database, ShopCmd::class);
        $shopAvendreRepository = new ShopAVendreRepository('shop_avendre' , $database , ShopAVendre::class);
        $shopCmdLigneRepository = new ShopCmdLigneRepository('shop_cmd_ligne', $database, ShopCmd::class);
        $lienUserClientRepository = new LienUserClientRepository('lien_user_client', $database, User::class);
        $ShopArticleRepository = new ShopArticleRepository('shop_article' , $database , ShopArticle::class);
        $security = new Security();
        // $auth = self::Auth($responseHandler, $security);
        // if ($auth != null)
        //     return $auth;
        $body = json_decode(file_get_contents('php://input'), true);
        var_dump('hye');
        die();
     
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
        
        $ligne = $shopCmdLigneRepository->findBy(['scl__scm_id' =>  $body['scm__id'] ] , false );

        foreach ($ligne as $key => $value) {
            $avendre = $shopAvendreRepository->findOneBy(['sav__id' =>  $value['scl__ref_id'] ], false);
            $article = $ShopArticleRepository->findOneBy(['saf__ref_id' => $avendre['sav__ref_id']] , false);
            $value['temp'] = $article;
        }

        $body_mail = $mailer->renderBody($mailer->header(), $mailer->renderBodyCommande($cmd ,$ligne), $mailer->signature());
        $mailer->sendMail('anthonybs.pro@gmail.com', 'Vous avez recu un message de Myrecode',  $body_mail);

        return $responseHandler->handleJsonResponse([
            "data" => 'l email à été transmis ',
        ], 200, 'Ok !');
        
    }
}