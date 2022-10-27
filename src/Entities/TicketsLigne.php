<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class TicketsLigne {

    public $tkl__id;

    public $tkl__tk_id;

    public $tkl__user_id;

    public $tkl__dt;

    public $tkl__motif_ligne;

    public $tkl__memo;

    public $tkl__user_id_dest;

    public $tkl__visible;


    /**
     * Get the value of tkl__id
     */ 
    public function getTkl__id()
    {
        return $this->tkl__id;
    }

    /**
     * Set the value of tkl__id
     *
     * @return  self
     */ 
    public function setTkl__id($tkl__id)
    {
        $this->tkl__id = $tkl__id;

        return $this;
    }

    /**
     * Get the value of tkl__tk_id
     */ 
    public function getTkl__tk_id()
    {
        return $this->tkl__tk_id;
    }

    /**
     * Set the value of tkl__tk_id
     *
     * @return  self
     */ 
    public function setTkl__tk_id($tkl__tk_id)
    {
        if (empty($tkl__tk_id)) {
            return 'l ID du ticket est obligatoire';
        }
        $this->tkl__tk_id = $tkl__tk_id;

        return $this;
    }

    /**
     * Get the value of tkl__user_id
     */ 
    public function getTkl__user_id()
    {
        return $this->tkl__user_id;
    }

    /**
     * Set the value of tkl__user_id
     *
     * @return  self
     */ 
    public function setTkl__user_id($tkl__user_id)
    {
        if (empty($tkl__user_id)) {
             return ' l ID de l utilisateur ne peut pas etre vide ';
        }
        $this->tkl__user_id = $tkl__user_id;

        return $this;
    }

    /**
     * Get the value of tkl__dt
     */ 
    public function getTkl__dt()
    {
        return $this->tkl__dt;
    }

    /**
     * Set the value of tkl__dt
     *
     * @return  self
     */ 
    public function setTkl__dt()
    {
        $this->tkl__dt = date('Y-m-d H:i:s');
        return $this;
    }

    /**
     * Get the value of tkl__motif_ligne
     */ 
    public function getTkl__motif_ligne()
    {
        return $this->tkl__motif_ligne;
    }

    /**
     * Set the value of tkl__motif_ligne
     *
     * @return  self
     */ 
    public function setTkl__motif_ligne($tkl__motif_ligne)
    {
        $this->tkl__motif_ligne = $tkl__motif_ligne;

        return $this;
    }

    /**
     * Get the value of tkl__memo
     */ 
    public function getTkl__memo()
    {
        return $this->tkl__memo;
    }

    /**
     * Set the value of tkl__memo
     *
     * @return  self
     */ 
    public function setTkl__memo($tkl__memo)
    {
        $this->tkl__memo = $tkl__memo;

        return $this;
    }

    /**
     * Get the value of tkl__user_id_dest
     */ 
    public function getTkl__user_id_dest()
    {
        return $this->tkl__user_id_dest;
    }

    /**
     * Set the value of tkl__user_id_dest
     *
     * @return  self
     */ 
    public function setTkl__user_id_dest($tkl__user_id_dest)
    {
        if (empty($tkl__user_id_dest)) {
            return ' l ID de l utilisateur destinataire ne peut pas etre vide ';
       }
       $this->tkl__user_id_dest = $tkl__user_id_dest;
        return $this;
    }

    /**
     * Get the value of tkl__visible
     */ 
    public function getTkl__visible()
    {
        return $this->tkl__visible;
    }

    /**
     * Set the value of tkl__visible
     *
     * @return  self
     */ 
    public function setTkl__visible($tkl__visible)
    {
        $this->tkl__visible = $tkl__visible;

        return $this;
    }
}