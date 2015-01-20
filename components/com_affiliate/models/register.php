<?php

/**
 * @package   VM Affiliate
 * @version   4.5.0 May 2011
 * @author    Globacide Solutions http://www.globacide.com
 * @copyright Copyright (C) 2006 - 2011 Globacide Solutions
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access

defined( '_JEXEC' ) or die( 'Direct access to this location is not allowed.' );

// import the joomla component model application

jimport( 'joomla.application.component.model' );
 
/**
 * Model file for VM Affiliate's Affiliate Panel component
 */
 
class AffiliateModelRegister extends JModel {
	
	/**
	 * Method to validate a registration form and return a filtered registration details array
	 */
	 
	function validateRegistration() {
		
		global $vmaSettings, $ps_vma;
		
		// initialize variables
		
		$mainframe 						= JFactory::getApplication();
		
		$redirectionLink				= JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=register"), false);
		
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
		
		$newAffiliate["agreed_terms"] 	= JRequest::getBool("agreed_terms", false, 	"post");
		
		// validate form input
		
		if (!$newAffiliate["username"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_USERNAME"), 		"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["password"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_PASSWORD"), 		"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["vpassword"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("RETYPE_PASSWORD"), 		"error");
			
			return false;
			
		}
		
		if ($newAffiliate["password"] != $newAffiliate["vpassword"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PASSWORDS_DIFFER"), 		"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["mail"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_EMAIL_ADDRESS"), 	"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["fname"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_FIRST_NAME"), 		"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["lname"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_LAST_NAME"), 		"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["street"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_ADDRESS"), 		"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["city"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_CITY"), 			"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["country"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_COUNTRY"), 		"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["zipcode"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_ZIPCODE"), 		"error");
			
			return false;
			
		}
		
		if ($vmaSettings->must_agree && !$newAffiliate["agreed_terms"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("YOU_MUST_AGREE"), 			"error");
			
			return false;
			
		}
		
		// get database object
		
		$database	= &JFactory::getDBO();
		
		// check if either the username or e-mail address are already registered

		$query 		= "SELECT `username`, `mail` FROM #__vm_affiliate WHERE `username` = '" . $newAffiliate['username'] . "' OR `mail` = '" . $newAffiliate['mail'] . "'";
		
		$database->setQuery( $query );
		
		$info		= $database->loadAssoc();
		
		if ($info["username"] == $newAffiliate['username']) {
			
			$mainframe->redirect($redirectionLink, JText::_("USERNAME_ALREADY_EXISTS"), "error");
			
			return false;
			
		}
		
		else if ($info["mail"] == $newAffiliate['mail']) {
			
			$mainframe->redirect($redirectionLink, JText::_("WARNREG_EMAIL_INUSE"), 	"error");
			
			return false;
			
		}
		
		// return the filtered registration array
		
		return $newAffiliate;
		
	}
	
}
