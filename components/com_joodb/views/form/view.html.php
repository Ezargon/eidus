<?php
/**
* @package		JooDatabase - http://joodb.feenders.de
* @copyright	Copyright (C) Computer - Daten - Netze : Feenders. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @author		Dirk Hoeschen (hoeschen@feenders.de)
*
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once( JPATH_COMPONENT_ADMINISTRATOR.'/helpers/form.php' );


/**
 * HTML View class for the JooDatabase singel element
 */
class JoodbViewForm extends JViewLegacy
{
	var $joobase = null;
    var $item = null;
	var $params = null;
	var $menu = null;

    public function display($tpl = null)
	{
		// Get the current menu item
        $app = JFactory::getApplication();
        $menu = $app->getMenu();
		$this->menu	= $menu->getActive();
        if (empty($this->menu)) $this->menu	=  $menu->getDefault();

		$this->params= $app->getParams();	
		// read database configuration from joobase table
		$this->joobase =  $this->get('joobase');

		$this->_prepareDocument();

        //get the data page
        $this->item = $this->get('data');

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{
		$document = JFactory::getDocument();
		$app = JFactory::getApplication();
	
		if (!$this->params->get( 'page_title') )
			$this->params->set('page_title',	JText::_( $this->joobase->name ));
	
		if (!$this->params->get( 'page_heading' ) )
			$this->params->set('page_heading',JText::_( $this->joobase->name ));
	
		//set document title
		$document->setTitle( $this->params->get('page_title')." - ".$app->get('sitename') );
		// add joodb style
		$document->addStyleSheet(JURI::root(true)."/components/com_joodb/assets/joodb.css");
	
		if ($this->params->get('menu-meta_description'))
		{
			$document->setDescription($this->params->get('menu-meta_description'));
		}
	
		if ($this->params->get('menu-meta_keywords'))
		{
			$document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

        $jquery = is_file(JPATH_LIBRARIES . '/cms/html/jquery.php');
        if (!$jquery) {
            $document->addScript(JURI::root(true) . '/media/joodb/js/jquery.min.js');
            $document->addScriptDeclaration('jQuery.noConflict();');
        } else {
            JHtml::_('jquery.framework');
        }

        // load administrator language
        $language = JFactory::getLanguage();
        $language->load('com_joodb' , JPATH_ADMINISTRATOR, $language->getTag(), true);

        JHtml::_('behavior.modal');
        JHtml::_('behavior.keepalive');
        JHtml::_('behavior.formvalidation');
        if ((int)JVERSION>=3) JHtml::_('formbehavior.chosen', 'select');
	}	
	
	/**
 	* Parse Template part and replace with view specific elements
 	*/
	protected function _parseTemplate(&$parts)
	{
		$output = "";
        $filter = new JFilterInput();
		// replace item content with wildcards
    	foreach( $parts as $part ) {
		  switch ($part->function) {
   			case ('submitbutton'):
   				$output .= '<button class="button btn btn-primary validate" onmousedown="validateForm();" type="submit">'.JText::_('Send')."</button>";
   				break;
   			case ('captcha'):
  				$output .=  JoodbHelper::getCaptcha();
   				break;
   			case ('form'):
				if (isset($this->joobase->fields[$part->parameter[0]]))
                $output .=  JoodbFormHelper::getFormField($this->joobase, $this->item, $this->joobase->fields[$part->parameter[0]]);
   				break;
   			case ('imageupload'):
  				$output .=  '<input name="joodb_dataset_image" class="inputbox file" type="file" accept="image/*" />';
   				break;
			default:
				$output .=  '';
		  }
   		  $output .= $part->text;
    	}
    	return $output;
	}

}
