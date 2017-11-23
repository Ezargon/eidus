<?php
namespace libraries\stubcode\view;

class Cita_Previa_Administrador_View
{
    private $array_cita_previa;
    
    public function __construct($array_cita_previa){
        $this->array_cita_previa = $array_cita_previa;
    }
    
    public function __print(){
        //print_r($this->array_cita_previa);
        $print_ = "";
        $print_ .= $this->creartabla();
        
        echo $print_;
    }
    
    private function creartabla(){
        $TABLA_HEAD = ['Inicio', 'Fin', 'Nombre', 'Apellidos', 'Email', 'Telefono', 'Validada'];
        $print_ = "";
        $print_ .= "<table class=\"table table-hover\">";
        $print_.="<thead>";
        $print_.="<tr>";
        foreach ($TABLA_HEAD as $head){
            $print_.="<th>".$head."</th>";
        }
        $print_.="</tr>";
        $print_.="</thead>";
        $print_.="<tbody>";
        foreach (  $this->array_cita_previa as $cita){
            if($cita->getVerificado() == 1){
                $print_.="<tr class=\"success\">";
            }else{
                $print_.="<tr class=\"danger\">";
            }
            
           
            $print_.="<td>".$cita->getDstart()."</td>";
            $print_.="<td>".$cita->getDtend()."</td>";
            $print_.="<td>".$cita->getNombre()."</td>";
            $print_.="<td>".$cita->getApellidos()."</td>";
            $print_.="<td>".$cita->getEmail()."</td>";
            $print_.="<td>".$cita->getTelefono()."</td>";
            $print_.="<td>".$cita->getVerificado()."</td>";
            $print_.="</tr>";
        }
        $print_.="</tbody>";
        $print_ .= "</table>";
        
        return $print_;
    }
    
    
    
}

