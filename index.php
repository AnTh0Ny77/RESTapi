<?php

require "vendor/autoload.php";
use Src\Controllers\AdController;
use Src\Controllers\MaxController;
use Src\Controllers\MailController;
use Src\Controllers\RoleController;
use Src\Controllers\UserController;
use Src\Controllers\LoginController;
use Src\Controllers\ClientController;
use Src\Controllers\TicketController;
use Src\Controllers\KeywordController;
use Src\Controllers\RefreshController;
use Src\Controllers\ShopCmdController;
use Src\Controllers\BasePathController;
use Src\Controllers\MaterielController;
use Src\Controllers\NotFoundController;
use Src\Controllers\DocumentsController;
use Src\Controllers\ListFilesController;
use Src\Controllers\TransfertController;
use Src\Controllers\UserSitesController;
use Src\Controllers\VerifUserController;
use Src\Controllers\CommercialController;
use Src\Controllers\VerifyMailController;
use Src\Controllers\ConfirmUserController;
use Src\Controllers\ImageClientController;
use Src\Controllers\ListSocieteController;
use Src\Controllers\RoleSossukeController;
use Src\Controllers\ShopAVendreController;
use Src\Controllers\TicketLigneController;
use Src\Controllers\UserSossukeController;
use Src\Controllers\FilesTicketsController;
use Src\Controllers\ShopCmdLigneController;
use Src\Controllers\TicketChampsController;
use Src\Controllers\NotificationsController;
use Src\Controllers\ShopConditionController;
use Src\Controllers\ForgotPasswordController;
use Src\Controllers\BoutiqueSossukeController;
use Src\Controllers\MaterielSossukeController;
use Src\Controllers\UserSitesSossukeController;
use Src\Controllers\ShopArticleController;
use Src\Controllers\MailCmdController;
use Src\Controller\PlanningController;


header("Access-Control-Allow-Origin: *");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('Europe/Paris');
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

    case $config->urls->base .RoleController::path() . $data:
        echo RoleController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base .MailController::path() . $data:
        echo MailController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base .AdController::path() . $data:
        echo AdController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base . VerifUserController::path() . $data:
        echo VerifUserController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base.ListSocieteController::path().$data:
        echo ListSocieteController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base.VerifyMailController::path().$data:
        echo VerifyMailController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base.UserSossukeController::path().$data:
        header('Access-Control-Allow-Origin: *'); 
        echo UserSossukeController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base.RoleSossukeController::path().$data:
        header('Access-Control-Allow-Origin: *'); 
        echo RoleSossukeController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base.UserSitesSossukeController::path().$data:
        header('Access-Control-Allow-Origin: *'); 
        echo UserSitesSossukeController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base . ShopAVendreController::path() . $data:
        header('Access-Control-Allow-Origin: *');
        echo ShopAVendreController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base .ShopCmdController::path() . $data:
        header('Access-Control-Allow-Origin: *');
        echo ShopCmdController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base . ShopCmdLigneController::path() . $data:
        header('Access-Control-Allow-Origin: *');
        echo ShopCmdLigneController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base . ShopConditionController::path() . $data:
        header('Access-Control-Allow-Origin: *');
        echo ShopConditionController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base . MaterielSossukeController::path() . $data:
        header('Access-Control-Allow-Origin: *');
        echo MaterielSossukeController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base . BoutiqueSossukeController::path() . $data:
        header('Access-Control-Allow-Origin: *');
        echo BoutiqueSossukeController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base . ShopArticleController::path() . $data:
        header('Access-Control-Allow-Origin: *');
        echo ShopArticleController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base . MailCmdController::path() . $data:
        header('Access-Control-Allow-Origin: *');
        echo MailCmdController::index($_SERVER['REQUEST_METHOD'], $data);
        break;

    case $config->urls->base . PlanningController::path() . $data:
        header('Access-Control-Allow-Origin: *');
        echo MailCmdController::index($_SERVER['REQUEST_METHOD'], $data);
        break;
        
	default:
		header('HTTP/1.0 404 not found');
        echo NotFoundController::index();
		break;
}
?>
