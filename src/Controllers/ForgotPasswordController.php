<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Entities\User;
use Src\Entities\Client;
use Src\Entities\Confirm;
use Src\Services\Security;
use Src\Services\ResponseHandler;
use Src\Services\MailerServices;
use Src\Repository\UserRepository;
use Src\Controllers\BaseController;
use Src\Repository\ClientRepository;
use Src\Repository\ConfirmRepository;

Class ForgotPasswordController  extends  BaseController {

    public static function path(){
        return 'forgot/password';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'forgotPassword',
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet d envoyer un lien sur le mail de l utilisateur pour qu il regenre son mot de passe' ,
                'reponse' => 'renvoi un message de succès avec un lien ', 
                "Auth" => 'Public ' 
             ]
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

    public static function get(){
        $database = new Database();
        $database->DbConnect();
        $mailer = new MailerServices();
        $responseHandler = new ResponseHandler();
        $userRepository = new UserRepository('User' , $database , User::class);
        $confirmRepository = new ConfirmRepository('confirm' , $database , Confirm::class);

        if (empty($_GET))
            return $responseHandler->handleJsonResponse('Unknow User' , 404, 'Bad Request');

        if (empty($_GET['user__mail'])) 
            return $responseHandler->handleJsonResponse('Le parametre user__email est obligatoire' , 400 , 'Bad Request');

        $user = $userRepository->findOneBy(['user__mail' => $_GET['user__mail'] ] , true);

        if(!$user instanceof User) 
            return $responseHandler->handleJsonResponse('Utilisateur inconnu' , 404 , 'Bad Request');

            $confirm = $confirmRepository->findOneBy(['confirm__user' => $_GET['user__mail']] , true);
            if(!$confirm instanceof Confirm ){
                $confirm = New Confirm();
                $confirm->setConfirm__user($_GET['user__mail']);
                $uniqueKey = strtoupper(substr(sha1(microtime()), rand(0, 5), 20));  
                $uniqueKey  = implode("-", str_split($uniqueKey, 5));
                $confirm->setConfirm__key($uniqueKey);
                $confirm->setConfirm__used(0);
                $date = date('Y-m-d H:i:s');
                $date = date('Y-m-d H:i:s', strtotime($date. ' +25 hours'));
                $confirm->setConfirm__exp( $date);
                $confirmRepository->insert((array)$confirm);
            }else{
                $uniqueKey = strtoupper(substr(sha1(microtime()), rand(0, 5), 20));  
                $uniqueKey  = implode("-", str_split($uniqueKey, 5));
                $confirm->setConfirm__key($uniqueKey);
                $confirm->setConfirm__used(0);
                $date = date('Y-m-d H:i:s');
                $date = date('Y-m-d H:i:s', strtotime($date. '  +25 hours'));
                $confirm->setConfirm__exp( $date);
                $confirmRepository->update((array)$confirm);
            }


            $body_mail = $mailer->renderBody($mailer->header(), $mailer->bodyResetPassword('http://localhost:8080/myRecode/forgot/password?confirm__key='.$confirm->getConfirm__key().'&confirm__user='.$confirm->getConfirm__user().''), $mailer->signature());
            $mailer->sendMail($_GET['user__mail'] , 'confirmation de votre compte Myrecode' ,  $body_mail );
            $response = [
                $data = $_GET['user__mail'] ,
                $message = 'Un lien afin de réinitialiser votre mot de passe à été envoyé à  '.$_GET['user__mail'].'  '
            ];
            return $responseHandler->handleJsonResponse($response , 401 , 'Unauthorized');

    }

}