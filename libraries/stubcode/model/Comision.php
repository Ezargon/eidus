<?php

class Comision
{
    //Cargo del Profesor
    private $cargo;
    private $profesor;
    private $idPrograma;
    
    /**
     * @return the $cargo
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * @return the $profesor
     */
    public function getProfesor()
    {
        return $this->profesor;
    }

    /**
     * @return the $idPrograma
     */
    public function getIdPrograma()
    {
        return $this->idPrograma;
    }

    /**
     * @param field_type $cargo
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;
    }

    /**
     * @param field_type $profesor
     */
    public function setProfesor($profesor)
    {
        $this->profesor = $profesor;
    }

    /**
     * @param field_type $idPrograma
     */
    public function setIdPrograma($idPrograma)
    {
        $this->idPrograma = $idPrograma;
    }

    
    
    
    
}

