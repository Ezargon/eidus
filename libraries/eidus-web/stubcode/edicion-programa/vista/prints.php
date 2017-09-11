<?php
include JPATH_BASE.'/libraries/eidus-web/mysql/eidus-fabrik/extract.php';
 $t_programa = t_programas_plan2011();
?>

<?php

function selectPrograma(){
   $buffer="";
   $t_programa = t_programas_plan2011();
   $count = count($t_programa);
   $buffer.="<select onchange=\"fetch_select(this.value);\">";
   for($i=0; $i<$count; $i++ ){
       $buffer.="<option value=".$t_programa[$i]->id.">".$t_programa[$i]->denominacion."</option>";  
   }
   $buffer.="</select>";
   return $buffer;
}
?>
