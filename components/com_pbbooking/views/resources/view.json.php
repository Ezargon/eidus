<?php
/**
 * @license	    GNU General Public License version 2 or later; see  LICENSE.txt
 * @author      Eric Fernance
 *
 * @copyright   Eric Fernance
 */

defined("_JEXEC") or die("Invalid Direct Access");


class PbbookingViewResources extends JViewLegacy {



    public function display($tpl = null)
    {
    	$this->items = $this->get("Items");
    	parent::display($tpl);
    }

}