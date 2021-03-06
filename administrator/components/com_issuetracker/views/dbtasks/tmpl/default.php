<?php
/*
 *
 * @Version       $Id: default.php 2060 2015-08-19 09:40:47Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.0.0
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-08-19 10:40:47 +0100 (Wed, 19 Aug 2015) $
 *
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

JHTML::_('behavior.mootools');
?>

<?php if(!empty($this->table)): ?>
<h1><?php echo JText::_('COM_ISSUETRACKER_OPTIMISE_INPROGRESS'); ?></h1>
<?php else: ?>
<h1><?php echo JText::_('COM_ISSUETRACKER_OPTIMISE_COMPLETE'); ?></h1>
<?php endif; ?>

<div id="progressbar-outer">
   <div id="progressbar-inner"></div>
</div>

<?php if(!empty($this->table)): ?>
<form action="index.php" name="adminForm" id="adminForm">
   <input type="hidden" name="option" value="com_issuetracker" />
   <input type="hidden" name="view" value="dbtasks" />
   <input type="hidden" name="task" value="optimize" />
   <input type="hidden" name="from" value="<?php echo $this->table ?>" />
   <input type="hidden" name="tmpl" value="component" />
</form>
<?php endif; ?>

<?php if($this->percent == 100): ?>
<div class="disclaimer">
   <h3><?php echo JText::_('COM_ISSUETRACKER_AUTOCLOSE_IN_3S'); ?></h3>
</div>
<?php endif; ?>