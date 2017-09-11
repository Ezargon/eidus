<?php
/*
 *
 * @Version       $Id: default_custom.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.1
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */
defined('_JEXEC') or die('Restricted Access');

$user       = JFactory::getUser();
$canEdit    = $user->authorise('core.edit',        'com_issuetracker');
$canChange  = $user->authorise('core.edit.state',  'com_issuetracker');

// Get custom group name for the display.
$gname = $this->GetCustomGroupName($this->data->related_project_id);
// echo "<pre>"; var_dump($this->custom); echo "</pre>";

?>
<fieldset>
   <legend><?php echo $gname; ?></legend>
   <dl>
      <?php foreach($this->custom as $extraField): ?>
         <?php if($extraField->type == 'header'): ?>
            <dt>
               <h4 class="ExtraFieldHeader">
                  <?php echo $extraField->name; ?>
               </h4>
            </dt>
            <dd></dd>
            <div class="clearfix"></div>
         <?php else: ?>
            <dt>
               <?php echo $extraField->name; ?>
            </dt>
            <dd class="dl-horizontal">
               <?php echo $extraField->element; ?>
            </dd>
         <?php endif; ?>
      <?php endforeach; ?>
   </dl>
</fieldset>
