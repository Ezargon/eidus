<?php
/*
 *
 * @Version       $Id: default_pages.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.4
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */
defined('_JEXEC') or die('Restricted Access');

$jversion = new JVersion();

// echo "<pre>"; var_dump($this->custom); echo "</pre>";
?>
<?php if( version_compare( $jversion->getShortVersion(), '3.2', 'ge' ) ) { ?>
<div class="pagination">
   <div class="limit pull-right">
      <?php echo JText::_('JGLOBAL_DISPLAY_NUM'); ?> <?php echo $this->pagination->getlimitBox(); ?>
   </div>
   <br />
   <?php echo $this->pagination->getPagesLinks(); ?>
   <br />
   <div class="clearfix"></div>
   <div style="text-align: center">
- <?php echo $this->pagination->getPagesCounter(); ?> -
   </div>
</div>
<?php } else { ?>
<?php echo $this->pagination->getListFooter(); ?>
<?php } ?>
