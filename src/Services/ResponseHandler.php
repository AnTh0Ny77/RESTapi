<?php
namespace Src\Services;
require  '././vendor/autoload.php';

Class ResponseHandler {

	public function handleJsonResponse($data, int $ResponseCode, string $message){
        $data = json_encode($data );
        header('HTTP/1.0 '.$ResponseCode.' '.$message.'');
        header('Content-Type: application/json; charset=utf-8');
        return $data;
    }


    // public function handleFileResponse($file , int $ResponseCode, string $message){
    //     header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    //     header("Cache-Control: public"); // needed for internet explorer
    //     header("Content-Type: application/zip");
    //     header("Content-Transfer-Encoding: Binary");
    //     header("Content-Length:".filesize($file));
    //     header("Content-Disposition: attachment; filename=file.zip");
    //     readfile($attachment_location);
    // }
}