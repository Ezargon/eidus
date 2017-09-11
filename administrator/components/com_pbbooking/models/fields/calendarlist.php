<?php

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.formfield');

class JFormFieldCalendarlist extends JFormFieldList
{

    protected $type = 'Calendarlist';

    protected function getOptions(){
        $db = JFactory::getDbo();
        $cals = $db->setQuery("select id as value, name as text from #__pbbooking_cals order by id asc")->loadAssocList();

        return $cals;
    }

}