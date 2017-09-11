<?php
/*
 *
 * @Version       $Id: edit_user_details.php 2050 2015-08-09 09:48:51Z geoffc $
 * @Package       Joomla Issue Tracker
 * @Subpackage    com_issuetracker
 * @Release       1.6.8
 * @Copyright     Copyright (C) 2011-2015 Macrotone Consulting Ltd. All rights reserved.
 * @License       GNU General Public License version 3 or later; see LICENSE.txt
 * @Contact       support@macrotoneconsulting.co.uk
 * @Lastrevision  $Date: 2015-08-09 10:48:51 +0100 (Sun, 09 Aug 2015) $
 *
 */
defined('_JEXEC') or die;
$parameters = $this->state->get('params');

$fieldSets = $this->form->getFieldsets('user_details');
foreach ($fieldSets as $name => $fieldSet) :
    ?>
   <fieldset>
        <legend>
            <?php echo JText::_('COM_ISSUETRACKER_IDENTIFIER_DETAILS'); ?>
        </legend>
        <?php foreach ($this->form->getFieldset($name) as $field) : ?>
            <div class="formelm">
                <?php echo $field->label; ?>
                <?php echo $field->input; ?>
            </div>
        <?php endforeach; ?>
   </fieldset>
<?php endforeach;
if ($parameters->get('captcha') == "recaptcha" ) {
   JPluginHelper::importPlugin('captcha');
   $dispatcher = JEventDispatcher::getInstance();
   // $dispatcher = JDispatcher::getInstance();
   $dispatcher->trigger('onInit','recaptcha');
}
?>
<div id="recaptcha"></div>
