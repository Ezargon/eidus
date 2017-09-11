<?php
/**
* @version		$Id:sql.php 6961 2007-03-15 16:06:53Z tcp $
* @package		Joomla.Framework
* @subpackage	Parameter
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Renders a SQL element
 *
 * @package 	Joomla.Framework
 * @subpackage	Form
 */

class JFormFieldSQLList extends JFormFieldList
{
	/**
	* The form field type
	*
	* @var		string
	*/
	public	$type = 'SQLList';

	protected function getOptions()
	{
		$options = array ();

		// Initialize some field attributes.
		$key 	= ($this->element['key_field'] ? (string)$this->element['key_field'] : 'value');
		$value 	= ($this->element['value_field'] ? (string)$this->element['value_field'] : (string)$this->element['name']);
		$query  = (string)$this->element['query'];

		//	get the database object
		$db			= JFactory::getDBO();

		//	set the query and get the result list
		$db->setQuery($query);
		$items = $db->loadObjectList();

		// Check for an error.
		if ($db->getErrorNum())
		{
			JError::raiseWarning(500, $db->getErrorMsg());
			return $options;
		}

		// Build the field options.
		if (!empty($items))
		{
			foreach($items as $item)
			{
				$options[] = JHtml::_('select.option', $item->$key, $item->$value);
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}