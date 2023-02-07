<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class ShopCmdLigne {

    public $scl__id;

    public $scl__scm_id;

    public $scl__ref_id;

    public $scl__prix_unit;

    public $scl__qte;

    public $scl__gar_mois;

    public $scl__gar_prix;


    /**
     * Get the value of scl__id
     */ 
    public function getScl__id()
    {
        return $this->scl__id;
    }

    /**
     * Set the value of scl__id
     *
     * @return  self
     */ 
    public function setScl__id($scl__id)
    {
        $this->scl__id = $scl__id;

        return $this;
    }

    /**
     * Get the value of scl__scm_id
     */ 
    public function getScl__scm_id()
    {
        return $this->scl__scm_id;
    }

    /**
     * Set the value of scl__scm_id
     *
     * @return  self
     */ 
    public function setScl__scm_id($scl__scm_id)
    {
        $this->scl__scm_id = $scl__scm_id;

        return $this;
    }

    /**
     * Get the value of scl__ref_id
     */ 
    public function getScl__ref_id()
    {
        return $this->scl__ref_id;
    }

    /**
     * Set the value of scl__ref_id
     *
     * @return  self
     */ 
    public function setScl__ref_id($scl__ref_id)
    {
        $this->scl__ref_id = $scl__ref_id;

        return $this;
    }

    /**
     * Get the value of scl__prix_unit
     */ 
    public function getScl__prix_unit()
    {
        return $this->scl__prix_unit;
    }

    /**
     * Set the value of scl__prix_unit
     *
     * @return  self
     */ 
    public function setScl__prix_unit($scl__prix_unit)
    {
        $this->scl__prix_unit = $scl__prix_unit;

        return $this;
    }

    /**
     * Get the value of scl__qte
     */ 
    public function getScl__qte()
    {
        return $this->scl__qte;
    }

    /**
     * Set the value of scl__qte
     *
     * @return  self
     */ 
    public function setScl__qte($scl__qte)
    {
        $this->scl__qte = $scl__qte;

        return $this;
    }

    /**
     * Get the value of scl__gar_mois
     */ 
    public function getScl__gar_mois()
    {
        return $this->scl__gar_mois;
    }

    /**
     * Set the value of scl__gar_mois
     *
     * @return  self
     */ 
    public function setScl__gar_mois($scl__gar_mois)
    {
        $this->scl__gar_mois = $scl__gar_mois;

        return $this;
    }

    /**
     * Get the value of scl__gar_prix
     */ 
    public function getScl__gar_prix()
    {
        return $this->scl__gar_prix;
    }

    /**
     * Set the value of scl__gar_prix
     *
     * @return  self
     */ 
    public function setScl__gar_prix($scl__gar_prix)
    {
        $this->scl__gar_prix = $scl__gar_prix;

        return $this;
    }
}