<?php
/**
* @package		JooDatabase - http://joodb.feenders.de
* @copyright	Copyright (C) Computer - Daten - Netze : Feenders. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @author		Dirk Hoeschen (hoeschen@feenders.de)
*
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');


/**
 * HTML View class for the JooDatabase cataloges
 */
class JoodbViewCatalog extends JViewLegacy
{
	var $joobase = null;
	var $items = null;
	var $params = null;

	function display($tpl = null)
	{	
		$app = JFactory::getApplication();
		$this->params = $app->getParams();
		// read database configuration from joobase table
		$this->joobase =  $this->get('joobase');
		//get the data items
		$this->items = $this->get('export');
	 	$fields = $this->getExportFields();

	 	require_once(JPATH_COMPONENT_ADMINISTRATOR.'/assets/PHPExcel.php');

	 	/** generate excel spreadsheet */	 	
		$objPHPExcel = new PHPExcel();		
		$objPHPExcel->getProperties()->setCreator("JooDatabase (joodb.feenders.de)")
							 ->setLastModifiedBy("JooDatabase (joodb.feenders.de)")
							 ->setTitle("Online Excel5 export of ".$this->joobase->name)
							 ->setSubject("Online Excel5 export of ".$this->joobase->name)
							 ->setDescription("Generated on ".addslashes($app->getCfg( 'sitename' ))." : Export of current selection")
							 ->setKeywords("export, database")
							 ->setCategory("Exportfile");
		$objPHPExcel->setActiveSheetIndex(0);		
		$row = 1;$cols = array(); $n=0;
		foreach ($fields as $field => $type) {
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($n,1, ucfirst($field));
			$cols[] = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow($n,1)->getColumn();
			if ($type=="text") {
				$objPHPExcel->getActiveSheet()->getColumnDimension(end($cols))->setWidth(100);
			} else if ($type=="varchar") {
				$objPHPExcel->getActiveSheet()->getColumnDimension(end($cols))->setWidth(30);
			}
			$objPHPExcel->getActiveSheet()->getStyle(end($cols)."1")->applyFromArray(array('font' => array( 'bold' => true),'borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN))));
			$n++;
		}
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.end($cols).'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
		$objPHPExcel->getActiveSheet()->getStyle('A1:'.end($cols).'1')->getFill()->getStartColor()->setARGB('FFFFEF83');
        if (!empty($this->items)) {
		    foreach ($this->items as $item) {
	 		    $n=0; $row++;
			    foreach ($fields as $field => $type) {
				    $objPHPExcel->getActiveSheet()->setCellValue($cols[$n].$row, strip_tags($item->{$field}));
				    $n++;
			    }
			    $rcol = (($row%2)==0) ? "FFFFFFFF" : "FFcfcfcf";
			    $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':'.end($cols).$row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
			    $objPHPExcel->getActiveSheet()->getStyle('A'.$row.':'.end($cols).$row)->getFill()->getStartColor()->setARGB($rcol);
		    }
        }
		
		
		$xlfile = JFilterOutput::stringURLSafe($this->joobase->name)."_".date('Y-m-d_H-m').".xls";
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$xlfile.'"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');		
	}

	/**
	 * Get fields for export
	 */
	function getExportFields()
	{
		$ef = $this->params->get('exportfields');
		if (!empty($ef)) {
			$fields = array();
			$fn = preg_split("/,/", $ef);
			foreach ($fn as $f) {
				$f = trim($f);
				if (isset($this->joobase->fields[$f]))
					$fields[$f] = $this->joobase->fields[$f];
			}
		} else 	$fields = $this->joobase->fields;
    	return $fields;
	}

}

