<?php
/*
 *
 * @Version       $Id: akismetapi.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.4.0
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

JLoader::import('joomla.form.formrule');

if(!defined('DS')){
   define('DS',DIRECTORY_SEPARATOR);
}

if (! class_exists('Akismet')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'classes'.DS.'Akismet.php');
}

// JLoader::register('Akismet', dirname(__FILE__).'/../../classes/Akismet.php');

/**
 * Class JFormRuleAkismetApi
 */
class JFormRuleAkismetApi extends JFormRule
{
   protected $regex = '[a-z0-9]{12}';
   protected $modifiers = 'u';

   /**
    * @param SimpleXMLElement $element
    * @param mixed $value
    * @param null $group
    * @param null $input
    * @param null $form
    * @return bool|JException
    */
   public function test(& $element, $value, $group = null, & $input = null, & $form = null)
   {
      if (empty($value) ) return true;

      if (!parent::test($element, $value, $group, $input , $form)) {
         return false;
      }

      $akismet = new Akismet($input->get('site_url'), $value);
      if (!$akismet->isKeyValid()) {
         return new JException(JText::_('COM_ISSUETRACKER_AKISMET_INVALID_API_KEY'), 500, E_ERROR);
      }
      return true;
   }
}
