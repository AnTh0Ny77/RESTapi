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
use Src\Services\Security;

Class ImageClientController  extends  BaseController{

    public static function path(){
        return '/clientimage';
    }

    public static function renderDoc(){
        $doc = [
            [
                'name' => 'postImageClient',
                "tittle" => 'Mise a jour de photo de profil', 
                'method' => 'POST',
                'body' =>  [
                    'type' => 'form-data',
                    'fields' => [
                            'cli__id',
                            'cli__logo' 
                    ]
                    ],
                'path' => self::path(),
                'description' => 'Permet d uploader ou de mettre à jour une image pour le client ' ,
                'reponse' => 'renvoi un tableau d objet de type client', 
                "Auth" => 'JWT + ADMIN ROLE' 
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
        $security = new Security();
        $userRepository = new UserRepository('User' , $database , User::class);
        $clientRepository = new ClientRepository('Client' , $database , Client::class ) ;
        
        //controle du client 
        if (empty($_POST['cli__id'])) 
            return $responseHandler->handleJsonResponse('La  société n est pas précisée' , 401 , 'Bad Request');

        $client = $clientRepository->findOneBy(['cli__id' => intval($_POST['cli__id']) ] , true);
        if (!$client instanceof CLient) 
            return $responseHandler->handleJsonResponse('Le client est inconnu' , 404 , 'NOT FOUND');
        
        // authentification du user 
        $auth = self::Auth($responseHandler,$security);
        if ($auth != null) 
            return $responseHandler->handleJsonResponse('Une reconnexion est requise' , 498 , ' JWT TOKEN NOT FOUND');

        //controle du champ de couverture 
        if (empty($_FILES['cover'])) 
            return $responseHandler->handleJsonResponse('Le champs d image est obligatoire' , 401 , 'Bad Request');

        $fileName = $_FILES['cover']['name'];
        $tempPath = $_FILES['cover']['tmp_name'];
        $fileSize = $_FILES['cover']['size'];

        if (empty($fileName)) 
            return $responseHandler->handleJsonResponse('Merci de télécharger une image' , 401 , 'Bad Request');

        //controle de l extension et de la taille de l image :
        $fileExtension = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
        $validExtension = array('jpeg' , 'jpg' , 'png' , 'gif');

        if (!in_array($fileExtension, $validExtension)) 
            return $responseHandler->handleJsonResponse('Le format de l image utilisée n est pas valide ' , 401 , 'Bad Request');
        
        if ($fileSize > 10000000) 
            return $responseHandler->handleJsonResponse('L image utilisée est trop volumineuse' , 401 , 'Bad Request');
        
        $pathToFile = 'public/img/clients/'. $client->getCli__id();
        if (is_dir($pathToFile)) 
            self::deleteDirectory($pathToFile);
        
        if (!mkdir($pathToFile, 7777)) 
            return $responseHandler->handleJsonResponse('Un problème est survenu dans la creation du dossier client ' , 500 , 'Internal server error');
        
        if (!move_uploaded_file($tempPath , $pathToFile. '/'.$fileName)) 
            return $responseHandler->handleJsonResponse('Un problème est survenu dans la sauvergarde de l image' , 500 , 'Internal server error');
         
        $client->setCli__logo($fileName);

        $client = $clientRepository->UpdateOne( (array) $client); 

        if (!$client instanceof Client) 
            return $responseHandler->handleJsonResponse('Un problème est survenu dans la sauvergarde de la société' , 500 , 'Internal server error');
            

        return $responseHandler->handleJsonResponse('L image à ete mise a jour avec succès' , 201 , 'Ressource created');
        
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