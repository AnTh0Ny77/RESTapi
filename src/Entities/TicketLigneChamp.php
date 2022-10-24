<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class TicketsLigneChamp {

    public $tklc__id;

    public $tklc__nom_champ;

    public $tklc__ordre;
    
    public $tklc__memo;

    /**
     * Get the value of tklc__id
     */ 
    public function getTklc__id()
    {
        return $this->tklc__id;
    }

    /**
     * Set the value of tklc__id
     *
     * @return  self
     */ 
    public function setTklc__id($tklc__id)
    {
        $this->tklc__id = $tklc__id;

        return $this;
    }

    /**
     * Get the value of tklc__nom_champ
     */ 
    public function getTklc__nom_champ()
    {
        return $this->tklc__nom_champ;
    }

    /**
     * Set the value of tklc__nom_champ
     *
     * @return  self
     */ 
    public function setTklc__nom_champ($tklc__nom_champ)
    {
        $this->tklc__nom_champ = $tklc__nom_champ;

        return $this;
    }

    /**
     * Get the value of tklc__ordre
     */ 
    public function getTklc__ordre()
    {
        return $this->tklc__ordre;
    }

    /**
     * Set the value of tklc__ordre
     *
     * @return  self
     */ 
    public function setTklc__ordre($tklc__ordre)
    {
        $this->tklc__ordre = $tklc__ordre;

        return $this;
    }

    /**
     * Get the value of tklc__memo
     */ 
    public function getTklc__memo()
    {
        return $this->tklc__memo;
    }

    /**
     * Set the value of tklc__memo
     *
     * @return  self
     */ 
    public function setTklc__memo($tklc__memo)
    {
        $this->tklc__memo = $tklc__memo;

        return $this;
    }
}