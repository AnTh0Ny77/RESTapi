<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class User {

	private $user__id;

    private $user__nom;

    private $user__prenom;

    private $user__mail;

    private $user__password;

    private $user__service;

    private $user__fonction;

    private $user__gsm;

    private $user__tel;

    private $user__d_creat;

    private $user__d_off;

    private $user__chrono;

    private $token;

	
	public function getUser__id(){
		return $this->user__id;
	}

	public function setUser__id($user__id){
		$this->user__id = $user__id;
		return $this;
	}

    public function getUser__nom(){
        return $this->user__nom;
    }
 
    public function setUser__nom($user__nom){
        $this->user__nom = $user__nom;
        return $this;
    }

    public function getUser__prenom(){
        return $this->user__prenom;
    }
   
    public function setUser__prenom($user__prenom){
        $this->user__prenom = $user__prenom;
        return $this;
    }

    public function getUser__mail(){
        return $this->user__mail;
    }

    public function setUser__mail($user__mail){
        $this->user__mail = $user__mail;
        return $this;
    }

    public function getUser__password(){
        return $this->user__password;
    }

    public function setUser__password($user__password){
        if (preg_match("/^(?=.*[0-9])(?=.*[A-Z]).{8,20}$/" ,  $user__password)) {
            $this->user__password = $user__password;
            return $this;
        }else return 'Le mot de pass doit contenir 8 charactères minimum ,un nombre, une majuscule et une minuscule ';
    }

    public function getUser__service(){
        return $this->user__service;
    }

    public function setUser__service($user__service){
        $this->user__service = $user__service;
        return $this;
    }

    public function getUser__fonction(){
        return $this->user__fonction;
    }

    public function setUser__fonction($user__fonction){
        $this->user__fonction = $user__fonction;
        return $this;
    }

    public function getUser__gsm(){
        return $this->user__gsm;
    }
   
    public function setUser__gsm($user__gsm){
        $this->user__gsm = $user__gsm;
        return $this;
    }

    public function getUser__tel(){
        return $this->user__tel;
    }

    public function setUser__tel($user__tel){
        $this->user__tel = $user__tel;
        return $this;
    }

    public function getUser__d_creat(){
        return $this->user__d_creat;
    }

    public function setUser__d_creat($user__d_creat){
        $this->user__d_creat = $user__d_creat;
        return $this;
    }

    public function getUser__d_off(){
        return $this->user__d_off;
    }

    public function setUser__d_off($user__d_off){
        $this->user__d_off = $user__d_off;
        return $this;
    }

    public function getUser__chrono(){
        return $this->user__chrono;
    }

    public function setUser__chrono($user__chrono){
        $this->user__chrono = $user__chrono;
        return $this;
    }

    public function getToken(){
        return $this->token;
    }

    public function setToken($token){
        $this->token = $token;
        return $this;
    }
}