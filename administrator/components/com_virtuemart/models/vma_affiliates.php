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

if (!class_exists('VmModel')) require(VMPATH_ADMIN . DS . 'helpers' . DS . 'vmmodel.php');

/**
 * Model for VM Affiliate
 */
 
class VirtueMartModelVma_affiliates extends VmModel {

	/**
	 * Model constructor
	 */
	 
	function __construct() {
		
		parent::__construct();

	}
	
	/**
	 * Method to build the data query
	 */
	 
	function _buildQuery($total = false) {
		
		global $vmaHelper;
		
		// get search condition
		
		$searchIn	= array("fname", "lname", "username", "mail", "website", "street", "city", "state", "country", "zipcode", "phoneno", "taxssn");
		
		$search		= JRequest::getWord('search');
		
		$condition	= $search ? " WHERE " . $vmaHelper->prepareSearch($searchIn, $search) : NULL;
		
		// build query
			
		$query		= !$total ? 
			
				 	  "SELECT * FROM #__vm_affiliate" 			. $condition :
				 
					  "SELECT COUNT(*) FROM #__vm_affiliate"	. $condition;
		
		// return query
					  
		return $query;
		
	}
	
	/**
	 * Method to get the data
	 */
	 
	function getData() {

		// if data hasn't already been obtained, load it
		
		if (empty($this->_data)) {

            $query 			= $this->_buildQuery();

            $this->_data 	= $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 

        }

        return $this->_data;

	}
	
	/**
	 * Method to get the total
	 */

	function getTotal() {
		
		$database	= JFactory::getDBO();
		
		$query		= $this->_buildQuery(true);
		
		$database->setQuery($query);
		
		$total		= $database->loadResult();
		
		return $total;
		
	}
	
	/**
	 * Method to get pagination
	 */
	 
	function getPagination() {
		
		// get mainframe
		
		$mainframe		= &JFactory::getApplication();
		
		// import joomla pagination library
		
		jimport( 'joomla.html.pagination' );
		
		// get pagination variables
		
		$limit			= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		
		$limitstart 	= $mainframe->getUserStateFromRequest(JRequest::getWord('option') . JRequest::getWord('view') . '.limitstart', 'limitstart', 0, 'int');

		// set the state pagination variables
		
		$this->setState('limit', 		$limit);
		
		$this->setState('limitstart',	$limitstart);
		
		// get total
		
		$total			= $this->getTotal();
		
		// get pagination
		
		$pagination 	= new JPagination($total, $limitstart, $limit);
		
		// return the pagination
		
		return $pagination;
		
	}

	/**
	 * Method to get affiliates to be paid
	 */
	 
	function toBePaid() {
		
		global $vmaSettings;
		
		$database	= &JFactory::getDBO();
		
		$payQuery	= "SELECT COUNT(*) FROM #__vm_affiliate WHERE `commissions` >= '" . $vmaSettings->pay_balance . "' " . 
		
					  "AND `method` != '' AND `method` != 'N/A' AND `blocked` != '1'";

		$database->setQuery($payQuery);

		$toBePaid	= $database->loadResult();
		
		return $toBePaid;
		
	}
	
	/**
	 * Method to get an affiliate's data
	 */
	 
	function getAffiliate($affiliateID) {
		
		$database = &JFactory::getDBO();
		
		$query			= "SELECT * FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
	
		$database->setQuery($query);
		
		$affiliate		= $database->loadObject();
		
		return $affiliate;
		
	}
	
}

// no closing tag