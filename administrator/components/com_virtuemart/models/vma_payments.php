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
 
class VirtueMartModelVma_payments extends VmModel {

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
	
		// get parameters
		
		$confirmed		= &JRequest::getVar("confirmed",	0);

		$affiliateID	= &JRequest::getVar("affiliate_id",	0);

		// get search condition
		
		$searchIn 		= array("affiliate_id", "method", "amount", "date", "status");
		
		$search			= JRequest::getWord('search');
		
		$condition		= "1 = 1";
		
		$condition	   .= $search 		? " AND " . $vmaHelper->prepareSearch($searchIn, $search, "payments")		: NULL;
		
		$condition	   .= $affiliateID 	? " AND payments.`affiliate_id` 	= '" . $affiliateID . "' "				: NULL;

		$condition	   .= $confirmed	? " AND payments.`status`			= 'C' "									: NULL;
		
		$groupBy		= " ORDER BY payments.`payment_id` DESC ";
		
		// build query
		
		$join			= "LEFT JOIN #__vm_affiliate affiliates ON payments.`affiliate_id` 	= affiliates.`affiliate_id`";

		$query			= !$total ? 
		
						  "SELECT payments.*, CONCAT(affiliates.`fname`, ' ', affiliates.`lname`) AS name " . 

				  		  "FROM #__vm_affiliate_payments payments " . 
						  
						  $join . " WHERE " . $condition . $groupBy :

						  "SELECT COUNT(*) FROM #__vm_affiliate_payments payments " . $join . " WHERE " . $condition;
		
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
	
}

// no closing tag