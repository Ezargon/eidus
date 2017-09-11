<?php
/**
 * @package		JooDatabase - http://joodb.feenders.de
 * @copyright	Copyright (C) Computer - Daten - Netze : Feenders. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * @author		Dirk Hoeschen (hoeschen@feenders.de)
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Component Helper
jimport('joomla.application.component.helper');

/**
 * JooDB Component Helper
 */
class JoodbHelper
{

    /**
     * Parse template for wildcards and return text
     *
     * @access public
     * @param JooDB-Objext with fieldnames, Array with template parts, Object with Item-Data
     * @return The parsed output$parmas
     *
     */
    static function parseTemplate(&$joobase, &$parts, &$item,&$params = null) {
        $output = "";
        // generate link to the item
        $itemlink = JRoute::_('index.php?option=com_joodb&view=article&joobase='.$joobase->id.'&id='.$item->{$joobase->fid}.':'.JFilterOutput::stringURLSafe($item->{$joobase->ftitle}),false);
        $doOutput = true;
        $level = 0;
        $imgpart = "/images/joodb/db".$joobase->id."/img".$item->{$joobase->fid};
        $filter = new JFilterInput();

        // replace item content with wildcards
        foreach( $parts as &$part ) {
            if ($doOutput) {
                // replace field command with 1st parameter
                if (!empty($part->function)) {
                    switch ($part->function) {
                        case "field" :
                            $part->function = $part->parameter[0];
                            array_shift($part->parameter);
                            if (isset($item->{$part->function}))
                                $output .= self::replaceField($joobase, $part, $item->{$part->function}, $itemlink, $item->{$joobase->fid});
                            break;
                        case "readon" :
                        case "readmore" :
                            $output .= self::getReadmore($itemlink);
                            break;
                        case "path2item" :
                            $output .= $itemlink;
                            break;
                        case "path2editform" :
                            $output .= JRoute::_('index.php?option=com_joodb&view=edit&joobase=' . $joobase->id . '&id=' . $item->{$joobase->fid}, false);
                            break;
                        case "nextbutton" :
                            $output .= self::getNavigationButton("next", $joobase);
                            break;
                        case "prevbutton" :
                            $output .= self::getNavigationButton("prev", $joobase);
                            break;
                        case "loopclass" :
                            $output .= ($params->get("counter")%2==0) ? "odd" : "even";
                            break;
                        case "loopcounter" :
                            $output .= $params->get("counter")+1;
                            break;
                        case "notepadbutton" :
                            $output .= self::getNotepadButton($item, $joobase);
                            break;
                        case "printbutton" :
                            $output .= self::getPrintPopup($item, $joobase);
                            break;
                        case "editbutton" :
                            $output .= self::getEditButton($item, $joobase, $part);
                            break;
                        case "deletebutton" :
                            $output .= self::getDeleteButton($item, $joobase);
                            break;
                        case "backbutton" :
                            $output .= self::getBackbutton();
                            break;
                        case "translate" :
                            $output .= JText::_(addslashes($part->parameter[0]));
                            break;
                        case "image" :
                            $image = JURI::root(true) . (file_exists(JPATH_ROOT . $imgpart . ".jpg") ? $imgpart . ".jpg" : "/components/com_joodb/assets/images/nopic.png");
                            $output .= '<img src="' . $image . '" alt="image" class="database-image';
                            if (!file_exists(JPATH_ROOT . $imgpart . ".jpg")) $output .= " nopic";
                            $output .= '" />';
                            break;
                        case "thumb" :
                            $thumb = JURI::root(true) . (file_exists(JPATH_ROOT . $imgpart . "-thumb.jpg") ? $imgpart . "-thumb.jpg" : "/components/com_joodb/assets/images/nopic.png");
                            $image = JURI::root(true) . (file_exists(JPATH_ROOT . $imgpart . ".jpg") ? $imgpart . ".jpg" : "/components/com_joodb/assets/images/nopic.png");
                            $output .= '<a href="' . $image . '" class="modal"><img src="' . $thumb . '" alt="thumb" class="database-thumb';
                            if (!file_exists(JPATH_ROOT . $imgpart . ".jpg")) $output .= " nopic";
                            $output .= '" /></a>';
                            break;
                        case "path2image" :
                            $output .= JURI::root(true) . (file_exists(JPATH_ROOT . $imgpart . ".jpg") ? $imgpart . ".jpg" : "/components/com_joodb/assets/images/nopic.png");
                            break;
                        case "path2thumb" :
                            $output .= JURI::root(true) . (file_exists(JPATH_ROOT . $imgpart . "-thumb.jpg") ? $imgpart . "-thumb.jpg" : "/components/com_joodb/assets/images/nopic.png");
                            break;
                        case "checkbox" :
                            $ids = JRequest::getVar('cid', array(), '', 'array');
                            $checked = (in_array($item->{$joobase->fid}, $ids)) ? 'checked="checked"' : '';
                            $output .= '<input class="inputbox check" type="checkbox" id="cb' . $item->{$joobase->fid} . '" name="cid[]" value="' . $item->{$joobase->fid} . '" ' . $checked . ' />';
                            break;
                        default:
                            if (isset($joobase->fields[$part->function])) {
                                /** @deprecated replace exisiting fields */
                                $output .= self::replaceField($joobase, $part, $item->{$part->function}, $itemlink, $item->{$joobase->fid});
                            }
                    }
                }
            }
            self::GetOutputState($item, $part,$doOutput,$level);
            if ($doOutput) $output .= $part->text;
        }
        return $output;
    }

    /**
     * Replaces a joodb fieldname with field contennt
     *
     * @access public
     * @param JooDB-Object with fieldnames, Part-object from the template, Text with field content
     * @return The parsed output
     *
     */
    static function replaceField(&$joobase, &$part, $field, $itemlink,$id) {
        $app = JFactory::getApplication();
        $params	= $app->getParams();
        $fieldname = $part->function;
        if (($fieldname==$joobase->ftitle) && ($params->get('link_titles','0')=='1')) {
            $field= "<a href='".$itemlink."' title='".Jtext::_('Read more...')."' class='joodb_titletink'>".$field."</a>";
        }
        $ftparse = preg_split("/\(/",$joobase->fields[$part->function]);
        $function =  strtolower($ftparse[0]);
        $vars = (!empty($ftparse[1])) ? preg_split("/,/",str_replace(array(")","'"),"",$ftparse[1])) : null;
        // convert some of the fieldtypes
        if (!empty($field)) {
            switch($function) {
                case "date":
                    $field= JHtml::_('date', $field, JText::_('DATE_FORMAT_LC3'));
                    break;
                case "datetime":
                    $field= JHtml::_('date', $field, JText::_('DATE_FORMAT_LC2'));
                    break;
                case "timestamp":
                    $field= JHtml::_('date', $field, JText::_('DATE_FORMAT_LC2'));
                    break;
                case "time":
                    $field= substr($field,0,5);
                    break;
                case "float" :
                    if (isset($part->parameter[0])) {
                        $field= number_format($field, (int)$part->parameter[0], JText::_('DECIMALS_SEPARATOR'), JText::_('THOUSANDS_SEPARATOR'));
                    }
                    break;
                case "decimal" :
                    $field= number_format($field, $vars[1], JText::_('DECIMALS_SEPARATOR'), JText::_('THOUSANDS_SEPARATOR'));
                    break;
                case "varchar":
                case "tinytext":
                case "text":
                case "mediumtext":
                case "longtext":
                    if (($function=="varchar" || $function=="tinytext") && $params->get('link_urls','0')=='1') {
                        // try to detect and link urls ans emails
                        if (preg_match('/^[^@]+@[a-zA-Z0-9._-]+\.[a-zA-Z]+$/', $field)) {
                            $field= JHtml::_('email.cloak', $field);
                        } else if (strtolower(substr($field,0,4))=="www.") {
                            $field= '<a href="http://'.$field.'" target"_blank">'.$field.'</a>';
                        } else if (preg_match('#^http(s)?://#',$field)) {
                            $field= '<a href="'.$field.'" target"_blank">'.preg_replace( "#^[^:/.]*[:/]+#i", "", $field).'</a>';
                        }
                    }
                    // shorten a text for abscracts
                    if ((!empty($part->parameter[0])) && ($part->parameter[0]>1)) {
                        $field = self::wrapText($field,$part->parameter[0]);
                    }
                    break;
                case "tinyblob" :
                case "mediumblob" :
                case "blob" :
                case "longblob" :
                    // @todo extend the field mode settings for downloads and so on to define filenames and scaling
                    $field = (!empty($field)) ? JURI::root().'index.php?option=com_joodb&task=getFileFromBlob&joobase='.$joobase->id.'&id='.$id.'&field='.$fieldname : "";
                    break;
            }
        }
        return $field;
    }


    /**
     * Split a template into parts return a array of of objects
     *
     * @access public
     * @param String with template text
     */
    static function splitTemplate($template) {
        $psplit = preg_split('/\{joodb /U', $template);
        $parts=array();
        // insert text only for the first part
        if (substr($template,0,6)!="{joodb") {
            $e = new joodbPart();
            $e->text = array_shift($psplit);
            $parts[] =$e;
        }
        foreach ($psplit as $part) {
            $part = $part;
            $e = new joodbPart();
            $p=strpos($part,"}");
            if ($p===false) {
                $e->text=$part;
            } else {
                $e->text=substr($part,$p+1);
                $e->parameter = preg_split("/\|/",trim(substr($part,0,$p)));
                $e->function = array_shift($e->parameter);
            }
            $parts[] =$e;
        }
        return $parts;
    }

    /**
     * Calculates the outputsituation from condition arguments
     *
     * @access public
     * @param misc $item
     * @param misc $part
     * @param bool $state
     */
    static function getOutputState(&$item, &$part,&$state,&$level) {
        if ($part->function=="ifis") { // check if field condition is true
            $level++;
            if (isset($part->parameter[1]) && $state) {
                $cond = (isset($part->parameter[2])) ? strtolower($part->parameter[2]) : "eq";
                switch ($cond) {
                    case "lt":
                        $state = ($item->{$part->parameter[0]}<=$part->parameter[1]) ? true : false;
                        break;
                    case "le":
                        $state = ($item->{$part->parameter[0]}<$part->parameter[1]) ? true : false;
                        break;
                    case "gt":
                        $state = ($item->{$part->parameter[0]}>$part->parameter[1]) ? true : false;
                        break;
                    case "ge":
                        $state = ($item->{$part->parameter[0]}>=$part->parameter[1]) ? true : false;
                        break;
                    case "ne":
                        $state = ($item->{$part->parameter[0]}!=$part->parameter[1]) ? true : false;
                        break;
                    default:
                        $state = ($item->{$part->parameter[0]}==$part->parameter[1]) ? true : false;
                        break;
                }
            } else {
                if ($state) $state = (!empty($item->{$part->parameter[0]})) ? true : false;
            }
        } else if ($part->function=="ifnot") { // check if field condition is false
            $level++;
            if (isset($part->parameter[1])) {
                $state = ($item->{$part->parameter[0]}!=$part->parameter[1]) ? true : false;
            } else {
                $state = (empty($item->{$part->parameter[0]})) ? true : false;
            }
        } else if ($part->function=="else") {	if($level==1) $state = !$state;
        } else if ($part->function=="endif") {	if($level==1) $state = true; $level--; }
    }


    /**
     * Returns popup link for printview as Icon or Text
     *
     * @access public
     * @param Item, Params
     */
    static function getPrintPopup(&$item, &$joobase, $attribs = array())
    {
        $params	= JComponentHelper::getParams('com_joodb');
        $url  = 'index.php?option=com_joodb&view=article&joobase='.$joobase->id.'&id='.$item->{$joobase->fid}.'&layout=print&tmpl=component&print=1';
        $status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=720,height=560,directories=no,location=no';

        // checks template image directory for image, if non found default are loaded
        if ( $params->get( 'show_icons', '1' ) ) {
            $text = JHtml::image('components/com_joodb/assets/images/print.png', JText::_( 'Print' ) );
        } else {
            $text = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_( 'Print' ) .'&nbsp;'. JText::_( 'ICON_SEP' );
        }

        $attribs['title']	= JText::_( 'Print' );
        $attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
        $attribs['rel']     = 'nofollow';

        return JHtml::link(JRoute::_($url), $text, $attribs);
    }

    /**
     * Returns a add to notepad or remove from notepad link
     *
     * @access public
     * @param object Item
     * @param object joobase
     * @return string
     */
    static function getNotepadButton(&$item, &$joobase)
    {
        if (JRequest::getCmd('tmpl')!='component') {

            $params	= JComponentHelper::getParams('com_joodb');

            $task = (JRequest::getCmd('layout')=="notepad") ? 'removeFromNotepad'  : 'addToNotepad';
            $url  = 'index.php?option=com_joodb&view=catalog&layout=notepad&joobase='.$joobase->id.'&task='.$task.'&article='.$item->{$joobase->fid};
            $urltext = (JRequest::getCmd('layout')=="notepad") ?  JText::_('Remove from Notepad') : JText::_('Add to Notepad');
            // checks template image directory for image, if non found default are loaded
            if ( $params->get( 'show_icons', '1' ) ) {
                $icon = (JRequest::getCmd('layout')=="notepad") ? "remove.png" : "add.png";
                $text = JHtml::image('components/com_joodb/assets/images/'.$icon,$urltext );
            } else {
                $text = JText::_( 'ICON_SEP' ) .'&nbsp;'. $urltext .'&nbsp;'. JText::_( 'ICON_SEP' );
            }
            $attribs= array('title'=> $urltext);
            return JHtml::link(JRoute::_($url), $text,$attribs);
        }
    }


    /**
     * Returns an icon or text link to edit item in frontend
     *
     * @access public
     * @param object Item
     * @param object joobase
     * @return string
     */
    static function getEditButton(&$item, &$joobase,&$part)
    {
        if (JRequest::getCmd('tmpl')!='component') {
            $params	= JComponentHelper::getParams('com_joodb');

            $view = ((isset($part->parameter[0])) && (self::parameterToBoolean($part->parameter[0]))) ? "form" : "edit";
            $url  = JRoute::_("index.php?option=com_joodb&view=".$view."&joobase=".$joobase->id."&id=".$item->{$joobase->fid});

            if (!self::checkAuthorization($joobase,"accesse",$item)) return;

            // checks template image directory for image, if non found default are loaded
            if ( $params->get( 'show_icons', '1' ) ) {
                $icon = "edit.png";
                $text = JHtml::image('components/com_joodb/assets/images/'.$icon,JText::_("EDIT_DATABASE_ENTRY"));
            } else {
                $text = JText::_( 'ICON_SEP' ) .'&nbsp;'. JText::_("EDIT_DATABASE_ENTRY") .'&nbsp;'. JText::_( 'ICON_SEP' );
            }
            $attribs= array('title'=> JText::_("EDIT_DATABASE_ENTRY"));
            return JHtml::link(JRoute::_($url), $text,$attribs);
        }
    }


    /**
     * Returns an icon to delete items in frontend
     *
     * @access public
     * @param object Item
     * @param object joobase
     * @return string
     */
    static function getDeleteButton(&$item, &$joobase) {
        if (!self::checkAuthorization($joobase,"accesse",$item)) return;

        $url  = JRoute::_("index.php?option=com_joodb&view=edit&joobase=".$joobase->id."&id=".$item->{$joobase->fid}."&task=delete&Itemid=".JRequest::getInt('Itemid'));
        $text = JHtml::image('components/com_joodb/assets/images/remove.png',JText::_("DELETE DATABASE ENTRY"));
        $attribs= array('title'=> JText::_("DELETE DATABASE ENTRY"),'onclick'=>'return confirm(\''.JText::_("REALLY DELETE").'\');');
        return JHtml::link(JRoute::_($url), $text,$attribs);
    }


    /**
     * Returns a back to prev page link
     *
     * @access public
     */
    static function getBackbutton() {
        $params	= JComponentHelper::getParams('com_joodb');
        if ($params->get('show_icons','1')==1) {
            $text = JHtml::image('components/com_joodb/assets/images/back.png', JText::_('BACK') );
        } else {
            $text = JText::_('BACK');
        }
        return JHtml::link('javascript:history.back();', $text,array('title'=>  JText::_('BACK'),'class'=>'backbutton'));
    }

    /**
     * Returns a back to prev page link
     *
     * @access public
     * @param string link
     */
    static function getReadmore($url) {
        $params	= JComponentHelper::getParams('com_joodb');
        if ($params->get('show_icons','1')==1) {
            $text = JHtml::image('components/com_joodb/assets/images/next.png',JText::_('READ MORE...'));
        } else {
            $text = JText::_('READ MORE...');
        }
        return  JHtml::link($url , $text,array('title'=>  JText::_('READ MORE...'),'class'=>'readonbutton'));
    }


    /**
     * Returns a next or previous Item link
     *
     * @access public
     * @param string next or prev
     * @return string
     */
    static function getNavigationButton($side="next",&$joobase) {
        require_once JPATH_BASE.'/components/com_joodb/models/catalog.php';
        $model = new JoodbModelCatalog();
        if (!$item=$model->getSideElementUrl($side)) return;
        $url = JRoute::_('index.php?option=com_joodb&view=article&joobase='.$joobase->id.'&id='.$item->{$joobase->fid}.':'.JFilterOutput::stringURLSafe($item->{$joobase->ftitle})."&position=".$item->jb_pos."&total=".$item->jb_total,false);
        if ($side=="next") {
            $btext = JText::_('Next entry');
            $image = "next.png";
        } else {
            $btext = JText::_('Previous Entry');
            $image = "back.png";
        }
        $params	= JComponentHelper::getParams('com_joodb');
        if ($params->get('show_icons','1')==1) {
            $text = JHtml::image('components/com_joodb/assets/images/'.$image, $btext );
        } else {
            $text = $btext;
        }
        return JHtml::link($url,$text,array('title'=>  $btext,'class'=>$side.'button'));
    }


    /**
     * Returns Search box for catalog view
     *
     * @access public
     * @param  current Searchstring, Joobase
     */
    static function getSearchbox($search="",$parameter)
    {
        $stext = JText::_('Search...');
        $sval = ($search!="") ? $search : $stext;
        $searchform =  '<div class="searchbox"><input class="inputbox searchword" type="text"'
            .' onfocus="if(this.value==\''.$stext.'\') this.value=\'\';" onblur="if(this.value==\'\') this.value=\''.$stext.'\';" '
            .' value="'.htmlspecialchars(stripcslashes($sval), ENT_QUOTES, "UTF-8").'" size="20" alt="'.$stext.'" maxlength="40" name="search" />';
        if (!empty($parameter[0])) {
            $fields = @preg_split("/,/",$parameter[0]);
            $searchform .= "&nbsp;<select class='inputbox' name='searchfield'><option value=''>".JText::_('All fields')."</option>" ;
            $rf = JRequest::getVar("searchfield");
            foreach ($fields as $field) {
                $field=trim($field);
                $searchform .= "<option value='".$field."' " ;
                if ($rf==$field) $searchform .= "selected";
                $searchform .= ">".ucfirst(str_replace(array("-","_")," ",$field))."</option>" ;
            }
            $searchform .= "</select>" ;
        }
        $searchform .=  "&nbsp;<input class='button btn search' type='submit' value='".$stext."' />"
            ."&nbsp;<input class='button btn reset' type='submit' value='".JText::_('Reset...')."' onmousedown='submitSearch(\"reset\");void(0);' /></div>";
        return $searchform;
    }

    /**
     * Returns a select-box of possible row to search for
     * @access public
     * @param  current Joobase, parameters, values
     */
    static function getGroupselect(&$joobase,$parameter,$values)
    {
        $app = JFactory::getApplication();
        $gs =  $app->getUserStateFromRequest("com_joodb".$joobase->id.'.gs', 'gs',array(), 'array');
        $sv = (isset($gs[$parameter[0]])) ? $gs[$parameter[0]] : array();
        $size = (isset($parameter[1]) && $parameter[1]>1) ? 'size="'.$parameter[1].'" multiple="multiple" ' : "";
        $searchform = '<select class="inputbox groupselect" id="gs_'.$parameter[0].'" name="gs['.$parameter[0].'][]" '.$size.' >' ;
        $searchform .= '<option value="">...</option>';
        if ($values)
            foreach ($values as $value) {
                $selected = (array_search($value->delimeter.$value->value.$value->delimeter, $sv)!==false) ? 'selected="selected"' : '';
                if (!empty($value->value))
                    $searchform .= '<option value="'.$value->delimeter.$value->value.$value->delimeter.'" '.$selected.'>'.$value->value.' ('.$value->count.')</option>';
            }
        $searchform .= "</select>";
        return $searchform;
    }


    /**
     * Returns a roman alphabet to select the first letters ot the title
     *
     * @access public
     * @param current Alphachar
     */
    static function getAlphabox($alphachar, &$joobase)
    {
        $alphabox = "<div class='pagination alphabox'><ul>";
        $alphabet= array ('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
        foreach ($alphabet as $achar) {
            if ($achar==$alphachar) {
                $alphabox .= "<li class='active'><span>".ucfirst($achar)."</span></li>";
            } else {
                $alphabox .= "<li><a href='".self::_findItem($joobase,"&letter=".$achar)."'>".ucfirst($achar)."</a></li>";
            }
        }
        $alphabox .=  "<li><a href='".self::_findItem($joobase)."'>&raquo;".JText::_('All')."</a></li></ul></div>";
        return $alphabox;
    }

    /**
     * Get complete link or only url to sort
     *
     * @access public
     * @params fieldname fpr sort, [linktext]
     */
    static function getOrderlink(&$parameter,&$joobase)
    {
        $app = JFactory::getApplication();
        $params	= $app->getParams();
        $ordering = "ASC"; $orderclass = "";
        if ($app->getUserStateFromRequest('com_joodb.orderby', 'orderby', $params->get('orderby','fid'), 'string')==$parameter[0]) {
            $ordering = (strtolower(JRequest::getCMD('ordering')) == "asc") ? "DESC" : "ASC";
            $orderclass = strtolower(JRequest::getCMD('ordering'));
        }
        $url = self::_findItem($joobase,'&orderby='.$parameter[0].'&ordering='.$ordering);
        if (count($parameter)>1) {
            $url = '<a href="'.$url.'" class="order '.$orderclass.'">'.$parameter[1]."</a>";
        }
        return $url;
    }


    /**
     * Returns a captcha box
     *
     * @access public
     *
     */
    static function getCaptcha(){
        $captcha ="<div class='joocaptcha' style='margin: 5px 0;' >"
            ."<img src='".Juri::root(false)."index.php?option=com_joodb&task=captcha&".microtime(true)."' alt='captcha' style='width:200px; height:50px; margin: 5px 0; border: 1px solid black;'  />"
            ."<br><input class='inputbox required' name='joocaptcha' id='joocaptcha' style='width:190px;' size='1' maxlength='5' /></div>";
        return $captcha;
    }

    /**
     * Output of a captcha image
     *
     * @access public
     *
     */
    static function printCaptcha(){

        header("Content-Type: image/png");

        // Generate code for Captcha
        $code = "";
        $codelength = 5;
        $pool = "qwertzupasdfghkyxcvbnm23456789";
        srand ((double)microtime()*1000000);
        for($n = 0; $n < $codelength; $n++) {
            $code .= substr($pool,(rand()%(strlen ($pool))), 1);
        }

        $includepath=JPATH_ROOT."/components/com_joodb/assets/images/";
        $fontsize=20;
        // Get the size
        $bbox = imagettfbbox($fontsize, 0, $includepath."captcha.ttf", $code);

        // calculate size of the image
        $x= $bbox[2]+(2*$bbox[3]);
        $y= (-$bbox[7])+(2*$bbox[3]);
        $background = imagecreatefromjpeg($includepath."captcha.jpg");

        //prepare the image
        $im  =  ImageCreateTrueColor ( 200,  50 );
        $fill = ImageColorAllocate ( $im ,  0,  0, 0 );
        $color = ImageColorAllocate ( $im , 235  , 235, 235 );

        imagecopy($im,$background,0,0,rand(0,600),rand(0,500),200,50);
        $startx = rand(5,110); $starty = rand(25,40);

        // rotate and shift each char randomly
        for($i=0; $i<$codelength; $i++) {
            $ch = $code{$i};
            ImageTTFText ($im, $fontsize, rand(-10,10) , $startx + (15*$i) , $starty , $color, $includepath."captcha.ttf", $ch);
        }

        ImagePNG ( $im );
        ImageDestroy ($im);

        // store the code to the session
        $session = JFactory::getSession();
        $session->set('joocaptcha',$code);

    }

    /**
     * Output text... trigger content event before ...
     * @param object $text
     * @param array $params
     * @param sting view name of the view
     */
    static function printOutput(& $page, & $params,$view="article") {
        $dispatcher = JDispatcher::getInstance();
        JPluginHelper::importPlugin('content');
        $dispatcher->trigger('onContentPrepare', array ('com_joodb.'.$view, &$page, &$params,0));
        $dispatcher->trigger('onContentBeforeDisplay', array ('com_joodb.'.$view, &$page, &$params,0));
        echo $page->text;
        $dispatcher->trigger('onContentAfterDisplay', array ('com_joodb.'.$view, &$page, &$params,0));
    }

    /**
     * Add new DS to joodb table
     * @param misc $jb joddb object
     * @param misc $item jobject
     */
    static function saveData(&$jb,&$item) {
        $app = JFactory::getApplication();
        $table = $jb->table;
        $fid = $jb->fid;
        $db	= $jb->getTableDBO();
        // load the jooDb object with table fiel infos
        $fields = $db->getTableColumns($table,false);
        foreach ($fields as $fname=>$fcell) {
            $fne = str_replace(" ","_",$fname);
            if (isset($_POST[$fne]) || isset($_FILES[$fne])) {
                $typearr = preg_split("/\(/",$fcell->Type);
                switch ($typearr[0]) {
                    case 'text' :
                    case 'tinytext' :
                    case 'mediumtext' :
                    case 'longtext' :
                        $item->{$fname} = JRequest::getVar($fne, '', 'post', 'string', JREQUEST_ALLOWHTML);
                        $item->{$fname} = nl2br($item->{$fname});
                        if (empty($item->{$fname}) && $fcell->Null=="YES") $item->{$fname}= NULL;
                        break;
                    case 'int' :
                    case 'tinyint' :
                    case 'smallint' :
                    case 'mediumint' :
                    case 'bigint' :
                    case 'year' :
                        $item->{$fname}= JRequest::getInt($fne);
                        break;
                    case 'date' :
                    case 'datetime' :
                    case 'timestamp' :
                        $item->{$fname}= preg_replace("/[^0-9\: \-]/","",JRequest::getVar($fne, '', 'post', 'string'));
                        if (empty($item->{$fname}) && $fcell->Null=="YES") $item->{$fname}= NULL;
                        break;
                    case 'float' :
                    case 'decimal' :
                        $item->{$fname}= str_replace(",",".",JRequest::getVar($fne));
                    case 'set' :
                        $values = JRequest::getVar($fne, array(), 'post', 'array');
                        $item->{$fname}= join(",",$values);
                        break;
                    case "tinyblob" :
                    case "mediumblob" :
                    case "blob" :
                    case "longblob" :
                        $newf = JRequest::getVar($fne, null, 'files', 'array');
                        if(!empty($newf) && $newf['size'] > 0) {
                            $fp = fopen($newf['tmp_name'], 'r');
                            $item->{$fname} = fread($fp, filesize($newf['tmp_name']));
                        }
                        break;
                    default:
                        $item->{$fname}= JRequest::getVar($fne, '', 'post','string');
                        if (empty($item->{$fname}) && $fcell->Null=="YES") $item->{$fname}= NULL;
                }
            } else {
                if ($fcell->Null=="YES") $item->{$fname}= NULL;
            }
        }

        // Update or insert object if ID exists
        if (!empty($item->{$fid})) {
            $db->updateObject($table,$item,$fid,true);
        } else {
            if ($fuser=$jb->getSubdata('fuser')) $item->{$fuser} = JFactory::getUser()->id;
            $db->insertObject($table,$item,$fid);
        }
        $error =  $db->getErrorMsg();
        if(!empty($error)){
            $msg = JText::_( 'Error' )." : ".$db->getErrorMsg();
        } else {
            $msg = JText::_( 'Item Saved' );
        }
        return $msg;
    }

    /**
     * Try to find menuitem for the database
     *
     * @access private
     * @param id of the referring database
     */
    static function _findItem(&$joobase,$params="")
    {
        return JRoute::_("index.php?option=com_joodb&view=catalog&joobase=".$joobase->id.$params,false);

    }

    /**
     * Check the Authorization ...
     * @todo Joomla ACL compatibilty
     * @param jobase misc
     * @param string $section
     * @param item misc
     * @return boolean
     */
    static function checkAuthorization(&$joobase, $section="accessd",&$item=null) {
        $jparams = new JRegistry($joobase->params);
        $user = JFactory::getUser();
        $fuser= $joobase->getSubdata('fuser');
        $levels	= $user->getAuthorisedViewLevels();
        $levels = array_flip($levels);

        $has_access = false;
        if ($item && $fuser) {
            if ($item->{$fuser}==$user->id) $has_access = true;
        }

        // editfunctions with special needs
        if ($section=="accesse") {
            if ($jparams->get("accesse","0")==1) {
                if (!array_key_exists($jparams->get("accessf","2"),$levels)) {
                    $has_access = false;
                } else {
                    if (!$fuser) $has_access = true;
                }
                if ($user->authorise('core.admin')) $has_access = true;
            }
        } else {
            $level_need = $jparams->get($section, 1);
            if (empty($level_need)) $level_need = 1;
            if (array_key_exists($level_need, $levels)) $has_access = true;
        }

        if (!$has_access) {
            if ($section=="accesse") return false;
            // redirect
            $uri = JUri::getInstance();
            $return	= $uri->toString();
            $url  = JRoute::_('index.php?option=com_users&return=');
            $url .= base64_encode($return);
            $app = JFactory::getApplication();
            $app->redirect($url, JText::_('Please login') );
            $app->close();
        }
        return true;
    }

    /**
     * Shorten a text
     *
     * @access public
     * @param String with text
     * @param Integer with maximum length
     * @return Truncated text
     *
     */
    static function wrapText($text,$maxlen=120) {
        $text = strip_tags($text);
        if (strlen($text)>$maxlen) {
            $len = strpos($text," ",$maxlen);
            if ($len) $text = substr($text,0,$len).' &hellip;';
        }
        return $text;
    }

    /**
     * Returns the condition of a boolean string parameter
     * @param string $value
     * @return boolean
     */
    static function parameterToBoolean($value) {
        if (empty($value)) return false;
        $def = array('true','on','1','yes');
        $value = trim(strtolower((string)$value));
        return (array_search($value,$def)===false) ? false : true;
    }

}

// a pure object class to keep parts
class joodbPart {
    //the joodb function
    var $function = false;
    //an array of parameters
    var $parameter = array();
    // the text to the next comand
    var $text = "";
}
