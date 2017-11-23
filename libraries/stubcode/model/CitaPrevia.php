<?php
class CitaPrevia
{
    private $dtend;
    private $dstart;
    private $email;
    private $nombre;
    private $apellidos;
    private $telefono;
    private $verificado;
    
    /**
     * @return the $dtend
     */
    public function getDtend()
    {
        return $this->dtend;
    }

    /**
     * @return the $dstart
     */
    public function getDstart()
    {
        return $this->dstart;
    }

    /**
     * @return the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return the $nombre
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @return the $apellidos
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * @return the $telefono
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * @return the $verificado
     */
    public function getVerificado()
    {
        return $this->verificado;
    }

    /**
     * @param field_type $dtend
     */
    public function setDtend($dtend)
    {
        $this->dtend = $dtend;
    }

    /**
     * @param field_type $dstart
     */
    public function setDstart($dstart)
    {
        $this->dstart = $dstart;
    }

    /**
     * @param field_type $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param field_type $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @param field_type $apellidos
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
    }

    /**
     * @param field_type $telefono
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;
    }

    /**
     * @param field_type $verificado
     */
    public function setVerificado($verificado)
    {
        $this->verificado = $verificado;
    }

    
    
    
}

