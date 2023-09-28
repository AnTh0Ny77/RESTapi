<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class Promo {
    
    public $ad__id;

    public $ad__titre;

    public $ad__img;

    public $ad__lien;

    public $ad__txt;



    /**
     * Get the value of ad__id
     */ 
    public function getAd__id()
    {
        return $this->ad__id;
    }

    /**
     * Set the value of ad__id
     *
     * @return  self
     */ 
    public function setAd__id($ad__id)
    {
        $this->ad__id = $ad__id;

        return $this;
    }

    /**
     * Get the value of ad__titre
     */ 
    public function getAd__titre()
    {
        return $this->ad__titre;
    }

    /**
     * Set the value of ad__titre
     *
     * @return  self
     */ 
    public function setAd__titre($ad__titre)
    {
        $this->ad__titre = $ad__titre;

        return $this;
    }

    /**
     * Get the value of ad__img
     */ 
    public function getAd__img()
    {
        return $this->ad__img;
    }

    /**
     * Set the value of ad__img
     *
     * @return  self
     */ 
    public function setAd__img($ad__img)
    {
        $this->ad__img = $ad__img;

        return $this;
    }

    /**
     * Get the value of ad__lien
     */ 
    public function getAd__lien()
    {
        return $this->ad__lien;
    }

    /**
     * Set the value of ad__lien
     *
     * @return  self
     */ 
    public function setAd__lien($ad__lien)
    {
        $this->ad__lien = $ad__lien;

        return $this;
    }

    /**
     * Get the value of ad__txt
     */ 
    public function getAd__txt()
    {
        return $this->ad__txt;
    }

    /**
     * Set the value of ad__txt
     *
     * @return  self
     */ 
    public function setAd__txt($ad__txt)
    {
        $this->ad__txt = $ad__txt;

        return $this;
    }
}