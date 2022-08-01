<?php
namespace Src\Controllers;
require  '././vendor/autoload.php';

Class BasePathController {

    public static function path(){
        return '';
    }

	public static function index(){
        $data =  json_encode([]);
       
        return $data ;
       
    }
}