<?php
class Plaza
{
    private $id;
    private $idPrograma;
    private $curso;
    private $total;
    /**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return the $idPrograma
     */
    public function getIdPrograma()
    {
        return $this->idPrograma;
    }

    /**
     * @return the $curso
     */
    public function getCurso()
    {
        return $this->curso;
    }

    /**
     * @return the $total
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param field_type $id
     */ 
    public function setId($id) 
    {
        $this->id = $id;
    }

    /**
     * @param field_type $idPrograma
     */
    public function setIdPrograma($idPrograma)
    {
        $this->idPrograma = $idPrograma;
    }

    /**
     * @param field_type $curso
     */
    public function setCurso($curso)
    {
        $this->curso = $curso;
    }

    /**
     * @param field_type $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    
}

