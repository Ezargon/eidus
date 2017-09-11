<?php

/**
 * Description of programa
 *
 * @author jdominguez10
 */
class programa {
    private $programa;
 
    public function __construct($id_programa){
        $this->programa=EidusFabrik::row_Programa($id_programa);
        
        
    }
    public function get_programa(){
        return $this->programa;
    }
}
