<?php

namespace Src\Controllers;

require  '././vendor/autoload.php';

use ReallySimpleJWT\Validate;
use Src\Services\ResponseHandler;
use Src\Database;
use Src\Repository\ClientRepository;
use Src\Controllers\BaseController;
use Src\Repository\UserRepository;
use Src\Entities\Client;
use Src\Entities\TicketsLigne;
use Src\Repository\TicketLigneRepository;
use Src\Services\Security;
use Src\Services\MailerServices;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\ClientHtpp;
use GuzzleHttp\Promise;
use ZipArchive;

class PlanningController  extends  BaseController
{

    public static function path()
    {
        return '/planning';
    }

    public static function renderDoc()
    {
        $doc = [
             [
                'name' => 'getPlanning',
                'tittle' => ' Planning', 
                'method' => 'GET',
                'path' => self::path(),
                'description' => 'Permet d optenir le planning ',
                'reponse' => 'renvoi la variable data ou msg en cas d echec',
                "Auth" => 'JWT '
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
        $responseHandler = new ResponseHandler();
        $security = new Security();

        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;

        $config = json_decode(file_get_contents('config.json'));
        $guzzle = new \GuzzleHttp\Client(['base_uri' => $config->guzzle->host]);

        if (!empty($_GET['user'])) {
            try {
                $response = $guzzle->get('/SoftRecode/apiPlanning?user='. $_GET['user']);
            } catch (ClientException $exeption) {
                $response = $exeption->getResponse();
            }
        }else{
            try {
                $response = $guzzle->get('/SoftRecode/apiPlanning');
            } catch (ClientException $exeption) {
                $response = $exeption->getResponse();
            }
        }

        $data = $response->getBody()->read(12047878);
        $data = json_decode($data, true);

        if (!empty($data['data'])) {
            return $responseHandler->handleJsonResponse([
                'data' => $data['data'],
            ], 200, 'ok');
        }else{
            return $responseHandler->handleJsonResponse([
                'data' =>  [],
            ], 200, 'Pas de planning');
        }
    }


    public static function post(){
        
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $userRepository = new UserRepository('user', $database, User::class);
        $security = new Security();
        $mailer = new MailerServices();

        $auth = self::Auth($responseHandler, $security);
        if ($auth != null)
            return $auth;

        $body = json_decode(file_get_contents('php://input'), true);
        $config = json_decode(file_get_contents('config.json'));
        $guzzle = new \GuzzleHttp\Client(['base_uri' => $config->guzzle->host]);

        try {
            $response = $guzzle->post('/SoftRecode/apiPlanning' , [ 'json' =>  $body]);
        } catch (ClientException $exeption) {
            $response = $exeption->getResponse();
        }

        $data = $response->getBody()->read(12047878);
        $data = json_decode($data, true);

        
        if (!empty($data['data']['to__abs_veto_motif'])) {
            var_dump(UserController::returnId__user($security)['uid']);
            die();
            $id_user = UserController::returnId__user($security)['uid'];
            $user = $userRepository->findOneBy(['user__id' => $id_user], true);
          
            $body_mail = $mailer->RenderbodyAnnulAbsence($data['data']['nom'] , $data['data']['to__abs_veto_motif'], $$data['data']['to__info'] ,$data['data']['to__out'] , $data['data']['to__in'] ); 
            $mailer->sendMail( $user->getUser__abs_adress(), 'ANNULATION ABSENCE',  $body_mail);
            $mailer->sendMail( $user->getUser__mail(), 'ANNULATION ABSENCE',  $body_mail);
        }
  
        if (!empty($body['user__abs']) and !empty($body['to__out'])) {
            $body_mail = $mailer->RenderbodyAbsence($body['user__abs'] , $body['motif__string'] , $body['to__info'] ,$body['to__out'] , $body['to__in'] ); 
            $mailer->sendMail( $body['abs__adress'], 'ABSENCE',  $body_mail);
        }

        return $responseHandler->handleJsonResponse([
        'data' => $data['data'],
        ], 200, 'ok');
       
    }

}