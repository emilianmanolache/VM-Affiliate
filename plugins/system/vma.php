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

// import joomla plugin application

jimport("joomla.plugin.plugin");

/**
 * Plugin file for the VM Affiliate Helper Plugin
 */

class plgSystemVma extends JPlugin {

	/**
	 * Constructor
	 */

	function plgSystemVma(&$subject, $config)	{

		global $mainframe;
		
		// get database, site configuration, and website
		
		$this->_db 			= &JFactory::getDBO();
		
		$this->_config 		= &JFactory::getConfig();
		
		$this->_website 	= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
		
		// load vma's configuration and language file
		
		$this->getVMASettings();

		$this->getVMALanguage();
				
		// run the constructor
		
		parent::__construct($subject, $config);

	}

	/**
	 * If this is the frontend, trigger the visit and order tracking functions. Also implement the VMA helper class.
	 */

	function onAfterInitialise() {
		
		global $mainframe;
		
		// load the vma helper class
			
		!class_exists("vmaHelper") ? require_once( JPATH_ROOT . DS . "administrator" . DS . "components" . DS . "com_virtuemart" . DS . "helpers" . DS . "vma.php" ) : NULL;

		$GLOBALS["vmaHelper"] 		= !isset($GLOBALS["vmaHelper"]) ? new VMAHelper() : $GLOBALS["vmaHelper"];
		
		// this is the frontend, so run the tracking routines
		
		if (!$mainframe->isAdmin()) {	
			
			$this->visitTrack();
			
			$this->offlineTrack();
			
		}
			
	}
	
	/**
	 * After the component has rendered, apply various regulatory fixes
	 */
	 
	function onAfterDispatch() {
		
		// initiate required variables
		
		global $vmaHelper;
		
		$document			= &JFactory::getDocument();
		
		$mainframe			= &JFactory::getApplication();
		
		$view				= &JRequest::getVar("view");
		
		$virtueMart		 	= &JRequest::getVar("option");
		
		$virtueMart			= $virtueMart == "com_virtuemart" ? true : false;
		
		$vmaFrontend		= &JRequest::getVar("option");
		
		$vmaFrontend		= $vmaFrontend == "com_affiliate" ? true : false;
		
		$isVirtueMartAdmin	= $mainframe->isAdmin() && $virtueMart;
		
		$vmaAdministration	= $isVirtueMartAdmin				&& ($view == "vma" || stristr($view, "vma_"));
		
		// if this is virtuemart's administration, fix the menu, and implement the affiliates button
		
		if ($isVirtueMartAdmin) {
			
			// fix the menu
			
			$buffer			= $document->getBuffer("component");
			
			if (stristr($buffer, "COM_VIRTUEMART_VMA_MOD")) {
			
				$document->setBuffer(str_replace("COM_VIRTUEMART_VMA_MOD", JText::_("AFFILIATE_MENU_NAME"), $buffer), "component");
				
			}
			
			$document->addStyleDeclaration("span.vmicon48.vm_shop_affiliates_48 {
	
											  background:				url(../components/com_affiliate/assets/images/shop_affiliates.png) no-repeat scroll left top transparent !important;
											  
										  }");
			
			// implement separators
			
			$buffer			= $document->getBuffer("component");
			
			$separatorLocations = array("<li>\n" . 

						"\t\t\t\t\t\t<a href=\"index.php?option=com_virtuemart&view=vma\" ><span class=\" vmicon vmicon-16-info\"></span>" . JText::_("SUMMARY") . "</a>\n" . 

					"\t\t\t\t\t</li>",

										"<li>\n" . 

						"\t\t\t\t\t\t<a href=\"index.php?option=com_virtuemart&view=vma_pay_affiliates\" ><span class=\"vmicon vmicon-16-content\"></span>" . JText::_("PAY_AFFILIATES") . "</a>\n" . 

					"\t\t\t\t\t</li>",

										"<li>\n" . 

						"\t\t\t\t\t\t<a href=\"index.php?option=com_virtuemart&view=vma_payment_methods\" ><span class=\"vmicon vmicon-16-content\"></span>" . JText::_("PAYMENT_METHODS") . "</a>\n" . 

					"\t\t\t\t\t</li>", 

										"<li>\n" . 

						"\t\t\t\t\t\t<a href=\"index.php?option=com_virtuemart&view=vma_category_ads\" ><span class=\"vmicon vmicon-16-content\"></span>" . JText::_("CATEGORY_ADS") . "</a>\n" . 

					"\t\t\t\t\t</li>",

										"<li>\n" . 

						"\t\t\t\t\t\t<a href=\"index.php?option=com_virtuemart&view=vma_payments\" ><span class=\"vmicon vmicon-16-content\"></span>" . JText::_("PAYMENTS") . "</a>\n" . 

					"\t\t\t\t\t</li>");
					
			foreach ($separatorLocations as $separatorLocation) {

				$buffer = str_replace($separatorLocation, $separatorLocation . '<li><hr /></li>', $buffer);
				
			}
			
			$document->setBuffer($buffer, "component");
			
			// implement affiliates button
			
			if (stristr($buffer, '<div id="cpanel"')) {
				
				$document->addScriptDeclaration($this->vmaToolbarIcon());
				
			}
			
		}
		
		// if this is vm affiliate's administration, load the corresponding style sheet
		
		if ($vmaAdministration) {
			
			$vmaHelper->includeAdminStyleSheet();
			
		}
		
		// if this is any vm affiliate page, load mootools, fix the squeezebox scrolling issues on firefox, and emulate ie8 on ie9
		
		if ($vmaFrontend || $vmaAdministration) {
			
			// load mootools
			
			JHTML::_('behavior.mootools');
			
			// fix the squeezebox scrolling issues on firefox > 3
			
			$document->addScriptDeclaration("window.addEvent('domready', function() {
											
												if (typeof(SqueezeBox) 					!= 'undefined' && 
												
													typeof(SqueezeBox.listeners) 		!= 'undefined' && 
													
													typeof(SqueezeBox.listeners.window) != 'undefined' &&
													
													(MooTools.version					== '1.11' 	   ||
													
													MooTools.version 					== '1.12')	   &&
													
													window.gecko									   &&
													
													navigator.userAgent								   &&
													
													navigator.userAgent.indexOf('Firefox/3') == -1)		{

													SqueezeBox.listeners.window = null;
													
												}
												
											});");
			
			// emulate ie8 on ie9, to fix mootools <1.2.4 incompatibility with ie9
				
			$document->setMetaData("X-UA-Compatible", "IE=EmulateIE8", true);
			
		}
		
	}
	
	/**
	 * After the page has rendered, monitor the affiliate orders' statuses
	 */
	 
	function onAfterRender() {

		$this->monitorOrders();
		
	}
	
	/**
	 * Method to receive the order finalization event
	 */
	 
	function plgVmConfirmedOrder($params, $order) {
		
		$this->orderTrack($order);
		
	}
	
	/** 
	 * Method to check whether the current visitor has been referred by an affiliate, case in which the visit is tracked, recorded and credited
	 */

	function visitTrack() {
		
		global $vmaHelper;
		
		// get referral parameter
		
		$online		= &JRequest::getVar($this->_settings->link_feed, NULL, "get");
		
		$offline	= &JRequest::getVar($this->_settings->link_feed, NULL, "post");
		
		$referral	= $online ? $online : $offline;
		
		// check if the visitor has been referred by an affiliate

		if (!$referral) {
			
			return false;
			
		}
		
		// check if the visitor hasn't arrived via a search engine link, and the visitor is not actually a spider
		
		if (!$this->filterTraffic()) {
			
			return false;
			
		}
		
		// check if the affiliate is blocked
		
		if ($vmaHelper->isBlocked($referral)) {
			
			return false;
			
		}
		
		// get the required variables
		
		$affiliateID 	= $referral;
		
		$referrer		= isset($_SERVER['HTTP_REFERER']) 		? $_SERVER['HTTP_REFERER'] 		: NULL;
		
		$browser		= isset($_SERVER['HTTP_USER_AGENT']) 	? $_SERVER['HTTP_USER_AGENT'] 	: NULL;
		
		// set the tracking cookie in the user's browser, if referred should be tracked
		
		if ($this->_settings->track_who != 1 || !isset($_COOKIE['aff_id'])) {
		
			$this->setTrackingCookie($affiliateID);
				
		}

		// check if click is unique or not

		$query	= "SELECT `RemoteAddress` FROM #__vm_affiliate_clicks WHERE `RemoteAddress` = " . 
		
				  "'" . $this->_db->getEscaped($_SERVER['REMOTE_ADDR']) . "' AND `AffiliateID` = '" . (int) $affiliateID . "'";

		$this->_db->setQuery( $query );

		$exists = $this->_db->loadResult();

		$unique = $exists ? false : true;

		// record the click

		$query 	= "INSERT INTO #__vm_affiliate_clicks VALUES ('', '" . (int) $affiliateID . "', '" . date("U") . "', " . 
		
				  "'" . $this->_db->getEscaped($_SERVER['REMOTE_ADDR']) . "', '" . $this->_db->getEscaped($referrer) . "', " . 
				  
				  "'" . $this->_db->getEscaped($browser) . "', '0', '" . date("Y-m-d\TH:i:s") . "')";

		$this->_db->setQuery( $query );

		$this->_db->query();

		// check if this is a banner or text ad hit
		
		if (isset($_REQUEST['banner_id']) || isset($_REQUEST['textad_id'])) {
			
			$this->recordAdHit();
			
		}			
		
		// credit the commissions for the click
		
		$this->creditClick($affiliateID, $unique);
		
		// redirect the page without the referral parameter, to prevent duplicate content or search engine indexing
		
		$this->sefRedirect($affiliateID);

	}
	
	/**
	 * Method to track users who are referred through offline advertising means
	 */
	 
	function offlineTrack() {
		
		global $vmaHelper;
		
		// check if offline tracking is enabled, otherwise cancel
		
		if (!$this->_settings->offline_tracking) {
			
			return false;
			
		}
		
		// check for username-based offline tracking
		
		if ($this->_settings->offline_type == 1 && isset($_POST["vm_partnersusername"])	&& !empty($_POST["vm_partnersusername"]))	{
			
			$affiliateUsername	= &JRequest::getVar("vm_partnersusername");

		}
		
		// check for name-based offline tracking
		
		if ($this->_settings->offline_type == 2 && isset($_POST["vm_partnersname"])		&& !empty($_POST["vm_partnersname"]))		{
			
			$affiliateID		= &JRequest::getVar("vm_partnersname");
			
			$affiliateID		= isset($affiliateID[0]) ? $affiliateID[0] : $affiliateID;

		}
		
		// check for coupon-based offline tracking
		
		if ($this->_settings->offline_type == 3 && isset($_POST["coupon_code"])			&& !empty($_POST["coupon_code"]))			{
			
			$affiliateUsername	= &JRequest::getVar("coupon_code");

		}
		
		// verify if such affiliate exists, and is published
		
		if (isset($affiliateUsername) || isset($affiliateID)) {
			
			$query				= "SELECT `affiliate_id` FROM #__vm_affiliate WHERE `blocked` = '0' AND " 			. 
			
								  (isset($affiliateUsername) ? ("`username` = '" 			. 	$this->_db->getEscaped($affiliateUsername)	. "'") :
								  
								  							   ("`affiliate_id` = '"		. 	(int) $affiliateID		. "'"));
															  
			$this->_db->setQuery($query);
			
			$validAffiliateID	= $this->_db->loadResult();
		
		}
		
		// if no valid affiliate found, cancel the operation
		
		if (!isset($validAffiliateID) || !$validAffiliateID) {
			
			return false;
			
		}
		
		// otherwise, adopt the valid affiliate id
		
		else {
			
			$affiliateID		= $validAffiliateID;
			
		}
		
		// write the tracking cookie
		
		$this->setTrackingCookie($affiliateID);
		
		// if valid discount coupon offline tracking found, generate the discount coupon
		
		if ($this->_settings->offline_type == 3) {
			
			// get the affiliate's discount rate
			
			$discountRate		= $vmaHelper->getDiscountRate($affiliateID);
			
			// determine discount type
			
			$discountType		= $discountRate["discount_type"] == 1 ? "total" : "percent";
			
			// remove any previous coupon with the same name
			
			$query				= "DELETE FROM #__virtuemart_coupons WHERE `coupon_code` = '" . $this->_db->getEscaped($affiliateUsername) . "'";
			
			$this->_db->setQuery($query);
			
			$this->_db->query();
			
			// generate a new gift discount coupon
			
			$query				= "INSERT INTO #__virtuemart_coupons (`virtuemart_coupon_id`, `coupon_code`, `percent_or_total`, `coupon_type`, `coupon_value`, " . 
			
								  "`coupon_start_date`, `coupon_expiry_date`, `coupon_value_valid`, `published`, `created_on`, `created_by`, `modified_on`, " . 
								  
								  "`modified_by`, `locked_on`, `locked_by`) " . 
			
								  "VALUES ('', '" . $this->_db->getEscaped($affiliateUsername) . "', '" . 
			
								  $discountType . "', 'gift', '" . $discountRate["discount_amount"] . "', '', '" . date("Y-m-d", strtotime("+2 days")) . "', '', '1', '', '', '', '', '', '')";
								  
			$this->_db->setQuery($query);
			
			$this->_db->query();
			
		}
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to set a tracking cookie in the visitor's browser
	 */
	 
	function setTrackingCookie($affiliateID) {
		
		// prepare required variables
		
		$cookieLifetime = time() + $this->_settings->cookie_time;
		
		$host			= isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : NULL;
		
		// write the standard cookie
		
		setcookie("aff_id", $affiliateID, $cookieLifetime, "/");

		// write subdomain-compatible cookie (if this is not localhost; otherwise it will create issues)

		if (!stristr($host, "localhost") && stristr($host, ".")) {
		
			$host		= str_replace("www.", "", $host);
			
			setcookie("aff_id", $affiliateID, $cookieLifetime, "/", "." . $host);
			
		}
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to confirm and track affiliate orders
	 */
	 
	function orderTrack($order) {
		
		global $vmaHelper;
		
		$confirmedStatuses		= $vmaHelper->_confirmedStatuses;
		
		if (isset($_COOKIE["aff_id"]) && !$vmaHelper->isBlocked($_COOKIE["aff_id"]) && 
		
			isset($order["details"]["BT"]->virtuemart_order_id) && $order["details"]["BT"]->virtuemart_order_id) {
			
			// ensure no duplicate tracking
			
			$query	= "SELECT `affiliate_id` FROM #__vm_affiliate_orders WHERE `order_id` = '" . $order["details"]["BT"]->virtuemart_order_id . "'";
			
			$this->_db->setQuery($query);
			
			$exists = $this->_db->loadResult();
			
			// ensure no self referrer
					
			/*$user	= &JFactory::getUser();
			
			$self	= false;
			
			if ($user->email) {
				
				$query	= "SELECT `mail` FROM #__vm_affiliate WHERE `mail` = '" . $user->email . "'";
				
				$this->_db->setQuery($query);
				
				$self	= $this->_db->loadResult();
				
			}*/
			
			// track the order
			
			if (!$exists/* && !$self*/) {
				
				// get order details
			
				$query = "SELECT * FROM #__virtuemart_orders WHERE `virtuemart_order_id` = '" . $order["details"]["BT"]->virtuemart_order_id . "'";
				
				$this->_db->setQuery($query);
				
				$orderDetails = $this->_db->loadObject();
				
				// track the order
				
				$query 	= "INSERT INTO #__vm_affiliate_orders VALUES ('', '" . $_COOKIE["aff_id"] . "', '" . $order["details"]["BT"]->virtuemart_order_id . "', " . 
				
						  "'" . $orderDetails->order_status . "', 0, '" . date('Y-m-d') . "')";
							  
				$this->_db->setQuery($query);
				
				$this->_db->query();
				
				// check if this should be credited right away
			
				if (in_array($orderDetails->order_status, $confirmedStatuses)) {
					
					$this->creditSale($_COOKIE["aff_id"], $orderDetails->virtuemart_order_id, $orderDetails->order_subtotal, $orderDetails->order_status, "credit");
					
				}
			
			}
			
		}
		
	}
	
	/**
	 * Method to monitor order's statuses, and give or remove commissions accordingly
	 */
	 
	function monitorOrders() {
		
		global $vmaHelper;
		
		// define statuses
				
		$unconfirmedStatuses 	= array_merge($vmaHelper->_pendingStatuses, $vmaHelper->_cancelledStatuses);
				
		$confirmedStatuses		= $vmaHelper->_confirmedStatuses;
				
		// prepare and run the query
		
		$query 					= "SELECT ao.`order_id` AS order_id, (o.`order_salesPrice` - o.`coupon_discount`) AS order_subtotal, "				. 
		
								  "ao.`affiliate_id` AS affiliate_id, ao.`order_status` AS aff_order_status, o.`order_status` AS order_status " . 
				
								  "FROM #__vm_affiliate_orders ao LEFT JOIN #__virtuemart_orders o ON ao.`order_id` = o.`virtuemart_order_id` "	.
						 
								  "WHERE ao.`paid` = 0 AND ao.`order_status` != o.`order_status` OR ao.`paid` = 0 AND o.`order_status` IS NULL";
				 
		$this->_db->setQuery($query);

		$changedOrders 			= $this->_db->loadAssocList();
		
		if (!empty($changedOrders) && is_array($changedOrders)) {
			
			foreach ($changedOrders as $changedOrder) {
				
				// check if the order has been confirmed, case in which give the appropriate commission
				
				if (in_array($changedOrder["aff_order_status"], $unconfirmedStatuses) &&
					
					in_array($changedOrder["order_status"], 	$confirmedStatuses)) {
						
					$this->creditSale($changedOrder["affiliate_id"], $changedOrder["order_id"], $changedOrder["order_subtotal"], $changedOrder["order_status"], "credit");
					
				}
				
				// check if the order has been unconfirmed, case in which remove the appropriate commission
				
				if (in_array($changedOrder["aff_order_status"], $confirmedStatuses) &&
					
					(in_array($changedOrder["order_status"], 	$unconfirmedStatuses) ||
					
					!$changedOrder["order_status"])) {
						
					$this->creditSale($changedOrder["affiliate_id"], $changedOrder["order_id"], $changedOrder["order_subtotal"], $changedOrder["order_status"], "uncredit");
					
				}
				
				// check if the order has been removed, case in which remove the affiliate order
				
				if (!$changedOrder["order_status"]) {
					
					$query = "DELETE FROM #__vm_affiliate_orders WHERE `order_id` = '" . $changedOrder["order_id"] . "'";
					
					$this->_db->setQuery($query);
			
					$this->_db->query();
				
				}
				
			}
			
		}
		
	}
	
	/**
	 * Method to filter traffic from spiders or search engines
	 */
	 
	function filterTraffic() {
		
		// check if traffic comes from crawlers and spiders
		
		if (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) {
			
			$spidersUAS = array("yahoofeedseeker", "yandex", "yahoo! slurp", "DotBot", "Exabot", "psbot", "Feedfetcher-Google", "simplepie", "yacybot",
			
								"Purebot", "Nutch", "heritrix", "Gigabot", "Googlebot", "ia_archiver", "Baiduspider");
	
			foreach ($spidersUAS as $spiderUAS) {
				
				if (stristr($_SERVER['HTTP_USER_AGENT'], $spiderUAS)) {
					
					return false;
					
				}
				
			}
		
		}
		
		// check if traffic comes from search engines
		
		if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
			
			$searchEnginesDomains = array("www.google", "www.yahoo", "search.yahoo", "www.yandex", "www.baidu", "www.bing");
			
			foreach ($searchEnginesDomains as $searchEngineDomain) {
				
				if (stristr($_SERVER['HTTP_REFERER'], $searchEngineDomain)) {
					
					return false;
					
				}
				
			}
		
		}
		
		// otherwise, log traffic
		
		return true;
		
	}
	
	/**
	 * Method to redirect a referral URL to its non-referral version, to avoid duplicate pages in search engines
	 */
	 
	function sefRedirect($affiliateID) {
		
		// initiate required variables
		
		$application		= &JFactory::getApplication();
		
		// parse the referral url
		
		$currentURI			= JURI::getInstance();
		
		$isReferral			= $currentURI->getVar($this->_settings->link_feed);
		
		if ($isReferral) {
			
			$currentURI->delVar($this->_settings->link_feed);
			
			$currentURL		= $currentURI->toString(array("path", "query"));
			
			$application->redirect(JRoute::_($currentURL));
		
		}
		
	}
	
	/**
	 * Method to record a banner or text ad hit
	 */
	
	function recordAdHit() {
		
		// get hit type and id
		
		if (isset($_REQUEST['banner_id'])) {
			
			$hitType 	= "banner";
			
			$adID		= $_REQUEST['banner_id'];
			
		}
		
		else if (isset($_REQUEST['textad_id'])) {
			
			$hitType 	= "textad";
			
			$adID		= $_REQUEST['textad_id'];
			
		}
		
		// check whether there has already been any hit of such type for the same ad from the same affiliate
		
		$query 		= "SELECT `hits` FROM #__vm_affiliate_" . $hitType . "s_hits WHERE `" . $hitType . "_id` = '" . (int) $adID . "' " . 
		
					  "AND `affiliate_id` = '" . (int) $_GET[$this->_settings->link_feed] . "'";

		$this->_db->setQuery( $query );

		$this->_db->query();

		$hits 		= @$this->_db->loadResult();

		// record the ad hit
		
		if (!$hits) {
			
			$query 	= "INSERT INTO #__vm_affiliate_" . $hitType . "s_hits VALUES ('', '" . (int) $adID . "', '1', '" . (int) $_GET[$this->_settings->link_feed] . "')";

			$this->_db->setQuery( $query );

			$this->_db->query();
			
		}
		
		else {
			
			$query 	= "UPDATE #__vm_affiliate_" . $hitType . "s_hits SET `hits` = `hits` + 1 WHERE `" . $hitType . "_id` = '" . (int) $adID . "' " . 
			
					  "AND `affiliate_id` = '" . (int) $_GET[$this->_settings->link_feed] . "'";

			$this->_db->setQuery( $query );

			$this->_db->query();
							
		}

	}
	
	/**
	 * Method which gives the required commission per click to the referrer affiliate as well as the parent tiers (if set and exist)
	 */
	
	function creditClick($affiliateID, $unique) {
		
		global $vmaHelper;
		
		// get commission rates
		
		$commissionRates 	= $vmaHelper->getCommissionRates($affiliateID);
		
		// get parent affiliates, if multi tier system is enabled
		
		$parentAffiliates 	= $this->_settings->multi_tier ? $vmaHelper->tierTree($affiliateID) : NULL;

		// whether the click is unique or not
		
		$field 				= $unique ? "per_unique_click_fixed" : "per_click_fixed";
		
		// update the affiliate's commission

		$commission 		= $commissionRates["affiliate"][$field];
		
		$query 				= "UPDATE #__vm_affiliate SET `commissions` = `commissions` + " . $commission . " WHERE `affiliate_id` = '" . $affiliateID . "'";

		$this->_db->setQuery( $query );

		$this->_db->query();

		// update the tiers' commissions, if multi tier system is enabled

		if ($this->_settings->multi_tier) {

			$i = 0;

			while ($i < ($this->_settings->tier_level - 1)) {

				$commission = $commissionRates["tiers"][$i][$field];

				if (isset($parentAffiliates[$i]) && !empty($parentAffiliates[$i])) {

					// update the commission for tier $i + 2

					$query 	= "UPDATE #__vm_affiliate SET `commissions` = `commissions` + " . $commission . " WHERE `affiliate_id` = '" . $parentAffiliates[$i] . "'";

					$this->_db->setQuery( $query );

					$this->_db->query();

				}

				$i++;

			}

		}
		
	}
	
	/**
	 * Method which gives or removes the required commission per sale to the referrer affiliate as well as the parent tiers (if set and exist)
	 */
	
	function creditSale($affiliateID, $orderID, $orderSubtotal, $newStatus, $action = "credit") {
		
		global $vmaHelper;
		
		// make sure the order subtotal is a positive value
		
		if ($orderSubtotal <= 0) {
			
			return false;
			
		}
		
		// get operation
		
		$sign				= $action == "credit" ? "+" : "-";
		
		// get commission rates
		
		$commissionRates 	= $vmaHelper->getCommissionRates($affiliateID);
		
		// get parent affiliates, if multi tier system is enabled
		
		$parentAffiliates 	= $this->_settings->multi_tier ? $vmaHelper->tierTree($affiliateID) : NULL;

		// update the affiliate's commission
		
		$commission			= $commissionRates["affiliate"]["per_sale_fixed"] + ($commissionRates["affiliate"]['per_sale_percentage'] / 100 * $orderSubtotal);
		
		$query 				= "UPDATE #__vm_affiliate SET `commissions` = `commissions` " . $sign . " " . $commission . " WHERE `affiliate_id` = '" . $affiliateID . "'";

		$this->_db->setQuery( $query );

		$this->_db->query();
		
		// update the tiers' commissions, if multi tier system is enabled

		if ($this->_settings->multi_tier) {

			$i = 0;

			while ($i < ($this->_settings->tier_level - 1)) {

				$commission = $commissionRates["tiers"][$i]["per_sale_fixed"] + ($commissionRates["tiers"][$i]['per_sale_percentage'] / 100 * $orderSubtotal);

				if (isset($parentAffiliates[$i]) && !empty($parentAffiliates[$i])) {

					// update the commission for tier $i + 2

					$query 	= "UPDATE #__vm_affiliate SET `commissions` = `commissions` " . $sign . " " . $commission . " WHERE `affiliate_id` = '" . $parentAffiliates[$i] . "'";

					$this->_db->setQuery( $query );

					$this->_db->query();

				}

				$i++;

			}

		}
		
		// update the affiliate order status
		
		$query 	= "UPDATE #__vm_affiliate_orders SET `order_status` = '" . $newStatus . "' WHERE `order_id` = '" . $orderID . "'";

		$this->_db->setQuery( $query );

		$this->_db->query();
		
		// if the affiliate sale is credited, e-mail the affiliate
		
		if ($action == "credit") {	
			
			// get affiliate's e-mail address
			
			$query			= "SELECT `mail` from #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
		
			$this->_db->setQuery($query);
	
			// prepare and send the e-mail
			
			$affiliateEmail	= $this->_db->loadResult();		
	
			$affiliateMail	= &JFactory::getMailer();
			
			$affiliateMail->addRecipient($affiliateEmail);
			
			$affiliateMail->setSender(array($this->_config->getValue( 'config.mailfrom' ), $this->_config->getValue( 'config.fromname' )));
			
			$affiliateMail->setSubject(JText::sprintf("NEW_SALE_SUBJECT", $this->_config->getValue( 'config.sitename' ) . "!"));
			
			$affiliateMail->setBody(JText::sprintf("NEW_SALE_SUBJECT", $this->_config->getValue( 'config.sitename' ) . "!") . "\r\n\r\n" . 
			
									JText::sprintf("NEW_SALE_MESSAGE", "\r\n" . JRoute::_($vmaHelper->vmaRoute($this->_website . "index.php?option=com_affiliate"), false) . 
									
									"\r\n\r\n" . $this->_config->getValue( 'config.sitename' ) . "\r\n" . $this->_website));
									
			$affiliateMail->send();

		}
		
	}
	
	/**
	 * Method to load and return the language class for VM Affiliate
	 */
	 
	function getVMALanguage() {
		
		global $mainframe;
		
		// get language object
		
		$language 	= &JFactory::getLanguage();
		
		$language->load("com_affiliate", JPATH_ROOT);
		
		return true;
			
	}

	/**
	 * Method to get VM Affiliate's settings
	 */
	
	function getVMASettings() {
		
		// query for the settings

		$this->_db->setQuery("SELECT * FROM #__vm_affiliate_settings WHERE `setting` = '1'");
		
		$vmaSettings = $this->_db->loadObject();
		
		// set them in the global variable
		
		$GLOBALS["vmaSettings"] = $this->_settings = $vmaSettings;
		
	}
	
	/**
	 * Method to build the VMA toolbar icon JavaScript function string
	 */
	 
	function vmaToolbarIcon() {
		
		global $vmaHelper;
		
		$vmaToolbarIcon = "window.addEvent('domready', function() {
			
								var panelIcons												= $('cpanel').getChildren();
								
								var affiliateIcon											= panelIcons[panelIcons.length - 3].clone();
								
								affiliateIcon.injectBefore(panelIcons[panelIcons.length - 3]);
								
								var affiliateIconElements									= affiliateIcon.getChildren();
								
								var affiliateIconLink										= affiliateIconElements[0];
								
								affiliateIconLink.href 										= '" . $vmaHelper->getAdminLink(false) . "';
																
								affiliateIconLink.title										= '" . JText::_("AFFILIATE_MENU_NAME", true) . "';
								
								affiliateIconLink.innerHTML									= '<span class=\"vmicon48 vm_shop_affiliates_48\"></span>" . 
								
																							  "<br />" . JText::_("AFFILIATE_MENU_NAME", true) . "';
								
							});";
							
		return $vmaToolbarIcon;
		
	}
	
}

?>