<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class Confirm {

    public $confirm__id;

    public $confirm__exp;

    public $confirm__key;

    public $confirm__user;

    public $confirm__used;

   
    public function getConfirm__exp(){
        return $this->confirm__exp;
    }

    public function setConfirm__exp($confirm__exp){
        $this->confirm__exp = $confirm__exp;

        return $this;
    }

    public function getConfirm__key(){
        return $this->confirm__key;
    }

    public function setConfirm__key($confirm__key){
        
        $this->confirm__key = $confirm__key;
        return $this;
    }

    public function getConfirm__user(){
        return $this->confirm__user;
    }

    public function setConfirm__user($confirm__user){
        $this->confirm__user = $confirm__user;

        return $this;
    }

    public function getConfirm__used(){
        return $this->confirm__used;
    }

    public function setConfirm__used($confirm__used){
        $this->confirm__used = $confirm__used;

        return $this;
    }

    public function getConfirm__id(){
        return $this->confirm__id;
    }

    public function setConfirm__id($confirm__id){
        $this->confirm__id = $confirm__id;

        return $this;
    }
}