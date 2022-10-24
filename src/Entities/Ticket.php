<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class Tickets {

    public $tk__id;

    public $tk__motif;

    public $tk__motif_id;

    public $tk__titre;

    public $tk__lu;

    public $tk__indic;

    public $tk__groupe;

    /**
     * Get the value of tk__id
     */ 
    public function getTk__id()
    {
        return $this->tk__id;
    }

    /**
     * Set the value of tk__id
     *
     * @return  self
     */ 
    public function setTk__id($tk__id)
    {
        $this->tk__id = $tk__id;

        return $this;
    }

    /**
     * Get the value of tk__motif
     */ 
    public function getTk__motif()
    {
        return $this->tk__motif;
    }

    /**
     * Set the value of tk__motif
     *
     * @return  self
     */ 
    public function setTk__motif($tk__motif)
    {
        $this->tk__motif = $tk__motif;

        return $this;
    }

    /**
     * Get the value of tk__motif_id
     */ 
    public function getTk__motif_id()
    {
        return $this->tk__motif_id;
    }

    /**
     * Set the value of tk__motif_id
     *
     * @return  self
     */ 
    public function setTk__motif_id($tk__motif_id)
    {
        $this->tk__motif_id = $tk__motif_id;

        return $this;
    }

    /**
     * Get the value of tk__titre
     */ 
    public function getTk__titre()
    {
        return $this->tk__titre;
    }

    /**
     * Set the value of tk__titre
     *
     * @return  self
     */ 
    public function setTk__titre($tk__titre)
    {
        $this->tk__titre = $tk__titre;

        return $this;
    }

    /**
     * Get the value of tk__lu
     */ 
    public function getTk__lu()
    {
        return $this->tk__lu;
    }

    /**
     * Set the value of tk__lu
     *
     * @return  self
     */ 
    public function setTk__lu($tk__lu)
    {
        $this->tk__lu = $tk__lu;

        return $this;
    }

    /**
     * Get the value of tk__indic
     */ 
    public function getTk__indic()
    {
        return $this->tk__indic;
    }

    /**
     * Set the value of tk__indic
     *
     * @return  self
     */ 
    public function setTk__indic($tk__indic)
    {
        $this->tk__indic = $tk__indic;

        return $this;
    }

    /**
     * Get the value of tk__groupe
     */ 
    public function getTk__groupe()
    {
        return $this->tk__groupe;
    }

    /**
     * Set the value of tk__groupe
     *
     * @return  self
     */ 
    public function setTk__groupe($tk__groupe)
    {
        $this->tk__groupe = $tk__groupe;

        return $this;
    }
} 