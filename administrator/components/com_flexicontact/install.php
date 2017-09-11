<?php
/********************************************************************
Product		: Flexicontact
Date		: 31 December 2015
Copyright	: Les Arbres Design 2009-2015
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class com_flexicontactInstallerScript
{
public function preflight($type, $parent) 
{
	$version = new JVersion();  			// get the Joomla version (JVERSION did not exist before Joomla 2.5)
	$joomla_version = $version->RELEASE.'.'.$version->DEV_LEVEL;

	if (version_compare($joomla_version,"2.5.5","<"))			// JDatabase::execute() was added in Joomla 2.5.5
		{
		Jerror::raiseWarning(null, "Flexicontact requires at least Joomla 2.5.5");
		return false;
		}
		
	if (get_magic_quotes_gpc())
		{
		Jerror::raiseWarning(null, "Flexicontact cannot run with PHP Magic Quotes ON. Please switch it off and re-install.");
		return false;
		}

	$app = JFactory::getApplication();
	$dbtype = $app->getCfg('dbtype');
	if (substr($dbtype,0,4) != 'mysq')
		{
		Jerror::raiseWarning(null, "Flexicontact currently only supports MySql databases. It cannot run with $dbtype");
		return false;
		}
		
	return true;
}

public function uninstall($parent)
{ 
	echo "<h2>Flexicontact has been uninstalled</h2>";
	echo "<h2>The database table was NOT deleted</h2>";
}

//-------------------------------------------------------------------------------
// The main install function
//
public function postflight($type, $parent)

{
// check the Joomla version

	if (substr(JVERSION,0,1) > "3")				// if > 3
		echo "This version of Flexicontact has not been tested on this version of Joomla.";
		
// get the component version from the component manifest xml file		

	$component_version = $parent->get('manifest')->version;

// delete redundant files from older versions

	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/admin.flexicontact.php');
	@unlink(JPATH_ROOT.'/administrator/components/com_flexicontact/toolbar.flexicontact.html.php'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_flexicontact/toolbar.flexicontact.php'); 
	@unlink(JPATH_ROOT.'/administrator/components/com_flexicontact/admin.flexicontact.html.php');
	@unlink(JPATH_ROOT.'/components/com_flexicontact/flexicontact.html.php');
	@unlink(JPATH_ROOT.'/components/com_flexicontact/RL_flexicontact.html.php');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/joomla15.xml');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/joomla16.xml');
	@unlink(JPATH_SITE.'/administrator/components/com_flexicontact/install.flexicontact.php');

// create the log table
// add new columns to the log table, in case the user is upgrading from an older version

	$this->_db = JFactory::getDBO();
	$this->create_log_table();
	$this->add_column('#__flexicontact_log', 'admin_email', "VARCHAR(60) NOT NULL DEFAULT '' AFTER `email`");
	$this->add_column('#__flexicontact_log', 'list_choice', "VARCHAR(60) DEFAULT NULL AFTER `browser_string`");
	$this->add_column('#__flexicontact_log', 'admin_from_email', "VARCHAR(60) DEFAULT NULL AFTER `admin_email`"); // 8.08
	$this->add_column('#__flexicontact_log', 'user_from_email', "VARCHAR(60) NOT NULL DEFAULT '' AFTER `admin_from_email`");		// 8.08
	$this->add_column('#__flexicontact_log', 'admin_reply_to_email', "VARCHAR(60) NOT NULL DEFAULT '' AFTER `user_from_email`");		// 8.08
	$this->add_column('#__flexicontact_log', 'config_show_copy', "TINYINT(4) NOT NULL DEFAULT '99' AFTER `message`");		// 8.08
	$this->add_column('#__flexicontact_log', 'show_copy', "TINYINT(4) NOT NULL DEFAULT '99' AFTER `config_show_copy`");		// 8.08

// we are done

	echo "Flexicontact version $component_version installed.";
	return true;
}

//---------------------------------------------------------------
// Create the log table if it doesn't exist
//
function create_log_table()
{
	$query = "CREATE TABLE IF NOT EXISTS `#__flexicontact_log` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `datetime` datetime NOT NULL,
				  `name` varchar(60) NOT NULL DEFAULT '',
				  `email` varchar(60) NOT NULL DEFAULT '',
				  `admin_email` varchar(60) NOT NULL DEFAULT '',
                  `admin_from_email` varchar(60) NOT NULL DEFAULT '',
                  `user_from_email` varchar(60) NOT NULL DEFAULT '',
                  `admin_reply_to_email` varchar(60) NOT NULL DEFAULT '',
				  `subject` varchar(100) NOT NULL DEFAULT '',
				  `message` text NOT NULL,
				  `config_show_copy` TINYINT(4) NOT NULL DEFAULT '99',
				  `show_copy` TINYINT(4) NOT NULL DEFAULT '99',
				  `status_main` varchar(255) NOT NULL DEFAULT '',
				  `status_copy` varchar(255) NOT NULL DEFAULT '',
				  `ip` varchar(40) NOT NULL DEFAULT '',
				  `browser_id` tinyint(4) NOT NULL DEFAULT '0',
				  `browser_string` varchar(20) NOT NULL DEFAULT '',
				  `list_choice` varchar(60) DEFAULT NULL,
				  `field1` varchar(100) DEFAULT NULL,
				  `field2` varchar(100) DEFAULT NULL,
				  `field3` varchar(100) DEFAULT NULL,
				  `field4` varchar(100) DEFAULT NULL,
				  `field5` varchar(100) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `DATETIME` (`datetime`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
				
	return $this->ladb_execute($query);
}

//-------------------------------------------------------------------------------
// Check whether a column exists in a table. Returns TRUE if exists, FALSE if it doesn't
//
function column_exists($table, $column)
{
	$fields = $this->_db->getTableColumns($table);
		
	if ($fields === null)
		return false;
		
	if (array_key_exists($column,$fields))
		return true;
	else
		return false;
}

//-------------------------------------------------------------------------------
// Add a column if it doesn't exist (the table must exist)
//
function add_column($table, $column, $details)
{
	if ($this->column_exists($table, $column))
		return;
	$query = 'ALTER TABLE `'.$table.'` ADD `'.$column.'` '.$details;;
	return $this->ladb_execute($query);
}

//-------------------------------------------------------------------------------
// Execute a SQL query and return true if it worked, false if it failed
//
function ladb_execute($query)
{
	if (version_compare(JVERSION,"3.0.0","<"))	// if < 3.0
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		if ($this->_db->getErrorNum())
			{
			echo '<div style="color:red">'.$this->_db->stderr().'</div>';
			return false;
			}
		return true;
		}
		
// for Joomla 3.0 use try/catch error handling

	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
		}
	catch (RuntimeException $e)
		{
	    echo '<div style="color:red">'.$e->getMessage().'</div>';
		return false;
		}
	return true;
}


} // class
