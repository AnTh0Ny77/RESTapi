<?php
namespace Src\Entities;
require  '././vendor/autoload.php';

Class Commercial {

    public $com__id;

    public $com__nom;

    public $com__email;

    public $com__fonction;

    public $com__img;


    /**
     * Get the value of com__id
     */ 
    public function getCom__id()
    {
        return $this->com__id;
    }

    /**
     * Set the value of com__id
     *
     * @return  self
     */ 
    public function setCom__id($com__id)
    {
        $this->com__id = $com__id;

        return $this;
    }

    /**
     * Get the value of com__nom
     */ 
    public function getCom__nom()
    {
        return $this->com__nom;
    }

    /**
     * Set the value of com__nom
     *
     * @return  self
     */ 
    public function setCom__nom($com__nom)
    {
        $this->com__nom = $com__nom;

        return $this;
    }

    /**
     * Get the value of com__email
     */ 
    public function getCom__email()
    {
        return $this->com__email;
    }

    /**
     * Set the value of com__email
     *
     * @return  self
     */ 
    public function setCom__email($com__email)
    {
        $this->com__email = $com__email;

        return $this;
    }

    /**
     * Get the value of com__fonction
     */ 
    public function getCom__fonction()
    {
        return $this->com__fonction;
    }

    /**
     * Set the value of com__fonction
     *
     * @return  self
     */ 
    public function setCom__fonction($com__fonction)
    {
        $this->com__fonction = $com__fonction;

        return $this;
    }

    /**
     * Get the value of com__img
     */ 
    public function getCom__img()
    {
        return $this->com__img;
    }

    /**
     * Set the value of com__img
     *
     * @return  self
     */ 
    public function setCom__img($com__img)
    {
        $this->com__img = $com__img;

        return $this;
    }
}