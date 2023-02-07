<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class ShopAVendre {

    public $sav__id;

    public $sav__cli_id;

    public $sav__ref_id;

    public $sav__etat;

    public $sav__prix;

    public $sav__memo_recode;

    public $sav__gar_std;

    public $sav__gar1_mois;

    public $sav__gar1_prix;

    public $sav__gar2_mois;

    public $sav__gar2_prix;

    public $sav__dlv;

    /**
     * Get the value of sav__id
     */ 
    public function getSav__id()
    {
        return $this->sav__id;
    }

    /**
     * Set the value of sav__id
     *
     * @return  self
     */ 
    public function setSav__id($sav__id)
    {
        $this->sav__id = $sav__id;

        return $this;
    }

    /**
     * Get the value of sav__cli_id
     */ 
    public function getSav__cli_id()
    {
        return $this->sav__cli_id;
    }

    /**
     * Set the value of sav__cli_id
     *
     * @return  self
     */ 
    public function setSav__cli_id($sav__cli_id)
    {
        $this->sav__cli_id = $sav__cli_id;

        return $this;
    }

    /**
     * Get the value of sav__ref_id
     */ 
    public function getSav__ref_id()
    {
        return $this->sav__ref_id;
    }

    /**
     * Set the value of sav__ref_id
     *
     * @return  self
     */ 
    public function setSav__ref_id($sav__ref_id)
    {
        $this->sav__ref_id = $sav__ref_id;

        return $this;
    }

    /**
     * Get the value of sav__etat
     */ 
    public function getSav__etat()
    {
        return $this->sav__etat;
    }

    /**
     * Set the value of sav__etat
     *
     * @return  self
     */ 
    public function setSav__etat($sav__etat)
    {
        $this->sav__etat = $sav__etat;

        return $this;
    }

    /**
     * Get the value of sav__prix
     */ 
    public function getSav__prix()
    {
        return $this->sav__prix;
    }

    /**
     * Set the value of sav__prix
     *
     * @return  self
     */ 
    public function setSav__prix($sav__prix)
    {
        $this->sav__prix = $sav__prix;

        return $this;
    }

    /**
     * Get the value of sav__memo_recode
     */ 
    public function getSav__memo_recode()
    {
        return $this->sav__memo_recode;
    }

    /**
     * Set the value of sav__memo_recode
     *
     * @return  self
     */ 
    public function setSav__memo_recode($sav__memo_recode)
    {
        $this->sav__memo_recode = $sav__memo_recode;

        return $this;
    }

    /**
     * Get the value of sav__gar_std
     */ 
    public function getSav__gar_std()
    {
        return $this->sav__gar_std;
    }

    /**
     * Set the value of sav__gar_std
     *
     * @return  self
     */ 
    public function setSav__gar_std($sav__gar_std)
    {
        $this->sav__gar_std = $sav__gar_std;

        return $this;
    }

    /**
     * Get the value of sav__gar1_mois
     */ 
    public function getSav__gar1_mois()
    {
        return $this->sav__gar1_mois;
    }

    /**
     * Set the value of sav__gar1_mois
     *
     * @return  self
     */ 
    public function setSav__gar1_mois($sav__gar1_mois)
    {
        $this->sav__gar1_mois = $sav__gar1_mois;

        return $this;
    }

    /**
     * Get the value of sav__gar1_prix
     */ 
    public function getSav__gar1_prix()
    {
        return $this->sav__gar1_prix;
    }

    /**
     * Set the value of sav__gar1_prix
     *
     * @return  self
     */ 
    public function setSav__gar1_prix($sav__gar1_prix)
    {
        $this->sav__gar1_prix = $sav__gar1_prix;

        return $this;
    }

    /**
     * Get the value of sav__gar2_mois
     */ 
    public function getSav__gar2_mois()
    {
        return $this->sav__gar2_mois;
    }

    /**
     * Set the value of sav__gar2_mois
     *
     * @return  self
     */ 
    public function setSav__gar2_mois($sav__gar2_mois)
    {
        $this->sav__gar2_mois = $sav__gar2_mois;

        return $this;
    }

    /**
     * Get the value of sav__gar2_prix
     */ 
    public function getSav__gar2_prix()
    {
        return $this->sav__gar2_prix;
    }

    /**
     * Set the value of sav__gar2_prix
     *
     * @return  self
     */ 
    public function setSav__gar2_prix($sav__gar2_prix)
    {
        $this->sav__gar2_prix = $sav__gar2_prix;

        return $this;
    }

    /**
     * Get the value of sav__dlv
     */ 
    public function getSav__dlv()
    {
        return $this->sav__dlv;
    }

    /**
     * Set the value of sav__dlv
     *
     * @return  self
     */ 
    public function setSav__dlv($sav__dlv)
    {
        $this->sav__dlv = $sav__dlv;

        return $this;
    }
}