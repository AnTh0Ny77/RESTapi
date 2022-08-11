<?php
require "vendor/autoload.php";
use Src\Controllers\BasePathController;
use Src\Controllers\NotFoundController;
use Src\Controllers\ClientController;
use Src\Controllers\UserController;
use Src\Controllers\LoginController;
use Src\Controllers\RefreshController;

$request = $_SERVER['REQUEST_URI'];
$request = explode('?' ,$request, 2);
$data = null;

if (isset($request[1])) 
	$data = '?' . $request[1];

$request = $request[0] . $data ; 
$config =  json_decode(file_get_contents('config.json'));

switch($request){
	
	case $config->urls->base.BasePathController::path():
        echo BasePathController::index();
		break;

    case $config->urls->base.ClientController::path().$data:
        echo ClientController::index();
        break;
    
    case $config->urls->base.UserController::path().$data:
        echo UserController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.LoginController::path().$data:
        echo LoginController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.RefreshController::path().$data:
        echo RefreshController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

	default:
		header('HTTP/1.0 404 not found');
        echo NotFoundController::index();
		break;
}
?>
