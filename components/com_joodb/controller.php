<?php
/**
* @package		JooDatabase - http://joodb.feenders.de
* @copyright	Copyright (C) Computer - Daten - Netze : Feenders. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @author		Dirk Hoeschen (hoeschen@feenders.de)
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

/**
 * JooDatabase Component Controller
 */
class JoodbController extends JControllerLegacy
{
	/**
	 * Method to show a view
	 */
	public function display($cachable = true, $urlparams = false)
	{
		// Set a default view if none exists
		if ( ! JRequest::getCmd( 'view' ) ) {
			JRequest::setVar('view', 'catalog' );
		}

        // TODO complete list of params
        $urlparams = array(
            'option' => 'CMD',
            'view' => 'CMD',
            'task' => 'CMD',
            'format' => 'CMD',
            'layout' => 'CMD',
            'id' => 'INT',
            'jbid' => 'INT',
            'letter' => 'CMD',
            'search' => 'STRING',
            'searchfield' => 'STRING',
            'gs' => 'ARRAY',
            'orderby' => 'CMD',
            'ordering' => 'CMD',
            'limit' => 'INT',
            'limitstart' => 'INT',
            'start' => 'INT',
            'print' => 'BOOLEAN',
            'lang' => 'CMD',
            'Itemid' => 'INT'
        );

		parent::display($cachable, $urlparams);
	}

	/**
	 * Submit Form Data. send email and insert to database
	 */
	public function submit()
	{
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();

		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$params	= $app->getParams();
		// read database configuration from joobase table
		$model= $this->getModel('form');
		$jb = $model->getJoobase();
		// merge the component with the joodb parameters
		jimport('joomla.html.parameter');		
		$jparams = new JRegistry($jb->params );
		$params->merge($jparams);

		// check captcha if any in form template
		if (strpos($jb->tpl_form,"{joodb captcha")!==false) {
	    	$session = JFactory::getSession();
            $code = $app->input->get('joocaptcha');
			if (!$session->get('joocaptcha') || $session->get('joocaptcha')!=$code) {
				$this->setError(JText::_('Captcha code invalid'));
                $app->input->set('joocaptcha','');
				JError::raiseWarning( 0, $this->getError() );
				$this->display();
				return;
			}
            $session->set('joocaptcha',null);
		}

		// insert form data
        $id = JRequest::getCmd($jb->fid);
		$item = $model->getData($id);
        $user = JFactory::getUser();
        // Test for User access to dataset
        if (!empty($id)) {
            $has_access = true;
            if ($fuser=$jb->getSubdata('fuser'))
                if ($item->{$fuser}!=$user->id) $has_access = false;
            if (!$has_access) JError::raiseError( 403, JText::_('ALERTNOTAUTH'));
        } else {
            if (!empty($jb->fdate)) {
                $date = new JDate();
                $item->{$jb->fdate} = $date->toSql();
            }
        }
		$msg = JoodbHelper::saveData($jb,$item);
		// send formdata to admin
		if (empty($id) && $params->get("infomail",0)==1) {
			$db->setQuery("SELECT email FROM `#__users` WHERE `id` ='".$params->get("infomail_user")."' LIMIT 1");
			if ($email = $db->loadResult()) {
				$MailFrom 	= $app->getCfg('mailfrom');
				$FromName 	= $app->getCfg('fromname');
				$subject = "JooDatabase - ".JText::_('New Database entry')." - ".$jb->name;
				$body = $subject."\r\n";
				$body .= "Site: ".$app->getCfg('sitename')." - ".JUri::Current()."\r\n\r\n";
				$body .= JText::_('Recieved values')."\r\n===================\r\n";
				foreach ($item as $var=>$val) 
					if (!empty($val))
						$body .= ucfirst($var).": ".$val."\r\n";;
				$body .= "===================\r\n".JText::_('Statusmessage').": ".$msg."\r\n\r\n";
				$mail = JFactory::getMailer();
				$mail->addRecipient( $email );
				$mail->setSender( array( $MailFrom, $FromName ) );
				$mail->setSubject( $FromName.': '.$subject );
				$mail->setBody( $body );
				$sent = $mail->Send();
                if (!$sent) $app->enqueueMessage('Mail could not be sent. Please inform admin!','warning');
			}
		}
		$id = $item->{$jb->fid};

		// TODO: Controller is not the right place
		// Attach and resize uploaded image
		$newimage = JRequest::getVar('joodb_dataset_image', null, 'files', 'array' );
		if ($newimage['name']!="") {
			require_once( JPATH_COMPONENT."/helpers/files.php");
			$destination = JPATH_ROOT."/images/joodb/db".$jb->id."/img".$id;
			$msg .= JoodbFilesHelper::processUploadedImage($newimage, $destination,$params);
		}

        $Itemid = ($params->get("redirect")==1) ? $params->get("redirect_to") : $app->input->getInt("Itemid");
		$this->setRedirect(JRoute::_('index.php?Itemid='.$Itemid,false), $msg);
	}

	/**
	 * Save database entry from edit form 
	 */
	public function save()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();
		$params	= $app->getParams();
		$model = $this->getModel('edit');
		$jb = $model->getJoobase();
		jimport('joomla.html.parameter');		
		$jparams = new JRegistry($jb->params);
		$params->merge($jparams);		
		
		// insert form data
        $id = $app->input->getInt($jb->fid);
		$item = new JObject();
        if (empty($id) && !empty($jb->fdate)) {
            $date = new JDate();
            $item->{$jb->fdate} = $date->toSql();
        }
		$msg = JoodbHelper::saveData($jb,$item);		
		$id = $item->{$jb->fid};

		// TODO: Controller is not the right place
		// Attach and resize uploaded image
		$newimage = JRequest::getVar('joodb_dataset_image', null, 'files', 'array' );
		if ($newimage['name']!="") {
			require_once( JPATH_COMPONENT."/helpers/files.php");
			$destination = JPATH_ROOT."/images/joodb/db".$jb->id."/img".$id;
			$msg .= JoodbFilesHelper::processUploadedImage($newimage, $destination,$params);
		}

		$this->setRedirect(JRoute::_('index.php?option=com_joodb&joobase='.$jb->id.'&view=article&id='.$id.'&Itemid='.JRequest::getInt('Itemid'),false), $msg);

	}

    /**
     * Delete an enty in the frontend
     */
    public function delete() {
        // insert form data
        $model = $this->getModel('edit');
        $jb = $model->getJoobase();
        $id = JRequest::getInt('id');
        $msg = JText::_("ERROR");
        if (!empty($id)) {
            $db = $jb->getTableDbo();
            $result = $db->setQuery("DELETE FROM `".$jb->table."` WHERE `".$jb->fid."` =".(int)$id)->execute();
            if (!empty($result)) $msg = JText::_("ITEM DELETED");
        }
        $this->setRedirect(JRoute::_('index.php?option=com_joodb&joobase='.$jb->id.'&view=catalog&Itemid='.JRequest::getInt('Itemid'),false), $msg);
    }


	/**
	 * Print out a captcha image
	 */
	public function captcha()
	{
		$app = JFactory::getApplication();
		JoodbHelper::printCaptcha();
		$app->close();
	}

	/**
	 * Add entries to notepad ...
	 */
	public function addToNotepad() {
  		$session = JFactory::getSession();
		$articles = preg_split("/:/",$session->get('articles',''));
		if ($articles[0]=="") unset($articles[0]);
		$articles[] = JRequest::getCmd("article");
		$session->set('articles', join(":",$articles));
		$this->display();
	}

	/**
	 * Remove entries from notepad
	 */
	public function removeFromNotepad() {
  		$session = JFactory::getSession();
		$articles = preg_split("/:/",$session->get('articles',''));
		if ($articles[0]=="") unset($articles[0]);
		$id = JRequest::getCmd("article");
		foreach ($articles as $ndx => $article)
	    	if ($article==$id) {
	    		unset($articles[$ndx]);
	    	}
		$session->set('articles', join(":",$articles));
		$this->display();
	}

	/**
	 * Delete all entries from notepad
	 */
	public function purgeNotepad() {
		$session = JFactory::getSession();
		$session->set('articles', '');
		$this->display();
	}
	
	/**
	 * Wrapper for blob images and files 
	 */
	public function getFileFromBlob() {
		$app = JFactory::getApplication();
		$model = $this->getModel("article");
		if ($item = $model->getData()) {
			if ($field = JRequest::getVar('field')) {
				$mime = JoodbAdminHelper::getMimeType($item->{$field});
				if (substr($mime, 0,5)=="image") {
					$im = imagecreatefromstring($item->{$field});
					header('Content-Type: image/png');
					imagepng($im);
					imagedestroy($im);
				} else {
					$p = preg_split("/\//", $mime);	
					$ext = ($mime!="application/octet-stream") ? $p[1] : "bin";
                    $jb = $model->getJoobase();
					header("Content-Type: ".$mime);
                    header('Content-Disposition: attachment; filename='.$field."-".JFilterOutput::stringURLSafe($item->{$jb->ftitle}).".".$ext);
					echo $item->{$field};
				}
			}
		}
		die();
	}

	/**
	 * Get anonymous registration info for validation
	 */
	public function getLicenseInfo() {
		$db = JFactory::getDbo();
		$db->setQuery("SELECT value FROM `#__joodb_settings` WHERE `name` = 'license' AND `jb_id` IS NULL",0,1);
		header('Content-type: application/json');
		$status=json_decode($db->loadResult());		
		echo '{"hash": "'.$status->hash.'"}';
		die();
	}
	

}

?>
