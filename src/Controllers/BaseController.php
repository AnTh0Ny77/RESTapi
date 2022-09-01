<?php
namespace Src\Controllers;
use Src\Services\Security;
use Src\Services\ResponseHandler;
require  '././vendor/autoload.php';


Class BaseController { 

    public static function Auth( ResponseHandler $responseHandler , Security $security){
        $token = $security->getBearerToken();
        if (empty($token)) {
            $body = [
                $message = 'JWT not found '
            ];
            return $responseHandler->handleJsonResponse($body , 401 , 'Unauthorized');
        }
        $isAuth = $security->verifyToken($token);
        if ($isAuth == false) {
            $body = [
                $message = 'invalid JWT'
            ];
            return $responseHandler->handleJsonResponse($body , 498 , 'Token expired/invalid');
        }
        $isExp = $security->verifyExp($token);
        if($isExp == false){
            $body = [
                $message = 'expired JWT'
            ];
            return $responseHandler->handleJsonResponse($body , 498 , 'Token expired/invalid');
        }

        return null;
    }

}