<?php
/**
 * @package LiveUpdate
 * @copyright Copyright ©2011 Nicholas K. Dionysopoulos / AkeebaBackup.com
 * @license GNU LGPLv3 or later <http://www.gnu.org/copyleft/lesser.html>
 */

defined('_JEXEC') or die();

/**
 * Configuration class for your extension's updates. Override to your liking.
 */
class LiveUpdateConfig extends LiveUpdateAbstractConfig
{
   var $_extensionName           = 'com_issuetracker';
   var $_extensionTitle          = 'Macrotone Issue Tracker';
   var $_updateURL               = 'http://www.macrotoneconsulting.co.uk/extensions/issuetracker.ini';
   var $_requiresAuthorization   = false;
   var $_versionStrategy         = 'vcompare';

   /**
    *
    */
   function __construct()
   {
      $this->_cacerts = dirname(__FILE__).'/../assets/cacert.pem';

      parent::__construct();
   }
}