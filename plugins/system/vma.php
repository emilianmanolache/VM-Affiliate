<?php

/**
 * @package   VM Affiliate
 * @version   4.5.0 May 2011
 * @author    Globacide Solutions http://www.globacide.com
 * @copyright Copyright (C) 2006 - 2011 Globacide Solutions
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
			
		!class_exists("ps_vma") ? require_once( JPATH_ROOT . DS . "administrator" . DS . "components" . DS . "com_virtuemart" . DS . "classes" . DS . "ps_vma.php" ) : NULL;

		$GLOBALS["ps_vma"] 		= !isset($GLOBALS["ps_vma"]) ? new ps_vma() : $GLOBALS["ps_vma"];
		
		// this is the frontend, so run the tracking routines
		
		if (!$mainframe->isAdmin()) {	
			
			$this->orderTrack();
			
			$this->visitTrack();

			$this->orderPretrack();
			
			$this->offlineTrack();
			
		}
			
	}
	
	/**
	 * After the component has rendered, apply various regulatory fixes
	 */
	 
	function onAfterDispatch() {
		
		// initiate required variables
		
		global $ps_vma;
		
		$document			= &JFactory::getDocument();
		
		$mainframe			= &JFactory::getApplication();
		
		$page				= &JRequest::getVar("page");
		
		$virtueMart		 	= &JRequest::getVar("option");
		
		$virtueMart			= $virtueMart == "com_virtuemart" ? true : false;
		
		$vmaFrontend		= &JRequest::getVar("option");
		
		$vmaFrontend		= $vmaFrontend == "com_affiliate" ? true : false;
		
		$isVirtueMartAdmin	= ($mainframe->isAdmin() && $virtueMart) || ($page && $virtueMart);
		
		$vmaAdministration	= $isVirtueMartAdmin				&& stristr($page, "vma.");
		
		// if this is virtuemart's administration, implement the vm affiliate menu
		
		if ($isVirtueMartAdmin) {
			
			$this->implementVMAMenu();
			
		}
		
		// if this is vm affiliate's administration, load the corresponding style sheet
		
		if ($vmaAdministration) {
			
			$ps_vma->includeAdminStyleSheet();
			
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
	 * Method to check whether the current visitor has been referred by an affiliate, case in which the visit is tracked, recorded and credited
	 */

	function visitTrack() {
		
		global $ps_vma;
		
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
		
		if ($ps_vma->isBlocked($referral)) {
			
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
		
		global $ps_vma;
		
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
			
			$discountRate		= $ps_vma->getDiscountRate($affiliateID);
			
			// determine discount type
			
			$discountType		= $discountRate["discount_type"] == 1 ? "total" : "percent";
			
			// remove any previous coupon with the same name
			
			$query				= "DELETE FROM #__vm_coupons WHERE `coupon_code` = '" . $this->_db->getEscaped($affiliateUsername) . "'";
			
			$this->_db->setQuery($query);
			
			$this->_db->query();
			
			// generate a new gift discount coupon
			
			$query				= "INSERT INTO #__vm_coupons VALUES ('', '" . $this->_db->getEscaped($affiliateUsername) . "', '" . 
			
								  $discountType . "', 'gift', '" . $discountRate["discount_amount"] . "')";
								  
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
	 * Method to check whether this is the final step of the checkout, case in which pre-track the order
	 */
	
	function orderPretrack() {
	
		// if the customer has been referred by an affiliate, and this is the thank you page of virtuemart, pre-track the order
		
		if (isset($_COOKIE["aff_id"]) && JRequest::getVar('option') == "com_virtuemart" && (JRequest::getVar('page') == 'checkout.thankyou' ||
		
		(JRequest::getVar('page') == 'checkout.onepage' && JRequest::getVar('order_id')))) {

			// get ip address
						
			$ipAddress			= $_SERVER['REMOTE_ADDR'];
				
			if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
				
				$ipAddress		= array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
				
			}
				
			if (JRequest::getVar('page') == 'checkout.thankyou') {
			
				// get the next auto increment value from the orders table
			
				$query 				= "SHOW TABLE STATUS LIKE '" . $this->_db->getPrefix() . "vm_orders'";
		
				$this->_db->setQuery($query);
		
				$tableInfo 			= $this->_db->loadAssoc();
				
				$nextAutoIncrement	= isset($tableInfo["Auto_increment"]) ? $tableInfo["Auto_increment"] : NULL;
				
				$estimatedOrderID 	= empty($nextAutoIncrement) ? 0 : $nextAutoIncrement;
			
			} 
			
			else {
				
				// get order id from one page checkout
				
				$estimatedOrderID	= JRequest::getVar('order_id');
				
			}
			
			// insert the pre-tracked order into the database
			
			$query					= "INSERT INTO #__vm_affiliate_orders_pretrack VALUES ('', '" . $estimatedOrderID . "', '" . 
			
									  (int) $_COOKIE["aff_id"] . "', '" . $this->_db->getEscaped($ipAddress) . "')";
								  
			$this->_db->setQuery($query); 
	
			$this->_db->query();
			
		}
		
	}
	
	/**
	 * Method to confirm and track the pre-tracked (estimated) affiliate orders
	 */
	 
	function orderTrack() {
		
		global $ps_vma;
		
		// initiate required variables
		
		$confirmedStatuses	= $ps_vma->_confirmedStatuses;
		
		// get all pre-tracked affiliate orders that have actually been placed, and actually track them
		
		$query 				= "SELECT o.`order_id` AS order_id, o.`cdate` AS date, o.`order_subtotal` AS order_subtotal, aop.`affiliate_id` AS affiliate_id, " . 
		
							  "o.`order_status` AS order_status FROM #__vm_affiliate_orders_pretrack aop, #__vm_orders o " . 
				 
							  "WHERE o.`order_id` = aop.`estimated_order_id` AND o.`ip_address` = aop.`customer_ip`";
		
		$this->_db->setQuery($query);

		$affiliateOrders 	= $this->_db->loadAssocList();
		
		// check and track all affiliate orders
		
		if (!empty($affiliateOrders) && is_array($affiliateOrders)) {
			
			foreach ($affiliateOrders as $affiliateOrder) {
				
				if (!$ps_vma->isBlocked($affiliateOrder["affiliate_id"])) {
					
					// ensure no duplicate tracking
					
					$query	= "SELECT `affiliate_id` FROM #__vm_affiliate_orders WHERE `order_id` = '" . $affiliateOrder["order_id"] . "'";
					
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
						
						// insert the order in the database
						
						$query 	= "INSERT INTO #__vm_affiliate_orders VALUES ('', '" . $affiliateOrder["affiliate_id"] . "', '" . $affiliateOrder["order_id"] . "', " . 
						
								  "'" . $affiliateOrder["order_status"] . "', 0, '" . date('Y-m-d', $affiliateOrder["date"]) . "')";
									  
						$this->_db->setQuery($query);
						
						$this->_db->query();
					
						// check if this should be credited right away
					
						if (in_array($affiliateOrder["order_status"], $confirmedStatuses)) {
							
							$this->creditSale($affiliateOrder["affiliate_id"], $affiliateOrder["order_id"], $affiliateOrder["order_subtotal"], $affiliateOrder["order_status"], "credit");
							
						}
					
					}
					
				}
				
			}
			
		}
		
		// remove all pre-tracked (estimated) orders
				
		$query				= "DELETE FROM #__vm_affiliate_orders_pretrack";
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
	}
	
	/**
	 * Method to monitor order's statuses, and give or remove commissions accordingly
	 */
	 
	function monitorOrders() {
		
		global $ps_vma;
		
		// define statuses
				
		$unconfirmedStatuses 	= array_merge($ps_vma->_pendingStatuses, $ps_vma->_cancelledStatuses);
				
		$confirmedStatuses		= $ps_vma->_confirmedStatuses;
				
		// prepare and run the query
		
		$query 					= "SELECT ao.`order_id` AS order_id, o.`order_subtotal` - o.`coupon_discount` AS order_subtotal, "				. 
		
								  "ao.`affiliate_id` AS affiliate_id, ao.`order_status` AS aff_order_status, o.`order_status` AS order_status " . 
				
								  "FROM #__vm_affiliate_orders ao LEFT JOIN #__vm_orders o ON ao.`order_id` = o.`order_id` "					.
						 
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
		
		global $ps_vma;
		
		// get commission rates
		
		$commissionRates 	= $ps_vma->getCommissionRates($affiliateID);
		
		// get parent affiliates, if multi tier system is enabled
		
		$parentAffiliates 	= $this->_settings->multi_tier ? $ps_vma->tierTree($affiliateID) : NULL;

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
		
		global $ps_vma;
		
		// make sure the order subtotal is a positive value
		
		if ($orderSubtotal <= 0) {
			
			return false;
			
		}
		
		// get operation
		
		$sign				= $action == "credit" ? "+" : "-";
		
		// get commission rates
		
		$commissionRates 	= $ps_vma->getCommissionRates($affiliateID);
		
		// get parent affiliates, if multi tier system is enabled
		
		$parentAffiliates 	= $this->_settings->multi_tier ? $ps_vma->tierTree($affiliateID) : NULL;

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
			
									JText::sprintf("NEW_SALE_MESSAGE", "\r\n" . JRoute::_($ps_vma->vmaRoute($this->_website . "index.php?option=com_affiliate"), false) . 
									
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
	 * Method to implement VMA's administration menu by injecting it into the document buffer, on-the-fly, into the VirtueMart's administration menu
	 */
	
	function implementVMAMenu() {
		
		// initiate required variables
		
		$document			= &JFactory::getDocument();
		
		$option				= &JRequest::getVar("option");
		
		$buffer				= $document->getBuffer("component");
		
		// return if this is not virtuemart
		
		if ($option != "com_virtuemart") {
			
			return false;
			
		}
		
		// prepare the injection identifiers
		
		$standardIdentifier	= '<h3 class="title-smenu" title="about"';
		
		$extendedIdentifier = '<iframe id="vmPage"';
		
		$storeIndIdentifier = '<div id="cpanel"';
		
		// inject the vma menu into the standard menu
		
		if (stristr($buffer, $standardIdentifier)) {
			
			$document->setBuffer(str_replace($standardIdentifier, $this->vmaStandardMenu() . $standardIdentifier, $buffer), "component");
			
			$document->addScriptDeclaration($this->vmaStandardMenuToggle());
			
		}
		
		// inject the vma menu into the extended menu
		
		else if (stristr($buffer, $extendedIdentifier)) {
			
			$document->addScriptDeclaration($this->vmaExtendedMenu());
			
		}
		
		// inject the vma icon into the virtuemart toolbar (store.index)
		
		if (stristr($buffer, $storeIndIdentifier)) {

			$document->addScriptDeclaration($this->vmaToolbarIcon());
			
		}
		
		return true;
		
	}
	
	/**
	 * Method to build the VMA standard menu toggle JavaScript function string
	 */
	 
	function vmaStandardMenuToggle() {
		
		$vmaStandardMenuToggle =  "window.addEvent('domready', function() {
									   
									   // get all menu handles
									   
									   var menuHandles 		= $('masterdiv2').getElements('h3');
									   
									   // initiate menu handle index
									   
									   window.currentHandle	= 0;
									   
									   // rewrite the onclick event for each menu handle
									   
									   menuHandles.each(function(menuHandle) {
										  
										  // remove current click events
											  
										  menuHandle.removeEvents('click');
											
										  menuHandle.setProperty('onclick', null);
										  
										  menuHandle.removeProperty('onclick');
										  
										  // get the current item number
										  
										  var currentHandle	= window.currentHandle + 1;
										  
										  // add the new click event
										  
										  var newSwitchMenu	= function() {
											  
											  (function(i) {
												
												  SwitchMenu(i);
												  
											  })(currentHandle);
										  
										  };
										  
										  menuHandle.addEvent('click', newSwitchMenu);
										  
										  // increase the menu handle index
										  
										  window.currentHandle++;
										  
									   });
									   
								   });";
						   
		return $vmaStandardMenuToggle;
		
	}
	
	/**
	 * Method to build the VMA standard menu string
	 */
	 
	function vmaStandardMenu() {
		
		global $ps_vma;
		
		$link			 = $ps_vma->getAdminLink(true, true);
		
		$vmaStandardMenu = '<h3 class="title-smenu" title="affiliate" id="VMAMenuHandle">' . JText::_("AFFILIATE_MENU_NAME") . '</h3>' .

							  '<div class="section-smenu" id="VMAMenu">' .
							  
								  '<ul>' .
								  
									  '<li class="item-smenu vmicon vmicon-16-info">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.summary") . '">' .
										  
											  JText::_("SUMMARY") .
											  
										  '</a>' 	.
										  
										  '<hr />' 	.
									  
									  '</li>' .
									  
									  '<li class="item-smenu vmicon vmicon-16-user">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.affiliate_list") . '">' .
										  
											  JText::_("MANAGE_AFFILIATES") .
											  
										  '</a>' .
										  
									  '</li>' .		
									  
									  '<li class="item-smenu vmicon vmicon-16-content">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.email_affiliates") . '">' .
										  
											  JText::_("EMAIL_AFFILIATES") .
											  
										  '</a>' 	.
										  
									  '</li>' .	
									  
									  '<li class="item-smenu vmicon vmicon-16-content">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.pay_affiliates") . '">' .
										  
											  JText::_("PAY_AFFILIATES") .
											  
										  '</a>' 	.
										  
										  '<hr />' 	.
										  
									  '</li>' .	
									  
									  '<li class="item-smenu vmicon vmicon-16-install">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.commission_rates_form") . '">' .
										  
											  JText::_("COMMISSION_RATES") .
											  
										  '</a>' .
										  
									  '</li>' .	
									  
									  '<li class="item-smenu vmicon vmicon-16-config">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.configuration_form") . '">' .
										  
											  JText::_("CONFIGURATION") .
											  
										  '</a>' .
										  
									  '</li>' .		
									  
									  '<li class="item-smenu vmicon vmicon-16-content">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.payment_methods_list") . '">' .
										  
											  JText::_("PAYMENT_METHODS") .
											  
										  '</a>' .
										  
										  '<hr />' . 
										  
									  '</li>' .
									  
									  '<li class="item-smenu vmicon vmicon-16-media">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.banners_list") . '">' .
										  
											  JText::_("BANNERS") .
											  
										  '</a>' .
										  
									  '</li>' .		
									  
									  '<li class="item-smenu vmicon vmicon-16-article">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.textads_list") . '">' .
										  
											  JText::_("TEXT_ADS") .
											  
										  '</a>' .
										  
									  '</li>' .		
									  
									  '<li class="item-smenu vmicon vmicon-16-content">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.product_ads") . '">' .
										  
											  JText::_("PRODUCT_ADS") .
											  
										  '</a>' .
										  
									  '</li>' .		
									  
									  '<li class="item-smenu vmicon vmicon-16-content">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.category_ads") . '">' .
										  
											  JText::_("CATEGORY_ADS") .
											  
										  '</a>' .
										  
										  '<hr />' . 
										  
									  '</li>' .		
									  
									  '<li class="item-smenu vmicon vmicon-16-info">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.statistics") . '">' .
										  
											  JText::_("STATISTICS") .
											  
										  '</a>' 	.
									  
									  '</li>' .
									  
									  '<li class="item-smenu vmicon vmicon-16-content">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.traffic") . '">' .
										  
											  JText::_("TRAFFIC") .
											  
										  '</a>' .
										  
									  '</li>' .		
									  
									  '<li class="item-smenu vmicon vmicon-16-content">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.sales") . '">' .
										  
											  JText::_("SALES") .
											  
										  '</a>' .
										  
									  '</li>' .		
									  
									  '<li class="item-smenu vmicon vmicon-16-content">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.payments") . '">' .
										  
											  JText::_("PAYMENTS") .
											  
										  '</a>' .
										  
										  '<hr />' . 
										  
									  '</li>' .		
									  
									  '<li class="item-smenu vmicon vmicon-16-content">' .
									  
										  '<a href="' . JRoute::_($link . "page=vma.about") . '">' .
										  
											  JText::_("ABOUT") .
											  
										  '</a>' .
										  
									  '</li>' .		
									
									  '<li class="item-smenu vmicon vmicon-16-content">' .
									  
										  '<a href="http://www.globacide.com/virtuemart-affiliate/user-manual.html">' .
										  
											  JText::_("MANUAL") .
											  
										  '</a>' .
										  
									  '</li>' .		
									  
									  '<li class="item-smenu vmicon vmicon-16-help">' .
									  
										  '<a href="http://www.globacide.com/virtuemart-affiliate/forum.html">' .
										  
											  JText::_("FORUM") .
											  
										  '</a>' .
										  
									  '</li>' .		
									  
									  '<li class="item-smenu vmicon vmicon-16-content">' .
									  
										  '<a href="http://www.globacide.com">Globacide.com</a>' .
										  
									  '</li>' .	
									  
								  '</ul>' .
								  
							  '</div>';
							  
		return $vmaStandardMenu;
							  
	}
	
	/**
	 * Method to build the VMA extended menu string
	 */
	 
	function vmaExtendedMenu() {

		$link			 = "index3.php?option=com_virtuemart&pshop_mode=admin&";
		
		$style			 = "style: 		'padding-left: 0px; font-weight: bold; background-repeat: no-repeat;',";
		
		$vmaExtendedMenu = "function loadVMAMenu() {
												
								// get the toolbar manager
								
								var extMenu		= 	window.Ext.ComponentMgr.all;
								
								// build the vma extended menu
								
								var VMAMenu		=	new Ext.Button({
								
									xtype: 	'tbbutton',
									
									text:	'" . JText::_("AFFILIATE_MENU_NAME", true) . "',
									
									menu: 	new Ext.menu.Menu({
												
										items: [{ 
										
											text: 		'" . JText::_("SUMMARY", true) . "',
										
											itemCls: 	'vmicon vmicon-16-info',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.summary") . "' )\")
											
										}, 
										
										'-', 
										
										{
										
											text: 		'" . JText::_("MANAGE_AFFILIATES", true) . "',
										
											itemCls: 	'vmicon vmicon-16-user',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.affiliate_list") . "' )\")
												
										}, {
										
											text: 		'" . JText::_("EMAIL_AFFILIATES", true) . "',
										
											itemCls: 	'vmicon vmicon-16-content',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.email_affiliates") . "' )\")
												
										}, {
										
											text: 		'" . JText::_("PAY_AFFILIATES", true) . "',
										
											itemCls: 	'vmicon vmicon-16-content',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.pay_affiliates") . "' )\")
												
										},
										
										'-', 
										
										{
										
											text: 		'" . JText::_("COMMISSION_RATES", true) . "',
										
											itemCls: 	'vmicon vmicon-16-install',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.commission_rates_form") . "' )\")
												
										}, {
										
											text: 		'" . JText::_("CONFIGURATION", true) . "',
										
											itemCls: 	'vmicon vmicon-16-config',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.configuration_form") . "' )\")
												
										}, {
										
											text: 		'" . JText::_("PAYMENT_METHODS", true) . "',
										
											itemCls: 	'vmicon vmicon-16-content',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.payment_methods_list") . "' )\")
												
										}, 
										
										'-', 
										
										{
										
											text: 		'" . JText::_("BANNERS", true) . "',
										
											itemCls: 	'vmicon vmicon-16-media',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.banners_list") . "' )\")
												
										}, {
										
											text: 		'" . JText::_("TEXT_ADS", true) . "',
										
											itemCls: 	'vmicon vmicon-16-article',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.textads_list") . "' )\")
												
										}, {
										
											text: 		'" . JText::_("PRODUCT_ADS", true) . "',
										
											itemCls: 	'vmicon vmicon-16-content',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.product_ads") . "' )\")
												
										}, {
										
											text: 		'" . JText::_("CATEGORY_ADS", true) . "',
										
											itemCls: 	'vmicon vmicon-16-content',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.category_ads") . "' )\")
												
										}, 
										
										'-', 
										
										{
										
											text: 		'" . JText::_("STATISTICS", true) . "',
										
											itemCls: 	'vmicon vmicon-16-info',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.statistics") . "' )\")
												
										}, {
										
											text: 		'" . JText::_("TRAFFIC", true) . "',
										
											itemCls: 	'vmicon vmicon-16-content',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.traffic") . "' )\")
												
										}, {
										
											text: 		'" . JText::_("SALES", true) . "',
										
											itemCls: 	'vmicon vmicon-16-content',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.sales") . "' )\")
												
										}, {
										
											text: 		'" . JText::_("PAYMENTS", true) . "',
										
											itemCls: 	'vmicon vmicon-16-content',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.payments") . "' )\")
												
										}, 
										
										'-', 
										
										{
										
											text: 		'" . JText::_("ABOUT", true) . "',
										
											itemCls: 	'vmicon vmicon-16-content',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( '" . JRoute::_($link . "page=vma.about") . "' )\")
												
										}, {
										
											text: 		'" . JText::_("MANUAL", true) . "',
										
											itemCls: 	'vmicon vmicon-16-content',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( 'http://www.globacide.com/virtuemart-affiliate/user-manual.html' )\")
												
										}, {
										
											text: 		'" . JText::_("FORUM", true) . "',
										
											itemCls: 	'vmicon vmicon-16-help',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( 'http://www.globacide.com/virtuemart-affiliate/forum.html' )\")
												
										}, {
										
											text: 		'Globacide.com',
										
											itemCls: 	'vmicon vmicon-16-content',
											
											" . $style . "
											
											handler: 	new Function(\"loadPage( 'http://www.globacide.com' )\")
												
										}]
										
									})
				  
								});
								
								// get the toolbar container element
								
								for (var i in extMenu.items) {
										
									if (extMenu.items[i].autoCreate != undefined) {
										
										// insert a new separator, before the last menu
										
										extMenu.items[i].insertButton(extMenu.items[i].items.length - 2, new Ext.Toolbar.Separator());
										
										// insert the vma extended menu, before the last menu
										
										extMenu.items[i].insertButton(extMenu.items[i].items.length - 2, VMAMenu);
									
									}
									
								};											
				  
							}
							
							// inject the vma extended menu when the vm extended menu has completely loaded
							
							window.addEvent('domready', function() {
								
								window.Ext.isIE ? window.Ext.EventManager.addListener(window, 'load', loadVMAMenu) : window.Ext.onReady(loadVMAMenu);
							
							});";
			
		return $vmaExtendedMenu;
			
	}
	
	/**
	 * Method to build the VMA toolbar icon JavaScript function string
	 */
	 
	function vmaToolbarIcon() {
		
		global $ps_vma;
		
		$vmaToolbarIcon = "window.addEvent('domready', function() {
			
								var panelIcons												= $('cpanel').getChildren();
								
								var affiliateIcon											= panelIcons[panelIcons.length - 1].clone();
								
								affiliateIcon.injectBefore(panelIcons[panelIcons.length - 1]);
								
								var affiliateIconElements									= affiliateIcon.getChildren();
								
								var affiliateIconDiv										= affiliateIconElements[0];
								
								var affiliateIconDivElements								= affiliateIconDiv.getChildren();
								
								var affiliateIconLink										= affiliateIconDivElements[0];
								
								affiliateIconLink.href 										= '" . $ps_vma->getAdminLink(false) . "page=vma.summary" . "';
																
								affiliateIconLink.title										= '" . JText::_("AFFILIATE_MENU_NAME", true) . "';
								
								affiliateIconLink.innerHTML									= '<img border=\"0\" align=\"middle\" name=\"image\" alt=\"" . 
								
																							  JText::_("AFFILIATE_MENU_NAME", true) . "\" src=\"" . $this->_website . 
																						  
																							  "components/com_affiliate/assets/images/shop_affiliates.png\"><br />" . 
																						  
																							  JText::_("AFFILIATE_MENU_NAME", true) . "';
								
							});";
							
		return $vmaToolbarIcon;
		
	}
	
}

?>