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
 
class VirtueMartModelVma_category_ads extends VmModel {

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
	
		// get language
		
		$lc			= $vmaHelper->getLanguageTag();
		
		// get search condition
		
		$searchIn 	= array("category_name");
		
		$search		= JRequest::getWord('search');
		
		$condition	= "categories.`published` = '1' AND products.`virtuemart_product_id` = xref.`virtuemart_product_id` AND " . 
		
					  "categories.`virtuemart_category_id` = xref.`virtuemart_category_id` AND products.`published` = '1' AND " . 
					  
					  "medias.`file_url` != '' ";
			  
		$condition	.= $search ? " AND (" . $vmaHelper->prepareSearch($searchIn, $search, "details") . ")" : NULL;
		
		$groupBy	= " GROUP BY categories.`virtuemart_category_id` ORDER BY details.`category_name` ASC ";
		
		// build query
					
		$join		= "LEFT JOIN #__vm_affiliate_links_categories categoryAds ON categories.`virtuemart_category_id` = categoryAds.`category_id` " 			. 
		
					  "LEFT JOIN #__virtuemart_categories_" . $lc . " details ON categories.`virtuemart_category_id` = details.`virtuemart_category_id` " 	. 
		
					  "LEFT JOIN #__virtuemart_category_medias cmedias ON cmedias.`virtuemart_category_id` = categories.`virtuemart_category_id` "			. 
					  
					  "LEFT JOIN #__virtuemart_medias medias ON medias.`virtuemart_media_id` = cmedias.`virtuemart_media_id`, " 				   			. 

			  		  "#__virtuemart_products products, #__virtuemart_product_categories xref";
			
		$query		= !$total ? 
			
				 	  "SELECT categories.`virtuemart_category_id` AS categoryID, details.`category_name` AS categoryName, categoryAds.`published` AS categoryPublished, "		. 

			  		  "COUNT(DISTINCT xref.`virtuemart_product_id`) AS productsNo FROM #__virtuemart_categories categories " . $join . " WHERE " . $condition . $groupBy :
				 
					  "SELECT COUNT(DISTINCT categories.`virtuemart_category_id`) FROM #__virtuemart_categories categories " . $join . " WHERE " . $condition;
		
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