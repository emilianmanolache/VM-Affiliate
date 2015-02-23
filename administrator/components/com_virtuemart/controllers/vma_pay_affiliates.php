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

if (!class_exists('VmController')) require(VMPATH_ADMIN . DS . 'helpers' . DS . 'vmcontroller.php');

/**
 * VM Affiliate backend controller
 */

class VirtuemartControllerVma_pay_affiliates extends VmController {

    /**
     * Method to display the view
     */

    public function __construct() {

        parent::__construct();

    }
	 
	/**
	 * Method that logs an affiliate payment and marks its records as paid
	 */
	
	function pay() {
		
		global $vmaHelper;
		
		$database		= &JFactory::getDBO();
		
		// initiate required variables
		
		$affiliateID 	= JRequest::getVar("affiliate_id", "");
		
		$query			= "SELECT * FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$database->setQuery($query);
		
		$affiliate		= $database->loadObject();
		
		$query			= "SELECT `method_name` FROM #__vm_affiliate_methods WHERE `method_id` = '" . $affiliate->method . "'";
		
		$database->setQuery($query);
		
		$paymentName	= $database->loadResult();
		
		// validate information
		
		if (!$affiliateID || $affiliate->commissions == 0) {
			
			$this->display();
			
			return false;
			
		}
		
		// log the payment
		
		$query			= "INSERT INTO #__vm_affiliate_payments VALUES ('', '" . $affiliate->affiliate_id . "', '" . $affiliate->username . 
		
						  "', '" . $paymentName . "', '" . $affiliate->commissions . "', '" . date("Y-m-d") . "', 'C', '')";
						  
		$database->setQuery($query);
		
		$database->query();
		
		// clear affiliate's balance
		
		$query			= "UPDATE #__vm_affiliate SET `commissions` = '0.0000' WHERE `affiliate_id` = '" 	. $affiliate->affiliate_id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// mark clicks as paid
		
		$query			= "UPDATE #__vm_affiliate_clicks SET `paid` = '1' WHERE `AffiliateID` = '" 			. $affiliate->affiliate_id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// mark orders as paid
		
		$query			= "UPDATE #__vm_affiliate_orders SET `paid` = '1' WHERE `affiliate_id` = '" 		. $affiliate->affiliate_id . "' " . 
		
						  "AND " . $vmaHelper->buildStatusesCondition("pending", "AND", "!=", "order_status") . " " .
						  
						  "AND " . $vmaHelper->buildStatusesCondition("cancelled", "AND", "!=", "order_status");
		
		$database->setQuery($query);
		
		$database->query();
		
		// show the confirmation message
		
		vmInfo(JText::_("PAYMENT_SUCCESSFUL"));
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
}