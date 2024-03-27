<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class ShopArticle {

    public $sar__ref_id;

    public $sar__model;

    public $sar__ref_constructeur;

    public $sar__description;

    public $sar__image;

    public $sar__marque;

    public $sar__famille;


    /**
     * Get the value of sar__ref_id
     */ 
    public function getSar__ref_id()
    {
        return $this->sar__ref_id;
    }

    /**
     * Set the value of sar__ref_id
     *
     * @return  self
     */ 
    public function setSar__ref_id($sar__ref_id)
    {
        $this->sar__ref_id = $sar__ref_id;

        return $this;
    }

    /**
     * Get the value of sar__ref_constructeur
     */ 
    public function getSar__ref_constructeur()
    {
        return $this->sar__ref_constructeur;
    }

    /**
     * Set the value of sar__ref_constructeur
     *
     * @return  self
     */ 
    public function setSar__ref_constructeur($sar__ref_constructeur)
    {
        $this->sar__ref_constructeur = $sar__ref_constructeur;

        return $this;
    }

    /**
     * Get the value of sar__description
     */ 
    public function getSar__description()
    {
        return $this->sar__description;
    }

    /**
     * Set the value of sar__description
     *
     * @return  self
     */ 
    public function setSar__description($sar__description)
    {
        $this->sar__description = $sar__description;

        return $this;
    }

    /**
     * Get the value of sar__image
     */ 
    public function getSar__image()
    {
        return $this->sar__image;
    }

    /**
     * Set the value of sar__image
     *
     * @return  self
     */ 
    public function setSar__image($sar__image)
    {
        $this->sar__image = $sar__image;

        return $this;
    }

    /**
     * Get the value of sar__marque
     */ 
    public function getSar__marque()
    {
        return $this->sar__marque;
    }

    /**
     * Set the value of sar__marque
     *
     * @return  self
     */ 
    public function setSar__marque($sar__marque)
    {
        $this->sar__marque = $sar__marque;

        return $this;
    }

    /**
     * Get the value of sar__famille
     */ 
    public function getSar__famille()
    {
        return $this->sar__famille;
    }

    /**
     * Set the value of sar__famille
     *
     * @return  self
     */ 
    public function setSar__famille($sar__famille)
    {
        $this->sar__famille = $sar__famille;

        return $this;
    }

    /**
     * Get the value of sar__model
     */ 
    public function getSar__model()
    {
        return $this->sar__model;
    }

    /**
     * Set the value of sar__model
     *
     * @return  self
     */ 
    public function setSar__model($sar__model)
    {
        $this->sar__model = $sar__model;

        return $this;
    }
}