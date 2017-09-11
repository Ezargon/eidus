<?php
/********************************************************************
Product		: Flexicontact
Date		: 31 December 2015
Copyright	: Les Arbres Design 2010-2015
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.html.pagination');
require_once JPATH_COMPONENT_ADMINISTRATOR .'/helpers/db_helper.php';

class FlexicontactModelLog extends LAFC_model
{
var $_data;
var $_app = null;
var $_pagination = null;

function __construct()
{
	parent::__construct();
	$this->_app = JFactory::getApplication();
}

//-------------------------------------------------------------------------------
// get an existing row
// return false if we couldn't find it
//
function getOne($id)
{
	$query = "SELECT * FROM #__flexicontact_log WHERE id = '$id'";
	$this->_data = $this->ladb_loadObject($query);
	return $this->_data;
}

//---------------------------------------------------------------
//
function store($email_data)
{
	$query = 'INSERT INTO `#__flexicontact_log` 
		(`datetime`, `name`, `email`, `admin_email`, `admin_from_email`, `user_from_email`, `admin_reply_to_email`,
			`subject`, `message`, `config_show_copy`, `show_copy`, `status_main`, `status_copy`, 
			`ip`, `browser_id`, `browser_string`, `list_choice`,
			`field1`, `field2`, `field3`, `field4`, `field5`) 
		VALUES
			( NOW(), '.
			$this->_db->Quote($email_data->from_name).','.
			$this->_db->Quote($email_data->from_email).','.
			$this->_db->Quote($email_data->admin_email).','.
			$this->_db->Quote($email_data->admin_from_email).','.
			$this->_db->Quote($email_data->user_from_email).','.
			$this->_db->Quote($email_data->admin_reply_to_email).','.
			$this->_db->Quote($email_data->subject).','.
			$this->_db->Quote($email_data->area_data).','.
			$this->_db->Quote($email_data->config_show_copy).','.
			$this->_db->Quote($email_data->show_copy).','.
			$this->_db->Quote($email_data->status_main).','.
			$this->_db->Quote($email_data->status_copy).','.
			$this->_db->Quote($email_data->ip).','.
			$this->_db->Quote($email_data->browser_id).','.
			$this->_db->Quote($email_data->browser_string).','.
			$this->_db->Quote($email_data->list_choice).','.
			$this->_db->Quote($email_data->field1).','.
			$this->_db->Quote($email_data->field2).','.
			$this->_db->Quote($email_data->field3).','.
			$this->_db->Quote($email_data->field4).','.
			$this->_db->Quote($email_data->field5).')';

	$result = $this->ladb_execute($query);

	return true;
}

//-------------------------------------------------------------------------------
// Return a pointer to our pagination object
// This should normally be called after getList()
//
function &getPagination()
{
	if ($this->_pagination == Null)
		$this->_pagination = new JPagination(0,0,0);
	return $this->_pagination;
}

//-------------------------------------------------------------------------------
// Get the list of logs for the log list screen
//
function &getList()
{
// if the table doesn't exist, just return an empty array

	if (!$this->table_exists())
		{
		$nothing = array();
		return $nothing;
		}
		
// get the filter states, order states, and pagination variables

	$filter_date = $this->_app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_date','filter_date',LAFC_LOG_LAST_28_DAYS,'int');
	$limit = $this->_app->getUserStateFromRequest('global.list.limit', 'limit', $this->_app->getCfg('list_limit'), 'int');
	$limitstart = $this->_app->getUserStateFromRequest(LAFC_COMPONENT.'.limitstart', 'limitstart', 0, 'int');
	$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0); // In case limit has been changed
	$filter_order = $this->_app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_order', 'filter_order', 'datetime');
	$filter_order_Dir = $this->_app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_order_Dir', 'filter_order_Dir', 'desc');

// build the query

	$query_count = "SELECT count(*) ";
	$query_cols  = "SELECT id, datetime, name, email, subject, SUBSTRING(message,1,60) AS short_message, status_main, status_copy ";
	$query_from  = "FROM #__flexicontact_log ";

// where

	$query_where = "WHERE 1 ";

	switch ($filter_date)
		{
		case LAFC_LOG_ALL:
			break;
		case LAFC_LOG_LAST_7_DAYS:
			$query_where .= "and datetime >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)";
			break;
		case LAFC_LOG_LAST_28_DAYS:
			$query_where .= "and datetime >= DATE_SUB(CURRENT_DATE(), INTERVAL 28 DAY)";
			break;
		case LAFC_LOG_LAST_12_MONTHS:
			$query_where .= "and datetime >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)";
		}	

// order by

	switch ($filter_order)							// validate column name
		{
		case 'name':
		case 'email':
		case 'subject':
			break;
		default:
			$filter_order = 'datetime';
		}

	if (strcasecmp($filter_order_Dir,'ASC') != 0)	// validate 'asc' or 'desc'
		$filter_order_Dir = 'DESC';

	$query_order = " ORDER BY ".$filter_order.' '.$filter_order_Dir;

// get the total row count

	$count_query = $query_count.$query_from.$query_where;
	$total = $this->ladb_loadResult($count_query);
	
	if ($total === false)
		{
		$this->_app->enqueueMessage($this->ladb_error_text, 'error');
		return $total;
		}

// setup the pagination object

	$this->_pagination = new JPagination($total, $limitstart, $limit);

// get the data, within the limits

	$main_query = $query_cols.$query_from.$query_where.$query_order;
	$this->_data = $this->ladb_loadObjectList($main_query, $limitstart, $limit);
	
	if ($this->_data === false)
		{
		$this->_app->enqueueMessage($this->ladb_error_text, 'error');
		return $this->_data;
		}
		
	return $this->_data;
}

//-------------------------------------------------------------------------------
// delete a log entry
//
function delete($id)
{
	$query = "DELETE FROM #__flexicontact_log WHERE id = $id";
	$result = $this->ladb_execute($query);
	if ($result === false)
		$this->_app->enqueueMessage($this->ladb_error_text, 'error');
}

//-------------------------------------------------------------------------------
// Check whether the log table exists in the database. 
// Returns TRUE if exists, FALSE if it doesn't
//
function table_exists()
{
	return $this->ladb_loadResult("SELECT 1 FROM `#__flexicontact_log` LIMIT 1");
}


}
		
		