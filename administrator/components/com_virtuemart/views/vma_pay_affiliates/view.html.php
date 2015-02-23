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

// load the view framework

if (!class_exists('VmViewAdmin')) require(VMPATH_ADMIN . DS . 'helpers' . DS . 'vmviewadmin.php');

/**
 * View file for the VM Affiliate backend
 */
 
class VirtuemartViewVma_pay_affiliates extends VmViewAdmin {

	/**
	 * Display the view
	 */
	
	function display($tpl = null) {
		
		global $vmaHelper;
		
		$model 		= VmModel::getModel('vma_pay_affiliates');
			
		$this->loadHelper('adminui');

		$this->loadHelper('shopFunctions');
		
		$this->loadHelper('html');
		
		$task		= &JRequest::getVar("task");
		
		if ($task == "payAffiliate") {
			
			$tpl				= "pay";
			
			$affiliateID		= &JRequest::getVar("affiliate_id");
			
			$affiliate			= $model->getAffiliate($affiliateID);
			
			$this->assignRef('affiliate', 			$affiliate);
			
		}
		
		else {
			
			$search		= &JRequest::getVar("search");

            $this->lists["search"] = $search;

			$pagination = $model->getPagination();
			
			$affiliates	= $model->getData();
			
			$this->assignRef('search', 		$search);
			
			$this->assignRef('pagination',	$pagination);
			
			$this->assignRef('affiliates',	$affiliates);
		
		}
		
		parent::display($tpl);
		
	}
	
}

// pure php no tag