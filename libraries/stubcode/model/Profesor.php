<?php
class Profesor
{
    private $id;
    private $dni;
    private $nombre;
    private $apellido1;
    private $apellido2;
    private $email;
    private $sexo;
    private $externo;
    private $idPrograma;
    private $sisiusid;
    
    public function __construct()
    {
    }
    
    
    /**
     * @return the $sisiusid
     */
    public function getSisiusid()
    {
        return $this->sisiusid;
    }

    /**
     * @param field_type $sisiusid
     */
    public function setSisiusid($sisiusid)
    {
        $this->sisiusid = $sisiusid;
    }

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return the $dni
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * @return the $nombre
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * @return the $apellido1
     */
    public function getApellido1()
    {
        return $this->apellido1;
    }

    /**
     * @return the $apellido2
     */
    public function getApellido2()
    {
        return $this->apellido2;
    }

    /**
     * @return the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return the $sexo
     */
    public function getSexo()
    {
        return $this->sexo;
    }

    /**
     * @return the $externo
     */
    public function getExterno()
    {
        return $this->externo;
    }

    /**
     * @return the $idPrograma
     */
    public function getIdPrograma()
    {
        return $this->idPrograma;
    }

    /**
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param field_type $dni
     */
    public function setDni($dni)
    {
        $this->dni = $dni;
    }

    /**
     * @param field_type $nombre
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    /**
     * @param field_type $apellido1
     */
    public function setApellido1($apellido1)
    {
        $this->apellido1 = $apellido1;
    }

    /**
     * @param field_type $apellido2
     */
    public function setApellido2($apellido2)
    {
        $this->apellido2 = $apellido2;
    }

    /**
     * @param field_type $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param field_type $sexo
     */
    public function setSexo($sexo)
    {
        $this->sexo = $sexo;
    }

    /**
     * @param field_type $externo
     */
    public function setExterno($externo)
    {
        $this->externo = $externo;
    }

    /**
     * @param field_type $idPrograma
     */
    public function setIdPrograma($idPrograma)
    {
        $this->idPrograma = $idPrograma;
    }

    
}

