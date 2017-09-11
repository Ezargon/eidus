<?php
/** part of JooBatabase component - see http://joodb.feenders.de */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.controller' );

/**
 * Main Contoller
 */
class JooDBController extends JControllerLegacy
{
    /**
     * Constructor
     */
    public function __construct( $config = array() )
    {
        parent::__construct( $config );
        // Register Extra tasks
        $this->registerTask( 'add',			'edit' );
        $this->registerTask( 'apply',		'save' );
        $this->registerTask( 'applydata',		'savedata' );
    }

    /** edit Database */
    public function edit()
    {
        $document = JFactory::getDocument();
        $vType	= $document->getType();
        $view = $this->getView( 'joodbentry', $vType);
        $vLayout = JRequest::getCmd( 'layout', 'default' );
        $view->setLayout($vLayout);
        $view->display();
    }

    /** list Data of JooDatabase tables */
    public function listdata()
    {
        $document = JFactory::getDocument();
        $vType	= $document->getType();
        $view =  $this->getView( 'listdata', $vType);
        $vLayout = JRequest::getCmd( 'layout', 'default' );
        $view->setLayout($vLayout);
        $view->display();
    }

    /** edit Data of JooDatabase tables */
    public function editdata() {
        $document = JFactory::getDocument();
        $vType	= $document->getType();
        $view = $this->getView( 'editdata', $vType);
        $vLayout = JRequest::getCmd( 'layout', 'default' );
        $view->setLayout($vLayout);
        $view->display();
    }

    /** add New entry */
    public function addNew(){
        parent::display();
    }

    /**
     * Save data entry in joodb data table
     */
    function savedata()
    {
        $app = JFactory::getApplication();

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // load the jooDb object with table fiel infos
        $joodbid = JRequest::getInt( 'joodbid');
        $jb = JTable::getInstance( 'joodb', 'Table' );
        $jb->load( $joodbid );
        $db	= $jb->getTableDBO();
        $fields = $db->getTableColumns($jb->table,false);
        $item= new JObject();
        foreach ($fields as $fname=>$fcell) {
            $fne = str_replace(" ","_",$fname);
            if (isset($_POST[$fne]) || isset($_FILES[$fne])) {
                $typearr = preg_split("/\(/",$fcell->Type);
                switch ($typearr[0]) {
                    case 'text' :
                    case 'tinytext' :
                    case 'mediumtext' :
                    case 'longtext' :
                        $item->{$fname}= JRequest::getVar($fne, '', 'post', 'string', JREQUEST_ALLOWHTML);
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
                        } else {
                            $delf = JRequest::getInt($fne."_del",0);
                            if (!empty($delf)) $item->{$fname} = NULL;
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
        if (!empty($item->{$jb->fid})) {
            $db->updateObject($jb->table,$item,$jb->fid,true);
        } else {
            $db->insertObject($jb->table,$item,$jb->fid);
        }

        $error =  $db->getErrorMsg();
        if (!empty($error)) {
            $msg = JText::_( 'Error' ).": ".$db->getErrorMsg();
        } else {
            $msg = JText::_( 'Item Saved' );
            $id =  $item->{$jb->fid};

            // Delete exiting image
            $ri = JRequest::getVar('delete_image', '', 'post','array');
            if (is_array($ri) && $ri[0]==1) {
                $image = JPATH_ROOT."/images/joodb/db".$jb->id."/img".$id;
                @unlink($image.".jpg");
                @unlink($image."-thumb.jpg");
            }

            // attach and resize uploaded image
            // Get the uploaded file information
            $newimage = JRequest::getVar('dataset_image', null, 'files', 'array' );
            if ($newimage['name']!="") {

                // Make sure that file uploads are enabled in php
                if (!(bool) ini_get('file_uploads')) {
                    JError::raiseWarning('SOME_ERROR_CODE', JText::_('WARNINSTALLFILE'));
                    return false;
                }
                $destination = JPATH_ROOT."/images/joodb/db".$jb->id."/img".$id;
                $org_img = $destination."-original".strrchr($newimage['name'],".");
                $params = new JRegistry($jb->params );
                // Move uploaded image
                jimport('joomla.filesystem.file');
                $uploaded = JFile::upload($newimage['tmp_name'], $org_img);
                if (file_exists($org_img)) {
                    chmod($org_img, 0664);
                    // normal image
                    JoodbAdminHelper::resizeImage($org_img,$destination.".jpg",$params->get("img_width",480),$params->get("img_height",600));
                    // thumbnail image
                    JoodbAdminHelper::resizeImage($org_img,$destination."-thumb.jpg",$params->get("thumb_width",120),$params->get("thumb_height",200));
                }
            }

            // store values from subtemplates
            $subdata = $app->input->get("jbSubForm",null,"array");
            if (!empty($subdata)) {
                $subitems = $jb->getSubitems();
                foreach ($subdata AS $name => $sdfield) {
                    $subitem = $subitems[$name];
                    if ($subitem->type=="2") { // n:m relation
                        // clear index from id
                        $db->setquery("DELETE FROM `".$subitem->idx_table."` WHERE `".$subitem->idx_id1."`=".$db->quote($id))->execute();
                        //rebuild index with data
                        foreach ($sdfield AS $sdval) {
                            $sdv = new stdClass();
                            $sdv->{$subitem->idx_id1} = $id;
                            $sdv->{$subitem->idx_id2} = $sdval;
                            $db->insertObject($subitem->idx_table,$sdv,"id");
                        }
                    }
                }
            }
        }

        $task = JRequest::getCmd( 'task' );
        $link = 'index.php?option=com_joodb&joodbid='.$jb->id.(($task=="applydata") ? "&view=editdata&cid[]=".$id : "&view=listdata");
        $this->setRedirect( $link, $msg );
    }

    /**
     * Save joodb entry
     */
    public function save()
    {
        // Initialize variables
        $row = JTable::getInstance('joodb', 'Table');

        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        if (!$row->bind(JRequest::get('post',2))) {
            JError::raiseError(500, $row->getError() );
        }

        $msg = JText::_( 'Item Saved' );

        if (!$row->check()) $msg = $row->getError();
        if (!$row->store()) Jerror::raiseError(500, $row->getError());

        $row->checkin();

        $task = JRequest::getCmd( 'task' );
        switch ($task)
        {
            case 'apply':
                $link = 'index.php?option=com_joodb&task=edit&view=joodbentry&cid[]='. $row->id ;
                break;
            case 'save':
            default:
                $link = 'index.php?option=com_joodb';
                break;
        }

        $this->setRedirect( $link, $msg );
    }

    public function cancel()
    {
        //cancel editing a record
        $this->setRedirect( 'index.php?option=com_joodb', JText::_( 'Edit canceled' ) );
    }

    public function cancelEditData()
    {
        //cancel editing a record get database
        $this->setRedirect( 'index.php?option=com_joodb', JText::_( 'Edit canceled' ) );
    }

    public function exitjoodb()
    {
        $this->setRedirect( 'index.php' );
    }

    /**
     * Copy one or more databases
     */
    public function copy() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $this->setRedirect( 'index.php?option=com_joodb' );

        $cid	= JRequest::getVar( 'cid', null, 'post', 'array' );
        $item  = JTable::getInstance('joodb', 'Table');
        $n		= count( $cid );

        if ($n > 0)
        {
            foreach ($cid as $id)
            {
                if ($item->load( (int)$id ))
                {
                    $item->id				= 0;
                    $item->title			= 'Copy of ' . $item->name;

                    if (!$item->store()) {
                        return JError::raiseWarning(501, $item->getError() );
                    }
                }
                else {
                    return JError::raiseWarning( 500, $item->getError() );
                }
            }
        }
        else {
            return JError::raiseWarning( 500, JText::_( 'No items selected' ) );
        }
        $this->setMessage( JText::sprintf( 'Items copied', $n ) );
    }

    /**
     * Remove entries from joodb database tables
     */
    public function removedata() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $joodbid	= JRequest::getInt( 'joodbid');

        $jb = JTable::getInstance( 'joodb', 'Table' );
        $jb->load( $joodbid );

        $this->setRedirect( 'index.php?option=com_joodb&view=listdata&joodbid='.$jb->id );

        // Initialize variables
        $db	= $jb->getTableDBO();
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $n		= count( $cid );
        JArrayHelper::toInteger( $cid );

        if (count($cid) < 1) {
            $this->setMessage(JText::_('Select an item to delete'));
        } else {
            $cids = implode(',', $cid);
            $query = 'DELETE FROM '.$jb->table
                . ' WHERE '.$jb->fid.' IN ( '. $cids. ' )';
            $db->setQuery( $query );
            if (!$db->query()) {
                JError::raiseWarning( 500, $db->getError() );
            }
        }

        $this->setMessage( JText::sprintf( 'Items removed', $n ) );
    }

    /**
     * Sets the publish state of a jodb data table entry to 1 ...
     */
    public function data_publish() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));;

        $joodbid	= JRequest::getInt( 'joodbid');
        $jb = JTable::getInstance( 'joodb', 'Table' );
        $jb->load( $joodbid );

        // Initialize variables
        $db	= $jb->getTableDBO();
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger( $cid );
        $n		= count( $cid );

        if ($n) {
            $cids = implode(',', $cid);
            $query = 'UPDATE '.$jb->table.' SET '.$jb->fstate.'=1'
                . ' WHERE '.$jb->fid.' IN ( '. $cids. ' )';
            $db->setQuery( $query );
            if (!$db->query()) {
                JError::raiseWarning( 500, $db->getError() );
            }
        }

        $this->setRedirect( 'index.php?option=com_joodb&view=listdata&joodbid='.$jb->id );
        $this->setMessage( JText::sprintf( 'Items Published', $n ) );
    }

    /**
     * Sets the publish state of a jodb data table entry to 0 ...
     */
    public function data_unpublish() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $joodbid	= JRequest::getInt( 'joodbid');
        $jb = JTable::getInstance( 'joodb', 'Table' );
        $jb->load( $joodbid );

        // Initialize variables
        $db	= $jb->getTableDBO();
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        JArrayHelper::toInteger( $cid );
        $n		= count( $cid );

        if ($n) {
            $cids = implode(',', $cid);
            $query = 'UPDATE '.$jb->table.' SET '.$jb->fstate.'=0'
                . ' WHERE '.$jb->fid.' IN ( '. $cids. ' )';
            $db->setQuery( $query );
            if (!$db->query()) {
                JError::raiseWarning( 500, $db->getError() );
            }
        }

        $this->setRedirect( 'index.php?option=com_joodb&view=listdata&joodbid='.$jb->id );
        $this->setMessage( JText::sprintf( 'Items Unpublished', $n ) );
    }


    /**
     * Remove item(s)
     */
    public function remove($view='joodb') {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $this->setRedirect( 'index.php?option=com_joodb' );

        // Initialize variables
        $db		= JFactory::getDBO();
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

        JArrayHelper::toInteger( $cid );

        if (count($cid) < 1) {
            $this->setMessage(JText::_('Select an item to delete'));
        } else {
            $query = 'DELETE FROM `#__joodb`'
                . ' WHERE id = ' . implode( ' OR id = ', $cid );
            $db->setQuery( $query );
            if (!$db->query()) {
                JError::raiseWarning( 500, $db->getError() );
            }
            $this->setMessage( JText::sprintf( 'Items removed', count( $cid )));
        }
    }


    /**
     * Un Publish item(s)
     */
    public function unpublish() {
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $db		= JFactory::getDBO();
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $n		= count( $cid );
        JArrayHelper::toInteger( $cid );

        if ($n) {
            $query = 'UPDATE #__joodb SET published=0 '
                . ' WHERE id = ' . implode( ' OR id = ', $cid );
            $db->setQuery( $query );
            if (!$db->query()) {
                JError::raiseWarning( 500, $db->getError() );
            } else {
                $msg = JText::sprintf( 'Items Unpublished', count( $cid ) );
            }
        }
        $this->setRedirect( 'index.php?option=com_joodb&task=display', $msg );
    }

    /**
     * Publish item(s)
     */
    public function publish()	{
        // Check for request forgeries.
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $db		= JFactory::getDBO();
        $cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );
        $n		= count( $cid );
        JArrayHelper::toInteger( $cid );

        if ($n) {
            $query = 'UPDATE #__joodb SET published=1 '
                . ' WHERE id = ' . implode( ' OR id = ', $cid );
            $db->setQuery( $query );
            if (!$db->query()) {
                JError::raiseWarning( 500, $db->getError() );
            } else {
                $msg = JText::sprintf( 'Items Published', count( $cid ) );
            }
        }
        $this->setRedirect( 'index.php?option=com_joodb&task=display', $msg );
    }

    /**
     * Test the existance of a table
     */
    public function  testtable() {
        $db = JFactory::getDbo();
        $exist = false;
        if ($tname = JRequest::getCmd("table"))
            $tables = $db->getTableList();
        $exist = (array_search($tname, $tables)!==false) ? true : false;
        header('Content-type: application/json');
        echo json_encode($exist);
        die();
    }

    /**
     * Tests an sql connection and retuns database names
     */
    public function  testconnection() {
        $dbs = array();
        $link = @mysqli_connect(JRequest::getVar("extdb_server"), JRequest::getVar("extdb_user"), JRequest::getVar("extdb_pass"),null);
        if ($link) {
            $db_list = mysqli_query($link,"SHOW DATABASES");
            while ($row = mysqli_fetch_assoc($db_list)) $dbs[] = $row['Database']."\n";
        };
        header('Content-type: application/json');
        if (!empty($dbs))
            echo '{"dbs":'.json_encode($dbs)."}";
        else if ($link)
            echo '{"connected": "true"}';
        else
            echo '{"error": "true"}';
        die();
    }

    /**
     * Get Tablefildlist from a Table of JooDB Database
     */
    public function getfieldlist() {
        header('Content-type: application/json');
        if ($id = JRequest::getInt('jbid')) {
            $row = JTable::getInstance( 'joodb', 'Table' );
            if ($row->load( $id )) {
                $tdb = $row->getTableDBO();
                $tdb->setQuery("SHOW COLUMNS FROM `".$tdb->escape(JRequest::getVar('table'))."`");
                try {
                    $fields = $tdb->loadObjectList();
                    $response = '{"fields":'.json_encode($fields)."}";
                } catch (RuntimeException $e) {
                    $response = '{"error":"'.$e->getMessage().'"}';
                }
            } else { $response = '{"error":"could not load table"}'; }
        } else { $response = '{"error":"no id"}'; }
        echo $response;
        die();
    }

}
