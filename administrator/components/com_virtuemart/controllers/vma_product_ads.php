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

class VirtuemartControllerVma_product_ads extends VmController {

    /**
     * Method to display the view
     */

    public function __construct() {

        parent::__construct();

    }
	
	/**
	 * Mirror function to publishing a product ad
	 */

	function publish() {
		
		JRequest::setVar("operation", "publish");
		
		$this->toggle();
		
	}
	
	/**
	 * Mirror function to unpublishing a product ad
	 */

	function unpublish() {
		
		JRequest::setVar("operation", "unpublish");
		
		$this->toggle();
		
	}
	
	/**
	 * Method to toggle a product ad's status between published and unpublished
	 */
	 
	function toggle() {
		
		// initiate required variables
		
		$database		= &JFactory::getDBO();
		
		$id				= &JRequest::getVar("id");
		
		$id				= &JRequest::getVar("cid", $id);
		
		$type			= &JRequest::getVar("type");
		
		$operation		= &JRequest::getVar("operation",		"toggle");

		$id				= is_array($id) ? $id : array($id);
		
		foreach ($id as $item) {
			
			// determine current ad status
			
			$query			= "SELECT `published` FROM #__vm_affiliate_links WHERE `product_id` = '" . $item . "'";
			
			$database->setQuery($query);
			
			$published		= $database->loadResult();
			
			// determine new status
			
			$newStatus		= $operation == "toggle" ? ($published == NULL ? 0 : ($published ? 0 : 1)) : ($operation == "publish" ? 1 : 0);
			
			// perform the toggle query
			
			$query			= $published == NULL ? 
			
							  "INSERT INTO #__vm_affiliate_links VALUES ('', '" . $item . "', '" . $newStatus . "')" : 
							  
							  "UPDATE #__vm_affiliate_links SET `published` = '" . ($newStatus) . "' WHERE `product_id` = '" . $item . "'";
			
			$database->setQuery($query);
			
			$database->query();
			
		}
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
}