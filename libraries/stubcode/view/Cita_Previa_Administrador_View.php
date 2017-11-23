<?php
namespace libraries\stubcode\view;

class Cita_Previa_Administrador_View
{
    private $array_cita_previa;
    private $fecha;
    
    public function __construct($array_cita_previa, $fecha){
        $this->array_cita_previa = $array_cita_previa;
        $this->fecha = $fecha;
    }
    
    public function __print(){
        //print_r($this->array_cita_previa);
        $print_ = "";
        
        
        $print_ .= $this->crearDateInput();
        $print_ .= $this->creartabla();
        $print_ .=  $this->scripts();
        
        echo $print_;
    }
    private function scripts(){
        $print_ = "";
        $print_.= "<script>";
        $print_.= "function post(path, params, method) {method = method || \"post\"; var form = document.createElement(\"form\");form.setAttribute(\"method\", method);form.setAttribute(\"action\", path);for(var key in params) {if(params.hasOwnProperty(key)) {var hiddenField = document.createElement(\"input\");hiddenField.setAttribute(\"type\", \"hidden\");hiddenField.setAttribute(\"name\", key);hiddenField.setAttribute(\"value\", params[key]);form.appendChild(hiddenField);}}document.body.appendChild(form);form.submit();}";
        $print_.= "</script>";
        return $print_;
    }
    
    private function crearDateInput(){
        $print_ = "";
        $print_.= "<div id='fecha_cita_previa'><label>Selecciona fecha <input type=\"date\" onchange=\"post('', {name: this.value});\" id=\"fecha\" name=\"fecha\" value=\"".$this->fecha."\"></label></div>";
        return $print_;
    }
    private function test($clicked){
        $print_ = "";
        $print_ .= "<div>".$clicked."</div>" ;
        return $print_;
    }
    private function creartabla(){
        $TABLA_HEAD = ['Hora', 'Nombre', 'Apellidos', 'Email', 'Telefono', 'Validada'];
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
            
           
            $print_.="<td>".substr($cita->getDstart(),10,-3)."</td>";
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

