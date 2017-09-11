<?php
/**
 * Created by     Eric Fernance
 * User:          Eric Fernance
 * Website:       http://hotchillisoftware.com
 * License:       GPL v2
 */


if (!defined('JPATH_BASE')) die();


class PbbookingModelSmss extends JModelList {

  protected function getListQuery()
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select("*")
			->from("#__pbbooking_smss")
			->order("id ASC");

		return $query;
	}


	public function getTable($name = 'Sms', $prefix = 'PbbookingsTable', $options = array())
	{
		return parent::getTable($name, $prefix, $options);
	}
}
