<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Entities\User;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Repository\UserRepository;
use Src\Services\MailerServices;
use Src\Controllers\BaseController;
use Src\Repository\RefreshRepository;
use Src\Controllers\NotFoundController;
use Src\Repository\LienUserClientRepository;
use Src\Entities\Confirm;
use Src\Repository\ClientRepository;
use Src\Repository\ConfirmRepository;

Class UserSossukeController extends BaseController{

    public static function path(){
        return '/usersossuke';
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
                return self::post();
                break;

            case 'GET':
                return $notFound::index();
                break;

            case 'PUT':
                return self::put();
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

	public static function post(){
        $database = new Database();
        $database->DbConnect();
        $mailer = new MailerServices();
        $responseHandler = new ResponseHandler();
        $userRepository = new UserRepository('user' , $database , User::class );
        $refreshRepository = new RefreshRepository($database);

        
        $body = json_decode(file_get_contents('php://input'), true);

        if(!empty($body['_METHOD']) and $body['_METHOD'] == 'PUT' ) {
            return self::put();
            die();
        }

        if (empty($body['secret']) && $body['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Opération impossible'
            ], 404, 'bad request');
        }

        if (!empty($body['user__mail'])) {
            $already_exist = $userRepository->findOneBy([ 'user__mail' => $body['user__mail'] ], false);
            if (!empty($already_exist)) {
                return $responseHandler->handleJsonResponse([
                    "data" => $already_exist['user__id']
                ] , 201 , 'ok');
            }
        }

        $body = [
            'user__mail' => $body['user__mail'] , 
            "user__password" => $body['user__password'], 
            "user__nom" => $body['user__nom'], 
            "user__prenom" => $body['user__prenom'],
            "user__fonction" => $body['user__fonction']
        ];

        $user = $userRepository->postUser($body);
        if (!$user instanceof User) {
            return $responseHandler->handleJsonResponse([
                "msg" => $user
            ], 400 , 'Bad Request');
        }

        $refresh_token = $refreshRepository->insertOne($user->getUser__id());
        $user->setRefresh_token($refresh_token);
        $confirm = new Confirm();
        $confirmRepository = new ConfirmRepository('confirm', $database, Confirm::class);
        $confirm->setConfirm__user($body['user__mail']);
        $uniqueKey = strtoupper(substr(sha1(microtime()), rand(0, 5), 20));
        $uniqueKey  = implode("-", str_split($uniqueKey, 5));
        $confirm->setConfirm__key($uniqueKey);
        $confirm->setConfirm__used(0);
        $date = date('Y-m-d H:i:s');
        $date = date('Y-m-d H:i:s', strtotime($date . ' +25 hours'));
        $confirm->setConfirm__exp($date);
        $confirmRepository->insert((array)$confirm);

        $body_mail = $mailer->renderBody($mailer->header(), $mailer->bodyResetPassword('http://myrecode.fr/pw_modif.php?getpw&confirm__key=' . $confirm->getConfirm__key() . '&confirm__user=' . $confirm->getConfirm__user() . ''), $mailer->signature());
        $mailer->sendMail($_GET['user__mail'], 'Définition de votre nouveau mot de passe ',  $body_mail);

        return $responseHandler->handleJsonResponse([
            "data" => $user->getUser__id()
        ] , 201 , 'ok');
    }

    public static function put(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $userRepository = new UserRepository('user', $database, User::class);
        $refreshRepository = new RefreshRepository($database);
        $body = json_decode(file_get_contents('php://input'), true);

        if (empty($body['secret']) && $body['secret'] != 'heAzqxwcrTTTuyzegva^5646478§§uifzi77..!yegezytaa9143ww98314528') {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Opération impossible'
            ], 404, 'bad request');
        }

        $body = [
            "user__id" => $body['user__id'] , 
            "user__nom" => $body['user__nom'], 
            "user__prenom" => $body['user__prenom'],
            "user__fonction" => $body['user__fonction'], 
            "user__gsm" =>  $body['user__gsm']
        ];

        if (empty($body['user__id'])) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'l id de l utilisateur nest pas renseigné'
            ], 401, 'bad request');
        }

        $user = $userRepository->findOneBy(['user__id' => $body['user__id']], false);
        if (empty($user)) {
            return $responseHandler->handleJsonResponse([
                "msg" => 'l utilisateur nexiste pas'
            ], 401, 'bad request');
        }

        $user = $userRepository->UpdateUser($body);
        if (!$user instanceof User) {
            return $responseHandler->handleJsonResponse([ "msg" => $user],400,'Bad Request');
        }else {
           
            return $responseHandler->handleJsonResponse([
                "data" => "utilisateur mis a jour avec succès"
            ], 201, 'ok');
        }
    }

   

}