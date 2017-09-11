<?php
/*
 *
 * @Version       $Id: default.php 1929 2015-02-08 16:25:12Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    mod_latest_issues
 * @Release       1.6.3
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-02-08 16:25:12 +0000 (Sun, 08 Feb 2015) $
 *
 */
defined('_JEXEC') or die('Restricted access');

$document = JFactory::getDocument();
$url = "modules/mod_latest_issues/css/style.css";
$document->addStyleSheet($url);

if(empty($issues)){ echo JText::_('MOD_LATEST_ISSUES_NO_ISSUES_FOUND_TEXT'); return; }

$length = $params->get('length',25);
$showissueno   = $params->get('show_issueno',1);
$showproject   = $params->get('show_project',0);
$showclosed    = $params->get('show_closedate',0);
$showstatus    = $params->get('show_status',0);
$popup         = $params->get('popup');

$llink = 'index.php?option=com_issuetracker&view=itissues';
if ( $popup )
   $llink .= '&tmpl=component';

$projectname   = JText::_('MOD_LATEST_ISSUES_PROJECTNAME_TEXT');
$statustext    = JText::_('MOD_LATEST_ISSUES_STATUSNAME_TEXT');
$closedate     = JText::_('MOD_LATEST_ISSUES_CLOSEDATE_TEXT');
$_issueno      = JText::_('MOD_LATEST_ISSUES_ISSUENO_TEXT');
$_summary      = JText::_('MOD_LATEST_ISSUES_SUMMARY_TEXT');
$_more         = JText::_('MOD_LATEST_ISSUES_MORE_TEXT');
$_show_links   = $params->get('show_links',1);

?>

<div class="IssueHeaderText<?php $params->get('moduleclass_sfx'); ?>">
   <?php if ( $showissueno == 1 ) { echo $_issueno."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; } ?>
   <?php echo $_summary; ?>
</div>

<div class="Issueslatest<?php echo $params->get('moduleclass_sfx'); ?>">
<?php foreach ($issues as $item) :  ?>
   <div class="IssueRecentRow<?php echo $params->get('moduleclass_sfx'); ?>">
      <?php if ($_show_links == 1) { ?>
         <a <?php if ($popup) { echo 'class="modal" rel="{handler: \'iframe\', size: {x: '.intval($params->get('popup_width',750)).', y: '.intval($params->get('popup_height',550)).'}}"'; } else { echo 'class="latestissues'.$params->get('moduleclass_sfx').'"';} ?> href="<?php echo JRoute::_($llink.'&id='.$item->id); ?>" >
            <?php if ( $showissueno == 0 ) {
               if (strlen($item->issue_summary) > $length ) {
                  echo substr($item->issue_summary, 0, $length) . $_more;
               } else {
                  echo $item->issue_summary;
               }
            } else {
               if (strlen($item->issue_summary) > $length ) {
                  echo $item->alias.' - '. substr($item->issue_summary, 0, $length) . $_more;
               } else {
                  echo $item->alias.' - '. $item->issue_summary;
               }
            }
            ?>
         </a>
      <?php } else { ?>
         <?php if ( $showissueno == 0 ) {
            if (strlen($item->issue_summary) > $length ) {
               echo substr($item->issue_summary, 0, $length) . $_more;
            } else {
               echo $item->issue_summary;
            }
         } else {
            if (strlen($item->issue_summary) > $length ) {
               echo $item->alias.' - '. substr($item->issue_summary, 0, $length) . $_more;
            } else {
               echo $item->alias.' - '. $item->issue_summary;
            }
         }
         ?>
      <?php } ?>

      <div class="IssueClosedDateText<?php echo $params->get('moduleclass_sfx'); ?>">
         <?php if ( $showproject == 1 ) { if (strlen($item->project_name) > $length) { echo "$projectname".substr($item->project_name, 0, $length).$_more.'<br />'; } else { echo "$projectname $item->project_name".'<br />'; } }  ?>
         <?php if ( $showstatus == 1 ) { if (strlen($item->status) > $length) { echo "$statustext".substr($item->status, 0, $length).$_more.'<br />'; } else { echo "$statustext $item->status".'<br />'; } }  ?>
         <?php if ( $showclosed == 1 ) echo "$closedate $item->actual_resolution_date"; ?>
         <br />
      </div>
    </div>
<?php endforeach; ?>
</div>