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
        return 'forgot';
    }

    public static function renderDoc(){
        $doc = [
             [
                'name' => 'forgotPassword',
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet d envoyer un lien sur le mail de l utilisateur pour qu il regenere son mot de passe' ,
                'reponse' => 'renvoi un message de succès avec un lien ', 
                "Auth" => 'Public ' 
             ],
             [
                'name' => 'PostPassword',
                'method' => 'POST',
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                            'confirm__key' , 
                            'user__password'
                    ]
                    ],
                'path' => self::path(),
                'description' => 'Reçois une clefs secrete et nouveau password' ,
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


            $body_mail = $mailer->renderBody($mailer->header(), $mailer->bodyResetPassword('http://localhost:8080/myRecode/reset?confirm__key='.$confirm->getConfirm__key().'&confirm__user='.$confirm->getConfirm__user().''), $mailer->signature());
            $mailer->sendMail($_GET['user__mail'] , 'Définition de votre nouveau mot de passe ' ,  $body_mail );
            $response = [
                $data = $_GET['user__mail'] ,
                $message = 'Un lien de résiliation votre mot de passe à été envoyé à  '.$_GET['user__mail'].'  '
            ];
            return $responseHandler->handleJsonResponse($response , 200 , 'Success');
    }

    
    public static function post(){
        $database = new Database();
        $database->DbConnect();
        $mailer = new MailerServices();
        $responseHandler = new ResponseHandler();
        $userRepository = new UserRepository('User' , $database , User::class);
        $confirmRepository = new ConfirmRepository('confirm' , $database , Confirm::class);

        $body = json_decode(file_get_contents('php://input'), true);

        if(empty($body['confirm__key']))
            return $responseHandler->handleJsonResponse('Le champ confirm__key est obligatoire' , 400 , 'Bad Request');

        $confirm =  $confirmRepository->findOneBy(['confirm__key' => $body['confirm__key']] ,true);

        if (!$confirm instanceof Confirm) 
            return $responseHandler->handleJsonResponse('La confirm__key est incorrecte' , 400 , 'Bad Request');

        if(empty($body['user__password']))
            return $responseHandler->handleJsonResponse('Le champ user__password est obligatoire' , 400 , 'Bad Request');

        $user = $userRepository->findOneBy(['user__mail' => $confirm->getConfirm__user() ] , true);

        if (!$user instanceof User) 
            return $responseHandler->handleJsonResponse('Utilisateur introuvable' , 400 , 'Bad Request');

        $confirm->setConfirm__used(1);
        $confirmRepository->update((array) $confirm);

        $pass  = $user->setUser__password($body['user__password']);
        if (!$pass instanceof User) 
            return $responseHandler->handleJsonResponse($pass , 400 , 'Bad Request');

        $password = $userRepository->encrypt_password($body['user__password']);
        $user->setUser__password($password);
        $userRepository->update((array) $user);

        return $responseHandler->handleJsonResponse('Le mot de passe à bien été mis à jour' , 200 , 'Bad Request');
        
    }

}