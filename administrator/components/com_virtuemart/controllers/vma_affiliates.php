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

class VirtuemartControllerVma_affiliates extends JController {
	
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
	 * Method to toggle an affiliate's status between enabled and disabled
	 */

	function affiliateToggle() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$database		= &JFactory::getDBO();
		
		$id				= &JRequest::getVar("id");
		
		$published		= &JRequest::getVar("published");
		
		// perform the toggle query
		
		$query			= "UPDATE #__vm_affiliate SET `blocked` = 1 - blocked WHERE `affiliate_id` = '" . $id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// update the offline tracking name values
			
		$vmaHelper->updateOfflineNameValue($id, !$published);
		
		// show the confirmation message
			
		vmInfo($published ? JText::_("AFFILIATE_DISABLED")		: JText::_("AFFILIATE_ENABLED"));
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
	/**
	 * Method to delete an affiliate
	 */

	function affiliateDelete() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$database		= &JFactory::getDBO();
		
		$id				= &JRequest::getVar("id");
		
		$type			= &JRequest::getVar("type");
		
		// update the offline tracking name values
			
		$vmaHelper->updateOfflineNameValue($id, false);
		
		// perform the delete query
		
		$query			= "DELETE FROM #__vm_affiliate WHERE `affiliate_id` = '" . $id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// show the confirmation message
			
		vmInfo(JText::_("AFFILIATE_REMOVED"));
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
	/**
	 * Method to add an affiliate
	 */
	 
	function affiliateAdd() {
		
		global $vmaSettings, $vmaHelper;
		
		$database						= &JFactory::getDBO();
		
		// get registration form fields
		
		$newAffiliate 					= array();
		
		$newAffiliate["username"] 		= JRequest::getString("username", 	"", 	"post");
		
		$newAffiliate["password"]		= JRequest::getString("password", 	"", 	"post", JREQUEST_ALLOWRAW);
		
		$newAffiliate["vpassword"]		= JRequest::getString("vpassword", 	"", 	"post", JREQUEST_ALLOWRAW);
		
		$newAffiliate["mail"] 			= JRequest::getString("mail", 		"", 	"post");
		
		$newAffiliate["website"] 		= JRequest::getString("website", 	"", 	"post");
		
		$newAffiliate["fname"] 			= JRequest::getString("fname", 		"", 	"post");
		
		$newAffiliate["lname"] 			= JRequest::getString("lname", 		"", 	"post");
		
		$newAffiliate["street"] 		= JRequest::getString("street", 	"", 	"post");
		
		$newAffiliate["city"] 			= JRequest::getString("city", 		"", 	"post");
		
		$newAffiliate["state"] 			= JRequest::getString("state", 		"", 	"post");
		
		$newAffiliate["country"] 		= JRequest::getString("country", 	"", 	"post");
		
		$newAffiliate["zipcode"] 		= JRequest::getString("zipcode", 	"", 	"post");
		
		$newAffiliate["phoneno"] 		= JRequest::getString("phoneno", 	"", 	"post");
		
		$newAffiliate["taxssn"] 		= JRequest::getString("taxssn", 	"", 	"post");
		
		// validate form input
		
		if (!$newAffiliate["username"]) {
			
			vmError(JText::_("PROVIDE_USERNAME"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["password"]) {
			
			vmError(JText::_("PROVIDE_PASSWORD"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["vpassword"]) {
			
			vmError(JText::_("RETYPE_PASSWORD"));
			
			$this->display();
			
			return false;
			
		}
		
		if ($newAffiliate["password"] != $newAffiliate["vpassword"]) {
			
			vmError(JText::_("PASSWORDS_DIFFER"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["mail"]) {
			
			vmError(JText::_("PROVIDE_EMAIL_ADDRESS"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["fname"]) {
			
			vmError(JText::_("PROVIDE_FIRST_NAME"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["lname"]) {
			
			vmError(JText::_("PROVIDE_LAST_NAME"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["street"]) {
			
			vmError(JText::_("PROVIDE_ADDRESS"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["city"]) {
			
			vmError(JText::_("PROVIDE_CITY"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["country"]) {
			
			vmError(JText::_("PROVIDE_COUNTRY"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["zipcode"]) {
			
			vmError(JText::_("PROVIDE_ZIPCODE"));
			
			$this->display();
			
			return false;
			
		}
		
		// check if either the username or e-mail address are already registered

		$query 		= "SELECT `username`, `mail` FROM #__vm_affiliate WHERE `username` = '" . $newAffiliate['username'] . "' OR `mail` = '" . $newAffiliate['mail'] . "'";
		
		$database->setQuery( $query );
		
		$info		= $database->loadAssoc();
		
		if ($info["username"] == $newAffiliate['username']) {
			
			vmError(JText::_("USERNAME_ALREADY_EXISTS"));
			
			$this->display();
			
			return false;
			
		}
		
		else if ($info["mail"] == $newAffiliate['mail']) {
			
			$this->display();
			
			return false;
			
		}
		
		// get new affiliate special parameters

		$initialBonus		= $vmaSettings->initial_bonus;
		
		$timeAndDate		= date("Y-m-d\TH:i:s");
		
		// properly escape data
		
		foreach ($newAffiliate as $property => $value) {
			
			if ($property != "password") {
				
				$newAffiliate[$property] = $database->getEscaped($newAffiliate[$property]);
			
			}
			
		}
		
		// insert the new affiliate in the database
		
		$query 				= "INSERT INTO #__vm_affiliate VALUES ('" . NULL . "', '" . $newAffiliate["username"] . "', '" . md5($newAffiliate["password"]) . "', " . 
		
							  "'" . $newAffiliate["fname"] . "', '" . $newAffiliate["lname"] . "', '" . $newAffiliate["mail"] . "', '" . $newAffiliate["website"] . "', " . 
							  
							  "'" . $initialBonus . "', '" . $newAffiliate["street"] . "', '" . $newAffiliate["city"] . "', " . "'" . $newAffiliate["state"] . "', " . 
							  
							  "'" . $newAffiliate["country"] . "', '" . $newAffiliate["zipcode"] . "', '" . $newAffiliate["phoneno"] . "', '" . $newAffiliate["taxssn"] . "', " .
							  
							  "'N/A', '0', '0', '0', '0', '2', '0', '1', '', '0', '', '" . $timeAndDate . "')";
							  
		$database->setQuery($query);
		
		$database->query();
		
		// get the new affiliate's id
		
		$affiliateID 		= $database->insertid();
		
		// update the offline tracking name values
		
		$vmaHelper->updateOfflineNameValue($affiliateID, true);
		
		// e-mail the new affiliate
		
		$affiliateMail		= &JFactory::getMailer();
			
		$affiliateMail->addRecipient($newAffiliate["mail"]);
		
		$affiliateMail->setSender(array($vmaHelper->_config->getValue( 'config.mailfrom' ), $vmaHelper->_config->getValue( 'config.fromname' )));
		
		$affiliateMail->setSubject(JText::sprintf("WELCOME_MESSAGE", $vmaHelper->_config->getValue( 'config.sitename' )));
		
		$affiliateMail->setBody(JText::sprintf("WELCOME_EMAIL", "\r\n\r\n", $newAffiliate["username"] . "\r\n\r\n", 
		
								"\r\n" . JRoute::_($vmaHelper->_website . "index.php?option=com_affiliate&view=login") . "\r\n\r\n", 
								
								"\r\n" . $vmaHelper->_config->getValue( 'config.sitename' ) . "\r\n" . $vmaHelper->_website));
		
		$affiliateMail->send();
		
		// show the confirmation message
		
		vmInfo(JText::_("AFFILIATE_ADDED"));
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
	/**
	 * Method to update an affiliate's details
	 */
	 
	function affiliateUpdate() {
		
		global $vmaSettings, $vmaHelper;
		
		$database						= &JFactory::getDBO();
		
		// get edit form fields
		
		$newAffiliate 					= array();
		
		$newAffiliate["affiliate_id"]	= JRequest::getInt("affiliate_id",	"",		"post");
		
		$newAffiliate["mail"] 			= JRequest::getString("mail", 		"", 	"post");
		
		$newAffiliate["website"] 		= JRequest::getString("website", 	"", 	"post");
		
		$newAffiliate["fname"] 			= JRequest::getString("fname", 		"", 	"post");
		
		$newAffiliate["lname"] 			= JRequest::getString("lname", 		"", 	"post");
		
		$newAffiliate["street"] 		= JRequest::getString("street", 	"", 	"post");
		
		$newAffiliate["city"] 			= JRequest::getString("city", 		"", 	"post");
		
		$newAffiliate["state"] 			= JRequest::getString("state", 		"", 	"post");
		
		$newAffiliate["country"] 		= JRequest::getString("country", 	"", 	"post");
		
		$newAffiliate["zipcode"] 		= JRequest::getString("zipcode", 	"", 	"post");
		
		$newAffiliate["phoneno"] 		= JRequest::getString("phoneno", 	"", 	"post");
		
		$newAffiliate["taxssn"] 		= JRequest::getString("taxssn", 	"", 	"post");
		
		// validate form input
		
		if (!$newAffiliate["affiliate_id"]) {
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["mail"]) {
			
			vmError(JText::_("PROVIDE_EMAIL_ADDRESS"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["fname"]) {
			
			vmError(JText::_("PROVIDE_FIRST_NAME"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["lname"]) {
			
			vmError(JText::_("PROVIDE_LAST_NAME"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["street"]) {
			
			vmError(JText::_("PROVIDE_ADDRESS"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["city"]) {
			
			vmError(JText::_("PROVIDE_CITY"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["country"]) {
			
			vmError(JText::_("PROVIDE_COUNTRY"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$newAffiliate["zipcode"]) {
			
			vmError(JText::_("PROVIDE_ZIPCODE"));
			
			$this->display();
			
			return false;
			
		}
		
		// check if the e-mail address is already registered

		$query 		= "SELECT `affiliate_id`, `mail` FROM #__vm_affiliate WHERE `mail` = '" . $newAffiliate["mail"] . "'";
		
		$database->setQuery($query);
		
		$info		= $database->loadAssoc();
		
		if ($info["mail"] == $newAffiliate['mail'] && $info["affiliate_id"] != $newAffiliate["affiliate_id"]) {
			
			$this->display();
			
			return false;
			
		}
		
		// properly escape data
		
		foreach ($newAffiliate as $property => $value) {
			
			$newAffiliate[$property] = $database->getEscaped($newAffiliate[$property]);
			
		}
		
		// insert the new affiliate in the database
		
		$query 				= "UPDATE #__vm_affiliate SET `fname` = '" 			. $newAffiliate["fname"] 	. "', `lname` = '" 	. $newAffiliate["lname"] 	. "', `mail` = '" 		. 
		
							  $newAffiliate["mail"] 	. "', `website` = '" 	. $newAffiliate["website"] 	. "', `street` = '" . $newAffiliate["street"] 	. "', `city` = '" 		. 
							  
							  $newAffiliate["city"] 	. "', `country` = '" 	. $newAffiliate["country"] 	. "', `state` = '" 	. $newAffiliate["state"] 	. "', `zipcode` = '" 	. 
							  
							  $newAffiliate["zipcode"] 	. "', `phoneno` = '" 	. $newAffiliate["phoneno"] 	. "', `taxssn` = '" . $newAffiliate["taxssn"] 	. "' WHERE " 			. 
							  
							  "`affiliate_id` = '" . $newAffiliate["affiliate_id"] . "'";
							  
		$database->setQuery($query);
		
		$database->query();
		
		// update the offline tracking name values
		
		$vmaHelper->updateOfflineNameValue($newAffiliate["affiliate_id"], "update");
		
		// show the confirmation message
		
		vmInfo(JText::_("AFFILIATE_UPDATED"));
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
	/**
	 * Method to change an affiliate's password
	 */
	 
	function affiliatePasswordUpdate() {
		
		$database						= &JFactory::getDBO();
		
		// get affiliate's id
		
		$affiliateID					= JRequest::getVar("affiliate_id", 						"");
		
		// get password form
		
		$password						= array();
		
		$password["new"] 				= JRequest::getString("newpassword", 		"", 	"post");
		
		$password["verify"] 			= JRequest::getString("verifypassword", 	"", 	"post");
		
		// validate form input
		
		if (!$password["new"]) {
			
			vmError(JText::_("PROVIDE_PASSWORD"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$password["verify"]) {
			
			vmError(JText::_("RETYPE_PASSWORD"));
			
			$this->display();
			
			return false;
			
		}
		
		if ($password["new"] != $password["verify"]) {
			
			vmError(JText::_("PASSWORDS_DIFFER"));
			
			$this->display();
			
			return false;
			
		}
		
		// update password
		
		$query 				= "UPDATE #__vm_affiliate SET `password` = '" . md5($password["new"]) . "' WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// show the confirmation message
		
		vmInfo(JText::_("AFFILIATE_UPDATED"));
		
		$this->display();
		
		return true;
		
	}
	
	/**
	 * Method to save affiliate's preferences, more specifically its payment method
	 */
	 
	function affiliatePreferencesUpdate() {
		
		$database			= &JFactory::getDBO();
		
		// get affiliate's id and chosen payment method
				
		$paymentMethod 		= JRequest::getVar('paymentmethod', '');
		
		$affiliateID		= JRequest::getVar('affiliate_id',	'');
		
		// validate payment method
		
		if (!$paymentMethod) {
			
			vmError(JText::_("PROVIDE_PAYMENT_METHOD"));
			
			$this->display();
			
			return false;
			
		}
		
		// get existing payment details
		
		$paymentDetails		= array();
		
		$query				= "SELECT `field_id` FROM #__vm_affiliate_payment_details WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$database->setQuery($query);
		
		$setPaymentDetails	= $database->loadResultArray();
		
		// get new payment details, validate them, and prepare their query
		
		foreach ($_POST as $key => $value) {
			
			if (stristr($key, "payment-field")) {
				
				list( , , $paymentFieldID)			= explode("-", $key);
				
				$paymentValue 						= JRequest::getVar($key, '');
				
				// paypal method validation
				
				if ($paymentMethod == '1' && $paymentFieldID == '1' && !$paymentValue) {
					
					vmError(JText::_("PROVIDE_EMAIL_ADDRESS"));
			
					$this->display();
					
					return false;
			
				}
				
				// prepare the payment field query
				
				$paymentDetails[]					= !in_array($paymentFieldID, $setPaymentDetails) ? 
				
													  "INSERT INTO #__vm_affiliate_payment_details VALUES ('" . $affiliateID . "', '" . 
				
													  $paymentFieldID . "', '" . $paymentValue . "')" :
													  
													  "UPDATE #__vm_affiliate_payment_details SET `field_value` = '" . $paymentValue . "' WHERE `affiliate_id` = '" . 
													  
													  $affiliateID . "' AND `field_id` = '" . $paymentFieldID . "'";
				
			}
			
		}
		
		// update the affiliate's payment method and its details
		
		$query = "UPDATE #__vm_affiliate SET `method` = '" . $paymentMethod . "' WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		foreach ($paymentDetails as $paymentField) {
			
			$database->setQuery($paymentField);
			
			$database->query();
			
		}
		
		// show confirmation message
		
		vmInfo(JText::_("PAYMENT_INFORMATION_UPDATED"));
		
		$this->display();
		
		return true;

	}
	
	/**
	 * Method to link an affiliate to a site user
	 */

	function affiliateLink() {
		
		$database			= &JFactory::getDBO();
		
		// get affiliate's id
		
		$affiliateID		= JRequest::getVar("affiliate_id", 	"");
			
		// get input
		
		$username			= JRequest::getVar("username", 		"");
		
		$password			= JRequest::getVar("password", 		"");
		
		// validate it
		
		if (!$username) {
			
			vmError(JText::_("PROVIDE_USERNAME"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$password) {
			
			vmError(JText::_("PROVIDE_PASSWORD"));
			
			$this->display();
			
			return false;
			
		}
		
		// check if such an account exists in joomla
		
		$query				= "SELECT `password` FROM #__users WHERE `username` = '" . $username . "'";
		
		$database->setQuery($query);
		
		$saltedPassword		= $database->loadResult();
		
		// no such account exists
		
		if (empty($saltedPassword)) {
			
			vmError(JText::_("E_LOGIN_AUTHENTICATE"));
			
			$this->display();
			
			return false;
			
		}
		
		// check password
		
		list( , $salt)	= explode(":", $saltedPassword);
		
		$password 		= md5($password . $salt) . ":" . $salt;
		
		// password is incorrect
		
		if ($password != $saltedPassword) {
			
			vmError(JText::_("INCORRECT_PASSWORD"));
			
			$this->display();
			
			return false;
			
		}
			
		// check if another affiliate is already linked to that user
		
		$query 			= "SELECT `affiliate_id` FROM #__vm_affiliate WHERE `linkedto` = '" . $username . "'";
		
		$database->setQuery($query);
		
		$alreadyLinked 	= $database->loadResult();
		
		// another affiliate is linked to that user
		
		if ($alreadyLinked) {
			
			vmError(JText::_("ALREADY_LINKED"));
			
			$this->display();
			
			return false;
			
		}
		
		// link affiliate to site user
		
		$query = "UPDATE #__vm_affiliate SET `linkedto` = '" . $username . "' WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// show confirmation message
		
		vmInfo(JText::_("LINKING_SUCCESSFUL"));
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
	/** 
	 * Method to unlink an affiliate from a site user
	 */
	
	function affiliateUnlink() {
		
		$database			= &JFactory::getDBO();
		
		// get affiliate's id
		
		$affiliateID		= JRequest::getVar("affiliate_id", "");
		
		// reset linking
				
		$query 				= "UPDATE #__vm_affiliate SET `linkedto` = '' WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// show confirmation message
		
		vmInfo(JText::_("UNLINKED"));
		
		// confirm the operation
		
		$this->display();
		
		return true;
			
	}
	
}