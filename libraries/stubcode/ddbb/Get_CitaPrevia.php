<?php
require_once('libraries/stubcode/model/CitaPrevia.php');

class Get_CitaPrevia
{
    
    function getCitasPrevias($fecha){
        $start = "2016-07-06 09:30:00"; 
        $end = "2016-07-06 09:40:00";
        //$fecha = "2016-07-06";
        $array_cita_previas = array();
        
        /**
         ################################
         # Extrae Informacion de fabrik #
         ################################
         */
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('eidus.doctous_pbbooking_events'));
        $query->where($db->quoteName('dtend') . ' LIKE '. $db->quote($fecha.'%'));
  //      $query->where($db->quoteName('dtstart')." = ".$db->quote($start))->where($db->quoteName('dtend')." = ".$db->quote($end));
        
        $db->setQuery($query);
        // Load the results as a list of stdClass objects (see later for more options on retrieving data).
        $rows = $db->loadObjectList();
        
        foreach($rows as $row){
            $citaPrevia = new CitaPrevia();
            
            $dtend =  $row->dtend;
            $dstart = $row->dtstart;
            $verified = $row->verified;
            
            $citaPrevia->setDtend($dtend);
            $citaPrevia->setDstart($dstart);
            $citaPrevia->setVerificado($verified);
            
            $json_code_data = $row->customfields_data;
            $objs = json_decode($json_code_data);
            foreach($objs as $obj){
                                
                //print_r($obj);
                switch ($obj->{'varname'}) {
                    case "nombre":
                        
                        $citaPrevia->setNombre($obj->{'data'});
                    break;
                    case "apellidos":
                        $citaPrevia->setApellidos($obj->{'data'});
                    break;
                    case "mobile":
                        $citaPrevia->setTelefono($obj->{'data'});
                    break;
                    case "email":
                        $citaPrevia->setEmail($obj->{'data'});
                        break;
                }
               
            }
            
            array_push ( $array_cita_previas , $citaPrevia );
          
        }
        
        
        //NombreTeléfono contactoEmail
        
        //print_r($rows);
      
        return $array_cita_previas;
    }
}

