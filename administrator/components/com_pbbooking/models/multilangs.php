<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modellist');

class PbbookingModelMultilangs extends JModelList
{

    protected function getListQuery()
    {
            // Create a new query object.           
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            // Select some fields from the hello table
            $query
                ->select('*')
                ->from('#__pbbooking_lang_override');
                 
            return $query;
    }


    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     *
     * @since   12.2
     */
    public function getItems()
    {
        $db = JFactory::getDbo();
        $items = $db->setQuery('select * from #__pbbooking_lang_override')->loadObjectList();
        $model = JModelLegacy::getInstance('Multilang','PbbookingModel');

        // Get a storage key.
        $store = $this->getStoreId();


        foreach ($items as $item)
        {
            switch ($item->type)
            {
                case 'customfield':
                    $item->primaryitem = $db->setQuery('select fieldname as originalvalue from #__pbbooking_customfields where id = '.(int)$item->original_id)->loadObject();
                    break;
                case 'calendar':
                    $item->primaryitem = $db->setQuery('select name as originalvalue from #__pbbooking_cals where id = '.(int)$item->original_id)->loadObject();
                    break;
                case 'service':
                    $item->primaryitem = $db->setQuery('select name as originalvalue from #__pbbooking_treatments where id = '.(int)$item->original_id)->loadObject();
                    break;
                case 'message':
                    $item->primaryitem = $db->setQuery("select emailbody as originalvalue from #__pbbooking_emails where email_tag = '".$db->escape($item->messagename)."'")->loadObject();
                    break;
                case 'subject':
                    $item->primaryitem = $db->setQuery("select emailsubject as originalvalue from #__pbbooking_emails where email_tag = '".$db->escape($item->messagename)."'")->loadObject();
                    break;

            }
        }

        return $items;
    }
}