<?php
/**
 * @license	    GNU General Public License version 2 or later; see  LICENSE.txt
 * @author      Eric Fernance
 *
 * @copyright   Eric Fernance
 */

defined("_JEXEC") or die("Invalid Direct Access");


class PbbookingControllerPbbooking extends JControllerLegacy
{
	/**
	 * load_slots_for_day($date,$grouping,$treatment) - passes back a JSON encoded data for the browser to view time slots
	 *
	 */

	function load_slots_for_day()
	{
		$input = JFactory::getApplication()->input;
		$input->set("format", "raw");
		$input->set("view", "pbbooking");
		$input->set("layout", "individual_freeflow_view_calendar");

		parent::display();

	}


	/**
	 * called by AJAX.  loads and returns json_encoded services for a calendar.
	 * @param int the id of the calendar to load services for
	 * @return string json encoded string of treatments
	 * @since 2.4.5.10
	 * @access public
	 */

	public function load_calendar_services()
	{
		$input = JFactory::getApplication()->input;

		$services = \Pbbooking\Model\Calendar::get_services_for_calendar((int)$input->get('cal_id'));

		//process the returned services array to get ready for output.
		foreach ($services as $service) {
			if (isset($GLOBALS["com_pbbooking_data"]["config"]->show_prices) && $GLOBALS["com_pbbooking_data"]["config"]->show_prices == 1) {
				$service->name = \Pbbooking\Pbbookinghelper::print_multilang_name($service, 'service') . ' - ' . \Pbbooking\Pbbookinghelper::pbb_money_format($service->price);
				if (isset($service->pointscost) && $service->pointscost > 0) {
					$service->name .= JText::_("COM_PBBOOKING_SERVICE_POINTS_OR");
					$service->name .= $service->pointscost. " ";
					$service->name .= JText::_("COM_PBBOOKING_SERVICE_POINTS_SUFFIX");
				}
			}
		}
		echo json_encode($services);
	}
}