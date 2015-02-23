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

class VirtuemartControllerVma_payment_methods extends VmController {

    /**
     * Method to display the view
     */

    public function __construct() {

        parent::__construct();

    }
	
	/**
	 * Method to save a payment method and its fields
	 */

	function save() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$database		= JFactory::getDBO();
		
		$methodID		= JRequest::getVar("method_id",	"");
		
		$methodName		= JRequest::getVar("name",		"");
		
		$newFields		= JRequest::getVar("fields",	array());
		
		$updatedFields	= array();
		
		$removedFields	= array();
		
		// get previous payment method's fields

		if ($methodID) {
			
			$query			= "SELECT `field_id` FROM #__vm_affiliate_method_fields WHERE `method_id` = '" . $methodID . "'";
			
			$database->setQuery($query);
			
			$previousFields = $database->loadObjectList();

		}
		
		// get filtered updated fields
		
		foreach ($_POST as $key => $value) {
			
			if (stristr($key, "field") && $key != "fields") {
				
				$fieldID					= str_replace("field", "", $key);
				
				$updatedFields[$fieldID]	= JRequest::getVar($key,	"");
				
			}
			
		}
		
		// make sure the payment method's name isn't empty
		
		if (!$methodName) {
			
			vmError(JText::_("PROVIDE_PAYMENT_METHOD_NAME"));
			
			$this->display();
			
			return false;
			
		}
		
		// add the payment method, if it doesn't exist
		
		if (!$methodID) {
			
			$query			= "INSERT INTO #__vm_affiliate_methods VALUES ('', '" . $methodName . "', '1', 'Manual', '0')";
			
			$database->setQuery($query);
			
			$database->query();
			
			$methodID		= $database->insertid();
			
		}
		
		// update the payment method's name
		
		else {
			
			$query			= "UPDATE #__vm_affiliate_methods SET `method_name` = '" . $methodName . "' WHERE `method_id` = '" . $methodID . "'";
			
			$database->setQuery($query);
			
			$database->query();
			
		}
		
		// insert the new fields
		
		foreach ($newFields as $newField) {
			
			if ($newField) {
				
				$query		= "INSERT INTO #__vm_affiliate_method_fields VALUES ('', '" . $methodID . "', '" . $newField . "')";
				
				$database->setQuery($query);
				
				$database->query();
			
			}
			
		}
		
		// update the updated fields
		
		foreach ($updatedFields as $fieldID => $fieldName) {
			
			// update the field name
			
			if ($fieldName) {
				
				$query				= "UPDATE #__vm_affiliate_method_fields SET `field_name` = '" . $fieldName . "' WHERE `field_id` = '" . $fieldID . "'";
				
				$database->setQuery($query);
				
				$database->query();
				
			}
			
			// name is empty, so trash it
			
			else {
				
				$removedFields[]	= $fieldID;
				
			}
			
		}

		// get removed fields

		if (isset($previousFields)) {
			
			foreach ($previousFields as $previousField) {

				if (!isset($updatedFields[$previousField->field_id])) {
					
					$removedFields[] = $previousField->field_id;
					
				}
				
			}
			
		}

		// remove all required fields

		foreach ($removedFields as $removedField) {

			// make sure the field isn't currently in use
			
			$query		= "SELECT affiliate.`affiliate_id` FROM #__vm_affiliate affiliate, #__vm_affiliate_payment_details details " 	. 
			
						  "WHERE details.`affiliate_id` = affiliate.`affiliate_id` AND details.`field_value` != '' " 					. 
						  
						  "AND affiliate.`method` = '" . $methodID . "' AND details.`field_id` = '" . $removedField . "'";
							  
			$database->setQuery($query);
			
			$fieldInUse = $database->loadResult();

			// if it isn't, remove it
			
			if (!$fieldInUse) {
				
				$query	= "DELETE FROM #__vm_affiliate_method_fields WHERE `field_id` = '" . $removedField . "'";
				
				$database->setQuery($query);
				
				$database->query();
				
			}
			
			// otherwise, issue an error
			
			else {
				
				vmError(JText::_("PAYMENT_METHOD_IN_USE"));
				
			}
			
		}
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
	/**
	 * Method to toggle a payment method's status between enabled and disabled
	 */

	function toggle() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$database		= &JFactory::getDBO();
		
		$id				= &JRequest::getVar("id");
		
		$type			= &JRequest::getVar("type");
		
		$published		= &JRequest::getVar("published");
		
		// perform the toggle query
		
		$query			= "UPDATE #__vm_affiliate_methods SET `method_enabled` = 1 - method_enabled WHERE `method_id` = '" . $id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// show the confirmation message
			
		vmInfo($published ? JText::_("PAYMENT_METHOD_DISABLED")	: JText::_("PAYMENT_METHOD_ENABLED"));
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
	/**
	 * Method to delete a payment method
	 */

	function delete() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$id				= &JRequest::getVar("id");
		
		$type			= &JRequest::getVar("type");
		
		$database		= &JFactory::getDBO();
		
		// validate payment method removal
		
		if ($type == "paymentmethod") {
			
			// prevent removal of the paypal payment method
			
			if ($id == 1) {
				
				vmInfo(JText::_("PAYMENT_METHOD_BUILT_IN"));
				
				$this->display();
				
				return false;
				
			}
			
			// prevent removal of payment methods in use
			
			$query			= "SELECT COUNT(*) FROM #__vm_affiliate WHERE `method` = '" . $id . "'";
			
			$database->setQuery($query);
			
			$inUse			= $database->loadResult();
			
			if ($inUse) {
				
				vmError(JText::_("PAYMENT_METHOD_IN_USE"));
				
				$this->display();
				
				return false;
				
			}
		
		}
		
		// perform the toggle query
		
		$query			= "DELETE FROM #__vm_affiliate_methods WHERE `method_id` = '" . $id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// show the confirmation message
			
		vmInfo(JText::_("PAYMENT_METHOD_REMOVED"));
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
}