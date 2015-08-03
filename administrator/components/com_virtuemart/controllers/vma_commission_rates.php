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

class VirtuemartControllerVma_commission_rates extends JControllerLegacy {
	
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
	 * Method to save the commission rates (either general, from a specific tier, or for a specific affiliate)
	 */
	 
	function update() {
		
		global $vmaHelper, $vmaSettings;
		
		// initialize required variables
		
		$database					= JFactory::getDBO();
		
		$affiliateID				= JRequest::getVar("affiliate_id",				"");
		
		$tier						= JRequest::getVar("tier",						"1");
		
		if ($affiliateID) {
		
			$useDefaults			= JRequest::getVar("use_defaults",				"0");
				
		}
		
		// get newly set commission rates
		
		$per_click_fixed			= JRequest::getVar("per_click_fixed",			"0");
		
		$per_unique_click_fixed 	= JRequest::getVar("per_unique_click_fixed",	"0");
		
		$per_sale_fixed				= JRequest::getVar("per_sale_fixed",			"0");
		
		$per_sale_percentage		= JRequest::getVar("per_sale_percentage",		"0");
		
		// get newly set discount rates, if applicable
		
		if ($vmaSettings->offline_tracking && $vmaSettings->offline_type == "3" && $tier == 1) {
			
			$discount_type			= JRequest::getVar("discount_type",				"1");
			
			$discount_amount		= JRequest::getVar("discount_amount",			"0");
			
		}
		
		// update the defaults setting
		
		if ($affiliateID) {
			
			$query					= "UPDATE #__vm_affiliate SET `use_defaults` = '" . $useDefaults . "' WHERE `affiliate_id` = '" . $affiliateID . "'";
			
			$database->setQuery($query);
			
			$database->query();
		
		}
		
		// update the commission rates
		
		if (!$affiliateID || !$useDefaults) {
			
			$query					= "UPDATE #__vm_affiliate" 	. (!$affiliateID ? "_rates" : NULL) 	. " SET " 							. 
			
									  "`per_click_fixed` = '" 	. $per_click_fixed 						. "', `per_unique_click_fixed` = '" . $per_unique_click_fixed 	. "', " .
									  
									  "`per_sale_fixed` = '" 	. $per_sale_fixed 						. "', `per_sale_percentage` = '" 	. $per_sale_percentage 		. "' " 	.
									  
									  "WHERE " 					. ($affiliateID ? "`affiliate_id` = '" 	. $affiliateID . "'" : "`rate` = '" . $tier . "'");
									  
			$database->setQuery($query);
			
			$database->query();
			
		}
		
		// update the discount rate
		
		if ($vmaSettings->offline_tracking && $vmaSettings->offline_type == "3" && $tier == 1 && (!$affiliateID || !$useDefaults)) {
			
			$query					= "UPDATE #__vm_affiliate" 	. (!$affiliateID ? "_settings" : NULL) 	. " SET " 							. 
		
									  "`discount_type` = '" 	. $discount_type 						. "', `discount_amount` = '"		. $discount_amount 	. "' " .
									  
									  "WHERE " 					. ($affiliateID ? "`affiliate_id` = '" 	. $affiliateID 						. "'" : "`setting` = '1'");
								  
			$database->setQuery($query);
			
			$database->query();
			
		}
		
		// show the confirmation message
		
		vmInfo(JText::_("COMMISSION_RATES_SET"));
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
}