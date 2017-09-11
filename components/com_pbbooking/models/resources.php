<?php
/**
 * @license	    GNU General Public License version 2 or later; see  LICENSE.txt
 * @author      Eric Fernance
 *
 * @copyright   Eric Fernance
 */

defined("_JEXEC") or die("Invalid Direct Access");


class PbbookingModelResources extends JModelList {


    public function getListQuery()
    {
        $query = $this->_db->getQuery(true);
        $query->select("name,hours,id")
            ->from("#__pbbooking_cals")
            ->order("id ASC");
        
        return $query;
    }


}