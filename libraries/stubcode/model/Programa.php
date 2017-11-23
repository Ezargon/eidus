<?php
class Programa
{
    private $id;
    private $codigo;
    private $denominacion;
    private $email;
    private $isced1;
    private $isced2;
    private $web;
    private $plan;
    private $interuniversitario;
    private $rama;
    //TIPO 'Profesor'
    private $coordinador;
    //ARRAY de 'Comision'
    private $array_comision;
    //ARRAY de 'Plaza'
    private $array_plaza;
    //MAP[Lineas - Array<Profesor>]
    private $map_array_lineasProfesor;
    //Map Codigo_lineas - Denominacion
    private $map_codigoLineas_denominacion;
    
   //HTML 
   private $contacto_administrativo;
   private $organos_participantes;
   private $perifl_ingreso;
   private $requisitos_criterios;
   private $doc_especifica;
   private $complementos_formativos;
   private $contacto_academico;
    
    
    /**
     * @return the $contacto_administrativo
     */
    public function getContacto_administrativo()
    {
        return $this->contacto_administrativo;
    }

/**
     * @return the $rama
     */
    public function getRama()
    {
        return $this->rama;
    }

    /**
     * @param field_type $rama
     */
    public function setRama($rama)
    {
        $this->rama = $rama;
    }

    /**
     * @return the $organos_participantes
     */
    public function getOrganos_participantes()
    {
        return $this->organos_participantes;
    }

/**
     * @return the $perifl_ingreso
     */
    public function getPerifl_ingreso()
    {
        return $this->perifl_ingreso;
    }

/**
     * @return the $requisitos_criterios
     */
    public function getRequisitos_criterios()
    {
        return $this->requisitos_criterios;
    }

/**
     * @return the $doc_especifica
     */
    public function getDoc_especifica()
    {
        return $this->doc_especifica;
    }

/**
     * @return the $complementos_formativos
     */
    public function getComplementos_formativos()
    {
        return $this->complementos_formativos;
    }

/**
     * @return the $contacto_academico
     */
    public function getContacto_academico()
    {
        return $this->contacto_academico;
    }

/**
     * @param field_type $contacto_administrativo
     */
    public function setContacto_administrativo($contacto_administrativo)
    {
        $this->contacto_administrativo = $contacto_administrativo;
    }

/**
     * @param field_type $organos_participantes
     */
    public function setOrganos_participantes($organos_participantes)
    {
        $this->organos_participantes = $organos_participantes;
    }

/**
     * @param field_type $perifl_ingreso
     */
    public function setPerifl_ingreso($perifl_ingreso)
    {
        $this->perifl_ingreso = $perifl_ingreso;
    }

/**
     * @param field_type $requisitos_criterios
     */
    public function setRequisitos_criterios($requisitos_criterios)
    {
        $this->requisitos_criterios = $requisitos_criterios;
    }

/**
     * @param field_type $doc_especifica
     */
    public function setDoc_especifica($doc_especifica)
    {
        $this->doc_especifica = $doc_especifica;
    }

/**
     * @param field_type $complementos_formativos
     */
    public function setComplementos_formativos($complementos_formativos)
    {
        $this->complementos_formativos = $complementos_formativos;
    }

/**
     * @param field_type $contacto_academico
     */
    public function setContacto_academico($contacto_academico)
    {
        $this->contacto_academico = $contacto_academico;
    }

    /**
     * @return the $map_codigoLineas_denominacion
     */
    public function getMap_codigoLineas_denominacion()
    {
        return $this->map_codigoLineas_denominacion;
    }

    /**
     * @param field_type $map_codigoLineas_denominacion
     */
    public function setMap_codigoLineas_denominacion($map_codigoLineas_denominacion)
    {
        $this->map_codigoLineas_denominacion = $map_codigoLineas_denominacion;
    }

    /**
     * @return the $map_array_lineasProfesor
     */
    public function getMap_array_lineasProfesor()
    {
        return $this->map_array_lineasProfesor;
    }

    /**
     * @param field_type $map_array_lineasProfesor
     */
    public function setMap_array_lineasProfesor($map_array_lineasProfesor)
    {
        $this->map_array_lineasProfesor = $map_array_lineasProfesor;
    }

    /**
     * @return the $array_plaza
     */
    public function getArray_plaza()
    {
        return $this->array_plaza;
    }

    /**
     * @param field_type $array_plaza
     */
    public function setArray_plaza($array_plaza)
    {
        $this->array_plaza = $array_plaza;
    }

    /**
     * @return the $array_comision
     */
    public function getArray_comision()
    {
        return $this->array_comision;
    }

    /**
     * @param field_type $array_comision
     */
    public function setArray_comision($array_comision)
    {
        $this->array_comision = $array_comision;
    }

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return the $codigo
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @return the $denominacion
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * @return the $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return the $isced1
     */
    public function getIsced1()
    {
        return $this->isced1;
    }

    /**
     * @return the $isced2
     */
    public function getIsced2()
    {
        return $this->isced2;
    }

    /**
     * @return the $web
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @return the $plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * @return the $interuniversitario
     */
    public function getInteruniversitario()
    {
        return $this->interuniversitario;
    }

    /**
     * @return the $coordinador
     */
    public function getCoordinador()
    {
        return $this->coordinador;
    }

    /**
     * @param field_type $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * @param field_type $denominacion
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;
    }

    /**
     * @param field_type $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param field_type $isced1
     */
    public function setIsced1($isced1)
    {
        $this->isced1 = $isced1;
    }

    /**
     * @param field_type $isced2
     */
    public function setIsced2($isced2)
    {
        $this->isced2 = $isced2;
    }

    /**
     * @param field_type $web
     */
    public function setWeb($web)
    {
        $this->web = $web;
    }

    /**
     * @param field_type $plan
     */
    public function setPlan($plan)
    {
        $this->plan = $plan;
    }


    /**
     * @param field_type $interuniversitario
     */
    public function setInteruniversitario($interuniversitario)
    {
        $this->interuniversitario = $interuniversitario;
    }

    /**
     * @param field_type $coordinador
     */
    public function setCoordinador($coordinador)
    {
        $this->coordinador = $coordinador;
    }

    
    
    
    
}

