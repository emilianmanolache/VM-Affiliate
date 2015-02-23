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

class VirtuemartControllerVma_sizegroups extends VmController {

    /**
     * Method to display the view
     */

    public function __construct() {

        parent::__construct();

    }
	
	/**
	 * Method to save a size group
	 */
	 
	function save() {
		
		global $vmaHelper;
		
		$database						= JFactory::getDBO();
		
		// determine type
		
		$type							= JRequest::getVar("type",						"");
		
		// get size group's id

		$sizeGroupID					= JRequest::getVar("size_group_id");
		
		if (is_array($sizeGroupID)) {
			
			list($sizeGroupID) 			= $sizeGroupID;
			
		}

		// get size group form

		$sizeGroup						= array();

		$sizeGroup["name"] 				= JRequest::getVar("name", 		"", 	"post");

		if (is_array($sizeGroup["name"])) {
			
			list($sizeGroup["name"]) 	= $sizeGroup["name"];
			
		}
		
		$sizeGroup["width"] 			= JRequest::getVar("width", 		"", 	"post");

		if (is_array($sizeGroup["width"])) {
			
			list($sizeGroup["width"]) 	= $sizeGroup["width"];
			
		}

		$sizeGroup["height"] 			= JRequest::getVar("height", 	"", 	"post");

		if (is_array($sizeGroup["height"])) {
			
			list($sizeGroup["height"]) 	= $sizeGroup["height"];
			
		}
		
		// check if such a size group doesn't already exist
		
		$query	= "SELECT * FROM #__vm_affiliate_size_groups WHERE `width` = '" . $sizeGroup["width"] . "' AND `height` = '" . $sizeGroup["height"] . "'";
		
		$database->setQuery($query);
		
		$result = $database->loadResult();
		
		if ($result) {
			
			$this->display();
			
			return false;
			
		}
		
		// determine query
		
		$query	= $sizeGroupID ? 
				  
				  "UPDATE #__vm_affiliate_size_groups SET `name` = '" . $sizeGroup["name"] . "', `width` = '" . $sizeGroup["width"] . "', `height` = '" . 
				  
				  $sizeGroup["height"] . "' WHERE `size_group_id` = '" . $sizeGroupID . "'" :
				  
				  "INSERT INTO #__vm_affiliate_size_groups VALUES ('', '" . $sizeGroup["width"] . "', '" . $sizeGroup["height"] . "', '" . $sizeGroup["name"] . "')";
		
		$database->setQuery($query);
		
		$database->query();
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
	/**
	 * Method to delete a size group
	 */

	function delete() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$database		= &JFactory::getDBO();
		
		$id				= &JRequest::getVar("id");
		
		$type			= &JRequest::getVar("type");
		
		// perform the delete query
		
		$query			= "DELETE FROM #__vm_affiliate_size_groups WHERE `size_group_id` = '" . $id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
}