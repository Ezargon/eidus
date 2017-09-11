<?php
class programa_tabla{
    private $programas;
 
    public function __construct(){
        $this->programas=EidusFabrik::t_programas();
    }
    public function get_programas(){
        return $this->programas;
    }
    public function get_programas2011(){
        return array_filter($this->programas, "plan2011");
    }
      
}
// FILTER
function plan2011($programas)
{
    return($programas->plan === '2011');
}
