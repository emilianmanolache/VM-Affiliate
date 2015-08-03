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

// load the model framework

jimport( 'joomla.application.component.model');

if (!class_exists('VmModel')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');

/**
 * Model for VM Affiliate
 */
 
class VirtueMartModelVma_email_affiliates extends VmModel {

	/**
	 * Model constructor
	 */
	 
	function __construct() {
		
		parent::__construct();

	}

	/**
	 * Method to get affiliates to e-mail
	 */

	function getAffiliates() {
		
		$database			= JFactory::getDBO();
		
		$query				= "SELECT `affiliate_id`, `fname`, `lname`, `mail` FROM #__vm_affiliate WHERE `blocked` = '0'";

		$database->setQuery($query);

		$affiliates			= $database->loadObjectList();
		
		return $affiliates;
		
	}
	
}

// no closing tag