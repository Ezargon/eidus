<?php
/*
 *
 * @Version       $Id: default25.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.2
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */

defined('_JEXEC') or die('Restricted access');

// Load date helper
if (! class_exists('IssueTrackerHelperDate')) {
    require_once( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_issuetracker'.DS.'helpers'.DS.'dates.php');
}

JHtml::_('behavior.tooltip');
JHTML::_('script','system/multiselect.js',false,true);
// Import CSS
// $document = &JFactory::getDocument();
// $document->addStyleSheet(JURI::root(true).'/media/com_issuetracker/css/administrator.css');

$user = JFactory::getUser();
$userId  = $user->get('id');
$listOrder  = $this->state->get('list.ordering');
$listDirn   = $this->state->get('list.direction');
$canOrder   = $user->authorise('core.edit.state', 'com_issuetracker');
$saveOrder  = $listOrder == 'a.ordering';
?>

<form action="<?php echo JRoute::_('index.php?option=com_issuetracker&view=attachments'); ?>" method="post" name="adminForm" id="adminForm">
   <fieldset id="filter-bar">
      <div class="filter-search fltlft">
         <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
         <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>" />
         <button type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
         <button type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
      </div>
      <div class="filter-select fltrt">

                <select name="filter_state" class="inputbox" onchange="this.form.submit()">
                    <option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
                    <?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->get('filter.state'), true);?>
                </select>

      </div>
   </fieldset>
   <div class="clr"> </div>

   <table class="ittable table-striped">
      <thead>
         <tr>
            <th style="width:1%">
               <input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
            </th>

            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ATTACHMENTS_ISSUE_ID', 'a.issue_id', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ATTACHMENTS_TITLE', 'a.title', $listDirn, $listOrder); ?>
            </th>
            <!-- th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ATTACHMENTS_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
            </th -->
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ATTACHMENTS_FILENAME', 'a.filename', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ATTACHMENTS_FILEPATH', 'a.filepath', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ATTACHMENTS_FILETYPE', 'a.filetype', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_ATTACHMENTS_SIZE', 'a.size', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
               <!-- Dummy for download button. -->
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_CREATED_ON', 'a.created_on', $listDirn, $listOrder); ?>
            </th>
            <th class='left'>
            <?php echo JHtml::_('grid.sort',  'COM_ISSUETRACKER_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
            </th>

            <?php if (isset($this->items[0]->state)) { ?>
               <th style="width:5%">
                  <?php echo JHtml::_('grid.sort',  'JPUBLISHED', 'a.state', $listDirn, $listOrder); ?>
               </th>
            <?php } ?>

            <?php if (isset($this->items[0]->ordering)) { ?>
               <th style="width:8%">
                  <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
                  <?php if ($canOrder && $saveOrder) :?>
                     <?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'attachments.saveorder'); ?>
                  <?php endif; ?>
               </th>
            <?php } ?>

            <?php if (isset($this->items[0]->id)) { ?>
               <th style="width:1%" class="nowrap">
                   <?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
               </th>
            <?php } ?>
         </tr>
      </thead>

      <tfoot>
         <tr>
            <td colspan="10">
               <?php echo $this->pagination->getListFooter(); ?>
            </td>
         </tr>
      </tfoot>
      <tbody>
      <?php foreach ($this->items as $i => $item) :
         $ordering   = ($listOrder == 'a.ordering');
         $canCreate  = $user->authorise('core.create',      'com_issuetracker');
         $canEdit    = $user->authorise('core.edit',        'com_issuetracker');
         $canCheckin = $user->authorise('core.manage',      'com_issuetracker');
         $canChange  = $user->authorise('core.edit.state',  'com_issuetracker');
         ?>
         <tr class="row<?php echo $i % 2; ?>">
            <td class="center">
               <?php echo JHtml::_('grid.id', $i, $item->id); ?>
            </td>

            <td>
               <?php if (isset($item->checked_out) && $item->checked_out) : ?>
                  <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'attachments.', $canCheckin); ?>
               <?php endif; ?>
               <?php echo $item->issue_id; ?>
            </td>

            <td>
               <?php echo $item->title; ?>
            </td>

            <!-- td>
              <?php echo $item->description; ?>
            </td -->

            <td>
            <?php if ($canEdit) : ?>
               <a href="<?php echo JRoute::_('index.php?option=com_issuetracker&task=attachment.edit&id='.(int) $item->id); ?>">
               <?php echo $this->escape($item->filename); ?></a>
            <?php else : ?>
               <?php echo $this->escape($item->filename); ?>
            <?php endif; ?>
            </td>

            <td>
               <?php echo $item->filepath; ?>
            </td>
            <td>
               <?php echo $item->filetype; ?>
            </td>
            <td>
               <?php echo $item->size; ?>
            </td>

            <td>
               <a class="btn btn-info" href="<?php echo 'index.php?option=com_issuetracker&task=attachment.read&id='.$item->id ?>">
               <class="icon-folder-open icon-white">
               <?php echo JText::_('COM_ISSUETRACKER_ATTACHMENT_DOWNLOAD') ?>
               </a>
            </td>

            <td>
               <?php echo IssueTrackerHelperDate::getDate($item->created_on); ?>
            </td>
            <td>
               <?php echo $item->created_by; ?>
            </td>

                <?php if (isset($this->items[0]->state)) { ?>
                <td class="center">
                   <?php echo JHtml::_('jgrid.published', $item->state, $i, 'attachments.', $canChange, 'cb'); ?>
                </td>
                <?php } ?>

                <?php if (isset($this->items[0]->ordering)) { ?>
                <td class="order">
                   <?php if ($canChange) : ?>
                      <?php if ($saveOrder) :?>
                         <?php if ($listDirn == 'asc') : ?>
                            <span><?php echo $this->pagination->orderUpIcon($i, true, 'attachments.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                            <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'attachments.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                         <?php elseif ($listDirn == 'desc') : ?>
                            <span><?php echo $this->pagination->orderUpIcon($i, true, 'attachments.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
                            <span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'attachments.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
                         <?php endif; ?>
                      <?php endif; ?>
                      <?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
                      <input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
                   <?php else : ?>
                      <?php echo $item->ordering; ?>
                   <?php endif; ?>
                </td>
                <?php } ?>
                <?php if (isset($this->items[0]->id)) { ?>
            <td class="center">
               <?php echo (int) $item->id; ?>
            </td>
                <?php } ?>
         </tr>
         <?php endforeach; ?>
      </tbody>
   </table>

   <div>
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
      <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
      <?php echo JHtml::_('form.token'); ?>
   </div>
</form>