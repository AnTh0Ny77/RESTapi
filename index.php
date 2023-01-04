<?php

require "vendor/autoload.php";
use Src\Controllers\MaxController;
use Src\Controllers\UserController;
use Src\Controllers\LoginController;
use Src\Controllers\ClientController;
use Src\Controllers\TicketController;
use Src\Controllers\KeywordController;
use Src\Controllers\RefreshController;
use Src\Controllers\BasePathController;
use Src\Controllers\MaterielController;
use Src\Controllers\NotFoundController;
use Src\Controllers\DocumentsController;
use Src\Controllers\ListFilesController;
use Src\Controllers\TransfertController;
use Src\Controllers\UserSitesController;
use Src\Controllers\CommercialController;
use Src\Controllers\ConfirmUserController;
use Src\Controllers\ImageClientController;
use Src\Controllers\TicketLigneController;
use Src\Controllers\FilesTicketsController;
use Src\Controllers\TicketChampsController;
use Src\Controllers\NotificationsController;
use Src\Controllers\ForgotPasswordController;

header("Access-Control-Allow-Origin: *");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_WARNING);
error_reporting(E_ERROR);
error_reporting(E_NOTICE);
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
        echo ClientController::index($_SERVER['REQUEST_METHOD'],$data);
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

    case $config->urls->base.CommercialController::path().$data:
        echo CommercialController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.ConfirmUserController::path().$data:
        echo ConfirmUserController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.ForgotPasswordController::path().$data:
        echo ForgotPasswordController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.ImageClientController::path().$data:
        echo ImageClientController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.MaterielController::path().$data:
        echo MaterielController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.MaterielController::path().$data:
        echo MaterielController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.TicketController::path().$data:
        echo TicketController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.TicketLigneController::path().$data:
        echo TicketLigneController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.TicketChampsController::path().$data:
        echo TicketChampsController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.MaxController::path().$data:
        echo MaxController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.KeywordController::path().$data:
        echo KeywordController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.UserSitesController::path().$data:
        echo UserSitesController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.NotificationsController::path().$data:
        echo NotificationsController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base.FilesTicketsController::path().$data:
        echo FilesTicketsController::index($_SERVER['REQUEST_METHOD'],$data);
        break;

    case $config->urls->base . DocumentsController::path() . $data:
        echo DocumentsController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base . ListFilesController::path() . $data:
        echo ListFilesController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base . TransfertController::path() . $data:
        echo TransfertController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

	default:
		header('HTTP/1.0 404 not found');
        echo NotFoundController::index();
		break;
}
?>
