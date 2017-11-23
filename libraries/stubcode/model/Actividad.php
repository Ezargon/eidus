<?php
class Actividad {
    
    private $id;
    private $titulo;
    private $descripcion;
    private $fecha_ini;
    private $fecha_fin;
    private $horas;
    private $documentacion;
    private $gestor;
    private $lugar;
    private $horario;
    private $plazas;
    private $plazo_inscripcion;
    private $inscripcion;
    private $gestion;
    private $modalidad;
    private $codigo_solicitud;
    private $codigo_ice;
    private $formador;
    private $financiacion;
    private $contacto;
    private $impartido;
    private $bloque;
    private $observaciones;
    private $enlace;
    
    
    
    /**
     * @return the $enlace
     */
    public function getEnlace()
    {
        return $this->enlace;
    }

    /**
     * @param field_type $enlace
     */
    public function setEnlace($enlace)
    {
        $this->enlace = $enlace;
    }

    /**
     * @return the $lugar
     */
    public function getLugar()
    {
        return $this->lugar;
    }

    /**
     * @param field_type $lugar
     */
    public function setLugar($lugar)
    {
        $this->lugar = $lugar;
    }

    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return the $titulo
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * @return the $descripcion
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * @return the $fecha_ini
     */
    public function getFecha_ini()
    {
        return $this->fecha_ini;
    }

    /**
     * @return the $fecha_fin
     */
    public function getFecha_fin()
    {
        return $this->fecha_fin;
    }

    /**
     * @return the $horas
     */
    public function getHoras()
    {
        return $this->horas;
    }

    /**
     * @return the $documentacion
     */
    public function getDocumentacion()
    {
        return $this->documentacion;
    }

    /**
     * @return the $gestor
     */
    public function getGestor()
    {
        return $this->gestor;
    }

    /**
     * @return the $horario
     */
    public function getHorario()
    {
        return $this->horario;
    }

    /**
     * @return the $plazas
     */
    public function getPlazas()
    {
        return $this->plazas;
    }

    /**
     * @return the $plazo_inscripcion
     */
    public function getPlazo_inscripcion()
    {
        return $this->plazo_inscripcion;
    }

    /**
     * @return the $inscripcion
     */
    public function getInscripcion()
    {
        return $this->inscripcion;
    }

    /**
     * @return the $gestion
     */
    public function getGestion()
    {
        return $this->gestion;
    }

    /**
     * @return the $modalidad
     */
    public function getModalidad()
    {
        return $this->modalidad;
    }

    /**
     * @return the $codigo_solicitud
     */
    public function getCodigo_solicitud()
    {
        return $this->codigo_solicitud;
    }

    /**
     * @return the $codigo_ice
     */
    public function getCodigo_ice()
    {
        return $this->codigo_ice;
    }

    /**
     * @return the $formador
     */
    public function getFormador()
    {
        return $this->formador;
    }

    /**
     * @return the $financiacion
     */
    public function getFinanciacion()
    {
        return $this->financiacion;
    }

    /**
     * @return the $contacto
     */
    public function getContacto()
    {
        return $this->contacto;
    }

    /**
     * @return the $impartido
     */
    public function getImpartido()
    {
        return $this->impartido;
    }

    /**
     * @return the $bloque
     */
    public function getBloque()
    {
        return $this->bloque;
    }

    /**
     * @return the $observaciones
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * @param field_type $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param field_type $titulo
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
    }

    /**
     * @param field_type $descripcion
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    }

    /**
     * @param field_type $fecha_ini
     */
    public function setFecha_ini($fecha_ini)
    {
        $this->fecha_ini = $fecha_ini;
    }

    /**
     * @param field_type $fecha_fin
     */
    public function setFecha_fin($fecha_fin)
    {
        $this->fecha_fin = $fecha_fin;
    }

    /**
     * @param field_type $horas
     */
    public function setHoras($horas)
    {
        $this->horas = $horas;
    }

    /**
     * @param field_type $documentacion
     */
    public function setDocumentacion($documentacion)
    {
        $this->documentacion = $documentacion;
    }

    /**
     * @param field_type $gestor
     */
    public function setGestor($gestor)
    {
        $this->gestor = $gestor;
    }

    /**
     * @param field_type $horario
     */
    public function setHorario($horario)
    {
        $this->horario = $horario;
    }

    /**
     * @param field_type $plazas
     */
    public function setPlazas($plazas)
    {
        $this->plazas = $plazas;
    }

    /**
     * @param field_type $plazo_inscripcion
     */
    public function setPlazo_inscripcion($plazo_inscripcion)
    {
        $this->plazo_inscripcion = $plazo_inscripcion;
    }

    /**
     * @param field_type $inscripcion
     */
    public function setInscripcion($inscripcion)
    {
        $this->inscripcion = $inscripcion;
    }

    /**
     * @param field_type $gestion
     */
    public function setGestion($gestion)
    {
        $this->gestion = $gestion;
    }

    /**
     * @param field_type $modalidad
     */
    public function setModalidad($modalidad)
    {
        $this->modalidad = $modalidad;
    }

    /**
     * @param field_type $codigo_solicitud
     */
    public function setCodigo_solicitud($codigo_solicitud)
    {
        $this->codigo_solicitud = $codigo_solicitud;
    }

    /**
     * @param field_type $codigo_ice
     */
    public function setCodigo_ice($codigo_ice)
    {
        $this->codigo_ice = $codigo_ice;
    }

    /**
     * @param field_type $formador
     */
    public function setFormador($formador)
    {
        $this->formador = $formador;
    }

    /**
     * @param field_type $financiacion
     */
    public function setFinanciacion($financiacion)
    {
        $this->financiacion = $financiacion;
    }

    /**
     * @param field_type $contacto
     */
    public function setContacto($contacto)
    {
        $this->contacto = $contacto;
    }

    /**
     * @param field_type $impartido
     */
    public function setImpartido($impartido)
    {
        $this->impartido = $impartido;
    }

    /**
     * @param field_type $bloque
     */
    public function setBloque($bloque)
    {
        $this->bloque = $bloque;
    }

    /**
     * @param field_type $observaciones
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    }

    
    
    
    
}