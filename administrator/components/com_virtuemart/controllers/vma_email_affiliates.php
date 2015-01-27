<?php

/**
 * @package   VM Affiliate
 * @version   4.5.2.0 January 2012
 * @author    Globacide Solutions http://www.globacide.com
 * @copyright Copyright (C) 2006 - 2012 Globacide Solutions
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access

defined( '_JEXEC' ) or die( 'Direct access to this location is not allowed.' );

// import the component controller library

jimport('joomla.application.component.controller');

/**
 * VM Affiliate backend controller
 */

class VirtuemartControllerVma_email_affiliates extends JController {
	
	/**
	 * Method to display the view
	 */
	 
	function display() {

		$document 	= JFactory::getDocument();
		
		$viewName 	= JRequest::getWord('view', '');
		
		$viewType 	= $document->getType();
		
		$view 		= $this->getView($viewName, $viewType);

		parent::display();
		
	}
	
	/**
	 * Method to send a mass e-mail to all or a specific affiliate
	 */
	 
	function send() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$database		= &JFactory::getDBO();
		
		$recipients		= &JRequest::getVar("recipients");
		
		$subject		= &JRequest::getVar("subject");
		
		$html			= &JRequest::getVar("html");
		
		$html			= $html ? true : false;
		
		$messageText	= &JRequest::getVar("affiliateMessageText",	"");
		
		$messageHTML	= $_POST["affiliateMessageHTML"];
		
		// check input
		
		if (!$subject || ($html && !$messageHTML || !$html && !$messageText)) {
			
			vmError(JText::_("FILL_IN_ALL_FIELDS"));
			
			$this->display();
			
			return false;
			
		}
		
		// prepare the e-mail
		
		$affiliateMail				= &JFactory::getMailer();
		
		$affiliateMail->setSender(array($vmaHelper->_config->getValue( 'config.mailfrom' ), $vmaHelper->_config->getValue( 'config.fromname' )));
		
		$affiliateMail->setSubject($subject);
		
		$affiliateMail->setBody($html ? $messageHTML : $messageText);
		
		$affiliateMail->IsHTML($html);
		
		// add recipients
		
		if ($recipients == "0") {
			
			// get all e-mail addresses
			
			$query		= "SELECT `mail` FROM #__vm_affiliate WHERE `blocked` = '0'";
			
			$database->setQuery($query);
			
			$recipients = $database->loadResultArray();
			
			// basic filtering
			
			foreach ($recipients as $key => $recipient) {
				
				if (!stristr($recipient, "@")) {
					
					unset($recipients[$key]);
					
				}
				
			}
			
		}
		
		// add recipients
		
		$affiliateMail->addBCC($recipients);
		
		// send the e-mails
							
		if (!$affiliateMail->send()) {
			
			vmError(JText::_("EMAIL_SENDING_FAILURE"));
			
			$this->display();
			
			return false;
				
		}
		
		else {
			
			vmInfo(JText::sprintf("EMAIL_SENDING_SUCCESS", count($recipients)));
			
			$this->display();
			
			return true;
			
		}
		
		$this->display();
		
	}
	
}