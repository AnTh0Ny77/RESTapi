<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class ShopCmd {


    public $scm__id;

    public $scm__user_id;

    public $scm__dt_cmd;

    public $scm__cmd_id;

    public $scm__prix_port;

    public $scm__ref_client;

    public $scm__memo_client;
    
    public $scm__client_id_livr;

    public $scm__client_id_fact;

    /**
     * Get the value of scm__id
     */ 
    public function getScm__id()
    {
        return $this->scm__id;
    }

    /**
     * Set the value of scm__id
     *
     * @return  self
     */ 
    public function setScm__id($scm__id)
    {
        $this->scm__id = $scm__id;

        return $this;
    }

    /**
     * Get the value of scm__user_id
     */ 
    public function getScm__user_id()
    {
        return $this->scm__user_id;
    }

    /**
     * Set the value of scm__user_id
     *
     * @return  self
     */ 
    public function setScm__user_id($scm__user_id)
    {
        $this->scm__user_id = $scm__user_id;

        return $this;
    }

    /**
     * Get the value of scm__dt_cmd
     */ 
    public function getScm__dt_cmd()
    {
        return $this->scm__dt_cmd;
    }

    /**
     * Set the value of scm__dt_cmd
     *
     * @return  self
     */ 
    public function setScm__dt_cmd($scm__dt_cmd)
    {
        $this->scm__dt_cmd = $scm__dt_cmd;

        return $this;
    }

    /**
     * Get the value of scm__cmd_id
     */ 
    public function getScm__cmd_id()
    {
        return $this->scm__cmd_id;
    }

    /**
     * Set the value of scm__cmd_id
     *
     * @return  self
     */ 
    public function setScm__cmd_id($scm__cmd_id)
    {
        $this->scm__cmd_id = $scm__cmd_id;

        return $this;
    }

    /**
     * Get the value of scm__prix_port
     */ 
    public function getScm__prix_port()
    {
        return $this->scm__prix_port;
    }

    /**
     * Set the value of scm__prix_port
     *
     * @return  self
     */ 
    public function setScm__prix_port($scm__prix_port)
    {
        $this->scm__prix_port = $scm__prix_port;

        return $this;
    }

    /**
     * Get the value of scm__ref_client
     */ 
    public function getScm__ref_client()
    {
        return $this->scm__ref_client;
    }

    /**
     * Set the value of scm__ref_client
     *
     * @return  self
     */ 
    public function setScm__ref_client($scm__ref_client)
    {
        $this->scm__ref_client = $scm__ref_client;

        return $this;
    }

    /**
     * Get the value of scm__memo_client
     */ 
    public function getScm__memo_client()
    {
        return $this->scm__memo_client;
    }

    /**
     * Set the value of scm__memo_client
     *
     * @return  self
     */ 
    public function setScm__memo_client($scm__memo_client)
    {
        $this->scm__memo_client = $scm__memo_client;

        return $this;
    }

    /**
     * Get the value of scm__client_id_livr
     */ 
    public function getScm__client_id_livr()
    {
        return $this->scm__client_id_livr;
    }

    /**
     * Set the value of scm__client_id_livr
     *
     * @return  self
     */ 
    public function setScm__client_id_livr($scm__client_id_livr)
    {
        $this->scm__client_id_livr = $scm__client_id_livr;

        return $this;
    }

    /**
     * Get the value of scm__client_id_fact
     */ 
    public function getScm__client_id_fact()
    {
        return $this->scm__client_id_fact;
    }

    /**
     * Set the value of scm__client_id_fact
     *
     * @return  self
     */ 
    public function setScm__client_id_fact($scm__client_id_fact)
    {
        $this->scm__client_id_fact = $scm__client_id_fact;

        return $this;
    }
}