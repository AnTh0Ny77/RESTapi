<?php
namespace Src\Services;
require  '././vendor/autoload.php';

Class ResponseHandler {

	public function handleJsonResponse($data, int $ResponseCode, string $message){
        $data = json_encode($data);
        header('HTTP/1.0 '.$ResponseCode.' '.$message.'');
        header('Content-Type: application/json; charset=utf-8');
        return $data;
    }
}