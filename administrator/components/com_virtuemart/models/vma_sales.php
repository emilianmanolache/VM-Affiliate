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
 
class VirtueMartModelVma_sales extends VmModel {

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
		
		$paid					= &JRequest::getVar("paid",			1);

		$confirmed				= &JRequest::getVar("confirmed",	0);

		$affiliateID			= &JRequest::getVar("affiliate_id",	0);
		
		$withTax				= $vmaHelper->getVATSetting();

		// define statuses

		$pendingStatuses		= $vmaHelper->_pendingStatuses;

		$unconfirmedStatuses 	= $vmaHelper->_cancelledStatuses;
				
		$confirmedStatuses		= $vmaHelper->_confirmedStatuses;

		// get search condition
		
		$searchIn 		= array("affiliate_id", "order_id", "order_status", "date");
		
		$search			= JRequest::getWord('search');
		
		$condition		= "1 = 1";
		
		$condition	   .= $search 		? " AND " . $vmaHelper->prepareSearch($searchIn, $search, "orders")		: NULL;
		
		$condition	   .= !$paid 		? " AND orders.`paid` 			= '0' " 								: NULL;

		$condition	   .= $affiliateID 	? " AND orders.`affiliate_id` 	= '" . $affiliateID . "' "				: NULL;
		
		$condition	   .= $confirmed	? " AND (" . $vmaHelper->buildStatusesCondition("confirmed", "OR", "=", "order_status", NULL, "orders") . ") " : NULL;
		
		$groupBy		= " ORDER BY orders.`order_id` DESC ";
		
		// build query
		
		$join			= "LEFT JOIN #__vm_affiliate affiliates 		ON orders.`affiliate_id` 				= affiliates.`affiliate_id` " . 

				  		  "LEFT JOIN #__virtuemart_orders vmorders 		ON vmorders.`virtuemart_order_id` 		= orders.`order_id`";

		$query			= !$total ? 
		
						  "SELECT orders.*, CONCAT(affiliates.`fname`, ' ', affiliates.`lname`) AS name, " . 
						  
						  "(vmorders.`order_salesPrice` - vmorders.`coupon_discount`) " . 

				  		  "AS order_subtotal, vmorders.`order_total` FROM #__vm_affiliate_orders orders " . 
						  
						  $join . " WHERE " . $condition . $groupBy :

						  "SELECT COUNT(*) FROM #__vm_affiliate_orders orders " . $join . " WHERE " . $condition;
		
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