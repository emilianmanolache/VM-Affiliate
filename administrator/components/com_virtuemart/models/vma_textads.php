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
 
class VirtueMartModelVma_textads extends VmModel {

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
		
		$searchIn 	= array("title", "content", "width", "height");
		
		$search		= JRequest::getWord('search');
		
		$condition	= $search ? " WHERE " . $vmaHelper->prepareSearch($searchIn, $search, "textads") : NULL;
		
		// build query
		
		$join		= "LEFT JOIN #__vm_affiliate_size_groups sizegroups ON textads.`width` = sizegroups.`width` AND textads.`height` = sizegroups.`height` ";
			
		$query		= !$total ? 
			
				 	  "SELECT textads.*, sizegroups.`name` AS sizegroup FROM #__vm_affiliate_textads textads " 	. $join . $condition :
				 
					  "SELECT COUNT(*) FROM #__vm_affiliate_textads textads " 									. $join	. $condition;
		
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
		
		$pagination		= new JPagination($total, $limitstart, $limit);
		
		// return the pagination
		
		return $pagination;
		
	}
	
}

// no closing tag