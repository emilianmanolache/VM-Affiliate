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
 
// import joomla component view application

jimport( 'joomla.application.component.view');
 
/**
 * View file for VM Affiliate's Affiliate Panel component
 */
 
class AffiliateViewPanel extends JViewLegacy {
	
	/**
     * Method to display the template
     */
	 
    function display($tpl = null) {
		
		global $vmaSettings, $vmaHelper;
		
		// get application
		
		$mainframe		= &JFactory::getApplication();
		
		// only load footer
		
		if ($tpl == "footer") {
			
			parent::display($tpl);
			
			return true;
			
		}
		
		// initialize variables
			
		$document		= &JFactory::getDocument();
		
		$database		= &JFactory::getDBO();
		
		$user			= &JFactory::getUser();
		
		$session 		= &JFactory::getSession();
		
		$pathway		= &$mainframe->getPathway();

		$menu   		= &JSite::getMenu();
		
		$item   		= $menu->getActive();
		
		$params			= isset($item->id) ? $menu->getParams($item->id): $menu->getParams(0);
			
		$model 			= &$this->getModel();

		$subview 		= JRequest::getVar("subview", "home");
		
		$affiliate		= $session->get("affiliate");

        $items 			= &$this->get('Data'); 
		     
        $pagination		= &$this->get('Pagination');
		
		$categoryID		= JRequest::getVar("category_id", "");
		
		$listType		= array("banners", "traffic", "sales", "payments");
		
		$pageTitles		= array("summary" 		=> JText::_("SUMMARY"),
		
								"banners" 		=> JText::_("BANNERS_AND_ADS"),
								
								"emails"		=> JText::_("EMAIL_CAMPAIGN"),
								
								"traffic"		=> JText::_("TRAFFIC"),
								
								"sales"			=> JText::_("SALES"),
								
								"payments"		=> JText::_("PAYMENTS"),
								
								"statistics"	=> JText::_("STATISTICS"),
								
								"details"		=> JText::_("EDIT_DETAILS"),
								
								"preferences"	=> JText::_("PREFERENCES"));
		
		$this->_addPath('template', $this->_basePath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . "footer" . DIRECTORY_SEPARATOR . 'tmpl');
		
		// set page titles and pathway
		
		$params->def( 'show_page_title', 1 );
		
		$pathwayURL	 	= "index.php?option=com_affiliate&view=panel";
		
		$title 			=  !$params->get( 'page_title' ) ? JText::_("AFFILIATE_PANEL") : $params->get( 'page_title' );
		
		if ($subview == $tpl && array_key_exists($subview, $pageTitles)) {
			
			$pathway->addItem( str_replace("&amp", "&", $pageTitles[$subview]), JRoute::_($vmaHelper->vmaRoute($pathwayURL . "&subview=" . $subview)) );
			
		}
		
		// set summary subview specific variables
		
		if ($subview == $tpl && $tpl == "summary") {
			
			// load required variables
			
			$generalRates		= $vmaHelper->getCommissionRates($affiliate->affiliate_id);
			
			$formattedRates 	= $vmaHelper->getFormattedCommissionRates($affiliate->affiliate_id);
			
			$currentClicks		= $vmaHelper->getClicks($affiliate->affiliate_id, true, false);
			
			$currentUClicks		= $vmaHelper->getClicks($affiliate->affiliate_id, true, true);
			
			$overallClicks		= $vmaHelper->getClicks($affiliate->affiliate_id, false, false);
			
			$overallUClicks		= $vmaHelper->getClicks($affiliate->affiliate_id, false, true);
			
			$currentASales		= $vmaHelper->getSales($affiliate->affiliate_id, true, true);
			
			$overallASales		= $vmaHelper->getSales($affiliate->affiliate_id, false, true);
			
			$pendingSales		= $vmaHelper->getSales($affiliate->affiliate_id, false, false);
			
			$currentBalance		= $vmaHelper->formatAmount($affiliate->commissions);
			
			$overallBalance		= $vmaHelper->getOverallBalance($affiliate->affiliate_id, $affiliate->commissions);
			
			if ($vmaSettings->multi_tier) {
				
				$referredAffs	= $vmaHelper->getReferredAffiliates($affiliate->affiliate_id);
				
				$this->assignRef( 'referredAffs', 	$referredAffs );
				
			}
			
			if ($vmaSettings->offline_tracking && $vmaSettings->offline_type == 3) {
				
				$discountRate	= $vmaHelper->getDiscountRate($affiliate->affiliate_id);
				
				$formattedRate	= "-" . ($discountRate["discount_type"] == 1 ? $vmaHelper->formatAmount($discountRate["discount_amount"]) : $discountRate["discount_amount"] . "%");
				
				$this->assignRef( 'discountRate', 	$discountRate );
				
				$this->assignRef( 'formattedRate', 	$formattedRate );
				
			}
			
			// set the template parameters
			
			$this->assignRef( 'generalRates', 		$generalRates );
	
			$this->assignRef( 'formattedRates', 	$formattedRates );
			
			$this->assignRef( 'currentClicks', 		$currentClicks );
			
			$this->assignRef( 'currentUClicks', 	$currentUClicks );
			
			$this->assignRef( 'overallClicks', 		$overallClicks );
			
			$this->assignRef( 'overallUClicks', 	$overallUClicks );
			
			$this->assignRef( 'currentASales', 		$currentASales );
			
			$this->assignRef( 'overallASales', 		$overallASales );
			
			$this->assignRef( 'pendingSales', 		$pendingSales );
			
			$this->assignRef( 'currentBalance', 	$currentBalance );
			
			$this->assignRef( 'overallBalance', 	$overallBalance );
		
		}
		
		// set list type subview specific variables
		
		if ($subview == $tpl && in_array($subview, $listType)) {	
			
			// get variables
			
			$rows		= $model->processRows();

			$variable	= $subview;
			
			// assign them to template
			
			if ($tpl == "banners") {
				
				$section 	= &JRequest::getVar('section', 'banners');
				
				$variable 	= $section;
				
				$this->assignRef( "section", $section);
				
				// get the category name
				
				if ($categoryID) {
					
					$lc				= $vmaHelper->getLanguageTag();
					
					$query 			= "SELECT `category_name` FROM #__virtuemart_categories_" . $lc . " WHERE `virtuemart_category_id` = '" . $categoryID . "'";
					
					$database->setQuery($query);
					
					$categoryName 	= $database->loadResult();
					
					$this->assignRef( "categoryName", $categoryName);
					
				}
				
				// fix squeezebox issue on mootools 1.2
				
				$vmaHelper->fixSqueezeBox();
				
			}
			
			$this->assignRef( $variable, $rows);
	
		}
		
		// get statistics subview specific variables
		
		if ($subview == $tpl && $tpl == "statistics") {
			
			// get variables
			
			$type 		= &JRequest::getVar("type", "traffic");
			
			$period 	= &JRequest::getVar("period", "month");
			
			$request 	= $type . $period;

			$data		= $vmaHelper->getStatisticsData($request);
			
			$link1		= "index.php?option=com_affiliate&view=panel&subview=statistics&type=" 		. $type 	. "&period=";
			
			$link2		= "index.php?option=com_affiliate&view=panel&subview=statistics&period=" 	. $period 	. "&type=";
			
			// assign to template
			
			$this->assignRef( "data", 		$data );
			
			$this->assignRef( "type", 		$type );
			
			$this->assignRef( "period", 	$period );
			
			$this->assignRef( "request", 	$request );
			
			$this->assignRef( "link1", 		$link1 );
			
			$this->assignRef( "link2",		$link2 );
			
		}
		
		// get active section menus
		
		if ($subview == "banners" && $tpl == "banners_menu") {

			$activeAdsMenus			= $session->get("activeAdsMenus");

			$this->assignRef( 'activeAdsMenus', $activeAdsMenus );
			
		}
		
		// get active statistics menus
		
		if ($subview == "statistics" && $tpl == "statistics_menu") {
			
			$activeStatisticsMenus 	= $vmaHelper->getActiveStatsMenus();
			
			$this->assignRef( 'activeStatisticsMenus', $activeStatisticsMenus );
			
		}
		
		// get traffic subview's menu specific variables
		
		if ($subview == "traffic" && $tpl == "traffic_menu") {
			
			$paid					= &JRequest::getVar("paid",			1);

			$unique					= &JRequest::getVar("unique",		0);
			
			$this->assignRef( 'paid',	$paid );
			
			$this->assignRef( 'unique', $unique );
			
		}
		
		// get sales subview specific variables
		
		if ($subview == "sales") {
			
			$paid					= &JRequest::getVar("paid",			1);

			$confirmed				= &JRequest::getVar("confirmed",	0);
			
			$this->assignRef( 'paid',		$paid );
			
			$this->assignRef( 'confirmed',	$confirmed );
			
		}
		
		// get preferences subview specific variables
		
		if ($subview == $tpl && $tpl == "preferences") {
			
			// get payment methods
			
			$paymentMethods			= $vmaHelper->getPaymentMethods($affiliate->affiliate_id);
			
			$this->assignRef( "paymentMethods", $paymentMethods );
			
		}
		
		// get emails subview specific variables
		
		if ($subview == $tpl && $tpl == "emails") {
			
			// get main affiliate link
			
			$affiliateLink	= JRoute::_(JURI::base() . "index.php?" . $vmaSettings->link_feed . "=" . $affiliate->affiliate_id);
			
			// get editor
			
			$editor			= &JFactory::getEditor();
			
			// get com_users component's language

			$language		= &JFactory::getLanguage();
			
			$language->load("com_users", JPATH_ROOT . DIRECTORY_SEPARATOR . "administrator");
			
			// fix squeezebox issue on mootools 1.2
				
			$vmaHelper->fixSqueezeBox();
				
			$this->assignRef( "affiliateLink",	$affiliateLink );
			
			$this->assignRef( "editor", 		$editor );
			
		}
		
		// set the page title
		
		$params->set( 'page_title', $title );
	
		$document->setTitle( $title );
		
		// set the general template parameters
				
		$this->assignRef( 'params', 	$params );
		
		$this->assignRef( 'affiliate', 	$affiliate );
		
		$this->assignRef( 'subview', 	$subview );
		
		$this->assignRef( 'items', 		$items );
		
        $this->assignRef( 'pagination', $pagination );
		
        parent::display($tpl);
		
    }
	
}

?>