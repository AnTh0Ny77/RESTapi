<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Controllers\NotFoundController;
use Src\Entities\Confirm;
use Src\Services\Security;
use Src\Services\MailerServices;
use Src\Repository\UserRepository;
use Src\Entities\User;
use Src\Repository\ConfirmRepository;
use Src\Repository\RefreshRepository;


Class LoginController {

    public static function path(){
        return '/login';
    }

    public static function renderDoc(){
        $doc = [
           [
                'name' => 'login',
                "tittle" => 'Login', 
                'method' => 'POST',
                'path' => self::path(),
                'description' => 'permet à l utilisateur de se connecter ' ,
                'body' =>  [
                    'type' => 'application/json',
                    'fields' => [
                            'user_mail' , 
                            'user__password'
                    ]
                    ],
                'reponse' => 'renvoi un objet de type User avec un token et refresh_token à conserver' , 
                "Auth" => 'PUBLIC'
                
            ] 
        ];
        return $doc;
    }


    public static function index($method,$data){
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
        $confirmRepository = new ConfirmRepository('confirm' , $database , Confirm::class);
        $responseHandler = new ResponseHandler();
        $mailer = new MailerServices();
        $userRepository = new UserRepository('user' , $database , User::class );
        $refreshRepository = new RefreshRepository($database);
        $body = json_decode(file_get_contents('php://input'), true);
        $login = $userRepository->loginUser($body);
       
        if (!$login instanceof User){
            $response = [
                 'msg' => $login, 
                 'data' =>  (array) $body 
            ];
            return $responseHandler->handleJsonResponse($response , 401 , 'Unauthorized');
        }

        if (intval($login->getUser__confirm()) == 0) {

            $confirm = $confirmRepository->findOneBy(['confirm__user' => $body['user__mail']] , true);
            if(!$confirm instanceof Confirm ){
                $confirm = New Confirm();
                $confirm->setConfirm__user($body['user__mail']);
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
           
            $body_mail = $mailer->renderBody($mailer->header(), $mailer->bodyConfirmUser('http://localhost:8080/myRecode/confirm?confirm__key='.$confirm->getConfirm__key().'&confirm__user='.$confirm->getConfirm__user().''), $mailer->signature());
            $mailer->sendMail($body['user__mail'] , 'confirmation de votre compte Myrecode' ,  $body_mail );
            $response = [
                $data = $body ,
                $msg = 'vous devez valider votre adresse email avant de vous connecter,
                 un lien vous à été envoyé '
            ];
            return $responseHandler->handleJsonResponse($response , 401 , 'Unauthorized');
        }

        $login->setToken($security->returnToken($login->getUser__id()));
        $refresh_token = $refreshRepository->insertOne($login->getUser__id());
        $login->setRefresh_token($refresh_token);
        $response = [
            'id' =>  $login->getUser__id(),
            'refresh_token' => $login->getRefresh_token(), 
            'token' => $login->getToken()
        ];
        return $responseHandler->handleJsonResponse($response , 200 , 'success');
    }
}