<?php
namespace Src\Repository;
require  '././vendor/autoload.php';
use Src\Database;
use Src\Entities\User;
use Src\Services\ResponseHandler;

Class UserRepository {


    public function postUser($mail , $pass , $nom , $prenom ){
        $responseHandler = new ResponseHandler;
        $user = new User();
        $pass  = $user->setUser__password($pass);
       
        if (!$pass instanceof User) 
            return $responseHandler->handleJsonResponse($pass , 400 , 'Bad request');
        
        

    }

    public function updateFields(){

    }
    
}