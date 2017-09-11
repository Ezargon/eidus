<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

namespace Akeeba\Engine\Platform;

// Protection against direct access
defined('AKEEBAENGINE') or die();

use Akeeba\Engine\Finalization\TestExtract;
use Akeeba\Engine\Util\Comconfig;
use Psr\Log\LogLevel;
use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use Akeeba\Engine\Platform\Base as BasePlatform;

if ( !defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR); // Still required by Joomla! :(
}

if (!class_exists('JDate'))
{
	jimport('joomla.utilities.date');
}

/**
 * Joomla! 1.6/1.7/2.5/3.x platform class
 */
class Joomla25 extends BasePlatform
{
	/** @var int Platform class priority */
	public $priority = 53;

	public $platformName = 'joomla25';

	function __construct()
	{
		$configOverrides = array();

		$configOverrides['volatile.core.finalization.action_handlers'] = array(
			new TestExtract()
		);
		$configOverrides['volatile.core.finalization.action_queue_before'] = array(
			'test_extract',
		);

		// Apply the configuration overrides, please
		$this->configOverrides = $configOverrides;
	}


	/**
	 * Loads the current configuration off the database table
	 *
	 * @param   int  $profile_id  The profile where to read the configuration from, defaults to current profile
	 *
	 * @return  bool  True if everything was read properly
	 */
	public function load_configuration($profile_id = null)
	{
		// Load the configuration
		parent::load_configuration($profile_id);

		// If there is no embedded installer or the wrong embedded installer is selected, fix it automatically
		$config = Factory::getConfiguration();
		$embedded_installer = $config->get('akeeba.advanced.embedded_installer', null);

		if (empty($embedded_installer) || ($embedded_installer == 'angie-joomla'))
		{
			$protectedKeys = $config->getProtectedKeys();
			$config->setProtectedKeys(array());
			$config->set('akeeba.advanced.embedded_installer', 'angie');
			$config->setProtectedKeys($protectedKeys);
		}
	}

	/**
	 * Saves the current configuration to the database table
	 *
	 * @param   int  $profile_id  The profile where to save the configuration to, defaults to current profile
	 *
	 * @return  bool  True if everything was saved properly
	 */
	public function save_configuration($profile_id = null)
	{
		// If there is no embedded installer or the wrong embedded installer is selected, fix it automatically
		$config = Factory::getConfiguration();
		$embedded_installer = $config->get('akeeba.advanced.embedded_installer', null);

		if (empty($embedded_installer) || ($embedded_installer == 'angie-joomla'))
		{
			$protectedKeys = $config->getProtectedKeys();
			$config->setProtectedKeys(array());
			$config->set('akeeba.advanced.embedded_installer', 'angie');
			$config->setProtectedKeys($protectedKeys);
		}

		// Save the configuration
		return parent::save_configuration($profile_id); // TODO: Change the autogenerated stub
	}


	/**
	 * Performs heuristics to determine if this platform object is the ideal
	 * candidate for the environment Akeeba Engine is running in.
	 *
	 * @return bool
	 */
	public function isThisPlatform()
	{
		// Make sure _JEXEC is defined
		if ( !defined('_JEXEC'))
		{
			return false;
		}

		// We need JVERSION to be defined
		if ( !defined('JVERSION'))
		{
			return false;
		}

		// Check if JFactory exists
		if ( !class_exists('JFactory'))
		{
			return false;
		}

		// Check if JApplication exists
		$appExists = class_exists('JApplication');
		$appExists = $appExists || class_exists('JCli');
		$appExists = $appExists || class_exists('JApplicationCli');
		$appExists = $appExists || class_exists('AkeebaCliBase');

		if ( !$appExists)
		{
			return false;
		}

		return true;
	}

	/**
	 * Returns an associative array of stock platform directories
	 *
	 * @return array
	 */
	public function get_stock_directories()
	{
		static $stock_directories = array();

		if (empty($stock_directories))
		{
			$jreg = \JFactory::getConfig();
			$tmpdir = $jreg->get('tmp_path');
			$stock_directories['[SITEROOT]']       = $this->get_site_root();
			$stock_directories['[ROOTPARENT]']     = @realpath($this->get_site_root() . '/..');
			$stock_directories['[SITETMP]']        = $tmpdir;
			$stock_directories['[DEFAULT_OUTPUT]'] = $this->get_site_root() . '/administrator/components/com_akeeba/backup';
		}

		return $stock_directories;
	}

	/**
	 * Returns the absolute path to the site's root
	 *
	 * @return string
	 */
	public function get_site_root()
	{
		static $root = null;

		if (empty($root) || is_null($root))
		{
			$root = JPATH_ROOT;

			if (empty($root) || ($root == DIRECTORY_SEPARATOR) || ($root == '/'))
			{
				// Try to get the current root in a different way
				if (function_exists('getcwd'))
				{
					$root = getcwd();
				}

				$app = \JFactory::getApplication();
				if ($app->isAdmin())
				{
					if (empty($root))
					{
						$root = '../';
					}
					else
					{
						$adminPos = strpos($root, 'administrator');
						if ($adminPos !== false)
						{
							$root = substr($root, 0, $adminPos);
						}
						else
						{
							$root = '../';
						}
						// Degenerate case where $root = 'administrator'
						// without a leading slash before entering this
						// if-block
						if (empty($root))
						{
							$root = '../';
						}
					}
				}
				else
				{
					if (empty($root) || ($root == DIRECTORY_SEPARATOR) || ($root == '/'))
					{
						$root = './';
					}
				}
			}
		}

		return $root;
	}

	/**
	 * Returns the absolute path to the installer images directory
	 *
	 * @return string
	 */
	public function get_installer_images_path()
	{
		return JPATH_ADMINISTRATOR . '/components/com_akeeba/assets/installers';
	}

	/**
	 * Returns the active profile number
	 *
	 * @return int
	 */
	public function get_active_profile()
	{
		if (defined('AKEEBA_PROFILE'))
		{
			return AKEEBA_PROFILE;
		}
		else
		{
			$session = \JFactory::getSession();

			return $session->get('profile', null, 'akeeba');
		}
	}

	/**
	 * Returns the selected profile's name. If no ID is specified, the current
	 * profile's name is returned.
	 *
	 * @return string
	 */
	public function get_profile_name($id = null)
	{
		if (empty($id))
		{
			$id = $this->get_active_profile();
		}
		$id = (int)$id;

		$sql = 'SELECT `description` FROM `#__ak_profiles` WHERE `id` = ' . $id;
		$db  = Factory::getDatabase($this->get_platform_database_options());
		$db->setQuery($sql);

		return $db->loadResult();
	}

	/**
	 * Returns the backup origin
	 *
	 * @return string Backup origin: backend|frontend
	 */
	public function get_backup_origin()
	{
		if (defined('AKEEBA_BACKUP_ORIGIN'))
		{
			return AKEEBA_BACKUP_ORIGIN;
		}

		if (\JFactory::getApplication()->isAdmin())
		{
			return 'backend';
		}
		else
		{
			return 'frontend';
		}
	}

	/**
	 * Returns a MySQL-formatted timestamp out of the current date
	 *
	 * @param string $date [optional] The timestamp to use. Omit to use current timestamp.
	 *
	 * @return string
	 */
	public function get_timestamp_database($date = 'now')
	{
		\JLoader::import('joomla.utilities.date');
		$jdate = new \JDate($date);

		if (method_exists($jdate, 'toSql'))
		{
			return $jdate->toSql();
		}

		return $jdate->toMySQL();
	}

	/**
	 * Returns the current timestamp, taking into account any TZ information,
	 * in the format specified by $format.
	 *
	 * @param string $format Timestamp format string (standard PHP format string)
	 *
	 * @return string
	 */
	public function get_local_timestamp($format)
	{
		\JLoader::import('joomla.utilities.date');
		\JLoader::import('joomla.environment.request');

		$jregistry = \JFactory::getConfig();
		$tzDefault = $jregistry->get('offset');
		$user = \JFactory::getUser();
		$tz   = $user->getParam('timezone', $tzDefault);

		$dateNow = new \JDate('now', $tz);

		return $dateNow->format($format, true);
	}


	/**
	 * Returns the current host name
	 *
	 * @return string
	 */
	public function get_host()
	{
		if ( !array_key_exists('REQUEST_METHOD', $_SERVER))
		{
			// Running under CLI
			if ( !class_exists('JUri', true))
			{
				$filename = JPATH_ROOT . '/libraries/joomla/environment/uri.php';
				if (file_exists($filename))
				{
					// Joomla! 2.5
					require_once $filename;
				}
				else
				{
					// Joomla! 3.x (and later?)
					require_once JPATH_ROOT . '/libraries/joomla/uri/uri.php';
				}
			}
			$url  = Platform::getInstance()->get_platform_configuration_option('siteurl', '');
			$oURI = new \JUri($url);
		}
		else
		{
			// Running under the web server
			$oURI = \JUri::getInstance();
		}

		return $oURI->getHost();
	}

	public function get_site_name()
	{
		$jconfig = \JFactory::getConfig();
		return $jconfig->get('sitename', '');
	}

	/**
	 * Gets the best matching database driver class, according to CMS settings
	 *
	 * @param bool $use_platform If set to false, it will forcibly try to assign one of the primitive type
	 *                           (Mysql/Mysqli) and NEVER tell you to use a platform driver.
	 *
	 * @return string
	 */
	public function get_default_database_driver($use_platform = true)
	{
		$jconfig = \JFactory::getConfig();
		$driver = $jconfig->get('dbtype');

		// Let's see what driver Joomla! uses...
		if ($use_platform)
		{
			$hasNookuContent = file_exists(JPATH_ROOT . '/plugins/system/nooku.php');

			switch ($driver)
			{
				// MySQL or MySQLi drivers are known to be working; use their
				// Akeeba Engine extended version, Akeeba\Engine\Driver\Joomla
				case 'mysql':
					if ($hasNookuContent)
					{
						return '\\Akeeba\\Engine\\Driver\\Mysql';
					}
					else
					{
						return '\\Akeeba\\Engine\\Driver\\Joomla';
					}
					break;

				case 'mysqli':
					if ($hasNookuContent)
					{
						return '\\Akeeba\\Engine\\Driver\\Mysqli';
					}
					else
					{
						return '\\Akeeba\\Engine\\Driver\\Joomla';
					}
					break;

				case 'sqlsrv':
				case 'sqlazure':
					return '\\Akeeba\\Engine\\Driver\\Joomla';
					break;

				case 'postgresql':
					return '\\Akeeba\\Engine\\Driver\\Joomla';
					break;

				case 'pdomysql':
					return '\\Akeeba\\Engine\\Driver\\Joomla';
					break;

				// Some custom driver. Uh oh!
				default:
					break;
			}
		}

		// Is this a subcase of mysqli or mysql drivers?
		if (strtolower(substr($driver, 0, 6)) == 'mysqli')
		{
			return '\\Akeeba\\Engine\\Driver\\Mysqli';
		}
		elseif (strtolower(substr($driver, 0, 5)) == 'mysql')
		{
			return '\\Akeeba\\Engine\\Driver\\Mysql';
		}
		elseif (strtolower(substr($driver, 0, 6)) == 'sqlsrv')
		{
			return '\\Akeeba\\Engine\\Driver\\Sqlsrv';
		}
		elseif (strtolower(substr($driver, 0, 8)) == 'sqlazure')
		{
			return '\\Akeeba\\Engine\\Driver\\Sqlazure';
		}
		elseif (strtolower(substr($driver, 0, 10)) == 'postgresql')
		{
			return '\\Akeeba\\Engine\\Driver\\Postgresql';
		}
		elseif (strtolower(substr($driver, 0, 8)) == 'pdomysql')
		{
			return '\\Akeeba\\Engine\\Driver\\Pdomysql';
		}

		// If we're still here, we have to guesstimate the correct driver. All bets are off.
		// And you'd better be using MySQL!!!
		if (function_exists('mysqli_connect'))
		{
			// MySQLi available. Let's use it.
			return '\\Akeeba\\Engine\\Driver\\Mysqli';
		}
		else
		{
			// MySQLi is not available; let's use standard MySQL.
			return '\\Akeeba\\Engine\\Driver\\Mysql';
		}
	}

	/**
	 * Returns a set of options to connect to the default database of the current CMS
	 *
	 * @return array
	 */
	public function get_platform_database_options()
	{
		static $options;

		if (empty($options))
		{
			$conf = \JFactory::getConfig();
			$options = array(
				'host'     => $conf->get('host'),
				'user'     => $conf->get('user'),
				'password' => $conf->get('password'),
				'database' => $conf->get('db'),
				'prefix'   => $conf->get('dbprefix')
			);
		}

		return $options;
	}

	/**
	 * Provides a platform-specific translation function
	 *
	 * @param string $key The translation key
	 *
	 * @return string
	 */
	public function translate($key)
	{
		return \JText::_($key);
	}

	/**
	 * Populates global constants holding the Akeeba version
	 */
	public function load_version_defines()
	{
		if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/version.php'))
		{
			require_once(JPATH_COMPONENT_ADMINISTRATOR . '/version.php');
		}

		if ( !defined('AKEEBA_VERSION'))
		{
			define("AKEEBA_VERSION", "svn");
		}
		if ( !defined('AKEEBA_PRO'))
		{
			define('AKEEBA_PRO', false);
		}
		if ( !defined('AKEEBA_DATE'))
		{
			\JLoader::import('joomla.utilities.date');
			$date = new \JDate();
			define("AKEEBA_DATE", $date->format('Y-m-d'));
		}
	}

	/**
	 * Returns the platform name and version
	 *
	 * @param string $platform_name Name of the platform, e.g. Joomla!
	 * @param string $version       Full version of the platform
	 */
	public function getPlatformVersion()
	{
		$v = new \JVersion();

		return array(
			'name'    => 'Joomla!',
			'version' => $v->getShortVersion()
		);
	}

	/**
	 * Logs platform-specific directories with LogLevel::INFO log level
	 */
	public function log_platform_special_directories()
	{
		$ret = array();

		Factory::getLog()->log(LogLevel::INFO, "JPATH_BASE         :" . JPATH_BASE);
		Factory::getLog()->log(LogLevel::INFO, "JPATH_SITE         :" . JPATH_SITE);
		Factory::getLog()->log(LogLevel::INFO, "JPATH_ROOT         :" . JPATH_ROOT);
		Factory::getLog()->log(LogLevel::INFO, "JPATH_CACHE        :" . JPATH_CACHE);
		Factory::getLog()->log(LogLevel::INFO, "Computed root      :" . $this->get_site_root());

		// If the release is older than 3 months, issue a warning
		if (defined('AKEEBA_DATE'))
		{
			$releaseDate = new \JDate(AKEEBA_DATE);

			if (time() - $releaseDate->toUnix() > 7776000)
			{
				if ( !isset($ret['warnings']))
				{
					$ret['warnings'] = array();
					$ret['warnings'] = array_merge($ret['warnings'], array(
						'Your version of Akeeba Backup is more than 90 days old and most likely already out of date. Please check if a newer version is published and install it.'
					));
				}
			}

		}

		// Detect UNC paths and warn the user
		if (DIRECTORY_SEPARATOR == '\\')
		{
			if ((substr(JPATH_ROOT, 0, 2) == '\\\\') || (substr(JPATH_ROOT, 0, 2) == '//'))
			{
				if ( !isset($ret['warnings']))
				{
					$ret['warnings'] = array();
				}

				$ret['warnings'] = array_merge($ret['warnings'], array(
					'Your site\'s root is using a UNC path (e.g. \\SERVER\path\to\root). PHP has known bugs which may',
					'prevent it from working properly on a site like this. Please take a look at',
					'https://bugs.php.net/bug.php?id=40163 and https://bugs.php.net/bug.php?id=52376. As a result your',
					'backup may fail.'
				));
			}
		}

		if (empty($ret))
		{
			$ret = null;
		}

		return $ret;
	}

	/**
	 * Loads a platform-specific software configuration option
	 *
	 * @param string $key
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_platform_configuration_option($key, $default)
	{
		// Get the component configuration option WITHOUT using the ever-changing Joomla! API...
		return Comconfig::getValue($key, $default);
	}

	/**
	 * Returns a list of emails to the Super Administrators
	 *
	 * @return  array
	 */
	public function get_administrator_emails()
	{
		$options = $this->get_platform_database_options();
		$db = Factory::getDatabase($options);

		// Load the root asset node and read the rules
		$query = $db->getQuery(true)
					->select($db->qn('rules'))
					->from('#__assets')
					->where($db->qn('name') . ' = ' . $db->q('root.1'));
		$db->setQuery($query);
		$jsonRules = $db->loadResult();

		$rules       = json_decode($jsonRules, true);
		$adminGroups = array();
		$mails       = array();

		if (array_key_exists('core.admin', $rules))
		{
			$rawGroups = $rules['core.admin'];

			if ( !empty($rawGroups))
			{
				foreach ($rawGroups as $group => $allowed)
				{
					if ($allowed)
					{
						$adminGroups[] = $db->q($group);
					}
				}
			}
		}

		if (empty($adminGroups))
		{
			return $mails;
		}

		$adminGroups = implode(',', $adminGroups);

		$query = $db->getQuery(true)
					->select(array(
						$db->qn('u') . '.' . $db->qn('name'),
						$db->qn('u') . '.' . $db->qn('email'),
					))
					->from($db->qn('#__users') . ' AS ' . $db->qn('u'))
					->join(
						'INNER', $db->qn('#__user_usergroup_map') . ' AS ' . $db->qn('m') . ' ON (' .
						$db->qn('m') . '.' . $db->qn('user_id') . ' = ' . $db->qn('u') . '.' . $db->qn('id') . ')'
					)
					->where($db->qn('m') . '.' . $db->qn('group_id') . ' IN (' . $adminGroups . ')');
		$db->setQuery($query);
		$superAdmins = $db->loadAssocList();

		if ( !empty($superAdmins))
		{
			foreach ($superAdmins as $admin)
			{
				$mails[] = $admin['email'];
			}
		}

		return $mails;
	}

	/**
	 * Sends a very simple email using the platform's mailer facility
	 *
	 * @param   string $to         The recipient's email address
	 * @param   string $subject    The subject of the email
	 * @param   string $body       The body of the email
	 * @param   string $attachFile The file to attach (null to not attach any files)
	 *
	 * @return  boolean
	 */
	public function send_email($to, $subject, $body, $attachFile = null)
	{
		Factory::getLog()->log(LogLevel::DEBUG, "-- Fetching mailer object");

		$mailer = Platform::getInstance()->getMailer();

		if ( !is_object($mailer))
		{
			Factory::getLog()->log(LogLevel::WARNING, "Could not send email to $to - Reason: Mailer object is not an object; please check your system settings");

			return false;
		}

		Factory::getLog()->log(LogLevel::DEBUG, "-- Creating email message");

		$recipient = array($to);
		$mailer->addRecipient($recipient);
		$mailer->setSubject($subject);
		$mailer->setBody($body);

		if ( !empty($attachFile))
		{
			Factory::getLog()->log(LogLevel::WARNING, "-- Attaching $attachFile");

			if ( !file_exists($attachFile) || !(is_file($attachFile) || is_link($attachFile)))
			{
				Factory::getLog()->log(LogLevel::WARNING, "The file does not exist, or it's not a file; no email sent");

				return false;
			}

			if ( !is_readable($attachFile))
			{
				Factory::getLog()->log(LogLevel::WARNING, "The file is not readable; no email sent");

				return false;
			}

			$filesize = @filesize($attachFile);
			if ($filesize)
			{
				// Check that we have AT LEAST 2.5 times free RAM as the filesize (that's how much we'll need)
				if ( !function_exists('ini_get'))
				{
					// Assume 8Mb of PHP memory limit (worst case scenario)
					$totalRAM = 8388608;
				}
				else
				{
					$totalRAM = ini_get('memory_limit');
					if (strstr($totalRAM, 'M'))
					{
						$totalRAM = (int)$totalRAM * 1048576;
					}
					elseif (strstr($totalRAM, 'K'))
					{
						$totalRAM = (int)$totalRAM * 1024;
					}
					elseif (strstr($totalRAM, 'G'))
					{
						$totalRAM = (int)$totalRAM * 1073741824;
					}
					else
					{
						$totalRAM = (int)$totalRAM;
					}
					if ($totalRAM <= 0)
					{
						// No memory limit? Cool! Assume 1Gb of available RAM (which is absurdely abundant as of March 2011...)
						$totalRAM = 1086373952;
					}
				}
				if ( !function_exists('memory_get_usage'))
				{
					$usedRAM = 8388608;
				}
				else
				{
					$usedRAM = memory_get_usage();
				}

				$availableRAM = $totalRAM - $usedRAM;

				if ($availableRAM < 2.5 * $filesize)
				{
					Factory::getLog()->log(LogLevel::WARNING, "The file is too big to be sent by email. Please use a smaller Part Size for Split Archives setting.");
					Factory::getLog()->log(LogLevel::DEBUG, "Memory limit $totalRAM bytes -- Used memory $usedRAM bytes -- File size $filesize -- Attachment requires approx. " . (2.5 * $filesize) . " bytes");

					return false;
				}
			}
			else
			{
				Factory::getLog()->log(LogLevel::WARNING, "Your server fails to report the file size of $attachFile. If the backup crashes, please use a smaller Part Size for Split Archives setting");
			}

			$mailer->addAttachment($attachFile);
		}

		Factory::getLog()->log(LogLevel::DEBUG, "-- Sending message");

		$result = $mailer->Send();

		if ($result instanceof \JException)
		{
			Factory::getLog()->log(LogLevel::WARNING, "Could not email $to:");
			Factory::getLog()->log(LogLevel::WARNING, $result->getMessage());
			$ret = $result->getMessage();
			unset($result);
			unset($mailer);

			return $ret;
		}
		else
		{
			Factory::getLog()->log(LogLevel::DEBUG, "-- Email sent");

			return true;
		}
	}

	/**
	 * Deletes a file from the local server using direct file access or FTP
	 *
	 * @param string $file
	 *
	 * @return bool
	 */
	public function unlink($file)
	{
		if (function_exists('jimport'))
		{
			\JLoader::import('joomla.filesystem.file');
			$result = \JFile::delete($file);
			if ( !$result)
			{
				$result = @unlink($file);
			}
		}
		else
		{
			$result = parent::unlink($file);
		}

		return $result;
	}

	/**
	 * Moves a file around within the local server using direct file access or FTP
	 *
	 * @param string $from
	 * @param string $to
	 *
	 * @return bool
	 */
	public function move($from, $to)
	{
		if (function_exists('jimport'))
		{
			\JLoader::import('joomla.filesystem.file');
			$result = \JFile::move($from, $to);
			// JFile failed. Let's try rename()
			if ( !$result)
			{
				$result = @rename($from, $to);
			}
			// Rename failed, too. Let's try copy/delete
			if ( !$result)
			{
				// Try copying with JFile. If it fails, use copy().
				$result = \JFile::copy($from, $to);
				if ( !$result)
				{
					$result = @copy($from, $to);
				}

				// If the copy succeeded, try deleting the original with JFile. If it fails, use unlink().
				if ($result)
				{
					$result = $this->unlink($from);
				}
			}
		}
		else
		{
			$result = parent::move($from, $to);
		}

		return $result;
	}

	/**
	 * Registers Akeeba Engine's core classes with JLoader
	 *
	 * @param string $path_prefix The path prefix to look in
	 */
	protected function register_akeeba_engine_classes($path_prefix)
	{
		global $Akeeba_Class_Map;
		\JLoader::import('joomla.filesystem.folder');
		foreach ($Akeeba_Class_Map as $class_prefix => $path_suffix)
		{
			// Bail out if there is such directory, so as not to have Joomla! throw errors
			if ( !@is_dir($path_prefix . '/' . $path_suffix))
			{
				continue;
			}

			$file_list = \JFolder::files($path_prefix . '/' . $path_suffix, '.*\.php');
			if (is_array($file_list) && !empty($file_list))
			{
				foreach ($file_list as $file)
				{
					$class_suffix = ucfirst(basename($file, '.php'));
					\JLoader::register($class_prefix . $class_suffix, $path_prefix . '/' . $path_suffix . '/' . $file);
				}
			}
		}
	}

	/**
	 * Joomla!-specific function to get an instance of the mailer class
	 *
	 * @return \JMail
	 */
	public function &getMailer()
	{
		$mailer = \JFactory::getMailer();
		if ( !is_object($mailer))
		{
			Factory::getLog()->log(LogLevel::WARNING, "Fetching Joomla!'s mailer was impossible; imminent crash!");
		}
		else
		{
			$emailMethod = $mailer->Mailer;
			Factory::getLog()->log(LogLevel::DEBUG, "-- Joomla!'s mailer is using $emailMethod mail method.");
		}

		return $mailer;
	}

	/**
	 * Stores a flash (temporary) variable in the session.
	 *
	 * @param   string $name  The name of the variable to store
	 * @param   string $value The value of the variable to store
	 *
	 * @return  void
	 */
	public function set_flash_variable($name, $value)
	{
		$session = \JFactory::getSession();
		$session->set($name, $value, 'akeebabackup');
	}

	/**
	 * Return the value of a flash (temporary) variable from the session and
	 * immediately removes it.
	 *
	 * @param   string $name    The name of the flash variable
	 * @param   mixed  $default Default value, if the variable is not defined
	 *
	 * @return  mixed  The value of the variable or $default if it's not set
	 */
	public function get_flash_variable($name, $default = null)
	{
		$session = \JFactory::getSession();
		$ret     = $session->get($name, $default, 'akeebabackup');
		$session->set($name, null, 'akeebabackup');

		return $ret;
	}

	/**
	 * Perform an immediate redirection to the defined URL
	 *
	 * @param   string $url The URL to redirect to
	 *
	 * @return  void
	 */
	public function redirect($url)
	{
		\JFactory::getApplication()->redirect($url);
	}
}