<?php
include JPATH_BASE.'/libraries/eidus-web/mysql/eidus-fabrik/extract.php';
  $t_programa = t_programas_plan2011();
  $count = count($t_programa);
  $idprograma = null;
  $row = null;
  try {
      if(isset($_POST['sel_programa']) ){

            $idprograma = explode("_",$_POST['sel_programa']);
            $id =  $idprograma[0];
            $row = row_Programa($id);
            
            $t_lineas = t_lineas_inves($id);
            $count_lineas = count($t_lineas);
            
            $t_ProfesoresPrograma = t_ProfesoresPrograma($id);
            $count_profesores = count($t_ProfesoresPrograma);
  
        }

  }catch(Exception $e){
      
  }
?>


<!-- SELECT PROGRAMA -->
<div id="programasSelect">
<label for="form_programa">Programa: </label>
<form method="post" action="" name="form_programa">
    <select name="sel_programa" onchange="this.form.submit();">
        <option value="" selected="selected">--Selecciona programa --</option>
        <?php for($i=0; $i<$count; $i++ ): ?>
            <?php  if($idprograma[0] === $t_programa[$i]->id):?>
             <option value="<?php echo $t_programa[$i]->id;?>" selected="selected"><?php echo $t_programa[$i]->denominacion;?></option>
            <?php else: ?>
              <option value="<?php echo $t_programa[$i]->id;?>">[<?php echo $t_programa[$i]->codigo; ?>] - <?php echo $t_programa[$i]->denominacion;?></option>
            <?php endif; ?>
         
         <?php endfor; ?>
    </select>
</form>
</div>
<!-- FIN    SELECT PROGRAMA -->
 
<!-- FORMULARIO PROGRAMA -->
<?php  if(isset($row)):?>
 
<div class="container">
     <h3><?php echo $row["denominacion"]; ?></h3>
     <table id="programa" class="table table-bordered table-striped">
     <tbody> 
         <tr><td width="30%">Email</td><td><a href="#" id="email" data-type="text" data-pk="<?php echo $row["id"]; ?>" data-url="post.php" data-title="Email"><?php echo $row["email"]; ?></a></td></tr>
         <tr><td width="30%">Web</td><td><a href="#" id="web" data-type="text" data-pk="<?php echo $row["id"]; ?>" data-url="/post.php" data-title="Web"><?php echo $row["web"]; ?></a></td></tr>
         <tr><td width="30%">ISCED1</td><td><a href="#" id="isced1"><?php echo $row["isced1"]; ?></a></td></tr>
         <tr><td width="30%">ISCED2</td><td><a href="#" id="isced2"></a></td></tr>
         <tr><td width="30%">Interuniversitario</td><td><a href="#" id="interuniversitario"><?php echo $row["interuniversitario"]; ?></a></td></tr>
    </tbody>
     </table>
 </div>

<div class="container" >
     <h3><?php echo $row["denominacion"]; ?></h3>
     <input type="button" id="buttonForm" value="Activar Formulario" class="formDisable"> 
    <form id="formPrograma">
     <table id="programa" class="table table-bordered table-striped">
         
     <tbody> 
 
         <tr><td>Email</td><td><input class='formPrograma' type="text" name="email"  disabled></td></tr>
         <tr><td>Web</td><td><input class='formPrograma' type="text" name="email" value='<?php echo $row["web"]; ?>' disabled></td></tr>
         <tr><td>isced1</td><td><input class='formPrograma' type="text" name="isced1" value='<?php echo $row["isced1"]; ?>' disabled></td></tr>

          </tbody>
       
     </table>
   </form>
 </div>



 
 <div name="lineaSelect" id="lineaSelect">
     <label for="sel_linea">Linea de Investigación: </label>
    <select name="sel_linea"  id="sel_linea" onchange="this.form.submit();">
        <option value="" selected="selected">--Selecciona linea --</option>
        <?php for($j=0; $j<$count_lineas; $j++ ): ?>
         <option value="<?php echo $t_lineas[$j]->id;?>">(<?php echo $t_lineas[$j]->codigo;?>) - <?php echo $t_lineas[$j]->denominacion;?></option>
         <?php endfor; ?>
    </select>
</div>






    <?php for($j=0; $j<$count_lineas; $j++ ): ?>
    <div id="lineaID<?php echo $t_lineas[$j]->id;?>" class="linea" style="display: none;"> 
        <?php 
            $codigoLinea = $t_lineas[$j]->codigo;
            echo $codigoLinea;    
        ?> 
    
    
        <table id="programa" class="table table-bordered table-striped">
        <tr>
<select name="profesorLinea" multiple>
  <option value="volvo">Volvo</option>

</select>

</tr>
<tr><a class="btn"><i class="fa fa-chevron-circle-left" aria-hidden="true"></i></a></tr>
<tr><a class="btn"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></a></tr>
<tr>
<select name="profesoresNoEstan" multiple>
    <?php 
    print_r($t_ProfesoresPrograma);
    
    for($j=0; $j<$count_profesores; $j++ ): 
        $nombre = $t_ProfesoresPrograma[$j]->nombre;
        $apellidos = $t_ProfesoresPrograma[$j]->apellidos;
        $dni = $t_ProfesoresPrograma[$j]->dni;
        $id =$t_ProfesoresPrograma[$j]->id;
     ?>
             <option value="<?php echo $id;?>"><?php echo "[".$dni."] ".$apellidos." ,".$nombre." "; ?></option>
     <?php endfor; ?>
</select>

</tr>
</table>
    
    
    
    </div>
    <?php endfor; ?>






<?php   endif; ?>

     
 

 
 
     
     <script>   
       jQuery(function($){
            //toggle `popup` / `inline` mode
    $.fn.editable.defaults.mode = 'popup';     

    
   $('#email').editable({
        type: 'text',
        pk: 1,
        url: '/post',
        title: 'Enter email',
    });
    
    $('#web').editable();

    $('#isced1').editable();
   
    $('#isced2').editable({
           type: 'text',
            url: 'post.php', 
          title: 'codigo isced',
           name: 'isced2',
           value: '<?php echo $row["isced2"]; ?>',
           ajaxOptions:{
                            type:'post'
                           }
          });
    
    
     $('#interuniversitario').editable({
        type: 'select',
        title: 'Select interuniversitario',
        placement: 'right',
        source: [
            {value: 1, text: 'si'},
            {value: 2, text: 'no'}
        ]

    }); 
    
     $('#sel_linea').change(function(){
            $('.linea').hide();
            $('#lineaID' + $(this).val()).show();
            
        });
    
     $('#active_form').change(function(){
            $('.linea').hide();
            $('#lineaID' + $(this).val()).show();
            
        });
    
    
    $( "#buttonForm").click(function() {
            $(".formPrograma").prop('disabled', false);
            $("#buttonForm").prop('value', "Desactivar");
            $("#buttonForm").prop('class', "formEnable");
     });
     

});
      
 </script>
 
 <?php
function linea($profesor, $codigo)
{
    return($profesor->codigo === $codigo);
}   