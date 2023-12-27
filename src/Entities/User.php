<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class User {

	public $user__id;

    public $user__nom;

    public $user__prenom;

    public $user__mail;

    public $user__password;

    public $user__service;

    public $user__fonction;

    public $user__gsm;

    public $user__memo;

    public $user__tel;

    public $user__d_creat;

    public $user__d_off;

    public $user__chrono;

    public $token;

    public $refresh_token;

    public $roles;

    public  $clients;

    public $clientsParc;

    public $user__confirm;

    public $user__abs_adress;

	
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
        if (strlen($user__nom) < 2 or strlen($user__nom) > 35 ) {
            return 'Le nom doit comporter entre 2 et 35 characters'; 
        }
        $this->user__nom = $user__nom;
        return $this;
    }

    public function getUser__prenom(){
        return $this->user__prenom;
    }
   
    public function setUser__prenom($user__prenom){
        if (strlen($user__prenom) < 2 or strlen($user__prenom) > 35 ) {
            return 'Le nom doit comporter entre 2 et 35 characters'; 
        }
        $this->user__prenom = $user__prenom;
        return $this;
    }

    public function getUser__mail(){
        return $this->user__mail;
    }

    public function setUser__mail($user__mail){
        if (filter_var($user__mail, FILTER_VALIDATE_EMAIL)) {
            $this->user__mail = $user__mail;
            return $this;
        }else return 'Le mail saisi n est pas un email valide'; 
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

    public function getUser__memo(){
        return $this->user__memo;
    }

    public function setUser__memo($user__memo){
        $this->user__memo = $user__memo;

        return $this;
    }

    public function getRefresh_token(){
        return $this->refresh_token;
    }
    
    public function setRefresh_token($refresh_token){
        $this->refresh_token = $refresh_token;

        return $this;
    }

    public function getRoles(){
        return $this->roles;
    }

    public function setRoles($roles){
        $this->roles = $roles;

        return $this;
    }

   
    public function getClients(){
        return $this->clients;
    }

    public function setClients($clients){
        $this->clients = $clients;

        return $this;
    }

    public function getUser__confirm(){
        return $this->user__confirm;
    }

    public function setUser__confirm($user__confirm){
        $this->user__confirm = $user__confirm;
        return $this;
    }

    public function getClientsParc()
    {
        return $this->clientsParc;
    }

    public function setClientsParc($clientsParc)
    {
        $this->clientsParc = $clientsParc;

        return $this;
    }

    
    public function getUser__abs_adress()
    {
        return $this->user__abs_adress;
    }

    public function setUser__abs_adress($user__abs_adress)
    {
        $this->user__abs_adress = $user__abs_adress;

        return $this;
    }
}