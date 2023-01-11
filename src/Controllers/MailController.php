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
use Src\Repository\RefreshRepository;
use Src\Controllers\NotFoundController;


class MailController extends BaseController
{

    public static function path()
    {
        return '/mail';
    }

    public static function renderDoc()
    {
        $doc = [
            [
                'name' => 'mail',
                "tittle" => 'Mail',
                'method' => 'POST',
                'path' => self::path(),
                'description' => 'permet d envoyer un mail',
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                        'mail',
                        'text'
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


    public static function post()
    {
        $database = new Database();
        $database->DbConnect();
        $security = new Security();
        $responseHandler = new ResponseHandler();
        $mailer = new MailerServices();
        $security = new Security();
        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;
        $body = json_decode(file_get_contents('php://input'), true);

        // if (filter_var($body['mail'], FILTER_VALIDATE_EMAIL)) {
        //     return $responseHandler->handleJsonResponse([
        //         "msg" => 'le mail n est pas valide',
        //     ], 401, 'bad request');
        // }

        if (empty($body['text'])) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'Le text semble vide ',
            ], 401, 'bad request');
        }

        $body_mail = $mailer->renderBody($mailer->header(), $mailer->bodyMail($body['text']), $mailer->signature());
        $mailer->sendMail($body['mail'], 'Vous avez reçu un message de Myrecode',  $body_mail);
        return $responseHandler->handleJsonResponse([
            "data" => 'l email à été transmis ',
        ], 200, 'Ok !');
        
    }
}
