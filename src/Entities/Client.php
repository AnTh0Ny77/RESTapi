<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class Client {

    public $cli__id;

    public $cli__nom;

    public $cli__id_mere;

    public $cli__groupement;

    public $cli__logo;

    public $cli__adr1;

    public $cli__adr2;

    public $cli__cp;

    public $cli__ville;

    public $cli__pays;

    public $cli__tel;

    public $cli__email;

    public function getCli__id(){
        return $this->cli__id;
    }

    public function setCli__id($cli__id){
        $this->cli__id = $cli__id;

        return $this;
    }

    public function getCli__nom(){
        return $this->cli__nom;
    }

    public function setCli__nom($cli__nom){
        if (strlen($cli__nom) < 2 or strlen($cli__nom) > 50 ) {
            return 'Le nom doit comporter entre 2 et 50 characters'; 
        }
        $this->cli__nom = $cli__nom;
        return $this;
    }

    public function getCli__id_mere(){
        return $this->cli__id_mere;
    }

    public function setCli__id_mere($cli__id_mere){
        $this->cli__id_mere = $cli__id_mere;

        return $this;
    }

    public function getCli__groupement(){
        return $this->cli__groupement;
    }

    public function setCli__groupement($cli__groupement){
        $this->cli__groupement = $cli__groupement;

        return $this;
    }

    public function getCli__logo(){
        return $this->cli__logo;
    }

    public function setCli__logo($cli__logo){
        $this->cli__logo = $cli__logo;

        return $this;
    }

    public function getCli__adr1(){
        return $this->cli__adr1;
    }

    public function setCli__adr1($cli__adr1){
        if (strlen($cli__adr1) < 2 or strlen($cli__adr1) > 200 ) {
            return 'L adresse doit comporter entre 5 et 200 characters'; 
        }
        $this->cli__adr1 = $cli__adr1;
        return $this;
    }

    public function getCli__adr2(){
        return $this->cli__adr2;
    }

    public function setCli__adr2($cli__adr2){
        $this->cli__adr2 = $cli__adr2;

        return $this;
    }

    public function getCli__cp(){
        return $this->cli__cp;
    }

    public function setCli__cp($cli__cp){
        if (strlen($cli__cp) < 2 or strlen($cli__cp) > 20 ){
            return 'le code postal doit contenir entre 2 et  20 chiffres'; 
        }
        $this->cli__cp = $cli__cp;
        return $this;
    }

    public function getCli__ville(){
        return $this->cli__ville;
    }

    public function setCli__ville($cli__ville){
        if (strlen($cli__ville) < 2 or strlen($cli__ville) > 70 ) {
            return 'La ville doit comporter entre 2 et  70 charactères'; 
        }
        $this->cli__ville = $cli__ville;
        return $this;
    }

    public function getCli__pays(){
        return $this->cli__pays;
    }
    
    public function setCli__pays($cli__pays){
        if (strlen($cli__pays) < 2 or strlen($cli__pays) > 70 ) {
            return 'Le pays doit comporter entre 2 et  70 charactères'; 
        }
        $this->cli__pays = $cli__pays;
        return $this;
    }

    public function getCli__tel(){
        return $this->cli__tel;
    }

    public function setCli__tel( $cli__tel){
        if(preg_match('/^[0-9\-\(\)\/\+\s]*$/', $cli__tel)) {
            $this->cli__tel =  $cli__tel;
            return $this;
        } 
       return 'numero de telephone invalide';
    }

    public function getCli__email(){
        return $this->cli__email;
    }

    public function setCli__email($cli__email){
        if (filter_var($cli__email, FILTER_VALIDATE_EMAIL)) {
            $this->cli__email = $cli__email;
            return $this;
        }else return 'Le mail saisi n est pas un email valide'; 
    }
}