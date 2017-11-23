<?php

class Usuario
{
    private $id;
    private $nombre;
    private $usuario;
    private $email;
    private $grupos;
    private $userid;
    private $creador;
    private $bloqueado;
    
    
    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return the $nombre
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @return the $usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @return the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return the $grupos
     */
    public function getGrupos()
    {
        return $this->grupos;
    }

    /**
     * @return the $userid
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @return the $creador
     */
    public function getCreador()
    {
        return $this->creador;
    }

    /**
     * @return the $bloqueado
     */
    public function getBloqueado()
    {
        return $this->bloqueado;
    }

    /**
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param field_type $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @param field_type $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * @param field_type $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param field_type $grupos
     */
    public function setGrupos($grupos)
    {
        $this->grupos = $grupos;
    }

    /**
     * @param field_type $userid
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    /**
     * @param field_type $creador
     */
    public function setCreador($creador)
    {
        $this->creador = $creador;
    }

    /**
     * @param field_type $bloqueado
     */
    public function setBloqueado($bloqueado)
    {
        $this->bloqueado = $bloqueado;
    }

    
}

