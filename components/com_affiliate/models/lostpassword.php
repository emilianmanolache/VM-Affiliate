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
 
class AffiliateModelLostpassword extends JModel {
	
	/**
	 * Method to validate a lost password form and return a filtered account details array
	 */
	 
	function validateLostPasswordForm() {
		
		global $vmaSettings, $ps_vma;
		
		// get the mainframe object
		
		$mainframe 						= JFactory::getApplication();
		
		// get lost password form fields
		
		$accountDetails					= array();
		
		$accountDetails["username"] 	= JRequest::getString("username", 	"", 	"post");
		
		$accountDetails["mail"] 		= JRequest::getString("mail", 		"", 	"post");
		
		// validate form input
		
		if (!$accountDetails["username"]) {
			
			$mainframe->redirect(JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=lostpassword"), false), JText::_("PROVIDE_USERNAME"), "error");
			
			return false;
			
		}
		
		if (!$accountDetails["mail"]) {
			
			$mainframe->redirect(JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=lostpassword"), false), JText::_("PROVIDE_EMAIL_ADDRESS"), "error");
			
			return false;
			
		}
		
		// return the filtered account details array
		
		return $accountDetails;
		
	}
	
}
