<?php
/**
 * @license	    GNU General Public License version 2 or later; see  LICENSE.txt
 * @author      Eric Fernance
 * 
 * @copyright   Eric Fernance
 */

defined("_JEXEC") or die("Invalid Direct Access");
 

class PbbookingRouter extends JComponentRouterBase {

	/**
	 *
	 * @param	array	An array of URL arguments
	 * @return	array	The URL arguments to use to assemble the subsequent URL.
	 * @since	1.5
	 */
	public function build(&$query)
	{
		$segments = array();

		if (isset($query['task'])) {
			$segments[] = $query['task'];
			unset($query['task']);
		}

		// Look up the menu item here.
		if (!isset($query["Itemid"]) && isset($query["view"])) {
			$db = JFactory::getDbo();
			$nonsef = "option=com_pbbooking&view=".$db->escape($query["view"]);
			$item = $db->setQuery("select * from #__menu where link like '%".$db->escape($nonsef)."%'")->loadObject();
			if ($item) {
				$query["Itemid"] = $item->id;
				unset($query["view"]);
			}
		}
		

		return $segments;
	}



	/**
	 * Parse the segments of a URL.
	 *
	 * @param	array	The segments of the URL to parse.
	 *
	 * @return	array	The URL attributes to be used by the application.
	 * @since	1.5
	 */
	public function parse(&$segments)
	{
		$vars = array();

		if (count($segments)>0) {

			$vars['task'] = $segments[0];

			return $vars;
		
		} else {
			return $segments;
		}
	}



}

