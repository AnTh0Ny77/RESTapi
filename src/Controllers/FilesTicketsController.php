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
use ZipArchive;

Class FilesTicketsController  extends  BaseController{

    public static function path(){
        return '/files';
    }

    public static function renderDoc(){
        $doc = [
            [
                'name' => 'postFilesTickets',
                "tittle" => 'Fichier', 
                'method' => 'POST',
                'body' =>  [
                    'type' => 'form-data',
                    'fields' => [
                            'tklc__id'
                    ]
                    ],
                'path' => self::path(),
                'description' => 'Permet d uploader ou de mettre à jour un fichier ' ,
                'reponse' => 'renvoi la variable data ou msg en cas d echec', 
                "Auth" => 'JWT ' 
                ],[
                    'name' => 'getFilesTickets',
                    'method' => 'GET',
                    'path' => self::path(),
                    'description' => 'Permet d uploader ou de mettre à jour un fichier ' ,
                    'reponse' => 'renvoi la variable data ou msg en cas d echec', 
                    "Auth" => 'JWT ' 
                ]
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
        $tiketLigne = new TicketLigneRepository('ticket_ligne' , $database , TicketsLigne::class);
        $security = new Security();
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $auth;
        
        if (empty($_GET['tkl__id'])) { 
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' La ligne de ticket n est pas précisée'
            ] , 404 , 'bad request');
        }

        $pathToFile = 'public/img/tickets/'. $_GET['tkl__id'];
        if ( ! is_dir($pathToFile)) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Aucun fichier pour cette ligne '
            ] , 404 , 'bad request');
        }
         
        $zip = new ZipArchive();
        $zip->open('ligne'.$_GET['tkl__id'].'.zip', ZipArchive::CREATE);
        $pathToFile = scandir($pathToFile);
        foreach($pathToFile as $file) {
           if (strlen($file) > 4) {
            $zip->addFile('public/img/tickets/'. $_GET["tkl__id"] .'/' .$file,  $file );
           }
        }
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Cache-Control: public"); // needed for internet explorer
        header("Content-Type: application/zip");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length:".filesize('ligne'. $_GET["tkl__id"] .'.zip'));
        header("Content-Disposition: attachment; filename=file.zip");
        
        readfile('ligne'. $_GET["tkl__id"] .'.zip');
        $zip->close();
    }

    public static function post(){
        $database = new Database();
        $database->DbConnect();
        $responseHandler = new ResponseHandler();
        $security = new Security();
        $tiketLigne = new TicketLigneRepository('ticket_ligne' , $database , TicketsLigne::class);
        //controle du client 
        if (empty($_POST['tkl__id'])){
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' La ligne de ticket n est pas précisée'
            ] , 404 , 'bad request');
        }


        $ligne = $tiketLigne->findOneBy(['tkl__id' => intval($_POST['tkl__id'])] , true);
        if (!$ligne instanceof TicketsLigne){
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Ligne inconnue'
            ] , 404 , 'bad request');
        }
        
        // authentification du user 
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
           return $auth;

        //controle du champ de couverture 
        if (empty($_FILES['file'])){
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Le champs file est obligatoire'
            ] , 404 , 'bad request');
        }

        $fileName = $_FILES['file']['name'];
        $tempPath = $_FILES['file']['tmp_name'];
        $fileSize = $_FILES['file']['size'];

        if (empty($fileName)){
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Merci de télécharger un fichier'
            ] , 404 , 'bad request');
        }

        //controle de l extension et de la taille de l image :
        $fileExtension = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
        $validExtension = array('jpeg' , 'jpg' , 'png' , 'gif' , 'pdf' , 'txt' );

        if (!in_array($fileExtension, $validExtension)) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' Merci de télécharger un fichier au format : jpeg , jpg , png , gif , pdf ou txt'
            ] , 401 , 'bad request');
        }
        
        if ($fileSize > 10000000) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  ' fichier trop volumineux'
            ] , 401 , 'bad request');
        }
        
        $pathToFile = 'public/img/tickets/'. $ligne->getTkl__id();
        $uniquename =  $fileName . '.' . $fileExtension;
        // if (is_dir($pathToFile)) 
        //     self::deleteDirectory($pathToFile);
        if ( ! is_dir($pathToFile)) {
            mkdir($pathToFile, 7777);
        }
        // if (!mkdir($pathToFile, 7777)){
        //     return $responseHandler->handleJsonResponse([
        //         'msg' =>  'Un problème est survenu dans la creation du dossier'
        //     ] , 500 , 'Internal server error');
        // }
        
        if (!move_uploaded_file($tempPath , $pathToFile. '/'.$uniquename)) {
            return $responseHandler->handleJsonResponse([
                'msg' =>  'Un problème est survenu dans la sauvergarde du fichier'
            ] , 500 , 'Internal server error');
        }
         
        return $responseHandler->handleJsonResponse([
            'data' =>  'Le fichier à été correctement sauvegardé'
        ] , 201 , 'ressource created');
        
    }

    public static function deleteDirectory($dir) {
        if (!file_exists($dir))
            return true;
    
        if (!is_dir($dir)) 
            return unlink($dir);
        
        foreach (scandir($dir) as $item){
            if ($item == '.' || $item == '..'){
                continue;
            }
    
            if (!self::deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)){
                return false;
            }
        }
        return rmdir($dir);
    }

}