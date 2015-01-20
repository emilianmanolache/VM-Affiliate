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

/**
 * VM Affiliate Helper Class
 */

class ps_vma {
	
	/**
	 * Class name variable
	 */
	 
	var $classname 			= "ps_affiliate";
	
	/**
	 * Error holder variable
	 */
	 
	var $error				= NULL;
	
	/**
	 * Website URL variable
	 */

	var $_website			= NULL;
	
	/**
	 * Database object
	 */
	 
	var $_db				= NULL;
	
	/**
	 * Configuration object
	 */
	 
	var $_config			= NULL;
	
	/**
	 * ItemID
	 */
	 
	var $_itemid			= 0;
	
	/**
	 * Currency settings
	 */
	 
	var $_currencySettings 	= NULL;
	
	/**
	 * Active stats menus
	 */
	 
	var $_activeStatsMenus	= NULL;
	
	/**
	 * Pending orders statuses
	 */
	 
	var $_pendingStatuses	= array("P");
	
	/**
	 * Confirmed orders statuses
	 */
	 
	var $_confirmedStatuses = array("C", "S");
	
	/**
	 * Cancelled orders statuses
	 */
	 
	var $_cancelledStatuses = array("X", "R");
	
	/**
	 * Constructor method for the VMA Helper Class
	 */
	 
	function ps_vma() {
		
		global $mainframe;
		
		// get database, site configuration, and website
		
		$this->_db 			= &JFactory::getDBO();
		
		$this->_config 		= &JFactory::getConfig();
		
		$this->_website 	= $mainframe->isAdmin() ? $mainframe->getSiteURL() : JURI::base();
		
		$this->_itemid		= $mainframe->isAdmin() ? 0 : $this->getItemID();
		
	}
	
	/**
	 * Method to append VMA's ItemID to the URL
	 */
	 
	function vmaRoute($link) {
			
		$newLink		= $link . (stristr($link, "?") ? "&" : "?") . "Itemid=" . $this->_itemid;
		
		return $newLink;
		
	}
	
	/**
	 * Method to get VM Affiliate's ItemID
	 */
	 
	function getItemID() {
		
		// get current itemid, if set
		
		$itemID = &JRequest::getVar("Itemid", 0);
		
		// get it from the database
		
		if (!$itemID) {
			
			$query = "SELECT `id` FROM #__menu WHERE `type` = 'component' AND `published` = '1' AND `link` LIKE '%option=com_affiliate%' ORDER BY `id` ASC";
			
			$this->_db->setQuery($query);
			
			$itemID = $this->_db->loadResult();

		}
		
		// get it from the current active menu
		
		if (!$itemID) {
			
			$menu			= JSite::getMenu();
			
			$currentMenu	= $menu->getActive();
			
			$itemID			= $currentMenu->id;
			
		}
		
		// just set it to the frontpage
		
		if (!$itemID) {
			
			$itemID			= 1;
			
		}
		
		// return the item id
		
		return $itemID;
		
	}
	
	/**
	 * Method to retrieve a button that toggles an item's status between enabled and disabled
	 */

	function itemToggleButton($id, $type, $published = 0, $keyword = "", $limitstart = 0) {
		
		// initiate required variables
		
		global $page, $sess;
		
		$function	 = $type == "productads" || $type == "categoryads"	? 	"vmaProductCategoryAdToggle" 	: "vmaItemToggle";
		
		$published	 = $type == "affiliate"								?	!$published						: $published;
		
		// button link
		
		$html 		 = "<a class=\"toolbar\" href=\"" . $this->getAdminLink() . "page=" . $page . "&amp;" . "type=" . $type . "&amp;" . "published=" . $published .
		
					   "&amp;" . "func=" . $function . "&amp;" . "id=" . $id . "&amp;" . "keyword=" . urlencode($keyword) . "&amp;" . "limitstart=" . 
					   
					   $limitstart . "&amp;" . "vmtoken=" . vmSpoofValue($sess->getSessionId()) . "\">";
		
		// button image
		
		$html		.= "<img src=\"" . $this->_website . "administrator/images/" . ($published ? "tick" : "publish_x") . ".png\" " . 
		
					   "alt=\"Toggle status\" style=\"border: 0 none;\" /></a>";

		return $html;
		
	}
	
	/**
	 * Method to retrieve a button that deletes an item
	 */
	 
	function itemDeleteButton($id, $type, $keyword = "", $limitstart = 0) {
		
		// initiate required variables
		
		global $page, $sess, $VM_LANG;
		
		$iconLink	 = $this->_website . "components/com_virtuemart/shop_image/ps_image/";
		
		// button link
		
		$html 		 = "<a class=\"toolbar\" href=\"" . $this->getAdminLink() . "page=" . $page . "&amp;" 	. "type=" . $type . 
		
					   "&amp;" . "func=" . "vmaItemDelete" . "&amp;" . "id=" . $id . "&amp;" . "keyword=" 	. urlencode($keyword) . "&amp;" . "limitstart=" . 
					   
					   $limitstart . "&amp;" . "vmtoken=" . vmSpoofValue($sess->getSessionId()) . "\" " 	. 
					   
					   "onclick=\"return confirm('" . $VM_LANG->_('PHPSHOP_DELETE_MSG') . "');\" " 			. 
					   
					   "onmouseout=\"document.getElementById('delete" 	. $id . "').src = '" . $iconLink 	. "delete.gif';\" " . 
					   
					   "onmouseover=\"document.getElementById('delete" 	. $id . "').src = '" . $iconLink 	. "delete_f2.gif';\">";
		
		// button image
		
		$html		.= "<img src=\"" . $iconLink . "delete.gif\" alt=\"Delete this record\" id=\"delete" . $id . "\" style=\"border: 0 none; text-align: center;\" /></a>";

		return $html;

	}
	
	/**
	 * Method to toggle an item's status between enabled and disabled
	 */

	function itemToggle() {
		
		global $vmLogger;
		
		// initiate required variables
		
		$id				= &JRequest::getVar("id");
		
		$type			= &JRequest::getVar("type");
		
		$published		= &JRequest::getVar("published");

		$table			= array("affiliate" 		=> "#__vm_affiliate", 	
		
								"banner" 			=> "#__vm_affiliate_banners",	
								
								"textad" 			=> "#__vm_affiliate_textads", 
								
								"paymentmethod" 	=> "#__vm_affiliate_methods");

		$valueField		= array("affiliate"			=> "blocked", 			
		
								"banner"			=> "published",				
								
								"textad" 			=> "published",
								
								"paymentmethod"		=> "method_enabled");
		
		$idField		= array("affiliate"			=> "affiliate_id", 		
		
								"banner"			=> "banner_id",				
								
								"textad"			=> "textad_id",
								
								"paymentmethod"		=> "method_id");
		
		$message		= array("affiliate"			=> $published ? JText::_("AFFILIATE_DISABLED")		: JText::_("AFFILIATE_ENABLED"),
		
								"paymentmethod"		=> $published ? JText::_("PAYMENT_METHOD_DISABLED")	: JText::_("PAYMENT_METHOD_ENABLED"));
		
		// perform the toggle query
		
		$query			= "UPDATE " . $table[$type] . " SET `" . $valueField[$type] . "` = 1 - " . $valueField[$type] . " WHERE `" . $idField[$type] . "` = '" . $id . "'";
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// update the offline tracking name values
		
		if ($type == "affiliate") {
			
			$this->updateOfflineNameValue($id, !$published);
		
		}
		
		// show the confirmation message
		
		if (isset($message[$type])) {
			
			$vmLogger->info($message[$type], false);
		
		}
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to delete an item
	 */

	function itemDelete() {
		
		global $vmLogger;
		
		// initiate required variables
		
		$id				= &JRequest::getVar("id");
		
		$type			= &JRequest::getVar("type");

		$table			= array("affiliate" 		=> "#__vm_affiliate", 	
		
								"banner" 			=> "#__vm_affiliate_banners",	
								
								"textad" 			=> "#__vm_affiliate_textads", 
								
								"paymentmethod" 	=> "#__vm_affiliate_methods",
								
								"sizegroup"			=> "#__vm_affiliate_size_groups");
		
		$idField		= array("affiliate"			=> "affiliate_id", 		
		
								"banner"			=> "banner_id",				
								
								"textad"			=> "textad_id",
								
								"paymentmethod"		=> "method_id",
								
								"sizegroup"			=> "size_group_id");
		
		$message		= array("affiliate"			=> JText::_("AFFILIATE_REMOVED"),
		
								"paymentmethod"		=> JText::_("PAYMENT_METHOD_REMOVED"));
		
		// update the offline tracking name values
		
		if ($type == "affiliate") {
			
			$this->updateOfflineNameValue($id, false);
		
		}
		
		// validate payment method removal
		
		if ($type == "paymentmethod") {
			
			// prevent removal of the paypal payment method
			
			if ($id == 1) {
				
				$vmLogger->info(JText::_("PAYMENT_METHOD_BUILT_IN"), false);
				
				return false;
				
			}
			
			// prevent removal of payment methods in use
			
			$query			= "SELECT COUNT(*) FROM #__vm_affiliate WHERE `method` = '" . $id . "'";
			
			$this->_db->setQuery($query);
			
			$inUse			= $this->_db->loadResult();
			
			if ($inUse) {
				
				$vmLogger->err(JText::_("PAYMENT_METHOD_IN_USE"), false);
				
				return false;
				
			}
		
		}
		
		// perform additional removal for banners
		
		if ($type == "banner") {
			
			// get banner filename
		
			$query		= "SELECT `banner_image`, `banner_type` FROM #__vm_affiliate_banners WHERE `banner_id` = '" . $id . "'";
			
			$this->_db->setQuery($query);
			
			$bannerInfo = $this->_db->loadObject();
			
			// determine paths and filename
			
			$commonPath	= JPATH_ROOT . DS . "components" . DS . "com_affiliate";
			
			$fileName	= $bannerInfo->banner_image . "." . $bannerInfo->banner_type;
			
			// remove all related files
			
			@unlink($commonPath . DS . "banners" 	. DS 				. $fileName);
			
			@unlink($commonPath . DS . "thumbs" 	. DS . "thumb_" 	. $fileName);
			
			@unlink($commonPath . DS . "thumbs" 	. DS . "thumbbig_" 	. $fileName);
		
		}
		
		// perform the toggle query
		
		$query			= "DELETE FROM " . $table[$type] . " WHERE `" . $idField[$type] . "` = '" . $id . "'";
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// show the confirmation message
		
		if (isset($message[$type])) {
			
			$vmLogger->info($message[$type], false);
		
		}
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to retrieve an edit button for affiliates
	 */
	 
	function affiliateEditButton($affiliateID) {
		
		global $sess;
		
		// button link
		
		$html 		 = "<a class=\"toolbar\" href=\"" . $this->getAdminLink() . 
		
					   "page=vma.affiliate_form" . "&amp;" . "affiliate_id=" . $affiliateID . "&amp;" . "vmtoken=" . vmSpoofValue($sess->getSessionId()) . "\">";
		
		// button image
		
		$html		.= "<img src=\"" . $this->_website . "components/com_affiliate/assets/images/edit.png\" " . 
		
					   "alt=\"Edit affiliate's details\" name=\"edit" . $affiliateID . "\" style=\"border: 0 none;\" /></a>";

		return $html;
		
	}
	
	/**
	 * Method to retrieve an individual commission rates button for affiliates
	 */
	 
	function affiliateRatesButton($affiliateID) {
		
		global $sess;
		
		// button link
		
		$html 		 = "<a class=\"toolbar\" href=\"" . $this->getAdminLink() . 
		
					   "page=vma.commission_rates_form" . "&amp;" . "affiliate_id=" . $affiliateID . "&amp;" . "vmtoken=" . vmSpoofValue($sess->getSessionId()) . "\">";
		
		// button image
		
		$html		.= "<img src=\"" . $this->_website . "components/com_affiliate/assets/images/rates.png\" " . 
		
					   "alt=\"Set affiliate's commission rates\" name=\"rates" . $affiliateID . "\" style=\"border: 0 none;\" /></a>";

		return $html;
		
	}
	
	/**
	 * Method to add an affiliate
	 */
	 
	function affiliateAdd() {
		
		global $vmaSettings, $vmLogger;
		
		// get registration form fields
		
		$newAffiliate 					= array();
		
		$newAffiliate["username"] 		= JRequest::getString("username", 	"", 	"post");
		
		$newAffiliate["password"]		= JRequest::getString("password", 	"", 	"post", JREQUEST_ALLOWRAW);
		
		$newAffiliate["vpassword"]		= JRequest::getString("vpassword", 	"", 	"post", JREQUEST_ALLOWRAW);
		
		$newAffiliate["mail"] 			= JRequest::getString("mail", 		"", 	"post");
		
		$newAffiliate["website"] 		= JRequest::getString("website", 	"", 	"post");
		
		$newAffiliate["fname"] 			= JRequest::getString("fname", 		"", 	"post");
		
		$newAffiliate["lname"] 			= JRequest::getString("lname", 		"", 	"post");
		
		$newAffiliate["street"] 		= JRequest::getString("street", 	"", 	"post");
		
		$newAffiliate["city"] 			= JRequest::getString("city", 		"", 	"post");
		
		$newAffiliate["state"] 			= JRequest::getString("state", 		"", 	"post");
		
		$newAffiliate["country"] 		= JRequest::getString("country", 	"", 	"post");
		
		$newAffiliate["zipcode"] 		= JRequest::getString("zipcode", 	"", 	"post");
		
		$newAffiliate["phoneno"] 		= JRequest::getString("phoneno", 	"", 	"post");
		
		$newAffiliate["taxssn"] 		= JRequest::getString("taxssn", 	"", 	"post");
		
		// validate form input
		
		if (!$newAffiliate["username"]) {
			
			$vmLogger->err(JText::_("PROVIDE_USERNAME"),							false);
			
			return false;
			
		}
		
		if (!$newAffiliate["password"]) {
			
			$vmLogger->err(JText::_("PROVIDE_PASSWORD"), 							false);
			
			return false;
			
		}
		
		if (!$newAffiliate["vpassword"]) {
			
			$vmLogger->err(JText::_("RETYPE_PASSWORD"), 							false);
			
			return false;
			
		}
		
		if ($newAffiliate["password"] != $newAffiliate["vpassword"]) {
			
			$vmLogger->err(JText::_("PASSWORDS_DIFFER"), 							false);
			
			return false;
			
		}
		
		if (!$newAffiliate["mail"]) {
			
			$vmLogger->err(JText::_("PROVIDE_EMAIL_ADDRESS"), 						false);
			
			return false;
			
		}
		
		if (!$newAffiliate["fname"]) {
			
			$vmLogger->err(JText::_("PROVIDE_FIRST_NAME"), 							false);
			
			return false;
			
		}
		
		if (!$newAffiliate["lname"]) {
			
			$vmLogger->err(JText::_("PROVIDE_LAST_NAME"), 							false);
			
			return false;
			
		}
		
		if (!$newAffiliate["street"]) {
			
			$vmLogger->err(JText::_("PROVIDE_ADDRESS"), 							false);
			
			return false;
			
		}
		
		if (!$newAffiliate["city"]) {
			
			$vmLogger->err(JText::_("PROVIDE_CITY"), 								false);
			
			return false;
			
		}
		
		if (!$newAffiliate["country"]) {
			
			$vmLogger->err(JText::_("PROVIDE_COUNTRY"), 							false);
			
			return false;
			
		}
		
		if (!$newAffiliate["zipcode"]) {
			
			$vmLogger->err(JText::_("PROVIDE_ZIPCODE"), 							false);
			
			return false;
			
		}
		
		// check if either the username or e-mail address are already registered

		$query 		= "SELECT `username`, `mail` FROM #__vm_affiliate WHERE `username` = '" . $newAffiliate['username'] . "' OR `mail` = '" . $newAffiliate['mail'] . "'";
		
		$this->_db->setQuery( $query );
		
		$info		= $this->_db->loadAssoc();
		
		if ($info["username"] == $newAffiliate['username']) {
			
			$vmLogger->err(JText::_("USERNAME_ALREADY_EXISTS"),						false);
			
			return false;
			
		}
		
		else if ($info["mail"] == $newAffiliate['mail']) {
			
			return false;
			
		}
		
		// get new affiliate special parameters

		$initialBonus		= $vmaSettings->initial_bonus;
		
		$timeAndDate		= date("Y-m-d\TH:i:s");
		
		// properly escape data
		
		foreach ($newAffiliate as $property => $value) {
			
			if ($property != "password") {
				
				$newAffiliate[$property] = $this->_db->getEscaped($newAffiliate[$property]);
			
			}
			
		}
		
		// insert the new affiliate in the database
		
		$query 				= "INSERT INTO #__vm_affiliate VALUES ('" . NULL . "', '" . $newAffiliate["username"] . "', '" . md5($newAffiliate["password"]) . "', " . 
		
							  "'" . $newAffiliate["fname"] . "', '" . $newAffiliate["lname"] . "', '" . $newAffiliate["mail"] . "', '" . $newAffiliate["website"] . "', " . 
							  
							  "'" . $initialBonus . "', '" . $newAffiliate["street"] . "', '" . $newAffiliate["city"] . "', " . "'" . $newAffiliate["state"] . "', " . 
							  
							  "'" . $newAffiliate["country"] . "', '" . $newAffiliate["zipcode"] . "', '" . $newAffiliate["phoneno"] . "', '" . $newAffiliate["taxssn"] . "', " .
							  
							  "'N/A', '0', '0', '0', '0', '2', '0', '1', '', '0', '', '" . $timeAndDate . "')";
							  
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// get the new affiliate's id
		
		$affiliateID 		= $this->_db->insertid();
		
		// update the offline tracking name values
		
		$this->updateOfflineNameValue($affiliateID, true);
		
		// e-mail the new affiliate
		
		$affiliateMail		= &JFactory::getMailer();
			
		$affiliateMail->addRecipient($newAffiliate["mail"]);
		
		$affiliateMail->setSender(array($this->_config->getValue( 'config.mailfrom' ), $this->_config->getValue( 'config.fromname' )));
		
		$affiliateMail->setSubject(JText::sprintf("WELCOME_MESSAGE", $this->_config->getValue( 'config.sitename' )));
		
		$affiliateMail->setBody(JText::sprintf("WELCOME_EMAIL", "\r\n\r\n", $newAffiliate["username"] . "\r\n\r\n", 
		
								"\r\n" . JRoute::_($this->_website . "index.php?option=com_affiliate&view=login") . "\r\n\r\n", 
								
								"\r\n" . $this->_config->getValue( 'config.sitename' ) . "\r\n" . $this->_website));
		
		$affiliateMail->send();
		
		// show the confirmation message
		
		$vmLogger->info(JText::_("AFFILIATE_ADDED"), false);
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to update an affiliate's details
	 */
	 
	function affiliateUpdate() {
		
		global $vmaSettings, $vmLogger;
		
		// get edit form fields
		
		$newAffiliate 					= array();
		
		$newAffiliate["affiliate_id"]	= JRequest::getInt("affiliate_id",	"",		"post");
		
		$newAffiliate["mail"] 			= JRequest::getString("mail", 		"", 	"post");
		
		$newAffiliate["website"] 		= JRequest::getString("website", 	"", 	"post");
		
		$newAffiliate["fname"] 			= JRequest::getString("fname", 		"", 	"post");
		
		$newAffiliate["lname"] 			= JRequest::getString("lname", 		"", 	"post");
		
		$newAffiliate["street"] 		= JRequest::getString("street", 	"", 	"post");
		
		$newAffiliate["city"] 			= JRequest::getString("city", 		"", 	"post");
		
		$newAffiliate["state"] 			= JRequest::getString("state", 		"", 	"post");
		
		$newAffiliate["country"] 		= JRequest::getString("country", 	"", 	"post");
		
		$newAffiliate["zipcode"] 		= JRequest::getString("zipcode", 	"", 	"post");
		
		$newAffiliate["phoneno"] 		= JRequest::getString("phoneno", 	"", 	"post");
		
		$newAffiliate["taxssn"] 		= JRequest::getString("taxssn", 	"", 	"post");
		
		// validate form input
		
		if (!$newAffiliate["affiliate_id"]) {
			
			return false;
			
		}
		
		if (!$newAffiliate["mail"]) {
			
			$vmLogger->err(JText::_("PROVIDE_EMAIL_ADDRESS"), 						false);
			
			return false;
			
		}
		
		if (!$newAffiliate["fname"]) {
			
			$vmLogger->err(JText::_("PROVIDE_FIRST_NAME"), 							false);
			
			return false;
			
		}
		
		if (!$newAffiliate["lname"]) {
			
			$vmLogger->err(JText::_("PROVIDE_LAST_NAME"), 							false);
			
			return false;
			
		}
		
		if (!$newAffiliate["street"]) {
			
			$vmLogger->err(JText::_("PROVIDE_ADDRESS"), 							false);
			
			return false;
			
		}
		
		if (!$newAffiliate["city"]) {
			
			$vmLogger->err(JText::_("PROVIDE_CITY"), 								false);
			
			return false;
			
		}
		
		if (!$newAffiliate["country"]) {
			
			$vmLogger->err(JText::_("PROVIDE_COUNTRY"), 							false);
			
			return false;
			
		}
		
		if (!$newAffiliate["zipcode"]) {
			
			$vmLogger->err(JText::_("PROVIDE_ZIPCODE"), 							false);
			
			return false;
			
		}
		
		// check if the e-mail address is already registered

		$query 		= "SELECT `affiliate_id`, `mail` FROM #__vm_affiliate WHERE `mail` = '" . $newAffiliate["mail"] . "'";
		
		$this->_db->setQuery( $query );
		
		$info		= $this->_db->loadAssoc();
		
		if ($info["mail"] == $newAffiliate['mail'] && $info["affiliate_id"] != $newAffiliate["affiliate_id"]) {
			
			return false;
			
		}
		
		// properly escape data
		
		foreach ($newAffiliate as $property => $value) {
			
			$newAffiliate[$property] = $this->_db->getEscaped($newAffiliate[$property]);
			
		}
		
		// insert the new affiliate in the database
		
		$query 				= "UPDATE #__vm_affiliate SET `fname` = '" 			. $newAffiliate["fname"] 	. "', `lname` = '" 	. $newAffiliate["lname"] 	. "', `mail` = '" 		. 
		
							  $newAffiliate["mail"] 	. "', `website` = '" 	. $newAffiliate["website"] 	. "', `street` = '" . $newAffiliate["street"] 	. "', `city` = '" 		. 
							  
							  $newAffiliate["city"] 	. "', `country` = '" 	. $newAffiliate["country"] 	. "', `state` = '" 	. $newAffiliate["state"] 	. "', `zipcode` = '" 	. 
							  
							  $newAffiliate["zipcode"] 	. "', `phoneno` = '" 	. $newAffiliate["phoneno"] 	. "', `taxssn` = '" . $newAffiliate["taxssn"] 	. "' WHERE " 			. 
							  
							  "`affiliate_id` = '" . $newAffiliate["affiliate_id"] . "'";
							  
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		
		// update the offline tracking name values
		
		$this->updateOfflineNameValue($newAffiliate["affiliate_id"], "update");
		
		// show the confirmation message
		
		$vmLogger->info(JText::_("AFFILIATE_UPDATED"), false);
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to change an affiliate's password
	 */
	 
	function affiliatePasswordUpdate() {
		
		global $vmLogger;
		
		// get affiliate's id
		
		$affiliateID					= JRequest::getVar("affiliate_id", 						"");
		
		// get password form
		
		$password						= array();
		
		$password["new"] 				= JRequest::getString("newpassword", 		"", 	"post");
		
		$password["verify"] 			= JRequest::getString("verifypassword", 	"", 	"post");
		
		// validate form input
		
		if (!$password["new"]) {
			
			$vmLogger->err(JText::_("PROVIDE_PASSWORD"), 	false);
			
			return false;
			
		}
		
		if (!$password["verify"]) {
			
			$vmLogger->err(JText::_("RETYPE_PASSWORD"), 	false);
			
			return false;
			
		}
		
		if ($password["new"] != $password["verify"]) {
			
			$vmLogger->err(JText::_("PASSWORDS_DIFFER"), 	false);
			
			return false;
			
		}
		
		// update password
		
		$query 				= "UPDATE #__vm_affiliate SET `password` = '" . md5($password["new"]) . "' WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// show the confirmation message
		
		$vmLogger->info(JText::_("AFFILIATE_UPDATED"), false);
			
		return true;
		
	}
	
	/**
	 * Method to save affiliate's preferences, more specifically its payment method
	 */
	 
	function affiliatePreferencesUpdate() {
		
		global $vmLogger;
		
		// get affiliate's id and chosen payment method
				
		$paymentMethod 		= JRequest::getVar('paymentmethod', '');
		
		$affiliateID		= JRequest::getVar('affiliate_id',	'');
		
		// validate payment method
		
		if (!$paymentMethod) {
			
			$vmLogger->err(JText::_("PROVIDE_PAYMENT_METHOD"), false);
			
			return false;
			
		}
		
		// get existing payment details
		
		$paymentDetails		= array();
		
		$query				= "SELECT `field_id` FROM #__vm_affiliate_payment_details WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$this->_db->setQuery($query);
		
		$setPaymentDetails	= $this->_db->loadResultArray();
		
		// get new payment details, validate them, and prepare their query
		
		foreach ($_POST as $key => $value) {
			
			if (stristr($key, "payment-field")) {
				
				list( , , $paymentFieldID)			= explode("-", $key);
				
				$paymentValue 						= JRequest::getVar($key, '');
				
				// paypal method validation
				
				if ($paymentMethod == '1' && $paymentFieldID == '1' && !$paymentValue) {
					
					$vmLogger->err(JText::_("PROVIDE_EMAIL_ADDRESS"), false);
			
					return false;
			
				}
				
				// prepare the payment field query
				
				$paymentDetails[]					= !in_array($paymentFieldID, $setPaymentDetails) ? 
				
													  "INSERT INTO #__vm_affiliate_payment_details VALUES ('" . $affiliateID . "', '" . 
				
													  $paymentFieldID . "', '" . $paymentValue . "')" :
													  
													  "UPDATE #__vm_affiliate_payment_details SET `field_value` = '" . $paymentValue . "' WHERE `affiliate_id` = '" . 
													  
													  $affiliateID . "' AND `field_id` = '" . $paymentFieldID . "'";
				
			}
			
		}
		
		// update the affiliate's payment method and its details
		
		$query = "UPDATE #__vm_affiliate SET `method` = '" . $paymentMethod . "' WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		foreach ($paymentDetails as $paymentField) {
			
			$this->_db->setQuery($paymentField);
			
			$this->_db->query();
			
		}
		
		// show confirmation message
		
		$vmLogger->info(JText::_("PAYMENT_INFORMATION_UPDATED"), false);
			
		return true;

	}
	
	/**
	 * Method to link an affiliate to a site user
	 */

	function affiliateLink() {
		
		global $vmLogger;
		
		// get affiliate's id
		
		$affiliateID		= JRequest::getVar("affiliate_id", 	"");
			
		// get input
		
		$username			= JRequest::getVar("username", 		"");
		
		$password			= JRequest::getVar("password", 		"");
		
		// validate it
		
		if (!$username) {
			
			$vmLogger->err(JText::_("PROVIDE_USERNAME"), 		false);
	
			return false;
			
		}
		
		if (!$password) {
			
			$vmLogger->err(JText::_("PROVIDE_PASSWORD"), 		false);
	
			return false;
			
		}
		
		// check if such an account exists in joomla
		
		$query				= "SELECT `password` FROM #__users WHERE `username` = '" . $username . "'";
		
		$this->_db->setQuery($query);
		
		$saltedPassword		= $this->_db->loadResult();
		
		// no such account exists
		
		if (empty($saltedPassword)) {
			
			$vmLogger->err(JText::_("E_LOGIN_AUTHENTICATE"), false);
	
			return false;
			
		}
		
		// check password
		
		list( , $salt)	= explode(":", $saltedPassword);
		
		$password 		= md5($password . $salt) . ":" . $salt;
		
		// password is incorrect
		
		if ($password != $saltedPassword) {
			
			$vmLogger->err(JText::_("INCORRECT_PASSWORD"), false);

			return false;
			
		}
			
		// check if another affiliate is already linked to that user
		
		$query 			= "SELECT `affiliate_id` FROM #__vm_affiliate WHERE `linkedto` = '" . $username . "'";
		
		$this->_db->setQuery($query);
		
		$alreadyLinked 	= $this->_db->loadResult();
		
		// another affiliate is linked to that user
		
		if ($alreadyLinked) {
			
			$vmLogger->err(JText::_("ALREADY_LINKED"), false);

			return false;
			
		}
		
		// link affiliate to site user
		
		$query = "UPDATE #__vm_affiliate SET `linkedto` = '" . $username . "' WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// show confirmation message
		
		$vmLogger->info(JText::_("LINKING_SUCCESSFUL"), false);
		
		// confirm the operation
		
		return true;
		
	}
	
	/** 
	 * Method to unlink an affiliate from a site user
	 */
	
	function affiliateUnlink() {
		
		global $vmLogger;
		
		// get affiliate's id
		
		$affiliateID		= JRequest::getVar("affiliate_id", "");
		
		// reset linking
				
		$query = "UPDATE #__vm_affiliate SET `linkedto` = '' WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// show confirmation message
		
		$vmLogger->info(JText::_("UNLINKED"), false);
		
		// confirm the operation
		
		return true;
			
	}
	
	/**
	 * Method that logs an affiliate payment and marks its records as paid
	 */
	
	function affiliatePay() {
		
		global $vmLogger;
		
		// initiate required variables
		
		$affiliateID 	= JRequest::getVar("affiliate_id", "");
		
		$query			= "SELECT * FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$this->_db->setQuery($query);
		
		$affiliate		= $this->_db->loadObject();
		
		$query			= "SELECT `method_name` FROM #__vm_affiliate_methods WHERE `method_id` = '" . $affiliate->method . "'";
		
		$this->_db->setQuery($query);
		
		$paymentName	= $this->_db->loadResult();
		
		// validate information
		
		if (!$affiliateID || $affiliate->commissions == 0) {
			
			return false;
			
		}
		
		// log the payment
		
		$query			= "INSERT INTO #__vm_affiliate_payments VALUES ('', '" . $affiliate->affiliate_id . "', '" . $affiliate->username . 
		
						  "', '" . $paymentName . "', '" . $affiliate->commissions . "', '" . date("Y-m-d") . "', 'C', '')";
						  
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// clear affiliate's balance
		
		$query			= "UPDATE #__vm_affiliate SET `commissions` = '0.0000' WHERE `affiliate_id` = '" 	. $affiliate->affiliate_id . "'";
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// mark clicks as paid
		
		$query			= "UPDATE #__vm_affiliate_clicks SET `paid` = '1' WHERE `AffiliateID` = '" 			. $affiliate->affiliate_id . "'";
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// mark orders as paid
		
		$query			= "UPDATE #__vm_affiliate_orders SET `paid` = '1' WHERE `affiliate_id` = '" 		. $affiliate->affiliate_id . "' " . 
		
						  "AND " . $this->buildStatusesCondition("pending", "AND", "!=", "order_status") . " " .
						  
						  "AND " . $this->buildStatusesCondition("cancelled", "AND", "!=", "order_status");
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// show the confirmation message
		
		$vmLogger->info(JText::_("PAYMENT_SUCCESSFUL"), false);
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to save a banner
	 */
	 
	function bannerSave() {
		
		global $vmLogger;
		
		// initiate required variables
		
		$bannerID			= JRequest::getVar('banner_id', 	'');
		
		$bannerName			= JRequest::getVar('name',			'');
		
		$bannerName			= $this->_db->getEscaped($bannerName);
		
		$bannerLink			= JRequest::getVar('link',			'');
		
		$bannerDetails		= JRequest::getVar('bannerDetails', '');
	
		$bannerSize			= JRequest::getVar('size',			'');
		
		$bannerLink			= str_replace("&amp;", "&", 		$bannerLink);
		
		// validate the banner form
		
		if (!$bannerName) {
			
			$vmLogger->err(JText::_("PROVIDE_BANNER_NAME"), false);
					
			return false;
				
		}
		
		if (!$bannerID && !$bannerDetails) {
				
			$vmLogger->err(JText::_("PROVIDE_BANNER_IMAGE"), false);
				
			return false;
			
		}
			
		// add the new banner
		
		if (!$bannerID) {
			
			// prepare required variables
			
			list($bannerImage, $bannerOldExtension, $bannerFileType) = explode("|", $bannerDetails);
		
			$bannerPath		= JPATH_ROOT 	. DS . "components" . DS . "com_affiliate" 	. DS 	. "banners";
			
			$temporaryFile	= $bannerPath	. DS . "temp_" 		. $bannerImage 			. "." 	. $bannerOldExtension;
			
			// preserve the image size
			
			if (!$bannerSize || $bannerSize == "preserve") {
				
				// determine banner dimensions
				
				list($bannerWidth, $bannerHeight) = @getimagesize($temporaryFile);
				
				// move uploaded file
				
				if (!@rename($temporaryFile, $bannerPath . DS . $bannerImage . "." . $bannerFileType)) {
					
					$vmLogger->err(JText::_("BANNER_MOVING_FAILURE"), false);
					
					return false;
					
				}
				
			}
			
			// resize the image
			
			else {
					
				// get the size group width and height
				
				$query			= "SELECT `width`, `height` FROM #__vm_affiliate_size_groups WHERE `size_group_id` = '" . $bannerSize . "'";
				
				$this->_db->setQuery($query);
				
				$size			= $this->_db->loadObject();
				
				// determine banner dimensions
				
				$bannerWidth	= $size->width;
				
				$bannerHeight	= $size->height;
				
				// resize the banner image
				
				$this->resizeImage($temporaryFile, $bannerImage, $type = "banner", $bannerWidth, $bannerHeight);
				
				// remove the temporary image
				
				@unlink($temporaryFile);
				
			}
			
			// insert the banner in the database
			
			$query = "INSERT INTO #__vm_affiliate_banners VALUES ('', '" . $bannerName 		. "', '" . $bannerImage . "', '" . 
			
					 $bannerFileType . "', '" . $bannerWidth 	. "', '" . $bannerHeight 	. "', '" . $bannerLink 	. "', '1')";
			
			$this->_db->setQuery($query);
			
			$this->_db->query();
			
		}
		
		// save the edited banner
		
		else {
			
			$query = "UPDATE #__vm_affiliate_banners SET `banner_name` = '" . $bannerName . "', `banner_link` = '" . $bannerLink . "' WHERE `banner_id` = '" . $bannerID . "'";
			
			$this->_db->setQuery($query);
			
			$this->_db->query();
			
		}
		
		// show the confirmation message
		
		$vmLogger->info(JText::_("BANNER_SUCCESSFULLY_SAVED"), false);
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to save a text ad
	 */
	 
	function textAdSave() {
		
		global $vmLogger;
		
		// get text ad's id
		
		$textAdID						= JRequest::getVar("textad_id", 					"");
		
		// get text ad form
		
		$textAd							= array();
		
		$textAd["title"] 				= JRequest::getString("title", 				"", 	"post");
		
		$textAd["title"]				= $this->_db->getEscaped($textAd["title"]);
		
		$textAd["link"] 				= JRequest::getString("link", 				"", 	"post");
		
		$textAd["size"] 				= JRequest::getString("size", 				"", 	"post");
		
		$textAd["text"] 				= $_POST["affiliateText"];
		
		$textAd["link"]					= str_replace("&amp;", "&", 				$textAd["link"]);
		
		// validate the text ad form
		
		if (!$textAd["title"]) {
			
			$vmLogger->err(JText::_("PROVIDE_TITLE"), false);
					
			return false;
				
		}
		
		if (!$textAd["text"]) {
				
			$vmLogger->err(JText::_("PROVIDE_CONTENT"), false);
				
			return false;
			
		}
		
		if (!$textAd["size"]) {
				
			$vmLogger->err(JText::_("FILL_IN_ALL_FIELDS"), false);
				
			return false;
			
		}
		
		// get the size group's parameters
		
		$query		= "SELECT `width`, `height` FROM #__vm_affiliate_size_groups WHERE `size_group_id` = '" . $textAd["size"] . "'";
		
		$this->_db->setQuery($query);
		
		$sizeGroup 	= $this->_db->loadObject();
		
		// determine query
		
		$query	= $textAdID ? 
				  
				  "UPDATE #__vm_affiliate_textads SET `title` = '" . $textAd["title"] . "', `content` = '" . $textAd["text"] . "', `width` = '" . 
				  
				  $sizeGroup->width . "', `link` = '" . $textAd["link"] . "', `height` = '" . $sizeGroup->height . "' WHERE `textad_id` = '" . $textAdID . "'" :
				  
				  "INSERT INTO #__vm_affiliate_textads VALUES ('', '" . $textAd["title"] . "', '" . $textAd["text"] . "', '1', " . 
				  
				  "'" . $textAd["link"] . "', '" . $sizeGroup->width . "', '" . $sizeGroup->height . "')";
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// show the confirmation message
		
		$vmLogger->info(JText::_("TEXT_AD_SAVED"), false);
		
		// confirm the operation
			
		return true;
		
	}
	
	/**
	 * Method to save a payment method and its fields
	 */

	function paymentMethodSave() {
		
		global $vmLogger;
		
		// initiate required variables
		
		$methodID		= JRequest::getVar("method_id",	"");
		
		$methodName		= JRequest::getVar("name",		"");
		
		$newFields		= JRequest::getVar("fields",	array());
		
		$updatedFields	= array();
		
		$removedFields	= array();
		
		// get previous payment method's fields

		if ($methodID) {
			
			$query			= "SELECT `field_id` FROM #__vm_affiliate_method_fields WHERE `method_id` = '" . $methodID . "'";
			
			$this->_db->setQuery($query);
			
			$previousFields = $this->_db->loadObjectList();

		}
		
		// get filtered updated fields
		
		foreach ($_POST as $key => $value) {
			
			if (stristr($key, "field") && $key != "fields") {
				
				$fieldID					= str_replace("field", "", $key);
				
				$updatedFields[$fieldID]	= JRequest::getVar($key,	"");
				
			}
			
		}
		
		// make sure the payment method's name isn't empty
		
		if (!$methodName) {
			
			$vmLogger->err(JText::_("PROVIDE_PAYMENT_METHOD_NAME"), false);
			
			return false;
			
		}
		
		// add the payment method, if it doesn't exist
		
		if (!$methodID) {
			
			$query			= "INSERT INTO #__vm_affiliate_methods VALUES ('', '" . $methodName . "', '1', 'Manual', '0')";
			
			$this->_db->setQuery($query);
			
			$this->_db->query();
			
			$methodID		= $this->_db->insertid();
			
		}
		
		// update the payment method's name
		
		else {
			
			$query			= "UPDATE #__vm_affiliate_methods SET `method_name` = '" . $methodName . "' WHERE `method_id` = '" . $methodID . "'";
			
			$this->_db->setQuery($query);
			
			$this->_db->query();
			
		}
		
		// insert the new fields
		
		foreach ($newFields as $newField) {
			
			if ($newField) {
				
				$query		= "INSERT INTO #__vm_affiliate_method_fields VALUES ('', '" . $methodID . "', '" . $newField . "')";
				
				$this->_db->setQuery($query);
				
				$this->_db->query();
			
			}
			
		}
		
		// update the updated fields
		
		foreach ($updatedFields as $fieldID => $fieldName) {
			
			// update the field name
			
			if ($fieldName) {
				
				$query				= "UPDATE #__vm_affiliate_method_fields SET `field_name` = '" . $fieldName . "' WHERE `field_id` = '" . $fieldID . "'";
				
				$this->_db->setQuery($query);
				
				$this->_db->query();
				
			}
			
			// name is empty, so trash it
			
			else {
				
				$removedFields[]	= $fieldID;
				
			}
			
		}

		// get removed fields

		if (isset($previousFields)) {
			
			foreach ($previousFields as $previousField) {

				if (!isset($updatedFields[$previousField->field_id])) {
					
					$removedFields[] = $previousField->field_id;
					
				}
				
			}
			
		}

		// remove all required fields

		foreach ($removedFields as $removedField) {

			// make sure the field isn't currently in use
			
			$query		= "SELECT affiliate.`affiliate_id` FROM #__vm_affiliate affiliate, #__vm_affiliate_payment_details details " 	. 
			
						  "WHERE details.`affiliate_id` = affiliate.`affiliate_id` AND details.`field_value` != '' " 					. 
						  
						  "AND affiliate.`method` = '" . $methodID . "' AND details.`field_id` = '" . $removedField . "'";
							  
			$this->_db->setQuery($query);
			
			$fieldInUse = $this->_db->loadResult();

			// if it isn't, remove it
			
			if (!$fieldInUse) {
				
				$query	= "DELETE FROM #__vm_affiliate_method_fields WHERE `field_id` = '" . $removedField . "'";
				
				$this->_db->setQuery($query);
				
				$this->_db->query();
				
			}
			
			// otherwise, issue an error
			
			else {
				
				$vmLogger->err(JText::_("PAYMENT_METHOD_IN_USE"), false);
				
			}
			
		}
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to save a size group
	 */
	 
	function sizeGroupSave() {
		
		global $vmLogger;
		
		// determine type
		
		$type							= JRequest::getVar("type",						"");
		
		// get size group's id
		
		$sizeGroupID					= JRequest::getVar("size_group_id", 			"");
		
		// get size group form
		
		$sizeGroup						= array();
		
		$sizeGroup["name"] 				= JRequest::getString("name", 		"", 	"post");
		
		$sizeGroup["width"] 			= JRequest::getString("width", 		"", 	"post");
		
		$sizeGroup["height"] 			= JRequest::getString("height", 	"", 	"post");
		
		// check if such a size group doesn't already exist
		
		$query	= "SELECT * FROM #__vm_affiliate_size_groups WHERE `width` = '" . $sizeGroup["width"] . "' AND `height` = '" . $sizeGroup["height"] . "'";
		
		$this->_db->setQuery($query);
		
		$result = $this->_db->loadResult();
		
		if ($result) {
			
			return false;
			
		}
		
		// determine query
		
		$query	= $sizeGroupID ? 
				  
				  "UPDATE #__vm_affiliate_size_groups SET `name` = '" . $sizeGroup["name"] . "', `width` = '" . $sizeGroup["width"] . "', `height` = '" . 
				  
				  $sizeGroup["height"] . "' WHERE `size_group_id` = '" . $sizeGroupID . "'" :
				  
				  "INSERT INTO #__vm_affiliate_size_groups VALUES ('', '" . $sizeGroup["width"] . "', '" . $sizeGroup["height"] . "', '" . $sizeGroup["name"] . "')";
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// confirm the operation
			
		return true;
		
	}
	
	/**
	 * Method to toggle a product/category ad's status between published and unpublished
	 */
	 
	function productCategoryAdToggle() {
		
		// initiate required variables
		
		$id				= &JRequest::getVar("id");
		
		$type			= &JRequest::getVar("type");
		
		$operation		= &JRequest::getVar("operation",		"toggle");

		$id				= is_array($id) ? $id : array($id);
		
		$column			= $type == "productads" ? "product_id"	: "category_id";
		
		$table			= $type == "productads" ? "links"		: "links_categories";
		
		foreach ($id as $item) {
			
			// determine current ad status
			
			$query			= "SELECT `published` FROM #__vm_affiliate_" . $table . " WHERE `" . $column . "` = '" . $item . "'";
			
			$this->_db->setQuery($query);
			
			$published		= $this->_db->loadResult();
			
			// determine new status
			
			$newStatus		= $operation == "toggle" ? ($published == NULL ? 0 : ($published ? 0 : 1)) : ($operation == "publish" ? 1 : 0);
			
			// perform the toggle query
			
			$query			= $published == NULL ? 
			
							  "INSERT INTO #__vm_affiliate_" 			. $table . " VALUES ('', '" . $item . "', '" . $newStatus . "')" : 
							  
							  "UPDATE #__vm_affiliate_" 				. $table . " SET `published` = '" . ($newStatus) . "' WHERE `" . $column . "` = '" . $item . "'";
			
			$this->_db->setQuery($query);
			
			$this->_db->query();
			
		}
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to save the commission rates (either general, from a specific tier, or for a specific affiliate)
	 */
	 
	function commissionRatesUpdate() {
		
		global $vmLogger, $vmaSettings;
		
		// initialize required variables
		
		$affiliateID				= JRequest::getVar("affiliate_id",				"");
		
		$tier						= JRequest::getVar("tier",						"1");
		
		if ($affiliateID) {
		
			$useDefaults			= JRequest::getVar("use_defaults",				"0");
			
		}
		
		// get newly set commission rates
		
		$per_click_fixed			= JRequest::getVar("per_click_fixed",			"0");
		
		$per_unique_click_fixed 	= JRequest::getVar("per_unique_click_fixed",	"0");
		
		$per_sale_fixed				= JRequest::getVar("per_sale_fixed",			"0");
		
		$per_sale_percentage		= JRequest::getVar("per_sale_percentage",		"0");
		
		// get newly set discount rates, if applicable
		
		if ($vmaSettings->offline_tracking && $vmaSettings->offline_type == "3" && $tier == 1) {
			
			$discount_type			= JRequest::getVar("discount_type",				"1");
			
			$discount_amount		= JRequest::getVar("discount_amount",			"0");
			
		}
		
		// update the defaults setting
		
		if ($affiliateID) {
			
			$query					= "UPDATE #__vm_affiliate SET `use_defaults` = '" . $useDefaults . "' WHERE `affiliate_id` = '" . $affiliateID . "'";
			
			$this->_db->setQuery($query);
			
			$this->_db->query();
		
		}
		
		// update the commission rates
		
		if (!$affiliateID || !$useDefaults) {
			
			$query					= "UPDATE #__vm_affiliate" 	. (!$affiliateID ? "_rates" : NULL) 	. " SET " 							. 
			
									  "`per_click_fixed` = '" 	. $per_click_fixed 						. "', `per_unique_click_fixed` = '" . $per_unique_click_fixed 	. "', " .
									  
									  "`per_sale_fixed` = '" 	. $per_sale_fixed 						. "', `per_sale_percentage` = '" 	. $per_sale_percentage 		. "' " 	.
									  
									  "WHERE " 					. ($affiliateID ? "`affiliate_id` = '" 	. $affiliateID . "'" : "`rate` = '" . $tier . "'");
									  
			$this->_db->setQuery($query);
			
			$this->_db->query();
			
		}
		
		// update the discount rate
		
		if ($vmaSettings->offline_tracking && $vmaSettings->offline_type == "3" && $tier == 1 && (!$affiliateID || !$useDefaults)) {
			
			$query					= "UPDATE #__vm_affiliate" 	. (!$affiliateID ? "_settings" : NULL) 	. " SET " 							. 
		
									  "`discount_type` = '" 	. $discount_type 						. "', `discount_amount` = '"		. $discount_amount 	. "' " .
									  
									  "WHERE " 					. ($affiliateID ? "`affiliate_id` = '" 	. $affiliateID 						. "'" : "`setting` = '1'");
								  
			$this->_db->setQuery($query);
			
			$this->_db->query();
			
		}
					
		// show the confirmation message
		
		$vmLogger->info(JText::_("COMMISSION_RATES_SET"), false);
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to save the configuration values
	 */

	function configurationUpdate() {
		
		global $vmLogger;
		
		// get form values
		
		$allowSignups		= JRequest::getVar("allow_signups", 									"0");
		
		$autoBlock			= JRequest::getVar("auto_block",										"0") == 0 ? 1 : 0;
		
		$initialBonus		= JRequest::getVar("initial_bonus",										"0");
		
		$payBalance			= JRequest::getVar("pay_balance",										"0");
		
		$payDay				= JRequest::getVar("pay_day",											"1");
		
		$trackWho			= JRequest::getVar("track_who",											"2");
		
		$linkFeed			= JRequest::getVar("link_feed",											"");
		
		$cookieTime			= JRequest::getInt("cookie_time",										"");
		
		$offlineTracking	= JRequest::getVar("offline_tracking",									"0");
		
		$offlineType		= JRequest::getVar("offline_type",										"1");
		
		$multiTier			= JRequest::getVar("multi_tier",										"0");
		
		$tierLevel			= JRequest::getVar("tier_level",										"5");
		
		$mustAgree			= JRequest::getVar("must_agree",										"0");
		
		$affTerms			= $this->_db->getEscaped(JRequest::getString("affiliateProgramTerms",	"", "POST", JREQUEST_ALLOWHTML));
		
		// validate input
		
		if (!$linkFeed) {
			
			$vmLogger->err(JText::_("PROVIDE_LINK_FEED_STRING"), 	false);
			
			return false;
			
		}
		
		if (!$cookieTime) {
			
			$vmLogger->err(JText::_("PROVIDE_COOKIE_LIFETIME"), 	false);
			
			return false;
			
		}
		
		// update the configuration
		
		$query				= "UPDATE #__vm_affiliate_settings SET " 		. 
		
							  "`allow_signups` 		= '" . $allowSignups 	. "', " . 
							  
							  "`auto_block`			= '" . $autoBlock		. "', " . 
							  
							  "`initial_bonus`		= '" . $initialBonus	. "', " . 
							  
							  "`pay_balance`		= '" . $payBalance		. "', " . 
							  
							  "`pay_day`			= '" . $payDay			. "', " . 
							  
							  "`track_who`			= '" . $trackWho		. "', " . 
							  
							  "`link_feed`			= '" . $linkFeed		. "', " . 
							  
							  "`cookie_time`		= '" . $cookieTime		. "', " . 
							  
							  "`offline_tracking`	= '" . $offlineTracking	. "', " .
							  
							  "`offline_type`		= '" . $offlineType		. "', " .
							  
							  "`multi_tier`			= '" . $multiTier		. "', " . 
							  
							  "`tier_level`			= '" . $tierLevel		. "', " . 
							  
							  "`must_agree`			= '" . $mustAgree		. "', " . 
							  
							  "`aff_terms`			= '" . $affTerms		. "' "	.
							  
							  "WHERE `setting` 		= '1'";
							  
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// update the offline tracking custom fields
		
		$query				= "UPDATE #__vm_userfield SET `published` = '0' WHERE `name` = 'vm_partnersusername' OR `name` = 'vm_partnersname'";
		
		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		if ($offlineTracking && $offlineType != 3) {
			
			$query				= "UPDATE #__vm_userfield SET `published` = '1' WHERE `name` = '" . ($offlineType == 1 ? "vm_partnersusername" : "vm_partnersname") . "'";
			
			$this->_db->setQuery($query);
		
			$this->_db->query();
			
		}
		
		// update the global settings object
		
		$query				= "SELECT * FROM #__vm_affiliate_settings WHERE `setting` = '1'";
		
		$this->_db->setQuery($query);
		
		$GLOBALS["vmaSettings"] = $this->_db->loadObject();
		
		// show the confirmation message
		
		$vmLogger->info(JText::_("CONFIGURATION_SAVED"), false);
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to send a mass e-mail to all or a specific affiliate
	 */
	 
	function massEmailSend() {
		
		global $vmLogger;
		
		// initiate required variables
		
		$recipients		= &JRequest::getVar("recipients");
		
		$subject		= &JRequest::getVar("subject");
		
		$html			= &JRequest::getVar("html");
		
		$html			= $html ? true : false;
		
		$messageText	= &JRequest::getVar("affiliateMessageText",	"");
		
		$messageHTML	= $_POST["affiliateMessageHTML"];
		
		// check input
		
		if (!$subject || ($html && !$messageHTML || !$html && !$messageText)) {
			
			$vmLogger->err(JText::_("FILL_IN_ALL_FIELDS"), false);
			
			return false;
			
		}
		
		// prepare the e-mail
		
		$affiliateMail				= &JFactory::getMailer();
		
		$affiliateMail->setSender(array($this->_config->getValue( 'config.mailfrom' ), $this->_config->getValue( 'config.fromname' )));
		
		$affiliateMail->setSubject($subject);
		
		$affiliateMail->setBody($html ? $messageHTML : $messageText);
		
		$affiliateMail->IsHTML($html);
		
		// add recipients
		
		if ($recipients == "0") {
			
			// get all e-mail addresses
			
			$query		= "SELECT `mail` FROM #__vm_affiliate WHERE `blocked` = '0'";
			
			$this->_db->setQuery($query);
			
			$recipients = $this->_db->loadResultArray();
			
			// basic filtering
			
			foreach ($recipients as $key => $recipient) {
				
				if (!stristr($recipient, "@")) {
					
					unset($recipients[$key]);
					
				}
				
			}
			
		}
		
		// add recipients
		
		$affiliateMail->addBCC($recipients);
		
		// send the e-mails
							
		if (!$affiliateMail->send()) {
			
			$vmLogger->err(JText::_("EMAIL_SENDING_FAILURE"), false);
			
			return false;
				
		}
		
		else {
			
			$vmLogger->info(JText::sprintf("EMAIL_SENDING_SUCCESS", count($affiliateMail->bcc)));
			
			return true;
			
		}
		
	}
	
	/**
	 * Method to get number of active registered affiliates
	 */
	
	function getRegisteredAffiliates($period = "overall") {
		
		// build main query
		
		$query					 = "SELECT COUNT(`affiliate_id`) FROM #__vm_affiliate WHERE `blocked` = '0' ";
		
		// determine period
		
		switch ($period) {
			
			case 'thismonth':
			
				$query 			.= "AND `date` LIKE '" . date("Y-m") . "%'";
				
				break;
				
			case 'lastmonth':
			
				$query 			.= "AND `date` LIKE '" . date("Y-m", strtotime("-1 month")) . "%'";
				
				break;
				
			case 'overall':
			
			default:
			
				break;
				
		}
		
		// retrieve data
		
		$this->_db->setQuery($query);
		
		$registeredAffiliates 	 = $this->_db->loadResult();
		
		return $registeredAffiliates;
		
	}
	
	/**
	 * Method to get number of confirmed orders
	 */
	
	function getConfirmedOrders($period = "overall") {
		
		// determine period
		
		switch ($period) {
			
			case 'thismonth':
			
				$period 		= "AND `date` LIKE '" . date("Y-m") . "%'";
				
				break;
				
			case 'lastmonth':
			
				$period 		= "AND `date` LIKE '" . date("Y-m", strtotime("-1 month")) . "%'";
				
				break;
				
			case 'overall':
			
			default:
			
				$period			= NULL;
				
				break;
				
		}
		
		// build main query
		
		$query					= "SELECT COUNT(`aff_order_id`) FROM #__vm_affiliate_orders WHERE " . 
		
								  $this->buildStatusesCondition("confirmed", "OR", "=", "order_status", $period);
		
		// retrieve data
		
		$this->_db->setQuery($query);
		
		$confirmedOrders 		= $this->_db->loadResult();
		
		return $confirmedOrders;
		
	}
	
	/**
	 * Method to get number of unique visitors
	 */
	
	function getUniqueVisitors($period = "overall") {
		
		// build main query
		
		$query					 = "SELECT COUNT(DISTINCT `RemoteAddress`) FROM #__vm_affiliate_clicks ";
		
		// determine period
		
		switch ($period) {
			
			case 'thismonth':
			
				$query 			.= "WHERE `date` LIKE '" . date("Y-m") . "%'";
				
				break;
				
			case 'lastmonth':
			
				$query 			.= "WHERE `date` LIKE '" . date("Y-m", strtotime("-1 month")) . "%'";
				
				break;
				
			case 'overall':
			
			default:
			
				$period			 = NULL;
				
				break;
				
		}
		
		// retrieve data
		
		$this->_db->setQuery($query);
		
		$uniqueVisitors 		 = $this->_db->loadResult();
		
		return $uniqueVisitors;
		
	}
	
	/**
	 * Method to retrieve affiliate's clicks count
     */
	
	function getClicks($affiliateID, $current = false, $unique = false) {
		
		$query		= "SELECT COUNT(";
		
		$query	   .= $unique ? "DISTINCT " : NULL;
		
		$query	   .= "`RemoteAddress`) FROM #__vm_affiliate_clicks WHERE `AffiliateID` = '" . $affiliateID . "'";
		
		$query	   .= $current ? " AND `paid` = '0'" : NULL;
		
		$this->_db->setQuery($query);
		
		$clicks = $this->_db->loadResult();
		
		return $clicks;
		
    }
	
	/**
     * Method to retrieve affiliate's sales count
     */
	
	function getSales($affiliateID, $current = true, $approved = true) {
		
		$type 			= $approved ? "confirmed" : "negative";
		
		$prefix			= " AND `affiliate_id` = '" . $affiliateID . "' " . ($current ? " AND `paid` = '0' " : NULL);
		
		$query			= "SELECT COUNT(`aff_order_id`) FROM #__vm_affiliate_orders WHERE " . 
		
						  $this->buildStatusesCondition($type, "OR", "=", "order_status", $prefix);
		
		$this->_db->setQuery($query);
		
		$sales 			= $this->_db->loadResult();
		
		return $sales;
		
    }
	
	/**
	 * Method to retrieve VirtueMart's currency settings
	 */
	 
	function getCurrencySettings() {
		
		// get the currency style from the database
			
		$query 					= "SELECT `vendor_currency_display_style` FROM #__vm_vendor WHERE `vendor_id` = 1";
		
		$this->_db->setQuery( $query );
		
		$displayStyle 			= $this->_db->loadResult();
		
		// get the currency style elements
		
		$dStyle					= explode( "|", $displayStyle );
		
		$cDisplay 				= array();
		
		$cDisplay["symbol"] 	= @$dStyle[1];
		
		$cDisplay["nbdecimal"] 	= @$dStyle[2];
		
		$cDisplay["sdecimal"] 	= @$dStyle[3];
		
		$cDisplay["thousands"] 	= @$dStyle[4];
		
		$cDisplay["position"] 	= @$dStyle[5];

		// get the currency position format
		
		switch ($cDisplay["position"]) {
			
			case '0':
			
				$cDisplay["format"] = '00Symb';
				
				break;
				
			case '1':
			
				$cDisplay["format"] = '00 Symb';
				
				break;
				
			case '2':

			default:
			
				$cDisplay["format"] = 'Symb00';
				
				break;
				
			case '3':
			
				$cDisplay["format"] = 'Symb 00';
				
			break;
			
		}
		
		$this->_currencySettings = $cDisplay;
		
	}
	
	/**
	 * Method to get the shop's currency symbol
	 */
	 
	function getCurrencySymbol() {
			
		$query 					= "SELECT `vendor_currency` FROM #__vm_vendor WHERE `vendor_id` = 1";
		
		$this->_db->setQuery( $query );
		
		$currencySymbol			= $this->_db->loadResult();
		
		return $currencySymbol;
		
	}
	
	/**
	 * Method to retrieve commission rates for a specific affiliate and its parent tiers
	 */
	 
	function getCommissionRates($affiliateID = NULL) {
		
		global $vmaSettings;
		
		// whether the affiliate is assigned general commission rates, or specific ones
		
		if ($affiliateID) {	
	
			$query 						= "SELECT `use_defaults` FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
	
			$this->_db->setQuery( $query );
	
			$useDefaults 				= $this->_db->loadResult();
			
		}

		// get the affiliate's commission rates
		
		$query 							= "SELECT * FROM #__vm_affiliate";
		
		$query 		   	   				.= !$affiliateID || $useDefaults ? "_rates WHERE `rate` = '1'" : " WHERE `affiliate_id` = '" . $affiliateID . "'";

		$this->_db->setQuery( $query );

		$affiliateRates					= $this->_db->loadAssoc();
		
		// get the tier rates, if multi tier system enabled
				
		$query 							= "SELECT * FROM #__vm_affiliate_rates WHERE `rate` > 1 ORDER BY `rate` ASC";

		$this->_db->setQuery( $query );

		$commissionRates["tiers"]		= $this->_db->loadAssocList();
		
		// return the commission rates;
		
		$commissionRates["affiliate"] 	= $affiliateRates;
		
		return $commissionRates;
		
	}
	
	/**
	 * Method to retrieve the discount rate (either general, or for a specific affiliate)
	 */
	 
	function getDiscountRate($affiliateID = NULL) {
		
		global $vmaSettings;
		
		// whether the affiliate is assigned general commission rates, or specific ones
		
		if ($affiliateID) {	
	
			$query 						= "SELECT `use_defaults` FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
	
			$this->_db->setQuery( $query );
	
			$useDefaults 				= $this->_db->loadResult();
			
		}

		// get the discount rates
		
		$query 							= "SELECT `discount_type`, `discount_amount` FROM #__vm_affiliate";
		
		$query 		   	   				.= !$affiliateID || $useDefaults ? "_settings WHERE `setting` = '1'" : " WHERE `affiliate_id` = '" . $affiliateID . "'";

		$this->_db->setQuery( $query );

		$discountRate					= $this->_db->loadAssoc();
		
		// return the discount rate;
		
		return $discountRate;
		
	}
	
	/**
	 * Method to retrieve the commission rates formatted according to VirtueMart's currency settings
	 */
	 
	function getFormattedCommissionRates($affiliateID = NULL) {
		
		global $vmaSettings;
		
		$commissionRates 														= $this->getCommissionRates($affiliateID);
		
		// retrieve formatted commission rates
		
		$generalFormattedRates 													= array();
		
		$generalFormattedRates["affiliate"]										= array();
		
		$generalFormattedRates["tiers"]											= array();
		
		$generalFormattedRates["affiliate"]["per_click_fixed"] 					= $this->formatAmount($commissionRates["affiliate"]["per_click_fixed"]);
		
		$generalFormattedRates["affiliate"]["per_unique_click_fixed"]			= $this->formatAmount($commissionRates["affiliate"]["per_unique_click_fixed"]);
		
		$generalFormattedRates["affiliate"]["per_sale_fixed"] 					= $this->formatAmount($commissionRates["affiliate"]["per_sale_fixed"]);
		
		// retrieve formatted tier commission rates
		
		if ($affiliateID && $vmaSettings->multi_tier) {
			
			for ($i = 0; $i < 4; $i++) {
				
				$generalFormattedRates["tiers"][$i]								= array();
				
				$generalFormattedRates["tiers"][$i]["per_click_fixed"] 			= $this->formatAmount($commissionRates["tiers"][$i]["per_click_fixed"]);
			
				$generalFormattedRates["tiers"][$i]["per_unique_click_fixed"]	= $this->formatAmount($commissionRates["tiers"][$i]["per_unique_click_fixed"]);
			
				$generalFormattedRates["tiers"][$i]["per_sale_fixed"] 			= $this->formatAmount($commissionRates["tiers"][$i]["per_sale_fixed"]);
				
			}
		
		}
		
		return $generalFormattedRates;
		
	}
	
	/**
     * Method to retrieve the overall commissions earned by an affiliate
     */
	
	function getOverallBalance($affiliateID, $currentCommissions) {
		
		$query 			= "SELECT SUM(`amount`) FROM #__vm_affiliate_payments WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$this->_db->setQuery($query);
		
		$commissions 	= $this->_db->loadResult();
		
		$commissions   += $currentCommissions;
		
		$commissions	= $this->formatAmount($commissions);
		
		return $commissions;
		
    }
	
	/**
	 * Method to resolve the page index (depending on the current VirtueMart menu) and build a link used throughout the administration pages
	 */
	 
	function getAdminLink($xhtml = true, $complete = false) {
		
		global $mainframe;
		
		$separator	= $xhtml ? "&amp;" : "&";
		
		$pageIndex	= stristr($_SERVER['PHP_SELF'], "index2.php") ? "index2.php" : (stristr($_SERVER['PHP_SELF'], "index.php") ? "index.php" : "index3.php");
		
		$adminLink	= ($complete ? $this->_website . ($mainframe->isAdmin() ? "administrator/" : NULL) : NULL) . 
		
					  $pageIndex . "?option=com_virtuemart" . $separator . "pshop_mode=admin" . $separator;
		
		// return the administration link
		
		return $adminLink;
		
	}
	
	/**
	 * Method to retrieve the payment methods available to the affiliate
	 */
	 
	function getPaymentMethods($affiliateID, $methodID = NULL) {
		
		// initiate required variables
		
		$paymentMethods			= array();
		
		// get the list of published payment methods, along with their corresponding fields and values
		
		$query					= "SELECT m.`method_id` AS id, m.`method_name` AS name, mf.`field_id` AS fid, mf.`field_name` AS fname, pd.`field_value` AS value " . 
		
								  "FROM #__vm_affiliate_methods m LEFT JOIN #__vm_affiliate_method_fields mf ON m.`method_id` = mf.`method_id` " . 
								   
								  "LEFT JOIN #__vm_affiliate_payment_details pd ON mf.`field_id` = pd.`field_id` AND pd.`affiliate_id` = '" . $affiliateID . "' " . 
		
								  "WHERE m.`method_enabled` = '1'" . ($methodID ? " AND m.`method_id` = '" . $methodID . "'" : NULL);
		
		$this->_db->setQuery($query);
		
		$paymentMethods			= $this->_db->loadAssocList();
		
		// filter the data
		
		$unique					= array();
		
		foreach ($paymentMethods as $index => $pm) {
			
			if (!in_array($pm["name"], $unique)) {
				
				$unique[]							= $pm["name"];
				
			}
			
			else {
				
				$paymentMethods[$index]["name"]		= NULL;
				
			}

		}
		
		return $paymentMethods;
		
	}
	
	/**
     * Method to retrieve the count of affiliates that a certain affiliate has referred
     */
	
	function getReferredAffiliates($affiliateID) {
		
		$query 			= "SELECT COUNT(DISTINCT `affiliate_id`) FROM #__vm_affiliate WHERE `referred` = '" . $affiliateID . "' AND `blocked` = '0'";
		
		$this->_db->setQuery($query);
		
		$referredAffs 	= $this->_db->loadResult();
		
		return $referredAffs;
		
    }
	
	/**
	 * Method to retrieve all payment methods' names and corresponding icons
	 */
	 
	function getPaymentMethodsNames() {
		
		// get all payment methods' names
		
		$mQuery			= "SELECT `method_id`, `method_name` FROM #__vm_affiliate_methods";
		
		$this->_db->setQuery($mQuery);
		
		$mNames			= $this->_db->loadAssocList();
		
		// filter all payment methods, and get their corresponding icons
		
		$methodsNames 	= array();
		
		foreach ($mNames as $mName) {
			
			$methodsNames[$mName["method_id"]] 						= array();
			
			$methodsNames[$mName["method_id"]]["name"] 				= $mName["method_name"];
			
			switch ($mName["method_id"]) {
				
				case '1':
				
					$methodsNames[$mName["method_id"]]["image"]		= "paypal";
					
					break;
					
				case '3':
					
					$methodsNames[$mName["method_id"]]["image"]		= "check";
					
					break;
					
				case '4':
				
					$methodsNames[$mName["method_id"]]["image"]		= "banktransfer";
					
					break;
					
				default:
				
					$methodsNames[$mName["method_id"]]["image"]		= "other";
					
					break;
					
			}
			
		}
		
		// return the methods' names and icons
		
		return $methodsNames;
		
	}
	
	/**
	 * Method to retrieve affiliate's automatic PayPal payment link
	 */
	
	function getPayPalLink($affiliate) {
		
		// get paypal e-mail
		
		$query					= "SELECT `field_value` FROM #__vm_affiliate_payment_details WHERE `affiliate_id` = '" . $affiliate->affiliate_id . "' AND `field_id` = '1'";
		
		$this->_db->setQuery($query);
		
		$paypalEmail			= $this->_db->loadResult();
		
		// build the return link
		
		$returnLink				= $this->getAdminLink(false, true) . "page=vma.affiliate_list";
			
		// build the automatic paypal payment link
		
		$paypalLink 			= "https://www.paypal.com/cgi-bin/webscr"																						.	"?"		. 
		
								  "cmd"				.	"="		.	"_xclick" 																					.	"&amp;" . 
								  
								  "business" 		. 	"="		.	urlencode($paypalEmail) 																	.	"&amp;" . 
								  
								  "item_name"		.	"="		.	urlencode(JText::_("PAYMENT"))																.	"&amp;" .
								  
								  "amount"			.	"="		.	urlencode(round(($affiliate->commissions - 0.005), 2)) 										.	"&amp;"	.
								  
								  "currency_code"	.	"="		.	$_SESSION["vendor_currency"]																.	"&amp;" .
								  
								  "button_subtype"	.	"="		.	"services"																					.	"&amp;"	.
								  
								  "no_note"			.	"="		.	"1"																							.	"&amp;"	.
								  
								  "no_shipping"		.	"="		.	"1"																							.	"&amp;"	.
								  
								  "rm"				.	"="		.	"1"																							.	"&amp;"	.
								  
								  "custom"			.	"="		.	$affiliate->affiliate_id																	.	"&amp;"	.
								  
								  "return"			.	"="		.	urlencode($returnLink)																		.	"&amp;"	.
								  
								  "cancel_return"	.	"="		.	urlencode($returnLink)																		.	"&amp;"	.
								  
								  "notify_url"		.	"="		.	urlencode(JRoute::_($this->_website . "index.php?option=com_affiliate&task=payAffiliate"));
		
		// return the automatic paypal payment link
		
		return $paypalLink;
		
	}
	
	/**
	 * Method to retrieve published menu items
	 */
	 
	function getMenuItems() {
		
		// get menu items
		
		$query 		= "SELECT #__menu.`id`, #__menu.`name`, #__menu.`link`, #__menu_types.`title` FROM #__menu, #__menu_types WHERE " . 
		
					  "#__menu.`published` = '1' AND #__menu.`type` = 'component' AND #__menu.`menutype` != 'usermenu' AND #__menu.`menutype` = #__menu_types.`menutype`";
		
		$this->_db->setQuery($query);
		
		$menuItems 	= $this->_db->loadObjectList();
		
		// return menu items
		
		return $menuItems;
		
	}
	
	/**
	 * Method to get unpublished products
	 */
	 
	function getUnpublishedProducts() {
		
		$unpublishedProducts 	= array();
						
		// get list of products from unpublished categories
		
		$query 	= "SELECT DISTINCT categories.`product_id` FROM #__vm_category category, #__vm_product_category_xref categories WHERE " . 
		
				  "category.`category_id` = categories.`category_id` AND category.`category_publish` != 'Y'";
				  
		$this->_db->setQuery($query);
		
		$productsFromUnpublishedCategories 	= $this->_db->loadAssocList();
		
		// filter unpublished products further

		if (count($productsFromUnpublishedCategories)) {
			
			// get list of products that are in both unpublished and published categories
			
			$query 	= "SELECT DISTINCT categories.`product_id` FROM #__vm_category category, #__vm_product_category_xref categories WHERE " . 
			
					  "category.`category_id` = categories.`category_id` AND category.`category_publish` = 'Y' AND categories.`product_id` IN (";
	
			for ($i = 0; $i < count($productsFromUnpublishedCategories); $i++) {
				
				$query .= "'" . $productsFromUnpublishedCategories[$i]["product_id"] . "'";
				
				if ($i != (count($productsFromUnpublishedCategories) - 1)) {
					
					$query .= ", ";
					
				}
				
			}
			
			$query .= ")";
					  
			$this->_db->setQuery($query);
			
			$productsFromPublishedCategories 	= $this->_db->loadAssocList();
			
			// remove the products that are still published in some categories from the unpublished products list
			
			if (count($productsFromPublishedCategories)) {
				
				$stillPublishedProducts				= array_intersect($productsFromUnpublishedCategories, $productsFromPublishedCategories);
				
				foreach ($stillPublishedProducts as $key => $value) {
					
					unset($productsFromUnpublishedCategories[$key]);
					
				}
			
				$unpublishedProducts				= array_merge_recursive($unpublishedProducts, $productsFromUnpublishedCategories);
			
			}
			
		}
		
		// get list of specifically unpublished products
		
		$query 	= "SELECT `product_id` FROM #__vm_affiliate_links WHERE `published` = '0'";
				  
		$this->_db->setQuery($query);
		
		$specificallyUnpublishedProducts 	= $this->_db->loadAssocList();
		
		$unpublishedProducts				= is_array($specificallyUnpublishedProducts) ? array_merge_recursive($unpublishedProducts, $specificallyUnpublishedProducts) : array();
		
		// make sure the array isn't empty
		
		$unpublishedProducts				= is_array($unpublishedProducts) && count($unpublishedProducts) ? $unpublishedProducts : NULL;
		
		return $unpublishedProducts;
						
	}
	
	/**
	 * Method to get unpublished categories
	 */
	 
	function getUnpublishedCategories() {
		
		$unpublishedCategories 	= array();
						
		// get list of specifically unpublished categories
		
		$query 	= "SELECT `category_id` FROM #__vm_affiliate_links_categories WHERE `published` = '0'";
				  
		$this->_db->setQuery($query);
		
		$specificallyUnpublishedCategories 	= $this->_db->loadAssocList();
		
		$unpublishedCategories				= is_array($specificallyUnpublishedCategories) ? array_merge_recursive($unpublishedCategories, $specificallyUnpublishedCategories) : NULL;
		
		return $unpublishedCategories;
						
	}
	
	/**
	 * Method to retrieve a list of products to link to
	 */
	
	function getProductsList() {
		
		// count the products
		
		$query					= "SELECT COUNT(`product_id`) FROM #__vm_product WHERE `product_parent_id` = '0' AND `product_publish` = 'Y'";
		
		$this->_db->setQuery($query);
		
		$products				= $this->_db->loadResult();
		
		// if there are more than 100 products, they are too many, so they just slow the app down, instead of being an useful feature
		
		if ($products > 200) {
			
			return NULL;
			
		}
		
		// build the product list query
						
		$query					= "SELECT `product_id`, `product_name` FROM #__vm_product WHERE `product_parent_id` = '0' AND `product_publish` = 'Y' ";
		
		// don't include unpublished products
		
		$unpublishedProducts 	= $this->getUnpublishedProducts();
		
		if (is_array($unpublishedProducts)) {
			
			foreach ($unpublishedProducts as $unpublishedProduct) {
				
				$query .= "AND `product_id` != '" . $unpublishedProduct["product_id"] . "' ";
				
			}
			
		}
		
		// retrieve the products from the database
		
		$this->_db->setQuery($query);
		
		$products 				= $this->_db->loadObjectList();
		
		// return the product list
		
		return $products;
	
	}
	
	/**
	 * Method to retrieve a list of categories to link to
	 */
	
	function getCategoriesList() {
		
		// count the categories
		
		$query					= "SELECT COUNT(`category_id`) FROM #__vm_category WHERE `category_publish` = 'Y'";
		
		$this->_db->setQuery($query);
		
		$categories				= $this->_db->loadResult();
		
		// if there are more than 100 products, they are too many, so they just slow the app down, instead of being an useful feature
		
		if ($categories > 200) {
			
			return NULL;
			
		}
		
		// build the category list query
						
		$query					= "SELECT `category_id`, `category_name` FROM #__vm_category WHERE `category_publish` = 'Y' ";
		
		// don't include unpublished categories
		
		$unpublishedCategories 	= $this->getUnpublishedCategories();
		
		if (is_array($unpublishedCategories)) {
			
			foreach ($unpublishedCategories as $unpublishedCategory) {
				
				$query .= "AND `category_id` != '" . $unpublishedCategory["category_id"] . "' ";
				
			}
			
		}
		
		// retrieve the categories from the database
		
		$this->_db->setQuery($query);
		
		$categories 			= $this->_db->loadObjectList();
		
		// return the category list
		
		return $categories;
	
	}
	
	/**
	 * Method to get banners and textads' hits
	 */
	
	function getHits($type = "banner", $id, $frontend = false, $affiliateID = NULL) {
		
		// initiate required variables
		
		$type	= $type == "banner" ? $type : "textad";
		
		$hitsNo	= 0;
		
		$quick	= $frontend && $affiliateID ? true : false;
		
		// prepare the query
		
		$query	= "SELECT `hits` FROM #__vm_affiliate_" . $type . "s_hits WHERE `" . $type . "_id` = '" . $id . "'";
		
		$query	.= $quick ? " AND `affiliate_id` = '" . $affiliateID . "'" : NULL;
		
		$this->_db->setQuery($query);
		
		// get data
		
		$hits	= $quick ? $this->_db->loadResult() : $this->_db->loadObjectList();
		
		// process data
		
		if ($quick) {
			
			$hitsNo 	= $hits ? $hits : $hitsNo;
			
		}
		
		else if (is_array($hits)) {

			foreach ($hits as $hit) {
				
				$hitsNo += $hit->hits;
				
			}
			
		}
		
		// return required data

		return $hitsNo;
		
	}
	
	/**
	 * Method to retrieve active statistics pages menus
	 */
	 
	function getActiveStatsMenus() {
			
		if (!$this->_activeStatsMenus) {
			
			$statisticsMenus								= array("trafficmonth", "trafficyear", "salesmonth", "salesyear");
				
			$activeStatisticsMenus							= array();
			
			foreach ($statisticsMenus as $statisticsMenu) {
				
				$data 										= $this->getStatisticsData($statisticsMenu, true);
				
				$activeStatisticsMenus[$statisticsMenu] 	= $data ? true : false;
				
			}
		
		}
		
		else {
			
			$activeStatisticsMenus = $this->_activeStatsMenus;
			
		}
		
		return $activeStatisticsMenus;
		
	}
	
	/**
	 * Method to get statistical data in the form of a graph for the statistics page
	 */
	 
	function getStatisticsData($request, $menuCheck = false) {
		
		global $page;
		
		// parse request
		
		$frontend	= JRequest::getVar("subview",	"");
		
		$type 		= stristr($request, "traffic")	? "traffic" 						: "sales";
		
		$period 	= stristr($request, "month")	? "month" 							: "year";
		
		$xTitle		= $period 	== "year" 			? JText::_("MONTHS")				: JText::_("DAYS");
		
		$yTitle		= $type		== "traffic" 		? JText::_("HITS")					: JText::_("SALES");
		
		$title		= $type		== "traffic" 		? JText::_("TRAFFIC")				: JText::_("SALES");
		
		$date		= $period 	== "month" 			? date("Y-m") 						: date("Y");
		
		$niceDate 	= $period 	== "month"			? date("M Y")						: date("Y");
		
		$totalItems	= $period 	== "year" 			? 12 								: date("t");
		
		$keyIndex	= $period 	== "year" 			? 1									: 2;
		
		$items 		= array();
		
		$fontFolder	= JPATH_ROOT . DS . "components" . DS . "com_affiliate" . DS . "assets" . DS . "pChart" . DS . "Fonts";
		
		if ($frontend) {
			
			$session	= &JFactory::getSession();
		
			$affiliate	= $session->get("affiliate");
		
		}
		
		$hashString	= $frontend ? $affiliate->affiliate_id : "admin";
		
		// get required data based on type
		
		switch ($type) {
			
			case 'traffic':
			
				$query 		= "SELECT `date` FROM #__vm_affiliate_clicks WHERE `date` LIKE '" . $date . "%' " . 
				
							  ($frontend ? "AND AffiliateID = '" . $affiliate->affiliate_id . "'" : NULL);
				
				break;
				
			case 'sales':
			
				$query 		= "SELECT `date` FROM #__vm_affiliate_orders WHERE `date` LIKE '" . $date . "%' " . 
				
							  ($frontend ? "AND affiliate_id = '" . $affiliate->affiliate_id . "'" : NULL);
				
				break;
				
		}
		
		$this->_db->setQuery($query);
				
		$this->_db->query();
		
		$dataRows			= $this->_db->loadAssocList();

		// determine if it should proceed on generating the statistics image

		if (!is_array($dataRows) || count($dataRows) == 0) {

			return false;
			
		}
		
		if ($menuCheck) {
			
			return true;
			
		}
		
		// load the pchart library
		
		require_once(JPATH_ROOT . DS . "components" . DS . "com_affiliate" . DS . "assets" . DS . "pChart" . DS . "pChart" . DS . "pData.class");  
		
		require_once(JPATH_ROOT . DS . "components" . DS . "com_affiliate" . DS . "assets" . DS . "pChart" . DS . "pChart" . DS . "pChart.class.php");  
				
		$dataSet	 		= new pData;
		
		// parse data based on period
		
		for ($i = 1; $i <= $totalItems; $i++) {
			
			$item			= strlen($i) == 1 ? 0 : NULL;
			
			$item 		   .= $i;
			
			$items[$item]	= 0;
			
		}
		
		foreach ($dataRows as $dataRow) {
			
			$item	 		= explode("-", $dataRow['date']);
			
			$items[$item[$keyIndex]]++;
			
		}
		
		// make sure that required libraries exist
		
		if (!function_exists("imageftbbox")) {
			
			echo "Please install the Freetype2 library for PHP, in order for graphs to work.";
			
			return false;
			
		}
		
		// render the graph
		
		$dataSet->AddPoint($items, 		"Statistics"); 
		
		$dataSet->AddAllSeries();  
		
		$dataSet->SetSerieName($this->fixUTF8($title), 	"Statistics"); 
		 
		$dataSet->SetYAxisName($this->fixUTF8($yTitle)); 
		 
		$dataSet->SetXAxisName($this->fixUTF8($xTitle));

		$statistics = new pChart(465, 250);
		 
		$statistics->setFontProperties($fontFolder . DS . "tahoma.ttf", 10);
		
		$statistics->setGraphArea(45, 30, 455, 200); 
		
		$statistics->drawGraphArea(252, 252, 252);
		
		$statistics->setFontProperties($fontFolder . DS . "tahoma.ttf", 8);
		
		$statistics->drawScale($dataSet->GetData(), $dataSet->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2, FALSE, 1, $period);
		
		$statistics->drawGrid(4, TRUE, 230, 230, 230, 255);

		$statistics->drawLineGraph($dataSet->GetData(), $dataSet->GetDataDescription());
		
		$statistics->drawPlotGraph($dataSet->GetData(), $dataSet->GetDataDescription(), 3, 2, 255, 255, 255);

		$statistics->setFontProperties($fontFolder . DS . "tahoma.ttf", 8);
		
		$statistics->drawLegend(50, 35, $dataSet->GetDataDescription(), 255, 255, 255);
		
		$statistics->setFontProperties($fontFolder . DS . "tahoma.ttf", 10);
		
		$statistics->drawTitle(0, 0, $this->fixUTF8(JText::_("STATISTICS") . ": " . $title . " - " . $niceDate), 50, 5, 50, 475, 30);
		
		$statistics->Render(JPATH_ROOT . DS . "components" . DS . "com_affiliate" . DS . "statistics" . DS . md5($hashString) . $type . $period . ".png");
		
		return true;
		
	}
	
	/**
	 * Method to fix UTF-8 issues with imagettftext()
	 */
	 
	function fixUTF8($text) {
		
		if (function_exists("mb_detect_encoding")) {
			
			$encoding 	= mb_detect_encoding($text, 'UTF-8, ISO-8859-1');
	
			if ($encoding != 'UTF-8') {
				
				$text 	= mb_convert_encoding($text, 'UTF-8', $encoding);
				
			}
	
			$text 		= mb_encode_numericentity($text, array (0x0, 0xffff, 0, 0xffff), 'UTF-8');
		
		}
		
		return $text;
		
	}
	
	/**
	 * Method to get the correct stats
	 */
	 
	function getActiveStatsSection() {
			
		$type				= JRequest::getVar('type', 		'traffic');
		
		$period				= JRequest::getVar('period', 	'month');
		
		$activeStatsMenus 	= $this->_activeStatsMenus = $this->getActiveStatsMenus();
		
		$nextPage			= false;
		
		foreach ($activeStatsMenus as $statsMenu => $statsMenuEnabled) {
			
			if ($statsMenu == $type . $period && !$statsMenuEnabled || $nextPage && !$statsMenuEnabled) {
				
				$nextPage	= true;
				
			}
			
			if ($nextPage && $statsMenuEnabled) {
				
				$newType	= stristr($statsMenu, "traffic") 	? "traffic" : "sales";
				
				$newPeriod	= stristr($statsMenu, "month") 		? "month" 	: "year";
				
				JRequest::setVar('type', 	$newType);
				
				JRequest::setVar('period', 	$newPeriod);
				
				$nextPage 	= false;
				
			}
			
		}
			
	}
	
	/**
	 * Method to get the links row on ad form pages
	 */
	
	function getLinksRow($type = "banners", $form = "add", $object = NULL) {
		
		// initiate required variables
		
		$type 				= $type == "banners"	?	$type					:	"textads";
		
		$form 				= $form == "add"		?	$form					:	"edit";
		
		$object				= $form == "edit"		?	$object					:	NULL;
		
		$language 			= &JFactory::getLanguage();
		
		$path				= JPATH_ROOT . DS . "administrator";
		
		$menuItems			= $this->getMenuItems();

		$products			= $this->getProductsList();
		
		$categories			= $this->getCategoriesList();

		$linksRow			= NULL;
		
		if ($object) {
			
			$link 			= $type == "banners"	?	$object->banner_link	:	$object->link;
	
			$id				= $type == "banners"	?	$object->banner_id		:	$object->textad_id;
		
		}
		
		// get com_menu language
		
		$language->load("com_menus", $path);

		// prepare the link row
		
		$linksRow				= '<select id="affiliateLink" name="link" style="visibility: visible;">
                    	
									  <option value="index.php" ' . 
									  
									  		($object ? (str_replace("?", "", $link) == "index.php" ? 'selected="selected"' : NULL) : NULL) . '>' . 
									  
											JText::_("MAIN_SITE") . 
										  
									  '</option>
									  
									  <option value="index.php?option=com_virtuemart" id="urlMainShop" ' . 
									  
									  		($object ? ($link == "index.php?option=com_virtuemart" || JRoute::_($link) == "index.php?option=com_virtuemart" ? 'selected="selected"' : NULL) : NULL) . '>' . 
									  
											JText::_("MAIN_SHOP") . 
										  
									  '</option>';
									  
		// add the product options
		
		if (is_array($products)) {
									  
			$linksRow				.=	  '<option value="" disabled="disabled" id="urlProductListSeparator"></option>
										  
										  <optgroup label="' . 
										  
												JText::_("PRODUCTS") . 
												
												':">';
												  
												// add each product option
												
												foreach ($products as $product) {
													
													$productLink =	 JRoute::_("index.php?option=com_virtuemart&page=shop.product_details&product_id=" . $product->product_id);
													
													$linksRow	.=	 '<option value="' . $productLink	. '" ' . 
													
																		($object ? ($link == $productLink || JRoute::_($link) == $productLink ? 'selected="selected"' : NULL) : NULL) . '>' . 
																
																		$product->product_name . 
																	 
																	 '</option>';
												
												}
												
			$linksRow				.=	  '</optgroup>';
		
		}
		
		// add the category options
		
		if (is_array($categories)) {
									  
			$linksRow				.=	  '<option value="" disabled="disabled"></option>
									  
										  <optgroup label="' . 
										  
										  		JText::_("CATEGORIES") . 
												
												':">';
												  
												// add each category option
												
												foreach ($categories as $category) {
													
													$categoryLink =	 JRoute::_("index.php?option=com_virtuemart&page=shop.browse&category_id=" . $category->category_id);
													
													$linksRow	 .=	 '<option value="' . $categoryLink . '" ' . 
													
																		 ($object ? ($link == $categoryLink || JRoute::_($link) == $categoryLink ? 'selected="selected"' : NULL) : NULL) . '>' . 
																	
																		 $category->category_name 	. 
																	 
																	 '</option>';
													
												}
										  
			$linksRow				.=	  '</optgroup>';
		
		}
		
		// add the menu item options
		
		if (is_array($menuItems)) {
								  
			$linksRow				.=	  '<option value="" disabled="disabled"></option>
									  
										  <optgroup label="' . 
										  
										  		JText::_("MENU ITEMS") . 
												
												':">';
												  
												// add each menu item option
												
												foreach ($menuItems as $menuItem) {
													
													$menuLink  =	 JRoute::_($menuItem->link . "&Itemid=" . $menuItem->id);
													
													$linksRow .=	 '<option value="' . $menuLink . '" ' . 
													
																		 ($object ? ($link == $menuLink || JRoute::_($link) == $menuLink ? 'selected="selected"' : NULL) : NULL) . '>' . 
																	
																		 $menuItem->name . ' ' . '(' . $menuItem->title . ')' . 
																
																	 '</option>';
													
												}
										  
			$linksRow				.=	  '</optgroup>';

		}
		
		$linksRow 	.=	'</select>
								  
						<a href="javascript:void(0);" id="affiliateTypeURL">' . 
						
							JText::_("TYPE_URL") . '&hellip;' . 
							
						'</a>';
						
		// return the built rows
		
		return $linksRow;
		
	}
	
	/**
	 * Method to build the publish/unpublish buttons for the product and category ads pages
	 */
	 
	function getPublishUnpublishButtons() {
		
		// initiate required variables
		
		$document = &JFactory::getDocument();
		
		// add the corresponding events to the mass publish/unpublish buttons

		$massToggleButtons = "window.addEvent('domready', function() {
									
									var adsList = $(document.forms['adminForm']);

									$('affiliatePublish').addEvent('click', function() {
										
										adsList.adopt(new Element('input', {
																			
															  'type':	'hidden',
															  
															  'name':	'operation',
															  
															  'value':	'publish'
															  
													  })
															
										);
																		
										adsList.func.value = 'vmaProductCategoryAdToggle';
										
										adsList.submit();
										
									});
									
									$('affiliateUnpublish').addEvent('click', function() {
										
										adsList.adopt(new Element('input', {
																			
															  'type':	'hidden',
															  
															  'name':	'operation',
															  
															  'value':	'unpublish'
															  
													  })
															
										);
																		
										adsList.func.value = 'vmaProductCategoryAdToggle';
										
										adsList.submit();
										
									});
									
								});";
		
		$document->addScriptDeclaration($massToggleButtons);
		
		// build the publish/unpublish buttons
		
		$publishUnpublishButtons = '<div class="affiliateTopMenu">
            
										<div class="affiliateTopMenuLink">
										
											<span style="background: url(' . $this->_website . 'administrator/images/publish_r.png) no-repeat left center;"
												
												  class="affiliateTopMenuLinkItem">
											
												<a href="javascript:void(0);" id="affiliateUnpublish">' . 
												   
												   JText::_("UNPUBLISH") . 
												   
												'</a>
												
											</span>
											
										</div>
										
										<div class="affiliateTopMenuLink">
										
											<span>|</span>
											
										</div>
										
										<div class="affiliateTopMenuLink">
										
											<span style="background: url(' . $this->_website . 'administrator/images/publish_g.png) no-repeat left center;"
											
											      class="affiliateTopMenuLinkItem">
											
												<a href="javascript:void(0);" id="affiliatePublish">' . 
												   
												   JText::_("PUBLISH") . 
												   
												'</a>
												
											</span>
											
										</div>
										
									</div>';
					
		// return the buttons
						
		return $publishUnpublishButtons;
    
	}
	
	/**
	 * Method to get the VAT setting from VirtueMart (whether the product prices should be displayed including tax)
	 */

	function getVATSetting() {
		
		// initiate required variables
		
		$database 		= &JFactory::getDBO();
		
		// get setting from the database
		
		$query 			= "SELECT `show_price_including_tax` FROM #__vm_shopper_group WHERE `shopper_group_id` = '5'";
		
		$database->setQuery($query);
		
		$database->query();
		
		$showPriceIncludingTax = $database->loadResult();
		
		// convert the settting
			
		$showPriceIncludingTax = $showPriceIncludingTax ? true : false;
		
		// return the setting
		
		return $showPriceIncludingTax;
		
	}
	
	/**
	 * Method to help build query conditions related to orders' statuses
	 */
	 
	function buildStatusesCondition($type = "confirmed", $operator = "AND", $condition = "=", $field = "order_status", $prefix = NULL, $table = NULL) {
		
		// initiate required variables
		
		$i		  = 0;
		
		$query	  = NULL;
		
		$statuses = $type	== "confirmed"	? $this->_confirmedStatuses : 
		
					($type	== "pending"	? $this->_pendingStatuses	: 
					
					($type	== "cancelled"	? $this->_cancelledStatuses :
					
					$type	== "negative"	? array_merge($this->_pendingStatuses, $this->_cancelledStatuses) : NULL));
		
		// make sure the statuses array isn't empty
		
		if (!is_array($statuses)) {
			
			return $query;
			
		}
		
		// build the query condition
		
		foreach ($statuses as $status) {
			
			$query 		.= " " . ($table ? $table . "." : NULL) . "`" . $field . "` " . $condition . " " . "'" . $status . "' " . $prefix . " ";
			
			$i++;
			
			if (isset($statuses[$i])) {
				
				$query	.= $operator . " ";
				
			}
			
		}
		
		// return the condition
		
		return $query;
		
	}
	
	/**
	 * Method to initiate and include the required functions for the ad form pages
	 */
	 
	function initiateAdForm($type = "banners", $form = "add", $object = NULL) {
		
		global $VM_LANG;
		
		// initiate required variables
		
		$type 				= $type == "banners"	?	$type					:	"textads";
		
		$form 				= $form == "add"		?	$form					:	"edit";
		
		$object				= $form == "edit"		?	$object					:	NULL;
			
		$link 				= $object				?	($type == "banners"		?	$object->banner_link	:	$object->link) : NULL;
		
		$adminLink			= $this->getAdminLink();
		
		$document			= &JFactory::getDocument();
		
		// include link related functions
	
		$includeURLSubmit	= "// add the onclick event to the new url submit button
		
							   window.addEvent('domready', function() {
									
									$('affiliateTypeURL').addEvent('click', function() {
									
										openTypeURLBox();
										
									});
									
							   });";
							   
		$document->addScriptDeclaration($includeURLSubmit);
		
		$urlModalWindow		= "// function to open the custom url modal window

							   function openTypeURLBox() {
						  
								  // compute the modal window width
								  
								  var modalWindowWidth = ('" . $this->_website . "'.length * 7) + 220;
								  
								  // create the custom url entry modal window
		
								  SqueezeBox.fromElement(new Element('div', {
									  
															'styles': 		{
															
																'margin':	'20px 20px 20px 20px',
																
																'overflow':	'hidden'
																	
															}
														
														})
														
														.appendText('" . JText::_("TYPE_URL", true) . ": ')
														
														.adopt(new Element('br'))
														
														.adopt(new Element('br'))
														
														.adopt(new Element('label', {
															
																  'for':	'affiliateCustomURL',
																  
																  'style': 	'font-weight: bold;'
																  
															  })
															  
															  .appendText('" . $this->_website . "'))
															  
														.adopt(new Element('input', {
															
																  'id':		'affiliateCustomURL',
																  
																  'type':	'text',
																  
																  'styles':	{
																		
																		'border-width':			'0px 0px 1px 0px',
																		
																		'border-color': 		'#CCCCCC',
																		
																		'border-style': 		'dashed',
																		
																		'background-color':		'transparent',
																		
																		'color':				'#333333',
																		
																		'font-size':			'11px',
																		
																		'height':				window.ie ? '14px' : '11px',
																		
																		'padding':				'0px 0px 0px 0px',
																		
																		'width':				'210px'
																		 
																  }
																
															  })
															  
														)
														
														.adopt(new Element('br'))
														
														.adopt(new Element('br'))
														
														.adopt(new Element('a', {
																  
																  'id':			'affiliateSaveCustomLink',
		
																  'href': 		'javascript:void(0);'
																  
															  })
															  
															  .appendText('" . JText::_("SAVE", true) . "')
														
														)
														
														.appendText(' | ')
														
														.adopt(new Element('a', {
																  
																  'id':			'affiliateCancelCustomLink',
		
																  'href': 		'javascript:void(0);'
																  
															  })
															  
															  .appendText('" . JText::_("CANCEL", true) . "')
														
														), {
										
									  handler: 	'adopt',
									  
									  size: 	{ 
									  
											x: 	modalWindowWidth,
										
											y: 	110
										
									  },
									  
									  onOpen:	function() {
										 
											setTimeout(function() {
												
												// focus the main input
												
												$('affiliateCustomURL').focus();
												
												// add the url submit button's events
												
												$('affiliateSaveCustomLink').addEvent('click', function() {
													
													submitCustomLink();
													
												});
												
												$('affiliateCancelCustomLink').addEvent('click', function() {
													
													SqueezeBox.close();
													

												});
		
												$('affiliateCustomURL').addEvent('keydown', function(e) {
													
													e = new Event(e);
													
													if (e.key == 'enter') {
														
														submitCustomLink();
													
													}
													
												});
												
											}, 500);
										  
									  },
									  
									  classWindow: 'affiliateCustomURLWindow'
									  
								  });
									
							  }";
					  
		$document->addScriptDeclaration($urlModalWindow);
							  
		$urlSubmitFunction	= "// function to add the custom url to the link select
		
							   function submitCustomLink() {
								  
								  // make sure the link field isn't empty
								  
								  if ($('affiliateCustomURL').value == '') {
									  
									  // alert the user the field is empty
									  
									  alert('" . JText::_("PROVIDE_LINK", true) . "');
									  
								  }
								  
								  // insert the custom link into the select box
								  
								  else {
									  
									  // properly format the url
									  
									  $('affiliateCustomURL').value						= 	$('affiliateCustomURL').value.replace(/&amp;/gi, '&');
									  
									  // inject the custom url
									  
									  injectCustomURL($('affiliateCustomURL').value);
									  
									  // close the modal window
									  
									  SqueezeBox.close();
									  
								  }
													
							  }";
							  
		$document->addScriptDeclaration($urlSubmitFunction);
		
		$injectCustomURL	= "// function to inject the custom url
		
							   function injectCustomURL(value) {
								   
									// check if the url option group is already displayed
										
									if ($('urlOptionGroup') == null) {
										  
										// create the url option group
										
										var urlOptionGroupSeparator = new Element('option', {
												
																			  'id':			'urlOptionGroupSeparator',
																			  
																			  'disabled':	'disabled'
																			  
										}).injectAfter($('urlMainShop'));
										
										var urlOptionGroup = new Element('optgroup', {
										  
																			  'id':			'urlOptionGroup',
																			  
																			  'label':		'" . JText::_("URL") . ":'
																				
										}).injectAfter($('urlOptionGroupSeparator'));
										
									}
									
									// insert the new custom value
									
									$('urlOptionGroup').adopt(new Element('option', {
										
																  'value':					value
													  
										  })
										  
										  .appendText(value)
															  
									);
									
									// select the newly inserted value
									  
									$('affiliateLink').selectedIndex 					= 	$('urlOptionGroup').getLast().index;
									
							   }";
		
		$document->addScriptDeclaration($injectCustomURL);
		
		if ($form == "edit") {
			
			// determine if the link is a custom one
			
			$determineCustomURL = "// determine if this is a custom url, and inject it in the link list
			
								   window.addEvent('domready', function() {
									  
									  if ($('affiliateLink').selectedIndex == 0 && '" . $link . "' != 'index.php' && '" . $link . "' != 'index.php?') {
										  
										  injectCustomURL('" . $link . "');
										  
									  }
									
								   });";
									
			$document->addScriptDeclaration($determineCustomURL);
			
		}
		
		// insert size related functions
		
		if ($type == "textads") {
			
			// display the size row, when the page is ready
			
			$displaySizeRow		= "// display the size row, on domready
			
								   window.addEvent('domready', function() {
									   
									   showSizeRow();
									   
								   });";
								   
			$document->addScriptDeclaration($displaySizeRow);
			
		}
		
		if ($form == "add" || $type == "textads") {
			
			// size group form submission function
	
			$sendSizeGroupForm	= "// function to send the size group form via ajax
			
								   function sendSizeGroupForm() {
																
									  // send the request
			
									  var saveSizeGroup = new Ajax('" . str_replace("&amp;", "&", $adminLink) . "page=vma.size_groups_list&modal=true&type=" . $type . "', {
																			
																	  data:			{
																	  
																					'func':				'vmaSizeGroupSave',
																				  
																					'size_group_id':	$$('div#sbox-content input[name=size_group_id]')		.getProperty('value'),
																				  
																					'name': 			$$('div#sbox-content input#affiliateSizeGroupName')		.getProperty('value'),
																				  
																					'width': 			$$('div#sbox-content input#affiliateSizeGroupWidth')	.getProperty('value'),
																				  
																					'height': 			$$('div#sbox-content input#affiliateSizeGroupHeight')	.getProperty('value'),
																				  
																					'vmtoken':			$$('div#sbox-content input[name=vmtoken]')				.getProperty('value')
																				  
																	  },
																	  
																	  method: 		'post',
																	  
																	  onRequest:	function()			{
																		  
																		  // disable form elements
																		  
																		  $$('div#sbox-content input#affiliateSizeGroupName')	.setProperty('disabled', 		'true');
																		  
																		  $$('div#sbox-content input#affiliateSizeGroupWidth')	.setProperty('disabled', 		'true');
																		  
																		  $$('div#sbox-content input#affiliateSizeGroupHeight')	.setProperty('disabled', 		'true');
																			
																		  $$('div#sbox-content input.affiliateSaveButton').replaceWith(new Element('img', {
															   
																				'src': '" . $this->_website . "components/com_affiliate/assets/images/spinner.gif'
																				   
																		  }));
																		  
																	  },
																	  
																	  onSuccess:	function(response)	{
																		  
																		  SqueezeBox.fromElement('" . str_replace("&amp;", "&", $adminLink) . "page=vma.size_groups_list&modal=true&type=" . $type . "', {
																			  
																			  classWindow: 'affiliateSizeGroupWindow'
																			  
																		  });
																		  
																	  }
																	  
																  }
																  
									  ).request();
									  
								  }";
								  
			$document->addScriptDeclaration($sendSizeGroupForm);
			
			// size select display function
			
			$vmaToken			= $this->buildToken();
			
			$sizeSelectFunction = "// function to show the size form row
			
								   function showSizeRow(imageDetails) {
									  
									  // determine the execution context
									  
									  var textads = '" . $type . "' == 'textads' ? true : false;
									  
									  // empty the form row
									  
									  $('affiliateSizeRow').empty();
									  
									  // insert required elements
									  
									  $('affiliateSizeRow').adopt(new Element('div', {
										  
																	  'class': 			'affiliateDetailsKey'
													
																  })
																  
																  .adopt(new Element('label', {
																	  
																			'for':	 	'affiliateSize'
																			
																		}).appendText('" . JText::_("SIZE", true) . "')
																		  
																  ), 
																  
																  new Element('div', {
																	  
																	  'id':				'affiliateSizeRowValue',
																	  
																	  'class': 		 	'affiliateDetailsValue affiliateLongerInputs'
																	  
																  })
																  
									  );
																  
									  // build the preserve size text
									  
									  if (!textads) {
										  
									  	var preserveSizeText = '" . JText::_("PRESERVE_SIZE", true) . ": ' + imageDetails.width + 'x' + imageDetails.height + (imageDetails.sizeGroupName != '' ? ' (' + imageDetails.sizeGroupName + ')' : '');
									  
									  }
									  
									  // add resizing options, for static content
									  
									  if (textads || (!imageDetails.animatedGIF && imageDetails.fileType != 'swf')) {
										  
										  // build the size select element
										  
										  $('affiliateSizeRowValue').adopt(new Element('select', {
																	  
																					'id': 		'affiliateSize',
																				
																					'name':		'size'
																					
																				}).setStyle('visibility', 'visible')
																				
																		  );
																		  
										  // add the preserve size option
										  
										  if (!textads) {
											  
											  $('affiliateSize').adopt(new Element('option', {
											  
																	  'value':	'preserve'
															  
																})
														  
																.appendText(preserveSizeText)
													
											  );
										  
										  }
										  
										  // populate the size groups list
										  
										  var getSizeGroups = new Ajax('" . JRoute::_($this->_website) . "', {
											  
																	method: 	'post',
																	
																	data:		{
																		
																		'option':		'com_affiliate',
																		
																		'task':			'exportSizeGroups',
																		
																		'type':			'" . $type . "',
																		
																		'format':		'raw',
																		
																		'vmatoken':		'" . $vmaToken->vmatoken . "',
																		
																		'vmatokenid':	'" . $vmaToken->vmatokenid . "'
																		
																	},
																	
																	onSuccess:	function(response) {
																		
																		// parse the response into size group arrays
																		
																		var sizeGroups = response.split('|');
																		
																		// verify there is at least one size group
																	
																		if (sizeGroups == '' || typeof(sizeGroups) !== 'object') {
			
																			return false;
																			
																		}
																		
																		// verify there's not just a single option which is the same as existing size
																		
																		if (sizeGroups.length == 2 && !textads) {
																			
																			singleSizeGroupElements = sizeGroups[0].split('-');
																			
																			if (singleSizeGroupElements[1] == imageDetails.width && singleSizeGroupElements[2] == imageDetails.height) {
																				
																				return false;
																				
																			}
																			
																		}
																		
																		// add an option separator
																			
																		if (!textads) {
																			
																			$('affiliateSize').adopt(new Element('option', {
																				
																									'disabled': 'disabled',
																									
																									'value':	''
																									
																									})
																									
																			);
																		
																		}
																		
																		// add the size groups option group
																		
																		$('affiliateSize').adopt(new Element('optgroup', {
																			
																									'id':		'affiliateSizeGroupsOptions',
																									
																									'label':	'" . JText::_("SELECT_SIZE_GROUP", true) . ":'
																									
																								})
																								
																		);
																		
																		// add each size group as a select option
																		
																		sizeGroups.each(function(sizeGroup) {
																				
																			var sizeGroupElements = sizeGroup.split('-');
																			
																			if (typeof(sizeGroupElements) === 'object' && sizeGroupElements[0] != '' &&
																			
																				(textads || (sizeGroupElements[1] != imageDetails.width || sizeGroupElements[2] != imageDetails.height))) {
																				
																				var sizeGroupWidth 		= sizeGroupElements[1] == '0' ? '∞' : sizeGroupElements[1];
																				
																				var sizeGroupHeight 	= sizeGroupElements[2] == '0' ? '∞' : sizeGroupElements[2];
																				
																				var completelyFluid		= sizeGroupElements[1] == '0' && sizeGroupElements[2] == '0' ? true : false;
																				
																				$('affiliateSizeGroupsOptions').adopt(new Element('option', {
																								
																														  'value':		sizeGroupElements[0]
																													  
																													  })
																											  
																													  .appendText((completelyFluid ? '∞' : (sizeGroupWidth + 'x' + sizeGroupHeight)) + (sizeGroupElements[3] != '' ? ' (' + sizeGroupElements[3] + ')' : ''))
																										
																				);
																				
																				" . 
																				
																				($form == "edit" && $type == "textads" ? 
																				
																				"if (sizeGroupElements[1] == '" . $object->width . "' && sizeGroupElements[2] == '" . $object->height . "') {
																					
																					$('affiliateSizeGroupsOptions').getLast().selected = true;
																					
																				}" :
																				
																				NULL)
																				
																				. "
																			
																			}
																			
																		});
																		
																	}
																	
															  }
															  
										  ).request();
										  
										  // add the size group management link
										  
										  $('affiliateSizeRowValue').appendText(' ');
										  
										  $('affiliateSizeRowValue').adopt(new Element('a', {
																				
																				'id':		'affiliateManageSizeGroups',
																				
																				'href': 	'javascript:void(0)',
																				
																				'events':	{
																				
																					'click':	function() {
																						
																						SqueezeBox.fromElement('" . str_replace("&amp;", "&", $adminLink) . "page=vma.size_groups_list&modal=true&type=" . $type . "', {
																								
																								classWindow: 'affiliateSizeGroupWindow',
																								
																								onClose: function() {
																									
																									showSizeRow(textads ? null : imageDetails);
																									
																								},
																								
																								onMove: function() {
																										
																									// verify the content has been loaded
																									
																									if ($$('div#sbox-content td#vmPage')) {
																										
																										// get the size group main page content
													
																										var sizeGroupMainPage = $$('div#sbox-content td#vmPage div.affiliateAdminPage').clone();
																										
																										// make sure the page isn't empty
																										
																										if ($$('div#sbox-content td#vmPage div.affiliateAdminPage') == '') {
																											
																											return false;
																											
																										}
																										
																										// completely empty the squeezebox
																										
																										$('sbox-content').empty();
																										
																										// insert just the size group main page content into the current page body
																										
																										$('sbox-content').adopt(sizeGroupMainPage);
																										
																										// make some visual adjustments
																										
																										$$('div#sbox-content div.affiliateAdminPage').setStyle('margin', '0px 0px 0px 0px');
																										
																										// get limit and limitstart values
																										
																										listLimitItem		= $$('div#sbox-content div#affiliatePaginationLimitContainer');
																										
																										listLimitStartItem	= $$('div#sbox-content div#affiliatePaginationLimitStartContainer');
																										
																										if (listLimitItem != '' && listLimitStartItem != '') {

																											listLimit 		= String(listLimitItem.getProperty('class')).replace('affiliatePaginationValue-', 		'');
																										
																											listLimitStart 	= String(listLimitStartItem.getProperty('class')).replace('affiliatePaginationValue-',	'');
																										
																										}
																										
																										// add the corresponding events to the size group name links, and size group remove buttons
																										
																										$$('div#sbox-content table.adminlist tr td a').each(function(el) {
																											
																											// this is a size group name link
																											
																											if (el.getProperty('class') == '') {
																												
																												// set the click event
																												
																												el.addEvent('click', function(e) {
																													
																													new Event(e).stop();
																													
																													SqueezeBox.fromElement(el, {
																													
																														size: {
																															
																															x:	640,
																															
																															y:	270
																															
																														},
																														
																														classWindow: 'affiliateSizeGroupWindow'
																														
																													});
																													
																												});
																												
																											}
																											
																											// this is a size group remove button
																											
																											else {
																												
																												// remove all other click events
																												
																												el.removeEvents('click');
														
																												el.setProperty('onclick', null);
																												
																												el.removeProperty('onclick');
																												
																												// set the click event
																												
																												el.addEvent('click', (function(e) {
																													
																													new Event(e).stop();
																													
																													if (confirm('" . $VM_LANG->_('PHPSHOP_DELETE_MSG') . "')) {
																														
																														// get the size group id
																														
																														var sizeGroupID		= this.getNext().innerHTML;
																														
																														// send the request
																														
																														var deleteSizeGroup = new Ajax(this.href, {
																																				  
																																				  method: 		'get',
																																				  
																																				  onSuccess:	function(response) {
																																					  
																																					  SqueezeBox.fromElement('" . str_replace("&amp;", "&", $adminLink) . "page=vma.size_groups_list&modal=true&type=" . $type . "', {
																																						  
																																						  	classWindow: 'affiliateSizeGroupWindow'
																																							
																																					  });
																																					  
																																				  }
																																				  
																																			  }
																																			
																														).request();
																														
																													}
																													
																												}).bind(el));
																											
																											}
																											
																										});
																										
																										// add the corresponding event to the new button
																										
																										$$('#affiliateNewSizeGroup').addEvent('click', function() {
																												
																											SqueezeBox.fromElement('" . str_replace("&amp;", "&", $adminLink) . "page=vma.size_groups_form&modal=true&type=" . $type . "', {
																												
																												size: {
																													
																													x:	640,
																													
																													y:	270
																													
																												},
																												
																												classWindow: 'affiliateSizeGroupWindow'
																												
																											});
																												
																										});
																										
																										// add the corresponding event to the size groups list link, and cancel button
																										
																										$$('a#affiliateReturnSizeGroup, a#affiliateCancelSizeGroup').addEvent('click', function(e) {
																											
																											new Event(e).stop();
																											
																											SqueezeBox.fromElement('" . str_replace("&amp;", "&", $adminLink) . "page=vma.size_groups_list&modal=true&type=" . $type . "', {
																												
																												classWindow: 'affiliateSizeGroupWindow'
																												
																											});
																											
																										});
																										
																										// remove all events from the form save button
																										
																										$$('div#sbox-content input.affiliateSaveButton').removeEvents('click');
														
																										$$('div#sbox-content input.affiliateSaveButton').setProperty('onclick', null);
																												
																										$$('div#sbox-content input.affiliateSaveButton').removeProperty('onclick');
																										
																										// add the corresponding event to the save buttons and apply button
																										
																										$$('div#sbox-content input.affiliateSaveButton, a#affiliateSaveSizeGroup').addEvent('click', function(e) {
																											
																											sendSizeGroupForm();
																										
																										});
																										
																										// add the corresponding events to the form fields
																										
																										$$('div#sbox-content input#affiliateSizeGroupName, div#sbox-content input#affiliateSizeGroupWidth, div#sbox-content input#affiliateSizeGroupHeight').addEvent('keydown', function(e) {
														
																											e = new Event(e);
																											
																											if (e.key == 'enter') {
																												
																												sendSizeGroupForm();
																											
																											}
																										
																										});
																										
																										// add the corresponding events to the pagination links
																										
																										$$('div#sbox-content table.adminlist ul.pagination li a').each(function(el) {
																											
																											// get pagination item's onclick value
																											
																											var onClickString = el.getProperty('onclick', null);
																											
																											// remove all click events from the pagination item
																											
																											el.removeEvents('click');
														
																											el.setProperty('onclick', null);
																												
																											el.removeProperty('onclick');
																											
																											// determine pagination item's limitstart value
																											
																											el.limitstart = onClickString.match(/document\.adminForm\.limitstart\.value\=([\d]+);/)[1];
																											
																											// add the new event

																											el.addEvent('click', function(e) {
																												
																												new Event(e).stop();

																												SqueezeBox.fromElement('" . str_replace("&amp;", "&", $adminLink) . "page=vma.size_groups_list&modal=true&type=" . 
																												
																																			$type . "&limitstart=' + this.limitstart + '&limit=' + window.listLimit, {
																													
																													classWindow: 'affiliateSizeGroupWindow'
																													
																												});
																											
																											});
																											
																										});
																										
																										// fix the pagination dropdown value
																										
																										$$('div#sbox-content table.adminlist select[name=\"limit\"] option').each(function(el) {
																											
																											if (el.value == listLimit) {
																												
																												el.selected = true;
																												
																											}
																											
																										});
																										
																										// get a handle for the pagination dropdown
																										
																										var paginationDropDown = $$('div#sbox-content table.adminlist select[name=\"limit\"]');
																										
																										// remove all change events from the pagination dropdown
																										
																										paginationDropDown.removeEvents('change');
														
																										paginationDropDown.setProperty('onchange', null);
																												
																										paginationDropDown.removeProperty('onchange');
																										
																										// add the new change event to the pagination dropdown
																										
																										paginationDropDown.addEvent('change', function(e) {
																											
																											new Event(e).stop();

																											SqueezeBox.fromElement('" . str_replace("&amp;", "&", $adminLink) . "page=vma.size_groups_list&modal=true&type=" . 
																											
																																		$type . "&limitstart=' + listLimitStart + '&limit=' + this.value, {
																												
																												classWindow: 'affiliateSizeGroupWindow'
																												
																											});
																												
																										});
																										
																									}
																									
																								}
																								
																						});
																						
																					}
																					
																				}
																				
																		  }).appendText('" . JText::_("SIZE_GROUPS", true) . "')
																		  
										  );
										  
										  $('affiliateManageSizeGroups').innerHTML += '&hellip;';
									  
									  }
									  
									  // add the preserve size text, for animated/dynamic content
									  
									  else {
										  
										  $('affiliateSizeRowValue').adopt(new Element('span', {
											  
																				'style': 'font-weight: bold; line-height: 15px;'
																				
																		  })
																		  
																		  .appendText(preserveSizeText)
																		  
										  );
										  
									  }
									  
									  // show the size row
									  
									  $('affiliateSizeRow').style.display = 'block';
									  
								  }";
								  
			$document->addScriptDeclaration($sizeSelectFunction);
			
			// include the number validation function
			
			$numberValidation	= $this->numberValidationFunction("integer");
			
			$document->addScriptDeclaration($numberValidation);
	
		}
		
		// include the banner form specific functions
		
		if ($type == "banners") {
					
			// include the image preview handling function
	
			$attachImagePreview = "// attach the image/flash file preview to the thumbnail
			
								   function attachImagePreview(width, height, fileType, fileName, fileExtension, temp) {
									   
									   // display the pointer cursor, if thumbnail is hovered upon
										  
									   $('thumbnailImage').style.cursor = window.ie && fileType == 'swf' ? 'cursor' : 'pointer';
										  
									   // determine preview image location
									   
									   var elementLocation	 = '" . $this->_website . "components/com_affiliate/banners/' + (temp ? 'temp_' : '') + fileName + '.' + fileExtension;
									   
									   // prepare the preview element
									   
									   if (fileType != 'swf') {
											  
										  var previewElement = new Element('img', {
											  
																					  'src': 		elementLocation	
																				
																		   });
																 
									   }
									  
									   else {
										  
										  var previewElement = window.ie ? new Element('img') : new Element('object', {
											  
																					  'data': 		elementLocation,
																			  
																					  'width':		width,
																			  
																					  'height':		height,
																			  
																					  'type':		'application/x-shockwave-flash'
																			  
																		  })
																		  
															  .adopt(new Element('param', {
																  
																					  'name':		'movie',
																					  
																					  'value':		elementLocation
																					  
																				  })
																				  
															  )
															  
															  .adopt(new Element('param', {
																  
																					  'name':		'quality',
																					  
																					  'value':		'high'
																					  
																				  })
																				  
															  );
										
										}
										
									   // add the click event to the thumbnail
										  
									   if (!window.ie || fileType != 'swf') {
										   
										   $('thumbnailImage').addEvent('click', function() {
			
												SqueezeBox.fromElement(new Element('div').adopt($$(previewElement)), {
																				
																			handler: 	'adopt',
																			
																			size:  	{
																			
																				x: width,
																				
																				y: height
																				
																			}
																			
																	   }
																	   
												);
				
											});	
										
									   }
										  
								   }";
			
			$document->addScriptDeclaration($attachImagePreview);
			
		}
			
		if ($form == "add" && $type == "banners") {

			// prepare the banner upload functions
		
			$document->addScript($this->_website . "components/com_affiliate/assets/js/ajaxupload.js");
			
			$vmaToken			= $this->buildToken();
			
			$bannerUploadFuncs	=  "// prepare the upload functions
			
								   window.addEvent('domready', function() {
										
										// initiate ajaxupload
									  
										new AjaxUpload('affiliateImage', { 
													   
											action: 		'" . JRoute::_($this->_website) . "', 
											
											data:			{
												
												'option':		'com_affiliate',
												
												'task':			'processUploadedBanner',
												
												'format':		'raw',
												
												'vmatoken':		'" . $vmaToken->vmatoken . "',
												
												'vmatokenid':	'" . $vmaToken->vmatokenid . "'
												
											},
											
											responseType: 	'text', 
											
											onSubmit: 		function(file, extension) { 
												
												extension = extension.toLowerCase();
												
												if (extension != 'gif' && extension != 'jpg' && extension != 'jpeg' && extension != 'png' && extension != 'swf') {
													
													alert('Only image or flash files can be uploaded (gif, png, jpg/jpeg, swf)!');
											
													return false;
													
												}
												
												else {
													
													$('affiliateSizeRow').style.display = 'none';
													
													showUploadSpinner(file);
													
												}
												
											}, 
											
											onComplete: function(file, response) { 
											
												onUploadComplete(response); 
												
											} 
											
										});
									  
										// preload the spinner
										
										var spinnerImagePreload 					= new Image();
				  
										spinnerImagePreload.src 					= '" . $this->_website . "components/com_affiliate/assets/images/spinner.gif';
										
								   });";
			
			$document->addScriptDeclaration($bannerUploadFuncs);
		
			// upload spinner display function
		
			$showUploadSpinner	= "// show the upload spinner
			
								   function showUploadSpinner(file) {
									
									  // hide the upload button
									  
									  $('affiliateImage').style.display = 'none';
									  
									  // remove any previous message
									  
									  if ($('uploadMessage') != null) {
										  
										  $('uploadMessage').remove();
										  
									  }
									  
									  // remove any previous thumbnail
									  
									  if ($('imageThumbnail') != null) {
										  
										  $('imageThumbnail').remove();
										  
									  }
									  
									  // create the spinner div
									  
									  var loader = new Element('div', {
															   
																  'id':		'uploadSpinner'
															   
															   })
															   
													  .appendText('Uploading ' + file)
															   
													  .inject($('affiliateImage'), 'after');
									  
									  // uploading text effect
									  
									  window['intervalSpinner'] = window.setInterval(function() {
																			  
										  if ($('uploadSpinner').innerHTML.substring($('uploadSpinner').innerHTML.length - 3) 		== '...') {
											  
											  $('uploadSpinner').innerHTML = 'Uploading ' + file;
											  
										  }
										  
										  else if ($('uploadSpinner').innerHTML.substring($('uploadSpinner').innerHTML.length - 2) 	== '..') {
											  
											  $('uploadSpinner').innerHTML = 'Uploading ' + file + '...';
											  
										  }
										  
										  else if ($('uploadSpinner').innerHTML.substring($('uploadSpinner').innerHTML.length - 1) 	== '.') {
											  
											  $('uploadSpinner').innerHTML = 'Uploading ' + file + '..';
											  
										  }
										  
										  else {
											  
											  $('uploadSpinner').innerHTML = 'Uploading ' + file + '.';
										  
										  }
										  
									  }, 200);
							  
								  }";
							  
			$document->addScriptDeclaration($showUploadSpinner);
			
			// post-upload banner handling function
			
			$onUploadComplete 	= "// process banner upload
			
								   function onUploadComplete(response) {
							
									  // stop text animation
									  
									  window.clearInterval(window['intervalSpinner']);
									  
									  // remove the spinner
									  
									  $('uploadSpinner').remove();
									  
									  // remove any previous message
									  
									  if ($('uploadMessage') != null) {
										  
										  $('uploadMessage').remove();
										  
									  }
									  
									  // remove any previous thumbnail
									  
									  if ($('imageThumbnail') != null) {
										  
										  $('imageThumbnail').remove();
										  
									  }
									  
									  // unhide the upload button
									  
									  $('affiliateImage').style.display = 'block';
									  
									  // parse the response
												  
									  var responses 	= response.split('|');
									  
									  imageDetails		= {
															
																'status':			responses[0],
									  
																'md5Hash':			responses[1],
																
																'oldExtension':		responses[2],
																
																'fileType':			responses[3],
																
																'animatedGIF':		responses[4],
																
																'width':			parseFloat(responses[5]),
																
																'height':			parseFloat(responses[6]),
																
																'sizeGroupName':	responses[7]
										  
														  };
									  
									  // if successful
			  
									  if (imageDetails.status == 'success') {
										  
										  // determine thumbnail location
			  
										  var thumbnailFile  = '" . $this->_website . "components/com_affiliate/thumbs/thumbbig_' + (imageDetails.fileType == 'swf' ? 'swf' : imageDetails.md5Hash) + '.' + (imageDetails.fileType == 'swf' ? 'png' : imageDetails.fileType);
										  
										  // display the image thumbnail
										  
										  var imageThumbnail = new Element('div', {
																		  
																			  'id':			'imageThumbnail',
																			  
																			  'styles': 	{
																						  
																							'clear': 	'both'
																				  
																			  }
															   
																		   })
															
																  .adopt(new Element('img', {
																						
																							'id':		'thumbnailImage',
																						  
																							'src':		thumbnailFile,
																						  
																							'style': 	'clear: both;'
																						  
																					})
																				  
																  )
																  
																  .inject($('affiliateImage'), 'before');
																						
										  // attach the preview image to the thumbnail
										  
										  attachImagePreview(imageDetails.width, imageDetails.height, imageDetails.fileType, imageDetails.md5Hash, imageDetails.oldExtension, true);
										  
										  // display the size inputs
										  
										  showSizeRow(imageDetails);
										  
										  // prepare the banner-specific hidden form fields
										  
										  var bannerDetails = imageDetails.md5Hash + '|' + imageDetails.oldExtension + '|' + imageDetails.fileType;
										  
										  if ($('affiliateBannerDetails')) {
											  
											  $('affiliateBannerDetails').value = bannerDetails;
											  
										  }
										  
										  else {
											  
											  var bannerDetailsInput = new Element('input', {
												  
																			'type':		'hidden',
																			
																			'name':		'bannerDetails',
																			
																			'id':		'affiliateBannerDetails',
																			
																			'value':	bannerDetails
																			
																		}).injectAfter('affiliateImage');
											  
										  }
										  
										  // center the swf icon
										  
										  if (imageDetails.fileType == 'swf') {
											  
											  $('thumbnailImage').setStyle('padding-left', '64px');
											  
											  $('thumbnailImage').setStyle('padding-right', '64px');
											  
										  }
										  
										  // focus the next field
										  
										  if ($('affiliateName').value == '') {
											  
											  $('affiliateName').focus();
											  
										  }
										  
									  }
									  
									  // if not successful
									  
									  else {
										  
										  // make sure the error message isn't empty
										  
										  imageDetails.status = imageDetails.status == '' ? 'Unknown upload error!' : imageDetails.status;
										  
										  // display the error mesage
										  
										  var errorMessage = window.ie ? alert(imageDetails.status) : new Element('div', {
																		  
																			  'id':		'uploadMessage',
															   
																			  'style': 	'background: url(" . $this->_website . "administrator/images/publish_x.png) no-repeat left center; display: block; padding: 4px 0px 4px 20px; position: relative; float: none; margin-left: 4px; margin-bottom: 2px; width: auto; color: red;'
															   
																		 })
																		 
															  .appendText(imageDetails.status)
													   
															  .adopt(new Element('span')
																				  
																	  .adopt(new Element('img', {
																							  
																							  'style':	'background: url(" . $this->_website . "components/com_affiliate/assets/images/close.png) no-repeat right center; cursor: pointer; float: right; width: 10px; height: 10px; margin-left: 4px; margin-top: 2px;'
																						  
																						})
																					  
																			.addEvents({
																					   

																							  'click':	function() {
																								  
																										  // remove error notice
																								  
																										  this.getParent().getParent().remove();
																								  
																							  }
																					   
																			})
																					  
																	)																
																																						  
															  )
															  
															  .inject($('affiliateImage'), 'before');
										  
									  }
										  
								   }";
			
			$document->addScriptDeclaration($onUploadComplete);
		
		}
		
		if ($form == "edit" && $type == "banners") {
		
			// attach the preview image to the thumbnail, when the page is ready
			
			$attachPreview		= "// attach the preview image to the thumbnail
			
								   window.addEvent('domready', function() {
									  
									  attachImagePreview(" . $object->banner_width . ", " . $object->banner_height . ", '" . $object->banner_type . "', '" . $object->banner_image . "', '" . $object->banner_type . "', false);
									  
								   });";
			
			$document->addScriptDeclaration($attachPreview);

		}
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to update an offline tracking name value
	 */
	 
	function updateOfflineNameValue($affiliateID, $insert) {
		
		// get the field's id
		
		$query				= "SELECT `fieldid` FROM #__vm_userfield WHERE `name` = 'vm_partnersname'";
		
		$this->_db->setQuery($query);
		
		$offlineFieldID		= $this->_db->loadResult();
		
		// get the affiliate's name
		
		$query				= "SELECT CONCAT(`fname`, ' ', `lname`) FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
		
		$this->_db->setQuery($query);
		
		$affiliateName		= $this->_db->loadResult();
		
		// perform the operation
		
		$query				= $insert === true 		? 
		
							  "INSERT INTO #__vm_userfield_values VALUES ('', '" 		. $offlineFieldID 	. "', '"										. 
							  
							  $affiliateName . "', '" . $affiliateID 	. "', '" 		. $affiliateID		. "', '0')" 									: 
							  
							  ($insert == "update"	?
							  
							  "UPDATE #__vm_userfield_values SET `fieldtitle` = '" . $affiliateName . "' WHERE `fieldvalue` = '" . $affiliateID . "'" 		:
							  
							  "DELETE FROM #__vm_userfield_values WHERE `fieldid` = '" 	. $offlineFieldID	. "' AND `fieldvalue` = '"	. $affiliateID . "'");

		$this->_db->setQuery($query);
		
		$this->_db->query();
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to format currency amounts, according to VirtueMart's settings
	 */
	
	function formatAmount($amount) {
		
		// get currency settings, if not already set
		
		if (!isset($this->_currencySettings)) {
			
			$this->getCurrencySettings();
	
		}
		
		// format amount
		
		$tempAmount 		= number_format($amount, $this->_currencySettings["nbdecimal"], $this->_currencySettings["sdecimal"], $this->_currencySettings["thousands"]);
		
		// place the currency symbol
		
		$formattedAmount 	= $this->_currencySettings["format"];
		
		$formattedAmount 	= str_replace("00", $tempAmount, $formattedAmount);
		
		$formattedAmount 	= str_replace("Symb", $this->_currencySettings["symbol"], $formattedAmount);
		
		// return the formatted amount
		
		return $formattedAmount;
		
	}
	
	/**
	 * Method to retrieve the tier tree
	 */
	 
	function tierTree($affiliateID) {
		
		global $vmaSettings;
		
		// get tier structure

		$parentAffiliates 			= array();

		$i 							= 1;

		while (isset($affiliateID) && !empty($affiliateID) && $i < $vmaSettings->tier_level) {

			$query 					= "SELECT `referred` FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";

			$this->_db->setQuery ($query);

			$parentAffiliate 		= $this->_db->loadResult();

			$affiliateID 			= $parentAffiliate;

			if (isset($parentAffiliate) && !empty($parentAffiliate)) { 
			
				$parentAffiliates[] = $parentAffiliate; 
			
			}

			$i++;

		}
		
		return $parentAffiliates;
					
	}
	
	/**
	 * Method to check whether an affiliate is blocked
	 */

	function isBlocked($affiliateID) {

		// perform the query

		$query		= "SELECT `blocked` from #__vm_affiliate WHERE `affiliate_id` = '" . (int) $affiliateID . "'";

		$this->_db->setQuery($query);

		$this->_db->query();
		
		$rows		= $this->_db->getNumRows();

		$blocked 	= $this->_db->loadResult();

		$blocked	= $blocked || !$rows;

		// return the result

		return $blocked;

	}
	
	/**
	 * Method to prepare a search query for a given field and value
	 */
	 
	function prepareSearch($field, $value, $table = NULL) {
		
		// convert to array if not already
		
		$field				 = !is_array($field) ? array($field) : $field;
		
		// determine the prefix
		
		$prefix				 = $table ? $table . "." : NULL;
		
		// open the search query
		
		$searchQuery		 = "(";
		
		// add each field to the search query
		
		for ($i = 0; $i < count($field); $i++) {
			
			$searchQuery 	.= $prefix . "`" . $field[$i] . "` LIKE '%" . $value . "%'";
			
			$searchQuery	.= isset($field[$i + 1]) ? " OR " : NULL;
			
		}
		
		// close the search query
		
		$searchQuery		 .= ")";
		
		// return the search query
		
		return $searchQuery;
		
	}
	
	/**
	 * Method to process a link into a readable destination
	 */
	 
	function processLink($link) {
		
		// determine if this is a homepage or a main shop link
		
		$caption = $link == "index.php" || $link == "index.php?" ? JText::_("MAIN_SITE") : ($link == "index.php?option=com_virtuemart" ? JText::_("MAIN_SHOP") : $link);
		
		// determine if this is a product or category link
		
		if (stristr($link, "product_id") || stristr($link, "category_id")) {
			
			// format link
			
			$link 				= str_replace("&amp;", "&", $link);
			
			// parse the link
			
			parse_str(parse_url($link, PHP_URL_QUERY));
			
			// this is a product link, so get its name
			
			if (isset($product_id) && !empty($product_id)) {
				
				$query 			= "SELECT `product_name` FROM #__vm_product WHERE `product_id` = '" 	. $product_id . "'";
				
				$this->_db->setQuery($query);
				
				$caption		= $this->_db->loadResult();
				
			}

			// this is a category link, so get its name
			
			if (isset($category_id) && !empty($category_id)) {
				
				$query 			= "SELECT `category_name` FROM #__vm_category WHERE `category_id` = '" 	. $category_id . "'";
				
				$this->_db->setQuery($query);
				
				$caption		= $this->_db->loadResult();
				
			}
			
		}
		
		// determine if this is a menu link
		
		if ($caption == $link) {
			
			// format link
			
			$link 				= str_replace("&amp;", "&", $link);
			
			// determine alternative link
			
			$link2				= preg_replace("/&Itemid(\=[^&]*)?(?=&|$)|^Itemid(\=[^&]*)?(&|$)/", "", $link);
			 
			// get the menu name
			
			$query 		= "SELECT `name` FROM #__menu WHERE `link` = '" . $link . "' OR `link` = '" . $link2 . "'";
			
			$this->_db->setQuery($query);
			
			$menuName 	= $this->_db->loadResult();
			
			$caption	= $menuName ? $menuName : $caption;
			
		}
		
		// construct the actual link
		
		$newLink = "<a href=\"" . JRoute::_($this->_website . $link) . "\">" . $caption . "</a>";
		
		// return the new link
		
		return $newLink;
		
	}
	
	/**
	 * Method to create a thumbnail from a picture
	 */
	
	function resizeImage($file, $filename, $type = "thumb", $width = NULL, $height = NULL) {
		
		// get image parameters
		
		$destination	= $type == "thumb" || $type == "thumbbig" 	? "thumbs" 	: "banners";
		
		$prefix			= $type == "thumb" 							? "thumb_" 	: ($type == "thumbbig" ? "thumbbig_" 	: NULL);
		
		$width			= $type == "thumb" 							? 32 		: ($type == "thumbbig" ? 152 			: $width);
		
		$height			= $type == "thumb" 							? 32 		: ($type == "thumbbig" ? 152 			: $height);
		
    	$info 			= getimagesize($file);
		
    	$image 			= '';

    	$final_width 	= 0;
		
    	$final_height 	= 0;
		
    	list($width_old, $height_old) = $info;
		
		$factor 		= min($width / $width_old, $height / $height_old);  

		$final_width 	= stristr($type, "thumb") ? round($width_old * $factor) : $width;
		
		$final_height 	= stristr($type, "thumb") ? round($height_old * $factor) : $height;

		// get image type
		
		switch ($info[2]) {
			
			case IMAGETYPE_GIF:
			
				$image 	= imagecreatefromgif($file);
				
				$ext	= "gif";
				
				break;
				
			case IMAGETYPE_JPEG:
			
			default:
			
				$image 	= imagecreatefromjpeg($file);
				
				$ext	= "jpg";
				
				break;
				
			case IMAGETYPE_PNG:
			
				$image 	= imagecreatefrompng($file);
				
				$ext	= "png";
				
				break;
				
		}
		
		// create the new image
		
		$image_resized = imagecreatetruecolor($final_width, $final_height);
		
		// deal with transparent gifs and pngs
		
		if (($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG)) {
				
			$trnprt_indx 		= imagecolortransparent($image);
			
			// transparent gif
			
			if ($trnprt_indx >= 0) {
								
				$trnprt_color	= @imagecolorsforindex($image, $trnprt_indx);
					
				$trnprt_indx	= imagecolorallocate($image, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
		 
				imagefill($image, 0, 0, $trnprt_indx);
		 
				imagecolortransparent($image, $trnprt_indx);
				
			}
			
			// png
			
			elseif ($info[2] == IMAGETYPE_PNG) {
   
				imagealphablending($image_resized, false);
	   
				$color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
	   
				imagefill($image_resized, 0, 0, $color);
	   
				imagesavealpha($image_resized, true);
				
			}
			
		}

		// resize the image
		
		imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
		
		// re-set transparency for resized transparent gif
		
		if ($info[2] == IMAGETYPE_GIF && isset($trnprt_indx) && $trnprt_indx >= 0) {
			
			imagecolortransparent($image_resized, imagecolorallocate($image_resized, 0, 0, 0));
			
			imagetruecolortopalette($image_resized, true, 256);
			
		}

		// create the resized file
		
		$output  = JPATH_ROOT . DS . "components" . DS . "com_affiliate" . DS . $destination . DS . $prefix . $filename . "." . $ext;
		
		switch ($info[2]) {
			
			case IMAGETYPE_GIF:
			
				imagegif($image_resized, 	$output);
				
				break;
				
			case IMAGETYPE_JPEG:
			
			default:
			
				imagejpeg($image_resized, 	$output, 100);
				
				break;
				
			case IMAGETYPE_PNG:
			
				imagepng($image_resized, 	$output, 9);
				
				break;
				
		}
		
		// destroy created images
		
		imagedestroy($image);
		
		imagedestroy($image_resized);
		
		return true;
		
	}
	
	/**
	 * Method to return a number validation function
	 */
	
	function numberValidationFunction($type) {
		
		$integerValidation 	= 	'// integer input validation function
								
							    function validateNumber(event, el, digits) {
								
									// initialize required variables
									
									var char;
									
									var valid;
									
									var selected;
									
									var startPos 		= el.selectionStart;
									
									var endPos 			= el.selectionEnd;
									
									var doc 			= document.selection;
									
									var allowedChars	= "0123456789";
									
									var allowedCodes	= /^8$|^9$|^13$|^16$|^17$|^18$|^35$|^36$|^37$|^38$|^39$|^40$|^45$|^46$/;
									
									var char			= window.event ? event.keyCode : event.which;
									
									// verify if the key is allowed
									
									valid				= allowedChars.indexOf(String.fromCharCode(char)) != -1 || allowedCodes.test(char);
									
									// verify if any substring is selected

									selected			= (doc && doc.createRange().text.length != 0) || (!doc && el.value.substring(startPos,endPos).length != 0) ? true : false;

									// restrict field to a certain number of digits
									
									valid				= el.value.length < digits || allowedCodes.test(char) || selected ? valid : false;
									
									return valid;
									
								}';
								
		$floatValidation 	= 	'// float input validation function
								
							    function validateNumberInput(event, el) {
						
									// initialize required variables
									
									var char;
									
									var valid;
									
									var selected;
									
									var caretPos 		= 0;
									
									var startPos 		= el.selectionStart;
									
									var endPos 			= el.selectionEnd;
									
									var doc 			= document.selection;
									
									var allowedChars	= "0123456789";
									
									var allowedCodes	= /^8$|^9$|^13$|^16$|^17$|^18$|^35$|^36$|^37$|^38$|^39$|^40$|^45$|^46$|^110$|^190$/;
									
									var char			= window.event ? event.keyCode : event.which;
								
									// verify if the key is allowed
									
									valid				= allowedChars.indexOf(String.fromCharCode(char)) != -1 || allowedCodes.test(char);
			
									// only allow one dot
									
									if (el.value.indexOf(".") != -1 && (char == 110 || char == 190)) {
										
										valid			= false;
										
									}
									
									// only allow four decimals for fixed rates, and two for the percentage
									
									var decimals		= el.value.split(".");
									
									if (typeof(decimals[1]) != "undefined" 		&& !allowedCodes.test(char) 	&& char != 110 && char != 190 && 
									
									   ((el.id == "affiliatePerSalePercentage" 	&& decimals[1].length >= 2) 	|| decimals[1].length >= 4)) {
										
										// verify if any substring is selected

										selected		= (doc && doc.createRange().text.length != 0) || (!doc && el.value.substring(startPos,endPos).length != 0) ? true : false;

										// in internet explorer
										
										if (doc) { 
			
											el.focus();
			
											var sel		= doc.createRange();
			
											sel.moveStart("character", -el.value.length);
										
											caretPos	= sel.text.length;
											
										}
										
										// in firefox
										
										else if (startPos || startPos == "0") {
											
											caretPos	= startPos;
										
										}
										
										// block excess digits
										
										if (caretPos > decimals[0].length && !selected) {
											
											valid		= false;
											
										}
										
									}
									
									return valid;
									
								}';
								
		return $type == "integer" ? $integerValidation : $floatValidation;

	}
	
	/**
	 * Method to display a "No results found!" message, while preserving compatibility with VirtueMart toolbar buttons
	 */
	 
	function displayNoResultsFound($page) {
		
		global $sess;
		
		// initialize required variables
		
		$link = $this->getAdminLink();
		
		// build the message to return
		
		return  '<div style="text-align: center;">' . JText::_("NO_RESULTS") . '</div>' . 
		
				'<form method="post" action="' 		. $link .	'page='	 	 . $page . '" name="adminForm">
					
					<input type="hidden" 	name="vmtoken" 		value="'	 . vmSpoofValue($sess->getSessionId()) . '" />
					
					<input type="hidden" 	name="page"			value="'	 . $page . '" />
					
					<input type="hidden" 	name="option"		value="com_virtuemart" />
					
					<input type="hidden" 	name="task"			value="" />
					
					<input type="hidden" 	name="func"			value="" />
					
					<input type="hidden" 	name="no_menu" 		value="0" />
					
					<input type="hidden" 	name="no_toolbar"	value="0" />
					
					<input type="hidden" 	name="only_page"	value="0" />
					
					<input type="hidden" 	name="pshop_mode"	value="admin" />
					
					<input type="checkbox"	name="toggle" 		onclick="" style="display: none;" />
					
					<input type="hidden" 	name="boxchecked" />
        
    			</form>';
		
	}
	
	/**
	 * Method to build a secure token used to communicate securely with the frontend via AJAX
	 */
	 
	function buildToken() {
		
		$user 						= &JFactory::getUser();
		
		$secureToken				= new stdClass();
		
		$secureToken->vmatoken		= md5($user->password);
		
		$secureToken->vmatokenid	= $user->id;
		
		return $secureToken;
		
	}
	
	/**
	 * Method to include the stylesheet used by VM Affiliate's administration
	 */
	
	function includeAdminStyleSheet() {
		
		// initialize required variables
		
		$document		= &JFactory::getDocument();
		
		$cssURL			= $this->_website . "components/com_affiliate/assets/css/";
		
		// include the admin style sheets
		
		$document->addStyleSheet($cssURL . "adminStyle.css");

		$document->addCustomTag('<!--[if lte IE 7]>'				.

									'<link rel="stylesheet" href="' . $cssURL . 'adminStyle.ie7.css' . '" type="text/css" />' . 
								
								'<![endif]-->');
								
		$document->addCustomTag('<!--[if IE 8]>'					.

									'<link rel="stylesheet" href="' . $cssURL . 'adminStyle.ie8.css' . '" type="text/css" />' . 
								
								'<![endif]-->');
								
		// confirm the operation
				
		return true;
						
	}
	
	/**
	 * Method to parse a link and ensure it's properly formatted
	 */

	function parseURL($url) {
		
		// make sure it uses the hypertext transport protocol
		
		if (!stristr($url, "http://")) {
			
			$url = "http://" . $url;
			
		}
		
		// return the formatted url
		
		return $url;
		
	}
	
	/**
	 * Method to fix the SqueezeBox modal class extend issues, when used along with mootools 1.2
	 */
	 
	function fixSqueezeBox() {
		
		$document	= &JFactory::getDocument();
			
		$document->addScriptDeclaration("window.addEvent('domready', function() {
											
											if (typeof(SqueezeBox) != 'undefined') {
												
												SqueezeBox.extend(SqueezeBox, Events.prototype);
												
												SqueezeBox.extend(SqueezeBox, Options.prototype);
												
												SqueezeBox.extend(SqueezeBox, Chain.prototype);
												
											}
											
										});");
		
	}
	
}

?>