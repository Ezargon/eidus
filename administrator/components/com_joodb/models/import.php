<?php
/**
* @file import.php created 28.07.2011, 12:23:47
* @package		Joomla
* @author	feenders - dirk hoeschen (hoeschen@feenders.de)
* @abstract	custom component for client
* @link	http://www.feenders.de
* @copyright	Copyright (C) 2011 computer daten netze :: feenders
* @license		CC-GNU-LGPL
* @version  1.0
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class joodbModelImport extends JModelLegacy {
	
	var $columns = array();
	var $has_column_names = 1;
	var $enclosure = "";
	var $delimeter = "";
	var $tablename = "";
	var $file = "";
	var $format = false;
	var $querycache = "";
	var $message = "";
	var $error = false;
	var $finished = false;
	var $highestRow = 0;
	var $chunksize = 500;
	var $startRow = 0;
	
	
	/**
	 * Contructor
	 * @param array $config
	 */
	function __construct($config) {
		$this->has_column_names = JRequest::getInt("has_column_names",1);
		$this->tablename = JRequest::getCmd("tablename","new_table");
		$this->enclosure = JRequest::getVar("enclosure","\"");
		$this->delimeter = JRequest::getVar("delimeter",";");
		parent::__construct($config);
	}

	/**
	 * Update dataset
	 * @param $id int id des datensatzes
	 * @param $item object
	 * @param $table string
	 */
	private function saveEntry($id, $item){
		$db = $this->getDbo();
		$savestring = "";
		foreach ($item as $field => $value) {
			$savestring .= " `".$field."` = ".(($value!="") ? "'".$db->escape($value)."'" : "NULL").",";
		}
		$query = "UPDATE `".$this->tablename."` SET " . substr($savestring,0,-1) . " WHERE id = $id";
		$db->setQuery($query);
		if(!$db->query()) {
			$this->setError($db->getErrorMsg());
			return false;
		} else { return true; }
	}

	/**
	 * Add new data
	 * @param object $item
	 */
	private function addEntry($item,$ferstellt=null){
        $db = JFactory::getDbo();
		$insert = ""; $values = "";
		$empty = true;
		foreach ($item as $field => $value) {
			$insert .= "`".$field."`,";
			$values .= ($value=="") ? "NULL," : "'".$db->escape($value)."',";
			if ($value!="") $empty = false;
		}
		if ($empty) return false;
		if($ferstellt) {
			$insert .= "`".$ferstellt."`,";
			$values .= "NOW(),";
		}
		if (empty($this->querycache)) {
			$this->querycache = "INSERT INTO `".$this->tablename."` (".substr($insert,0,-1).") VALUES (".substr($values,0,-1).") ";
		} else {
			$this->querycache .= ", (".substr($values,0,-1).") ";
		}	
		return true;		
	}


	/**
	 * Set column titles and types by analyzing first row ...
	 * @param misc $sw
	 * @return boolean true or false
	 */
	private function generateTable(&$ws) {		
		if (!$row=$ws->getRowIterator()->current()) return false;
  		$app = JFactory::getApplication();		
		$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(true);
		$genid = true;
		$genpublished = true;
		
		foreach ($cellIterator as $i => $cell)
			if (!is_null($cell)) {			
				$this->columns[$i] = new JObject();
				// get column names
				if ($this->has_column_names==1) {
				   $this->columns[$i]->title = str_replace(" ","_",preg_replace("/[^a-zA-Z0-9_\- ]/", "", $cell->getCalculatedValue()));
				   if (strtolower($this->columns[$i]->title)=="id") $genid=false;
				   if (strtolower($this->columns[$i]->title)=="published") $genpublished=false;
				} else {
				   $this->columns[$i]->title = "column_".sprintf( '%03d', $i);
				}
				// get cell row 2 and type / value
				$dcell = $ws->getCell($cell->getColumn()."2");
				$this->columns[$i]->type = $dcell->getDataType();
				$this->columns[$i]->format = $ws->getStyle($dcell->getCoordinate())->getNumberFormat()->getFormatCode();
				$f = strtolower($this->columns[$i]->format);
				if ($this->columns[$i]->type=="s" && PHPExcel_Shared_Date::isDateTime($dcell)) {
					if (strpos($f,"yy")!==false) {
						$this->columns[$i]->format = (strpos($f,"h")!==false) ? "yyyy-mm-dd hh:mm:ss" : "yyyy-mm-dd";
						$this->columns[$i]->type = (strpos($f,"h")!==false) ? "datetime DEFAULT NULL" : "date DEFAULT NULL";
					} else if (strpos($f,"hh")!==false) {
						$this->columns[$i]->format = "hh:mm:ss";
						$this->columns[$i]->type = "time DEFAULT NULL";
					} else $this->columns[$i]->type =  "text";
				} else if ($this->columns[$i]->type=="n") {
					if ($f=="general") {
						$value = preg_replace("/[^0-9.]/","",trim($dcell->getValue()));
						if (is_numeric($value)) {
							$this->columns[$i]->type = (strpos($value,".")!==false) ? "float" : "int(12)";
						} else 	$this->columns[$i]->type = "text";
					} else $this->columns[$i]->type = "varchar(254) DEFAULT NULL";
				} else	$this->columns[$i]->type = "text";
				if ($this->columns[$i]->format=="@") $this->columns[$i]->type =  "text";
			}
		$db = $this->getDbo();
        // if the table has the same size - clear only
        $db->setQuery("SHOW COLUMNS FROM `".$this->tablename."`");
        $gentable = true;
        try {
            $fields = $db->loadObjectList();
            $sum_columns = count($this->columns);
            if ($genid) $sum_columns++;
            if ($genpublished) $sum_columns++;
            if (count($fields) == $sum_columns) {
                $db->setquery("TRUNCATE `".$this->tablename."`;");
                $gentable = false;
            } else {
                $db->setquery("DROP TABLE IF EXISTS `".$this->tablename."`;");
            }
            $db->query();
        } catch (RuntimeException $e) { }
        // generate table
        if ($gentable) {
            $query = " CREATE TABLE IF NOT EXISTS `".$this->tablename."` (";
            if ($genid) $query .= " `id` int(11) NOT NULL AUTO_INCREMENT, ";
            if ($genpublished) $query .= " `published` BOOLEAN NOT NULL DEFAULT '1' ,";
            foreach ($this->columns AS $n => $column) {
                if ($column->title=="id" && $genid) continue;
                if ($column->title=="published" && $genpublished) continue;
                $query .= " `".$column->title."` ".$column->type.",";
            }
            $query .= "PRIMARY KEY (`id`))";
            $db->setquery($query);
            try {
                $db->query();
            } catch (RuntimeException $e) {
                $this->setError($e->getMessage());
                return false;
            }
        }
		return true;
	}
	
	/**
	 * Set own error message
	 * @message string
	 */
	public function setError($message) {
		$this->error = true;
		$this->message = $message;
	}
	
 	/**
 	* Import spredsheet into a table
 	* @param sting $file
 	*/
  public function importSheet($file) {
  	$app = JFactory::getApplication();
  	$this->file = $app->getCfg('tmp_path')."/jbtableimport"; 
  	// move to temp jbimport to allow chunk import
  	jimport('joomla.filesystem.file');  	
  	if (!JFile::upload($file['tmp_name'], $this->file)) {
  		$this->setError("Unable to create local file. Please check Joomla temp path.");
  		return false;
  	}
  	 
  	// find format from filetype
  	$ext = strtolower(strrchr($file['name'],"."));
  	switch ($ext) {
   		case ".xml":
   		$this->format = "Excel2003xml";
		break;
   		case ".xlsx":
   		$this->format = "Excel2007";
   		$or = PHPExcel_IOFactory::createReader($this->format);
   		if (!$or->canRead($this->file)) $this->format = "OOCalc"; 
		break;
   		case ".xls":
   		$this->format = "Excel5";
		break;
   		case ".csv":
   		$this->format = "CSV";
		break;
   		case ".ods":
   		$this->format = "OOCalc";
		break;
   		default:
		$this->format = false;
	}
	if ($this->format!=false) {
		$or = PHPExcel_IOFactory::createReader($this->format);
		if ($this->format=="CSV") {
			$or->setEnclosure($this->enclosure);
			$or->setDelimiter($this->delimeter);
		} else {
			$or->setReadDataOnly(false);
			$or->setLoadSheetsOnly(true);	
		}	
		$oe = $or->load($this->file);
		$ws = $oe->getActiveSheet();
		if ($this->format=="CSV") {
			// @todo: not propper but csv-reader does not count the total lines 
			$sheet=file($this->file);
			$this->highestRow  = count($sheet);
			unset($sheet);
		} else {
  			$this->highestRow = $ws->getHighestRow();
		}
		if ($this->generateTable($ws)) $this->message = JText::_("Table created");
		$this->startRow = ($this->has_column_names==0) ? 1 : 2;
	} else {
		$this->setError(JText::_("Error importing Table")." : ".JText::_("Upload valid Excel file"));
		return false;
	}
	return true;
  }

	/**
 	* Import the next X rows. Prevent timeout and memoryproblems 
 	*/
  public function importChunk() {
  	// get the sheet
  	$or = PHPExcel_IOFactory::createReader($this->format);
	if ($this->format=="CSV") {
		$or->setEnclosure($this->enclosure);
		$or->setDelimiter($this->delimeter);
	} else {
		$or->setReadDataOnly(false);
		$or->setLoadSheetsOnly(true);	
	}		
  	$oe = $or->load($this->file);
  	$ws = $oe->getActiveSheet();

  	$db = $this->getDbo();
  	$endRow = $this->startRow+$this->chunksize;
  	$trigger = 0;
  	
  	while ($this->startRow<$endRow) {
	 	$row = new PHPExcel_Worksheet_Row($ws, $this->startRow);
	  	$cellIterator = $row->getCellIterator();
		$cellIterator->setIterateOnlyExistingCells(false);
		$item = array();
		foreach ($cellIterator as $i => $cell) {
			if (!is_null($cell) && isset($this->columns[$i]->title)) {
				$ws->getStyle($cell->getCoordinate())->getNumberFormat()->setFormatCode($this->columns[$i]->format);
				$item[$this->columns[$i]->title] = (!is_null($cell)) ? $cell->getFormattedValue() : "";
			}
		}
		$this->addEntry($item);
		if ($this->startRow>=$this->highestRow) {
			$this->finished = true;
			break;
		}
		$this->startRow++;
		// write sql data after 10 rows
		$trigger++;
		if ($trigger>=10) {
			$db->setQuery($this->querycache);
			$db->query();
			$this->querycache = "";		
			$trigger=0;
		}
  	}		
  	if (!empty($this->querycache)) {
  		$db->setQuery($this->querycache);
  		$db->query();
  		$this->querycache = "";  		
  	}
  	if ($msg = $db->getErrorMsg()) {
  		$this->setError($msg);
  	}
  }
  
  
  public function xportToSession() {
  	$session = JFactory::getSession();
  	$data = array();
  	$data["columns"] = $this->columns;
  	$data["has_column_names"] = $this->has_column_names;
  	$data["enclosure"] = $this->enclosure;
  	$data["delimeter"] = $this->delimeter;
  	$data["file"] = $this->file;
  	$data["format"] = $this->format;
  	$data["tablename"] = $this->tablename;
  	$data["highestRow"] = $this->highestRow;
  	$data["startRow"] = $this->startRow;  	
  	$session->set('importdata', @json_encode($data));
  }
  
}

?>
