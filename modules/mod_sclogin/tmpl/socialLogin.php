<?php
/**
 * @package         SCLogin
 * @copyright (c)   2009-2017 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v7.2.2
 * @build-date      2017/10/08
 */

defined('_JEXEC') or die('Restricted access');

if (!class_exists('JFBCFactory'))
    return;

$loginButtons = $helper->getLoginButtons($orientation, $alignment);

if ($loginButtons != '')
{
    $introText = JText::_('MOD_SCLOGIN_SOCIAL_INTRO_TEXT_LABEL');
    $postText = JText::_('MOD_SCLOGIN_SOCIAL_POST_TEXT_LABEL');

    echo '<div class="sclogin-social-login '.$socialSpan . ' ' . $layout . ' ' . $orientation.'">';
    if($introText)
        echo '<span class="sclogin-social-intro '.$socialSpan.'">'.$introText.'</span>';
    echo $loginButtons;
    if($postText)
        echo '<span class="sclogin-social-post-text '.$socialSpan.'">'.$postText.'</span>';
    echo '</div>';
}