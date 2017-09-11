<?php
/**
 * @package		JooDatabase - http://joodb.feenders.de
 * @copyright	Copyright (C) Computer - Daten - Netze : Feenders. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * @author		Dirk Hoeschen (hoeschen@feenders.de)
 */

/**
 * Helper class for related fields and tables
 */

jimport('joomla.application.component.helper');

class JoodbFormHelper
{

    /**
     * Output of a form field regarding related tables and field type
     * @param $joobase
     * @param $item
     * @param $field
     * @return string
     */
    public static function getFormField(&$joobase,&$item,&$field) {
        $result= "";
        $name = &$field->Field;
        $version = new JVersion();
        $typearr = preg_split("/\(/",$field->Type);
        $fid = "jform_".preg_replace("/[^A-Z0-9]/i","",$name);
        if (!isset($item->{$name})) $item->{$name} = null;
        $typevals = array("");
        $required = ($field->Null=="NO"  || $name==$joobase->ftitle) ? " required" :"";
        if ($name==$joobase->ftitle) $required .= " text-large ";
        if (isset($typearr[1])) { $typevals =  preg_split("/','/",trim($typearr[1],"')"));	}
        // get default value
        if (empty($item->{$joobase->fid}) && ($field->Default!=NULL)) { $item->{$name} = $field->Default; }
        if ($field->Extra=='auto_increment') {
            $result .= '<input class="inputbox input-small" type="text" name="'.$name.'" id="'.$fid.'" value="'.htmlspecialchars($item->{$name}, ENT_COMPAT, 'UTF-8').'" ize="40" disabled />';
        } else
            switch ($typearr[0]) {
                case 'varchar' :
                case 'char' :
                case 'tinytext' :
                    $result .= '<input class="inputbox input-'.(($typevals[0]<30) ? 'large' : 'xxlarge')." ".$required.'" type="text" name="'.$name.'" id="'.$fid.'" value="'.htmlspecialchars($item->{$name}, ENT_COMPAT, 'UTF-8').'" maxlength="'.$typevals[0].'" size="60" />';
                    break;
                case 'int' :
                case 'smallint' :
                case 'mediumint' :
                case 'bigint' :
                case 'decimal' :
                case 'float' :
                case 'double' :
                case 'real' :
                    if ($name==$joobase->getSubdata('fuser') && intval($version->RELEASE)>=3) {
                        $ua = simplexml_load_string('<field name="'.$name.'" type="user" label="User" class="inputbox" filter="unset" '.$required.' />');
                        $uf = new JFormFieldUser();
                        $uf->setup($ua,(int)$item->{$name});
                        $result .= $uf->renderField(array("hiddenLabel"=>true));
                    } else {
                        $result .= '<input class="inputbox input-medium '.$required.'" type="text" name="'.$name.'" id="'.$fid.'" value="'.$item->{$name}.'" />';
                    }
                    break;
                case 'tinyint' :
                    if (!empty($joobase->fstate) && $joobase->fstate==$name) {
                        $result .=  '<select class="inputbox input-small" name="'.$name.'" id="'.$fid.'"><option value="0">'.JText::_('JNo').'</option><option value="1" ';
                        if (!empty($item->{$name})) $result .= 'selected="selected"';
                        $result .= '>'.JText::_('JYes').'</option></select>';
                    } else
                        $result .= '<input class="inputbox input-mini '.$required.'" type="text" name="'.$name.'" value="'.htmlspecialchars($item->{$name}, ENT_COMPAT, 'UTF-8').'" maxlength="4" size="4" />';
                    break;
                case 'datetime' :
                case 'timestamp' :
                    $item->{$name} = preg_replace("/[^0-9:\- ]/","",$item->{$name});
                    $result .= JHTML::_('calendar', $item->{$name} , $name, $fid, '%Y-%m-%d %H:%M:%S', array('class'=>'inputbox input-medium '.$required, 'size'=>'25',  'maxlength'=>'19'));
                    break;
                case 'date' :
                    $item->{$name} = preg_replace("/[^0-9\-]/","",$item->{$name});
                    $result .= JHTML::_('calendar', $item->{$name} , $name, $fid, '%Y-%m-%d', array('class'=>'inputbox input-small '.$required, 'size'=>'25',  'maxlength'=>'10'));
                    break;
                case 'year' :
                    $result .= '<input class="inputbox input-small '.$required.'" type="text" name="'.$name.'" id="'.$fid.'" value="'.((int) $item->{$name}).'" maxlength="4" size="4" />';
                    break;
                case 'time' :
                    $result .= '<input class="inputbox input-small '.$required.'" type="text" name="'.$name.'"  id="'.$fid.'" value="'.($item->{$name}).'" maxlength="8" size="4" />';
                    break;
                case 'text' :
                case 'mediumtext' :
                case 'longtext' :
                    $result .= '<div style="display:inline-block; width: auto;">';
                    // 	Load the JEditor object
                    $editor = JFactory::getEditor();
                    $result .= $editor->display($name, stripslashes($item->{$name}), '450', '250', '40', '6',false,"joodb_".$name);
                    $result .= '</div>';
                    break;
                // special handling for enum and set
                case 'enum' :
                    $result .= '<select class="inputbox input-medium '.$required.'" type="text" name="'.$name.'" id="'.$fid.'" />';
                    $result .= '<option value="" >...</option>';
                    foreach ($typevals as $value) {
                        $result .= '<option value="'.$value.'" '.(($value==$item->{$name}) ? 'selected' : '' ).'>'.$value.'</option>';
                    }
                    $result .= '</select>';
                    break;
                case 'set' :
                    $setarray = preg_split("/,/",$item->{$name});
                    if (count($typevals)<=4) {
                        foreach ($typevals as $n => $value) {
                            $result .= '<label class="inline checkbox" for="'.$fid.$n.'"><input type="checkbox" class="inline"  name="'.$name.'[]" id="'.$fid.$n.'" value="'.$value.'" '.((in_array($value,$setarray))? 'checked' : '' ).' />&nbsp;'.$value.'</label> ';
                        }
                    } else {
                        $result .= '<select class="inputbox input-xxlarge '.$required.'" type="text" style="width: 100%;" multiple="multiple" name="'.$name.'[]" id="'.$fid.'" >';
                        foreach ($typevals as $value) {
                            $result .= '<option value="'.$value.'" '.(in_array($value,$setarray)? 'selected' : '' ).'>'.$value.'</option>';
                        }
                        $result .= '</select>';
                    }
                    break;
                case 'tinyblob' :
                case 'mediumblob' :
                case 'blob' :
                case 'longblob' :
                    $result .=  '<input class="inputbox'.$required.'" type="file" name="'.$name.'" id="'.$fid.'" size="30" />';
                    $result .=  '&nbsp;<label class="inline">'.JText::_('EMPTY_FIELD').'&nbsp;<input class="checkbox" type="checkbox" name="'.$name.'_del" value="1" /></label>';
                    if (!empty($item->{$name})) {
                        $mime = JoodbAdminHelper::getMimeType($item->{$name});
                        $fileurl = JUri::root().'index.php?option=com_joodb&task=getFileFromBlob&joobase='.$joobase->id.'&id='.$item->{$joobase->fid}.'&field='.$name;
                        $result .= '<div style="clear:both; font-size: 12px; margin: 5px;" > '.strlen($item->{$name}).' Bytes ('.$mime.')';
                        if (substr($mime, 0,5)=="image") {
                            $result .= '<a href="'.$fileurl.'" class="modal" rel="{handler: \'image\'}">';
                            $result .= '<img style="max-width:80px; max-height: 60px; border: 1px solid #ccc; float:left; margin-right: 15px;" src="data:'.$mime.';base64,'.base64_encode($item->{$name}).'" alt="*" />';
                            $result .= '</a>';
                        } else {
                            $result .= ' &raquo;<a href="'.$fileurl.'" target="_blank">'.JText::_('DOWNLOAD').'</a>&laquo;';
                        }
                        $result .= '</div>';
                    }
                    break;
                default:
                    $result .= '<input class="inputbox input-xlarge '.$required.'" type="text" name="'.$name.'" id="'.$fid.'" value="'.htmlspecialchars($item->{$name}, ENT_COMPAT, 'UTF-8').'" maxlength="'.$typevals[0].'" size="60" style="width: 500px;" />';
            }
        return $result;
    }

}
