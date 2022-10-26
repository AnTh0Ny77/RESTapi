<?php

namespace src;

use PDO;
use PDOException;

class Sossuke {

    public $config;
    public PDO $Pdo;

    public function __construct(){
        
        $this->config = json_decode(file_get_contents('config.json'));
    }

    public function DbConnect(){
        if(!isset($this->Pdo)){
            try {
                $pdo = new PDO('mysql:dbname='.$this->config->sossuke->name.';host='.$this->config->sossuke->host.'', ''.$this->config->sossuke->user.'', ''.$this->config->sossuke->pass.'' ,  array(1002 => 'SET NAMES utf8'));
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->Pdo = $pdo;
                return $this->Pdo;
                } 
                catch (PDOException $e) {
                echo $e->getMessage() . "impossible de se connecter à la base de donnée";
                }
        }
       
    }
}