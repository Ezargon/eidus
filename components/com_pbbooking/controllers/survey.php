<?php
/**
 * @license	    GNU General Public License version 2 or later; see  LICENSE.txt
 * @author      Eric Fernance
 *
 * @copyright   Eric Fernance
 */

defined("_JEXEC") or die("Invalid Direct Access");

class PbbookingControllerSurvey extends JControllerLegacy
{

	public function display($cachable = false,$urlparams = array()) {
		
		parent::display($cachable, $urlparams);	

	}


	public function save(){
		$survey = $this->getModel('Survey');

		$input = JFactory::getApplication()->input;

		$config = $GLOBALS["com_pbbooking_data"]["config"];

		$s_response = array();
		$s_response['event_id'] = $input->get("id", null,"integer");

		foreach (json_decode($config->testimonial_questions,true) as $question) {
			$s_response['content'][$question['testimonial_field_varname']] = $input->get($question['testimonial_field_varname'],null,'string');
		}

		$s_response['content'] = json_encode($s_response['content']);
		$s_response['submission_ip'] = $_SERVER['REMOTE_ADDR'];

		if ($survey->save_survey($s_response)) {
			$j_config = JFactory::getConfig();
			\Pbbooking\Pbbookinghelper::send_email(JText::_('COM_PBBOOKING_EMAIL_NEW_SURVEY_SUBJECT'),JText::_('COM_PBBOOKING_EMAIL_NEW_SURVEY_BODY'),$j_config->get('mailfrom'),$bcc=null);
			$this->setRedirect('index.php',JText::_('COM_PBBOOKING_SURVEY_SUCCESS'));
		}
		else {
			$error = true;
		}


		if (isset($error)) {
			$this->setRedirect('index.php',JText::_('COM_PBBOOKING_SURVEY_ERROR'));
		}

		return;
	}




    
}