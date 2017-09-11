<?php
class Programa_View {
    public static function SelectHTML($t_programa, $idprograma, $selectName){
        $buffer = "";
        $buffer.= "<div class=\"divSelect\">";
        $buffer.= "<form method=\"post\" action=\"\" name=\"".$selectName."\">";
        $buffer.= "<select name=\"".$selectName."\" onchange=\"this.form.submit();\">";
        $buffer.= "<option value=\"\" selected=\"selected\">--Selecciona programa --</option>";
        foreach ($t_programa as $r_programa) {
            if($idprograma === $r_programa->id){
             $buffer.= "<option value=\"".$r_programa->id."\" selected=\"selected\">[".$r_programa->codigo."] ".$r_programa->denominacion."</option>";
            }else{
             $buffer.= "<option value=\"".$r_programa->id."\">[".$r_programa->codigo."] ".$r_programa->denominacion."</option>";
            }
         }
        $buffer.="</select>";
        $buffer.="</form>";
        $buffer.="</div>";
        
        echo $buffer;
    
    }
    
    public static function tablaHTML($programa){
        $buffer = "";
        $buffer.= "<div class=\"container\">";
 
        $buffer.= "<h3>".$programa["denominacion"]."</h3>";
        $buffer.= "<table class=\"table table-striped\">";
        $buffer.= "</table>";  
        $buffer.= "</div>";
        
        echo $buffer;
    }


}
