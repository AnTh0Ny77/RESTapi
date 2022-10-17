<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class Materiel {

    public $mat__id;

    public $mat__cli__id;

    public $mat__type;

    public $mat__marque;

    public $mat__model;

    public $mat__pn;

    public $mat__memo;

    public $mat__sn;

    public $mat__idnec;

    public $mat__ident;

    public $mat__date_in;

    public $mat__kw_tg;

    public $mat__date_offg;

    public $date__date_maj;

    public $mat__user_id;

    public $mat__contrat_id;

    public $mat__contrat_ligne;

    public $mat__actif;

    /**
     * Get the value of mat__id
     */ 
    public function getMat__id()
    {
        return $this->mat__id;
    }

    /**
     * Set the value of mat__id
     *
     * @return  self
     */ 
    public function setMat__id($mat__id)
    {
        $this->mat__id = $mat__id;

        return $this;
    }

    /**
     * Get the value of mat__cli__id
     */ 
    public function getMat__cli__id()
    {
        return $this->mat__cli__id;
    }

    /**
     * Set the value of mat__cli__id
     *
     * @return  self
     */ 
    public function setMat__cli__id($mat__cli__id)
    {
        $this->mat__cli__id = $mat__cli__id;

        return $this;
    }

    /**
     * Get the value of mat__type
     */ 
    public function getMat__type()
    {
        return $this->mat__type;
    }

    /**
     * Set the value of mat__type
     *
     * @return  self
     */ 
    public function setMat__type($mat__type)
    {
        $this->mat__type = $mat__type;

        return $this;
    }

    /**
     * Get the value of mat__marque
     */ 
    public function getMat__marque()
    {
        return $this->mat__marque;
    }

    /**
     * Set the value of mat__marque
     *
     * @return  self
     */ 
    public function setMat__marque($mat__marque)
    {
        $this->mat__marque = $mat__marque;

        return $this;
    }

    /**
     * Get the value of mat__model
     */ 
    public function getMat__model()
    {
        return $this->mat__model;
    }

    /**
     * Set the value of mat__model
     *
     * @return  self
     */ 
    public function setMat__model($mat__model)
    {
        $this->mat__model = $mat__model;

        return $this;
    }

    /**
     * Get the value of mat__pn
     */ 
    public function getMat__pn()
    {
        return $this->mat__pn;
    }

    /**
     * Set the value of mat__pn
     *
     * @return  self
     */ 
    public function setMat__pn($mat__pn)
    {
        $this->mat__pn = $mat__pn;

        return $this;
    }

    /**
     * Get the value of mat__memo
     */ 
    public function getMat__memo()
    {
        return $this->mat__memo;
    }

    /**
     * Set the value of mat__memo
     *
     * @return  self
     */ 
    public function setMat__memo($mat__memo)
    {
        $this->mat__memo = $mat__memo;

        return $this;
    }

    /**
     * Get the value of mat__sn
     */ 
    public function getMat__sn()
    {
        return $this->mat__sn;
    }

    /**
     * Set the value of mat__sn
     *
     * @return  self
     */ 
    public function setMat__sn($mat__sn)
    {
        $this->mat__sn = $mat__sn;

        return $this;
    }

    /**
     * Get the value of mat__idnec
     */ 
    public function getMat__idnec()
    {
        return $this->mat__idnec;
    }

    /**
     * Set the value of mat__idnec
     *
     * @return  self
     */ 
    public function setMat__idnec($mat__idnec)
    {
        $this->mat__idnec = $mat__idnec;

        return $this;
    }

    /**
     * Get the value of mat__ident
     */ 
    public function getMat__ident()
    {
        return $this->mat__ident;
    }

    /**
     * Set the value of mat__ident
     *
     * @return  self
     */ 
    public function setMat__ident($mat__ident)
    {
        $this->mat__ident = $mat__ident;

        return $this;
    }

    /**
     * Get the value of mat__date_in
     */ 
    public function getMat__date_in()
    {
        return $this->mat__date_in;
    }

    /**
     * Set the value of mat__date_in
     *
     * @return  self
     */ 
    public function setMat__date_in($mat__date_in)
    {
        $this->mat__date_in = $mat__date_in;

        return $this;
    }

    /**
     * Get the value of mat__kw_tg
     */ 
    public function getMat__kw_tg()
    {
        return $this->mat__kw_tg;
    }

    /**
     * Set the value of mat__kw_tg
     *
     * @return  self
     */ 
    public function setMat__kw_tg($mat__kw_tg)
    {
        $this->mat__kw_tg = $mat__kw_tg;

        return $this;
    }

    /**
     * Get the value of mat__date_offg
     */ 
    public function getMat__date_offg()
    {
        return $this->mat__date_offg;
    }

    /**
     * Set the value of mat__date_offg
     *
     * @return  self
     */ 
    public function setMat__date_offg($mat__date_offg)
    {
        $this->mat__date_offg = $mat__date_offg;

        return $this;
    }

    /**
     * Get the value of date__date_maj
     */ 
    public function getDate__date_maj()
    {
        return $this->date__date_maj;
    }

    /**
     * Set the value of date__date_maj
     *
     * @return  self
     */ 
    public function setDate__date_maj($date__date_maj)
    {
        $this->date__date_maj = $date__date_maj;

        return $this;
    }

    /**
     * Get the value of mat__user_id
     */ 
    public function getMat__user_id()
    {
        return $this->mat__user_id;
    }

    /**
     * Set the value of mat__user_id
     *
     * @return  self
     */ 
    public function setMat__user_id($mat__user_id)
    {
        $this->mat__user_id = $mat__user_id;

        return $this;
    }

    /**
     * Get the value of mat__contrat_id
     */ 
    public function getMat__contrat_id()
    {
        return $this->mat__contrat_id;
    }

    /**
     * Set the value of mat__contrat_id
     *
     * @return  self
     */ 
    public function setMat__contrat_id($mat__contrat_id)
    {
        $this->mat__contrat_id = $mat__contrat_id;

        return $this;
    }

    /**
     * Get the value of mat__contrat_ligne
     */ 
    public function getMat__contrat_ligne()
    {
        return $this->mat__contrat_ligne;
    }

    /**
     * Set the value of mat__contrat_ligne
     *
     * @return  self
     */ 
    public function setMat__contrat_ligne($mat__contrat_ligne)
    {
        $this->mat__contrat_ligne = $mat__contrat_ligne;

        return $this;
    }

    /**
     * Get the value of mat__actif
     */ 
    public function getMat__actif()
    {
        return $this->mat__actif;
    }

    /**
     * Set the value of mat__actif
     *
     * @return  self
     */ 
    public function setMat__actif($mat__actif)
    {
        $this->mat__actif = $mat__actif;

        return $this;
    }
}