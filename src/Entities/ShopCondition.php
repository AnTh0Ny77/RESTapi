<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class ShopCondition {

    public $sco__cli_id;

    public $sco__type_port;

    public $sco__francoa;

    public $sco__prix_port;

    public $sco__cli_id_fact;

    public $sco__vue_ref;

    /**
     * Get the value of sco__cli_id
     */ 
    public function getSco__cli_id()
    {
        return $this->sco__cli_id;
    }

    /**
     * Set the value of sco__cli_id
     *
     * @return  self
     */ 
    public function setSco__cli_id($sco__cli_id)
    {
        $this->sco__cli_id = $sco__cli_id;

        return $this;
    }

    /**
     * Get the value of sco__type_port
     */ 
    public function getSco__type_port()
    {
        return $this->sco__type_port;
    }

    /**
     * Set the value of sco__type_port
     *
     * @return  self
     */ 
    public function setSco__type_port($sco__type_port)
    {
        $this->sco__type_port = $sco__type_port;

        return $this;
    }

    /**
     * Get the value of sco__francoa
     */ 
    public function getSco__francoa()
    {
        return $this->sco__francoa;
    }

    /**
     * Set the value of sco__francoa
     *
     * @return  self
     */ 
    public function setSco__francoa($sco__francoa)
    {
        $this->sco__francoa = $sco__francoa;

        return $this;
    }

    /**
     * Get the value of sco__prix_port
     */ 
    public function getSco__prix_port()
    {
        return $this->sco__prix_port;
    }

    /**
     * Set the value of sco__prix_port
     *
     * @return  self
     */ 
    public function setSco__prix_port($sco__prix_port)
    {
        $this->sco__prix_port = $sco__prix_port;

        return $this;
    }

    /**
     * Get the value of sco__cli_id_fact
     */ 
    public function getSco__cli_id_fact()
    {
        return $this->sco__cli_id_fact;
    }

    /**
     * Set the value of sco__cli_id_fact
     *
     * @return  self
     */ 
    public function setSco__cli_id_fact($sco__cli_id_fact)
    {
        $this->sco__cli_id_fact = $sco__cli_id_fact;

        return $this;
    }

    /**
     * Get the value of sco__vue_ref
     */ 
    public function getSco__vue_ref()
    {
        return $this->sco__vue_ref;
    }

    /**
     * Set the value of sco__vue_ref
     *
     * @return  self
     */ 
    public function setSco__vue_ref($sco__vue_ref)
    {
        $this->sco__vue_ref = $sco__vue_ref;

        return $this;
    }
}