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

// import joomla component controller application

jimport('joomla.application.component.controller');
 
/**
 * Controller file for VM Affiliate's Affiliate Panel component
 */
 
class AffiliateController extends JController {
	
    /**
     * Method to display the view
     */
	 
    function display() {
		
		global $vmaHelper, $vmaSettings;
		
		// fail if the vm affiliate helper plugin is not installed or is disabled
		
		if (!JPluginHelper::isEnabled("system", "vma")) {
			
			$this->setRedirect("index.php", "The VM Affiliate Helper Plugin is not installed or is disabled!", "error");
			
			return false;
			
		}
		
		// check affiliate login status
		
		$session 	= &JFactory::getSession();
		
		$affiliate 	= !$session->isNew() ? $session->get("affiliate") : NULL;
		
		// get the requested view
		
		$view 		= JRequest::getVar("view", "login", "get");
		
		$subview	= JRequest::getVar("subview", "");
		
		// set which view the affiliate is able to access while logged in and while not
		
		$inViews	= array("panel");
		
		$outViews	= array("login", "register", "lostpassword", "terms");

		// redirect affiliate according to his login status
		
		$view		= isset($affiliate->affiliate_id) 	&& in_array($view, $outViews) 	? "panel" 	: $view;
		
		$view		= !isset($affiliate->affiliate_id) 	&& in_array($view, $inViews) 	? "login" 	: $view;
		
		$subview	= !isset($affiliate->affiliate_id)									? NULL 		: $subview;
		
		// check if frontend registration is disabled
		
		$view		= $view == "register" && !$vmaSettings->allow_signups				? "login"	: $view;
		
		// check if a site user is logged in, and there is any affiliate linked to it
		
		if ($view == "login" && $this->checkLinking()) {
			
			return true;

		}

		// set the view
		
		JRequest::setVar("view", $view);
		
		// load the required info for the preview view
		
		if ($view == "prev") {
			
			$this->previewAd();
			
		}
		
		// refresh affiliate object if this is the panel
		
		if ($view == "panel") {
			
			$this->refreshAffiliate();
			
		}
		
		// get correct section of ads pages
		
		if ($subview == "banners") {
			
			$this->getActiveAdsSection();
		
		}
		
		// get correct section of stats pages
		
		if ($subview == "statistics") {
			
			$vmaHelper->getActiveStatsSection();
		
		}
		
		// display the component
		
		parent::display();
		
		// prevent gzip issues
		
		$format = JRequest::getVar("format");
		
		if ($format == "raw") ob_end_flush();
		
    }
	
	/**
	 * Method to verify the secure VMA token used to communicate with the backend via AJAX
	 */
	 
	function checkVMAToken() {
		
		// initiate required variables
		
		$vmaToken 	= JRequest::getVar("vmatoken", 		NULL);
		
		$vmaTokenID = JRequest::getVar("vmatokenid", 	NULL);
		
		// validate information

		if ($vmaToken && $vmaTokenID) {
			
			$user 	= JFactory::getUser($vmaTokenID);
			
			$valid 	= md5($user->password) == $vmaToken;
			
			return $valid;
			
		}
		
		// validation failes
		
		return false;
		
	}
	
	/**
	 * Method to log an affiliate in
	 */
	 
	function login() {
		
		global $vmaHelper;
		
		$session 	= &JFactory::getSession();
		
		$redirect	= JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=login"), false);
		
		// check for request forgeries
		
		if (!$session->isNew()) {
			
			JRequest::checkToken("request") or jexit( "Invalid Token" );
		
		}
		
		// get credentials
		
		$username 	= JRequest::getString("username", "", "post");
		
		$password	= JRequest::getString("passwd", "", "post", JREQUEST_ALLOWRAW);
		
		// validate login form
		
		if (empty($username) || empty($password)) {
			
			$this->setRedirect($redirect, JText::_("JGLOBAL_AUTH_EMPTY_PASS_NOT_ALLOWED"), "error");
			
			return false;
				
		}
		
		// get database object
		
		$database	= &JFactory::getDBO();
		
		// check login
		
		$query 		= "SELECT * FROM #__vm_affiliate WHERE `username` = '" . $username . "' AND `password` = '" . md5($password) . "'";
		
		$database->setQuery( $query );
		
		$affiliate	= $database->loadObject();

		// if exists, insert the session
		
		if ($affiliate && $affiliate->affiliate_id) {
			
			// check if the affiliate is blocked
		
			if ($vmaHelper->isBlocked($affiliate->affiliate_id)) {
				
				$this->setRedirect($redirect, JText::_("JERROR_NOLOGIN_BLOCKED"), "error");
			
				return false;
				
			}
			
			// start the affiliate session
			
			$session->set('affiliate', $affiliate);
			
			JRequest::setVar('view', 'panel');
			
			parent::display();
			
			return true;
			
		}
		
		else {
			
			$this->setRedirect($redirect, JText::_("JGLOBAL_AUTH_INCORRECT"), "error");
			
			return false;
			
		}
		
	}
	
	/**
	 * Method to log an affiliate out
	 */
	 
	function logout() {
		
		global $vmaHelper;
		
		$session = &JFactory::getSession();
		
		$session->clear("affiliate");
		
		$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=login"), false), JText::_("You have successfully been logged out of the affiliate panel!"), "message");
			
		return true;
		
	}
	
	/**
	 * Method to register an affiliate
	 */
	 
	function register() {
		
		global $vmaSettings, $vmaHelper;
		
		$session 			= &JFactory::getSession();
		
		// check for request forgeries
		
		if (!$session->isNew()) {
			
			JRequest::checkToken("request") or jexit( "Invalid Token" );
		
		}
		
		// check if registration is allowed
		
		if (!$vmaSettings->allow_signups) {
			
			$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=login"), false), JText::_("New affiliate applications are not currently accepted."), "error");
			
			return false;
			
		}
		
		// get registration model
		
		$model				= $this->getModel("register");
		
		// validate the registration form
		
		if (!$newAffiliate 	= $model->validateRegistration()) {
			
			return false;
			
		}
		
		// get database object
		
		$database			= &JFactory::getDBO();
		
		// get configuration object
		
		$config				= &JFactory::getConfig();
		
		// get user object
		
		$user	 			= &JFactory::getUser();
		
		// get new affiliate special parameters
		
		$parentTier			= isset($_COOKIE['aff_id']) ? $_COOKIE['aff_id'] : NULL;
		
		$initialBonus		= $vmaSettings->initial_bonus;

		$blocked			= $vmaSettings->auto_block;
		
		$timeAndDate		= date("Y-m-d\TH:i:s");
		
		$siteUserName		= $user->get('id') > 0 ? $user->get('username') : NULL;
		
		// properly escape data
		
		foreach ($newAffiliate as $property => $value) {
			
			if ($property != "password") {
				
				$newAffiliate[$property] = $database->getEscaped($newAffiliate[$property]);
			
			}
			
		}
		
		// insert the new affiliate in the database
		
		$query 				= "INSERT INTO #__vm_affiliate VALUES ('" . NULL . "', '" . $newAffiliate["username"] . "', '" . md5($newAffiliate["password"]) . "', " . 
		
							  "'" . $newAffiliate["fname"] . "', '" . $newAffiliate["lname"] . "', '" . $newAffiliate["mail"] . "', '" . $newAffiliate["website"] . "', " . 
							  
							  "'" . $initialBonus . "', '" . $newAffiliate["street"] . "', '" . $newAffiliate["city"] . "', " . "'" . $newAffiliate["state"] . "', " . 
							  
							  "'" . $newAffiliate["country"] . "', '" . $newAffiliate["zipcode"] . "', '" . $newAffiliate["phoneno"] . "', '" . $newAffiliate["taxssn"] . "', " .
							  
							  "'N/A', '0', '0', '0', '0', '2', '0', '1', '" . $siteUserName . "', '" . $blocked . "', '" . $parentTier . "', '" . $timeAndDate . "')";
							  
		$database->setQuery( $query );
		
		$database->query();
		
		// get the new affiliate's id
		
		$affiliateID 		= $database->insertid();
		
		// update the offline tracking name values
		
		if (!$vmaSettings->auto_block) {
			
			$vmaHelper->updateOfflineNameValue($affiliateID, true);
		
		}
		
		// e-mail the new affiliate
		
		$affiliateMail		= &JFactory::getMailer();
			
		$affiliateMail->addRecipient($newAffiliate["mail"]);
		
		$affiliateMail->setSender(array($config->getValue( 'config.mailfrom' ), $config->getValue( 'config.fromname' )));
		
		$affiliateMail->setSubject(JText::sprintf("WELCOME_MESSAGE", $config->getValue( 'config.sitename' )));
		
		$affiliateMail->setBody(JText::sprintf("WELCOME_EMAIL", "\r\n\r\n", $newAffiliate["username"] . "\r\n\r\n", 
		
								"\r\n" . JRoute::_($vmaHelper->vmaRoute(JURI::base() . "index.php?option=com_affiliate&view=login")) . "\r\n\r\n", 
								
								"\r\n" . $config->getValue( 'config.sitename' ) . "\r\n" . JURI::base()));
		
		$affiliateMail->send();
		
		// e-mail the administrator
		
		$adminMail			= &JFactory::getMailer();
		
		//$adminMail->to		= NULL;
		
		$adminMail->addRecipient($config->getValue( 'config.mailfrom' ));
		
		$adminMail->setSender(array($config->getValue( 'config.mailfrom' ), $config->getValue( 'config.fromname' )));
		
		$adminMail->setSubject(JText::sprintf("NEW_AFFILIATE_REGISTERED_SUBJECT", $config->getValue( 'config.sitename' ) . "!"));
		
		$adminMail->setBody(JText::sprintf("NEW_AFFILIATE_REGISTERED_SUBJECT", $config->getValue( 'config.sitename' ) . "!") . 
		
							"\r\n\r\n" . JText::_("NEW_AFFILIATE_REGISTERED_MESSAGE") . ":" . "\r\n\r\n" . 
							
							JText::_("AFFILIATE_ID") . ": " . $affiliateID . "\r\n" . 
							
							JText::_("JGLOBAL_USERNAME") . ": " . $newAffiliate["username"]);
							
		$adminMail->send();
		
		// redirect to the login page
		
		$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=login"), false), JText::_("SUCCESSFUL_REGISTRATION"), "message");
			
		return true;
		
	}
	
	/**
	 * Method to create and send a new password for an affiliate
	 */
	 
	function lostPassword() {
		
		global $vmaSettings, $vmaHelper;
		
		$session 			= &JFactory::getSession();
		
		// check for request forgeries
		
		if (!$session->isNew()) {
			
			JRequest::checkToken("request") or jexit( "Invalid Token" );
		
		}
		
		// get model
		
		$model				= $this->getModel("lostpassword");
		
		// validate the registration form
		
		if (!$affiliate		= $model->validateLostPasswordForm()) {
			
			return false;
			
		}
		
		// get database object
		
		$database			= &JFactory::getDBO();
		
		// get configuration object
		
		$config				= &JFactory::getConfig();
		
		// check if the affiliate details exist
		
		$query 				= "SELECT `affiliate_id` FROM #__vm_affiliate WHERE `username` = '" . $affiliate["username"] . "' AND `mail` = '" . $affiliate["mail"] . "'";
		
		$database->setQuery( $query );
		
		$affiliateID 		= $database->loadResult();
		
		if (!$affiliateID) {
			
			$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=lostpassword"), false), JText::_("NO_AFFILIATE_FOUND"), "error");
			
			return false;
			
		}

		// create new password and update it in the database
		
		$newPassword		= md5(rand(10000000, 50000000));
		
		$query 				= "UPDATE #__vm_affiliate SET `password` = '" . md5($newPassword) . "' WHERE `affiliate_id` = '" . $affiliateID . "'";
							  
		$database->setQuery( $query );
		
		$database->query();
		
		// e-mail the affiliate
		
		$affiliateMail		= &JFactory::getMailer();
			
		$affiliateMail->addRecipient($affiliate["mail"]);
		
		$affiliateMail->setSender(array($config->getValue( 'config.mailfrom' ), $config->getValue( 'config.fromname' )));
		
		$affiliateMail->setSubject(JText::sprintf("SEND_PASSWORD_SUBJECT", $config->getValue( 'config.sitename' )));
		
		$affiliateMail->setBody(JText::sprintf("NEW_PASSWORD_MESSAGE_BODY", $affiliate["username"], "\r\n\r\n", 
		
								$config->getValue( 'config.sitename' ), "\r\n\r\n", $newPassword . "\r\n\r\n"));
		
		$affiliateMail->send();
		
		// redirect to the login page
		
		$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=login"), false), JText::_("NEW_PASSWORD_SENT"), "message");
			
		return true;
		
	}
	
	/**
	 * Method to change an affiliate's password
	 */
	 
	function changePassword() {
		
		global $vmaSettings, $vmaHelper;
		
		$session 			= &JFactory::getSession();
		
		$affiliate			= $session->get("affiliate");
		
		// check for request forgeries
		
		if (!$session->isNew()) {
			
			JRequest::checkToken("request") or jexit( "Invalid Token" );
		
		}
		
		// get model
		
		$model				= $this->getModel("panel");
		
		// validate the form
		
		if (!$password		= $model->validatePasswordChange()) {
			
			return false;
			
		}
		
		// get database object
		
		$database			= &JFactory::getDBO();
		
		// update password
		
		$query 				= "UPDATE #__vm_affiliate SET `password` = '" . md5($password["new"]) . "' WHERE `affiliate_id` = '" . $affiliate->affiliate_id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// redirect to the login page
		
		$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel"), false), JText::_("PASSWORD_CHANGED"), "message");
			
		return true;
		
	}
	
	/**
	 * Method to update an affiliates' details
	 */
	 
	function details() {
		
		global $vmaSettings, $vmaHelper;
		
		// initiate variables
		
		$session 			= &JFactory::getSession();
		
		$affiliate			= $session->get("affiliate");
		
		$model				= $this->getModel("panel");
		
		$database			= &JFactory::getDBO();
		
		// check for request forgeries
		
		if (!$session->isNew()) {
			
			JRequest::checkToken("request") or jexit( "Invalid Token" );
		
		}
		
		// validate the affiliate's form
		
		if (!$newAffiliate 	= $model->validateDetails()) {
			
			return false;
			
		}

		// properly escape data
		
		foreach ($newAffiliate as $property => $value) {
			
			$newAffiliate[$property] = $database->getEscaped($newAffiliate[$property]);
			
		}
		
		// update the affiliate's details
		
		$query 				= "UPDATE #__vm_affiliate SET `fname` = '" 			. $newAffiliate["fname"] 	. "', `lname` = '" 	. $newAffiliate["lname"] 	. "', `mail` = '" 		. 
		
							  $newAffiliate["mail"] 	. "', `website` = '" 	. $newAffiliate["website"] 	. "', `street` = '" . $newAffiliate["street"] 	. "', `city` = '" 		. 
							  
							  $newAffiliate["city"] 	. "', `country` = '" 	. $newAffiliate["country"] 	. "', `state` = '" 	. $newAffiliate["state"] 	. "', `zipcode` = '" 	. 
							  
							  $newAffiliate["zipcode"] 	. "', `phoneno` = '" 	. $newAffiliate["phoneno"] 	. "', `taxssn` = '" . $newAffiliate["taxssn"] 	. "' WHERE " 			. 
							  
							  "`affiliate_id` = '" . $affiliate->affiliate_id . "'";
							  
		$database->setQuery( $query );
		
		$database->query();
		
		// update the offline tracking name values
		
		$vmaHelper->updateOfflineNameValue($affiliate->affiliate_id, "update");
		
		// update the affiliate's session object
		
		$this->refreshAffiliate();
		
		// redirect to the login page
		
		$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=details"), false), JText::_("DETAILS_UPDATED"), "message");
			
		return true;
		
	}
	
	/**
	 * Method to check whether there is a logged in user, which happens to be linked with an affiliate account, case in which log the affiliate in
	 */
	 
	function checkLinking() {
		
		// get database object
		
		$database			= &JFactory::getDBO();
		
		// get user object
		
		$user	 			= &JFactory::getUser();
		
		// if there is any user logged in, check if it's linked with an affiliate account

		if (!$user->get('guest')) {

			$query			= "SELECT * FROM #__vm_affiliate WHERE `linkedto` = '" . $user->get('username') . "' AND `blocked` = '0'";
			
			$database->setQuery( $query );
		
			$affiliate		= $database->loadObject();
			
			if ($affiliate && $affiliate->affiliate_id) {
				
				// start the affiliate session, and log the affiliate in

				$session 	= &JFactory::getSession();
				
				$session->set('affiliate', $affiliate);
				
				$this->display();
				
				return true;
				
			}
			
		}
		
	}
	
	/**
	 * Method to get and parse ad information for the preview view
	 */
	 
	function previewAd() {
		
		global $vmaHelper;
		
		// initiate variables
		
		$type 				= JRequest::getVar('type', 'banners');
		
		$database			= &JFactory::getDBO();
		
		$itemID 			= JRequest::getVar('id', '');
				
		empty($itemID) 		? JError::raiseError( 500, "No ID specified") : NULL;
				
		// build the info query based on preview type
		
		switch ($type) {
			
			case 'banners':
		
				$query 		= "SELECT * FROM #__vm_affiliate_banners WHERE `banner_id` = '" . $itemID . "'";	
				
				break;
			
			case 'textads':
		
				$query 		= "SELECT * FROM #__vm_affiliate_textads WHERE `textad_id` = '" . $itemID . "'";	
				
				break;
			
			case 'productads':
		
				$lc			= $vmaHelper->getLanguageTag();
				
				$query 		= "SELECT product.`virtuemart_product_id` AS product_id, details.`product_name`, " . 
							  
							  "medias.`file_url`, price.`product_price` AS product_price, " .
							  
							  "tax.`calc_value` AS product_tax, tax.`calc_value_mathop` AS product_tax_math, " .
							  
							  "tax.`publish_up` AS tax_start, tax.`publish_down` AS tax_end, " .  
							  
							  "discount.`calc_value` AS product_discount, discount.`calc_value_mathop` AS product_discount_math, discount.`calc_kind` AS discount_type, " .
							  
							  "discount.`publish_up` AS discount_start, discount.`publish_down` AS discount_end, " . 
							  
							  "price.`override` AS discount_override, price.`product_override_price` AS final_discount, " . 
							  
							  "MIN(price.`price_quantity_start`) FROM #__virtuemart_products product " . 
							  
							  "LEFT JOIN #__virtuemart_products_" . $lc . " details ON product.`virtuemart_product_id` = details.`virtuemart_product_id` " . 
							  
							  "LEFT JOIN #__virtuemart_product_medias pmedias ON product.`virtuemart_product_id` = pmedias.`virtuemart_product_id` AND pmedias.`ordering` = '1' " . 
							  
							  "LEFT JOIN #__virtuemart_medias medias ON pmedias.`virtuemart_media_id` = medias.`virtuemart_media_id` " . 
							  
							  "LEFT JOIN #__virtuemart_product_prices price ON product.`virtuemart_product_id` = price.`virtuemart_product_id` " . 
							  
							  "LEFT JOIN #__virtuemart_calcs tax ON price.`product_tax_id` = tax.`virtuemart_calc_id` " . 
							  
							  "LEFT JOIN #__virtuemart_calcs discount ON price.`product_discount_id` = discount.`virtuemart_calc_id` " . 
							  
							  "WHERE product.`virtuemart_product_id` = '" . $itemID . "' AND product.`published` = '1' " . 
							  
							  "AND medias.`file_is_downloadable` = '0' AND medias.`file_is_forSale` = '0' AND medias.`file_url` != '' AND " .
								  
							  "(price.`virtuemart_shoppergroup_id` = '5' OR price.`virtuemart_shoppergroup_id` = '0' OR ISNULL(price.`virtuemart_shoppergroup_id`)) ";
				
				break;
			
			case 'categoryads':
		
				$lc			= $vmaHelper->getLanguageTag();
				
				$query 		= "SELECT category.`virtuemart_category_id`, medias.`file_url`, details.`category_name` FROM #__virtuemart_categories category " .
								  
							  "LEFT JOIN #__virtuemart_categories_" . $lc . " details ON details.`virtuemart_category_id` = category.`virtuemart_category_id` " . 
							  
							  "LEFT JOIN #__virtuemart_category_medias cmedias ON category.`virtuemart_category_id` = cmedias.`virtuemart_category_id` " . 
							  
							  "LEFT JOIN #__virtuemart_medias medias ON cmedias.`virtuemart_media_id` = medias.`virtuemart_media_id` " . 
							  
							  "WHERE category.`virtuemart_category_id` = '" . $itemID . "' AND category.`published` = '1' ";
				
				break;
					
		}
		
		// get and parse the item info
		
		$database->setQuery( $query );
		
		$database->query();

		$item				= $database->loadObjectList();

		$model 				= $this->getModel("panel");
		
		list($item)			= $model->processRows($type, $item);
		
		!is_array($item) 	? JError::raiseError( 500, "The corresponding item does not exist") : NULL;
		
		// store it in the session, for the view to fetch it
		
		$session			= &JFactory::getSession();
		
		$session->set("item", $item);
		
	}
	
	/**
	 * Method to send out mass advertising e-mails containing the affiliate's link
	 */
	 
	function emails() {
		
		global $vmaSettings, $vmaHelper;
		
		// get affiliate object
		
		$session		= &JFactory::getSession();
		
		$affiliate		= $session->get("affiliate");
		
		// get required variables
		
		$recipients 	= &JRequest::getVar("recipients");
		
		$subject 		= &JRequest::getVar("subject");
		
		$html			= &JRequest::getVar("html");
		
		$html			= $html ? true : false;

		$message		= $html ? $_POST["messageHTML"] : JRequest::getVar("messageText");

		// check input
		
		if (!$recipients || !$subject || !$message) {
			
			$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=emails"), false), JText::_("FILL_IN_ALL_FIELDS"), "error");
			
			return false;
			
		}
		
		// parse the affiliate link
		
		$affiliateLink	= JRoute::_(JURI::base() . "index.php?" . $vmaSettings->link_feed . "=" . $affiliate->affiliate_id);
		
		$message		= str_replace("{afflink}", $affiliateLink, $message);
		
		// prepare the e-mail
		
		$affiliateMail	= &JFactory::getMailer();
		
		$affiliateMail->setSender(array($affiliate->mail, $affiliate->fname . " " . $affiliate->lname));
		
		$affiliateMail->setSubject($subject);
		
		$affiliateMail->setBody($message);
		
		$affiliateMail->IsHTML($html);
		
		// parse the recipients
		
		$recipientsBCC	= explode(",", $recipients);
		
		// filter recipients
		
		foreach ($recipientsBCC as $key => $recipient) {
			
			$recipient	= trim($recipient);
			
			if (empty($recipient)) {
				
				unset($recipientsBCC[$key]);
				
			}
		
		}
		
		// add the recipients
		
		$affiliateMail->addBCC($recipientsBCC);
		
		// send the e-mails
							
		if (!$affiliateMail->send()) {
			
			$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=emails"), false), JText::_("EMAIL_SENDING_FAILURE"), "error");
			
			return false;
				
		}
		
		else {
			
			$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=emails"), false), JText::sprintf("EMAIL_SENDING_SUCCESS", count($recipientsBCC)));
			
			return true;
			
		}
		
	}
	
	/**
	 * Method to save the preferred payment method, along with its details
	 */
	
	function paymentMethod() {
		
		global $vmaHelper;
		
		// get required variables
		
		$session			= &JFactory::getSession();
		
		$affiliate			= $session->get("affiliate");
		
		$database			= &JFactory::getDBO();
		
		// get chosen payment method and validate it
		
		$paymentMethod 		= JRequest::getVar('paymentmethod', '');
		
		// validate payment method
		
		if (!$paymentMethod) {
			
			$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=preferences"), false), JText::_("PROVIDE_PAYMENT_METHOD"), "error");
			
			return false;
			
		}
		
		// get existing payment details
		
		$paymentDetails		= array();
		
		$query				= "SELECT `field_id` FROM #__vm_affiliate_payment_details WHERE `affiliate_id` = '" . $affiliate->affiliate_id . "'";
		
		$database->setQuery($query);
		
		$setPaymentDetails	= $database->loadResultArray();
		
		// get new payment details, validate them, and prepare their query
		
		foreach ($_POST as $key => $value) {
			
			if (stristr($key, "payment-field")) {
				
				list( , , $paymentFieldID)			= explode("-", $key);
				
				$paymentValue 						= JRequest::getVar($key, '');
				
				// paypal method validation
				
				if ($paymentMethod == '1' && $paymentFieldID == '1' && !$paymentValue) {
					
					$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=preferences"), false), JText::_("PROVIDE_EMAIL_ADDRESS"), "error");
			
					return false;
			
				}
				
				// prepare the payment field query
				
				$paymentDetails[]					= !in_array($paymentFieldID, $setPaymentDetails) ? 
				
													  "INSERT INTO #__vm_affiliate_payment_details VALUES ('" . $affiliate->affiliate_id . "', '" . 
				
													  $paymentFieldID . "', '" . $paymentValue . "')" :
													  
													  "UPDATE #__vm_affiliate_payment_details SET `field_value` = '" . $paymentValue . "' WHERE `affiliate_id` = '" . 
													  
													  $affiliate->affiliate_id . "' AND `field_id` = '" . $paymentFieldID . "'";
				
			}
			
		}
		
		// update the affiliate's payment method and its details
		
		$query = "UPDATE #__vm_affiliate SET `method` = '" . $paymentMethod . "' WHERE `affiliate_id` = '" . $affiliate->affiliate_id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		foreach ($paymentDetails as $paymentField) {
			
			$database->setQuery($paymentField);
			
			$database->query();
			
		}
		
		// refresh affiliate
		
		$this->refreshAffiliate();
		
		// show confirmation message
		
		$this->setRedirect(JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=preferences"), false), JText::_("PAYMENT_INFORMATION_UPDATED"), "message");
			
		return true;
		
	}
	
	/**
	 * Method to link or unlink the affiliate to a site user
	 */
	 
	function linkTo() {
		
		global $vmaHelper;
		
		$session			= &JFactory::getSession();
		
		$database			= &JFactory::getDBO();
		
		$affiliate			= $session->get("affiliate");
		
		$redirectLink		= "index.php?option=com_affiliate&view=panel&subview=preferences";
		
		// determine operation (link or unlink)
		
		$operation			= isset($_POST["username"]) && isset($_POST["password"]) ? "link" : "unlink";
		
		// execute the operation
		
		switch ($operation) {
			
			// link the affiliate to the site user
			
			case 'link':
			
				// get input
				
				$username	= JRequest::getVar('username', '');
				
				$password	= JRequest::getVar('password', '');
				
				// validate it
				
				if (!$username) {
					
					$this->setRedirect(JRoute::_($vmaHelper->vmaRoute($redirectLink), false), JText::_("PROVIDE_USERNAME"), "error");
			
					return false;
					
				}
				
				if (!$password) {
					
					$this->setRedirect(JRoute::_($vmaHelper->vmaRoute($redirectLink), false), JText::_("PROVIDE_PASSWORD"), "error");
			
					return false;
					
				}
				
				// check if such an account exists in joomla
				
				$query				= "SELECT `password` FROM #__users WHERE `username` = '" . $username . "'";
				
				$database->setQuery($query);
				
				$saltedPassword 	= $database->loadResult();
				
				// no such account exists
				
				if (empty($saltedPassword)) {
					
					$this->setRedirect(JRoute::_($vmaHelper->vmaRoute($redirectLink), false), JText::_("E_LOGIN_AUTHENTICATE"), "error");
			
					return false;
					
				}
				
				// check password
				
				list( , $salt)	= explode(":", $saltedPassword);
				
				$password 		= md5($password . $salt) . ":" . $salt;
				
				// password is incorrect
				
				if ($password != $saltedPassword) {
					
					$this->setRedirect(JRoute::_($vmaHelper->vmaRoute($redirectLink), false), JText::_("INCORRECT_PASSWORD"), "error");
		
					return false;
					
				}
					
				// check if another affiliate is already linked to that user
				
				$query = "SELECT `affiliate_id` FROM #__vm_affiliate WHERE `linkedto` = '" . $username . "'";
				
				$database->setQuery($query);
				
				$alreadyLinked = $database->loadResult();
				
				// another affiliate is linked to that user
				
				if ($alreadyLinked) {
					
					$this->setRedirect(JRoute::_($vmaHelper->vmaRoute($redirectLink), false), JText::_("ALREADY_LINKED"), "error");
		
					return false;
					
				}
				
				// link affiliate to site user
				
				$query = "UPDATE #__vm_affiliate SET `linkedto` = '" . $username . "' WHERE `affiliate_id` = '" . $affiliate->affiliate_id . "'";
				
				$database->setQuery($query);
				
				$database->query();
				
				// refresh affiliate
				
				$this->refreshAffiliate();
				
				// show confirmation message
				
				$this->setRedirect(JRoute::_($vmaHelper->vmaRoute($redirectLink), false), JText::_("AFFILIATE_LINKED"), "message");
	
				return true;
				
				break;
				
			// unlink the affiliate from the site user
			
			case 'unlink':
				
				// reset linking
				
				$query = "UPDATE #__vm_affiliate SET `linkedto` = '' WHERE `affiliate_id` = '" . $affiliate->affiliate_id . "'";
				
				$database->setQuery($query);
				
				$database->query();
				
				// refresh affiliate
				
				$this->refreshAffiliate();
				
				// show confirmation message
				
				$this->setRedirect(JRoute::_($vmaHelper->vmaRoute($redirectLink), false), JText::_("UNLINKED"), "message");
			
				return true;
				
				break;
				
		}
		
		return true;

	}
	
	/**
	 * Method to refresh affiliate's session object
	 */
	 
	function refreshAffiliate() {
		
		// get required variables
		
		$session			= &JFactory::getSession();
		
		$database			= &JFactory::getDBO();
		
		$affiliate			= $session->get("affiliate");
		
		// get new affiliate object
		
		$query				= "SELECT * FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliate->affiliate_id . "'";
		
		$database->setQuery( $query );
		
		$database->query();
		
		$newAffiliate 		= $database->loadObject();
		
		// refresh it in the session
		
		$session->set("affiliate", $newAffiliate);
		
		return true;
		
	}
	
	/**
	 * Method to get the correct ads subview section
	 */
	 
	function getActiveAdsSection() {
		
		$session			= &JFactory::getSession();
		
		$panelModel			= $this->getModel("panel");
			
		$section			= JRequest::getVar('section', 'banners');
		
		$activeAdsMenus 	= $panelModel->getActiveAdsMenus();
		
		$session->set("activeAdsMenus", $activeAdsMenus);
		
		$nextPage			= false;
		
		foreach ($activeAdsMenus as $adMenu => $adMenuEnabled) {
			
			if ($adMenu == $section && !$adMenuEnabled || $nextPage && !$adMenuEnabled) {
				
				$nextPage 	= true;
				
			}
			
			if ($nextPage && $adMenuEnabled) {
				
				$adMenu		= $adMenu == "productads" && $adMenuEnabled == "productadscategories" ? $adMenuEnabled : $adMenu;
				
				JRequest::setVar('section', $adMenu);
				
				$nextPage 	= false;
				
			}
			
		}
			
	}
	
	/**
	 * Method to listen for a PayPal IPN response, and take the appropriate measures with respect to an affiliate payment
	 */

	function payAffiliate() {

		// initiate required variables
		
		$header 		= NULL;
		
		$request		= "cmd=_notify-validate";
		
		$database		= JFactory::getDBO();
		
		// handle escape characters
		
		if (function_exists('get_magic_quotes_gpc')) { 
		
			$get_magic_quotes_exits = true;
			
		}
		
		foreach ($_POST as $key => $value) {

			if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
				
				$value	= urlencode(stripslashes($value));
				
			} else {
			
				$value	= urlencode($value);
				
			}  
			
			$request	.= "&" . $key . "=" . $value;
			
		}
		
		// post back to PayPal to validate
		
		$header			.= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		
		$header			.= "Content-Type: application/x-www-form-urlencoded\r\n";
		
		$header			.= "Content-Length: " . strlen($request) . "\r\n\r\n";
		
		$fp				= fsockopen('www.paypal.com', 80, $errno, $errstr, 30);
		 
		// process validation from PayPal 

		if ($fp) {

			fputs($fp, $header . $request);
			
			while(!feof($fp)) {
				
				$res	= fgets($fp, 1024); 
				
				// if the transaction is successfully verified, insert/update the payment
				
				if (strcmp($res, "VERIFIED") == 0) {
					
					// post-process required variables
					
					$_POST["txn_id"] 	= isset($_POST["parent_txn_id"]) && $_POST["parent_txn_id"] != $_POST["txn_id"] ? $_POST["parent_txn_id"] : $_POST["txn_id"];
					
					$confirmedStatuses	= array("Completed", 	"Canceled_Reversal");
					
					$pendingStatuses	= array("Created", 		"Pending", 				"Processed");
					
					$cancelledStatuses	= array("Denied", 		"Expired", 				"Failed", 		"Refunded", 	"Reversed", 	"Voided");
					
					$status				= in_array($_POST["payment_status"], 			$confirmedStatuses) 			? "C" 			:
					
										  (in_array($_POST["payment_status"], 			$pendingStatuses) 				? "P" 			: "X");
					
					$payment			= NULL;
					  
					// check if this transaction already exists
					
					$query 				= "SELECT * FROM #__vm_affiliate_payments WHERE `affiliate_id` = '" . $_POST["custom"] 			. "' " . 
					
										  "AND `transaction` = '" 											. $_POST["txn_id"] 			. "'";
					
					$database->setQuery($query);
					
					$payment			= $database->loadObject();
					
					// if transaction exists, update the payment

					if ($payment && is_object($payment) && isset($payment->payment_id) && $payment->payment_id) {
						
						$this->updateTransaction($_POST["custom"], $_POST["txn_id"], $status);
						
					}
					
					// if the transaction does not exist, insert the payment into the database
					
					else {
						
						// get affiliate username
						
						$query			= "SELECT `username` FROM #__vm_affiliate WHERE `affiliate_id` = '" . $_POST["custom"] . "'";
						
						$database->setQuery($query);
						
						$username		= $database->loadResult();
						
						// insert the payment into the database
						
						$query			= "INSERT INTO #__vm_affiliate_payments VALUES ('', '" . $_POST["custom"] . "', '" . $username . "', 'PayPal', '" . 
						
										  $_POST["mc_gross"] . "', '" . date("Y-m-d") . "', '" . $status . "', '" . $_POST["txn_id"] . "')";
										  
						$database->setQuery($query);
						
						$database->query();

						// update the affiliate's balance, if required
						
						if ($status == "C" || $status == "P") {
							
							$query		= "UPDATE #__vm_affiliate SET `commissions` = `commissions` - " . $_POST["mc_gross"] . " WHERE `affiliate_id` = '" . $_POST["custom"] . "'";
			
							$database->setQuery($query);
							
							$database->query();
							
						}
						
						$this->updateTransaction($_POST["custom"], $_POST["txn_id"], $status);
						
					}
					
				}
			  
			}
			  
		}

		fclose($fp);
		
	}
	
	/**
	 * Method to update a certain transaction
	 */

	function updateTransaction($affiliateID, $transactionID, $status) {

		global $vmaHelper;
		
		// initiate required variables
		
		$database 	= JFactory::getDBO();
		
		$payment	= NULL;
		
		// get payment details
		
		$query 		= "SELECT * FROM #__vm_affiliate_payments WHERE `affiliate_id` = '" . $affiliateID . "' AND `transaction` = '" . $transactionID . "'";
		
		$database->setQuery($query);

		$payment	= $database->loadObject();

		// validate transaction
		
		if (!$payment->payment_id) {
			
			return false;
			
		}
		
		// get previous payment date (if exists)
		
		$query		= "SELECT `date` FROM #__vm_affiliate_payments WHERE `affiliate_id` = '" . $affiliateID . "' AND `payment_id` < '" . $payment->payment_id . "' ORDER BY `payment_id` DESC";
		
		$database->setQuery($query);
		
		$prevDate	= $database->loadResult();

		// determine operation
		
		$operation	= (($status == "C" || $status == "P") && $payment->status == "X") ? "-" :
		
					  (($status == "X" && ($payment->status == "C" || $payment->status == "P")) ? "+" : NULL);
		
		// update the transaction
		
		$query		= "UPDATE #__vm_affiliate_payments SET `status` = '" . $status . "' WHERE `affiliate_id` = '" . $affiliateID . "' AND `transaction` = '" . $transactionID . "'";
		
		$database->setQuery($query);
		
		$database->query();
			
		// update clicks' paid status
		
		$query		= "UPDATE #__vm_affiliate_clicks SET `paid` = '" 	. ($status == "C" ? "1" : "0") 		. "' WHERE `AffiliateID` = '" 	. $affiliateID . 
		
					  ($prevDate && $status != "C" ? " AND `date` >= '" . $prevDate . "'" : NULL) 			. "' AND `date` <= '" 			. $payment->date . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// update orders' paid status
		
		$query		= "UPDATE #__vm_affiliate_orders SET `paid` = '" 		. ($status == "C" ? "1" : "0") 		. "' WHERE " . 
		
					  $vmaHelper->buildStatusesCondition("negative", "AND", "!=", "order_status")  				. 
					  
					  " AND (`affiliate_id` = '" . $affiliateID 			. "'" . ($prevDate && $status != "C" ? " AND `date` >= '" . $prevDate . "'" : NULL) 	. 
					  
					  " AND `date` <= '" 									. $payment->date 	. "')";
		
		$database->setQuery($query);
		
		$database->query();
		
		// update balance (if required)
		
		if ($operation) {
			
			$query	= "UPDATE #__vm_affiliate SET `commissions` = `commissions` " . $operation . " " . $payment->amount . " WHERE `affiliate_id` = '" . $affiliateID . "'";
			
			$database->setQuery($query);
			
			$database->query();
		
		}
		
	}
	
	/**
	 * Method to process an uploaded banner
	 */

	function processUploadedBanner() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$database				= JFactory::getDBO();
		
		$destination 			= JPATH_ROOT . DS . "components" . DS . "com_affiliate" . DS . "banners" . DS;
		
		$uploadedName			= basename($_FILES['userfile']['name']);
		
		$fileNameParts			= explode(".", $uploadedName);
		
		$extension 				= strtolower($fileNameParts[count($fileNameParts) - 1]);
		
		$md5Hash 				= md5($uploadedName . time());
		
		$uploadedFile 			= $destination . "temp_" . $md5Hash . "." . $extension;
		
		$temporaryName			= $_FILES['userfile']['tmp_name'];
		
		$allowedExts			= array("gif", "png", "jpg", "jpeg", "swf");
		
		$type					= NULL;
		
		$animated				= false;
		
		// make sure this is a secure request

		if (!$this->checkVMAToken()) {

			return false;
			
		}
		
		// validate extension and move file
		
		if (in_array($extension, $allowedExts)) {
			
			if (@move_uploaded_file($temporaryName, $uploadedFile)) {
				
				echo "success";
			
			}
			
			else {
				
				echo JText::_("BANNER_MOVING_FAILURE");
				
				return false;
				
			}
			
		}
		
		else {
			
			return false;
			
		}
		
		// get image parameters
				
		list($width, $height, $imageType) = getimagesize($uploadedFile);
		
		// get image type
		
		switch ($imageType) {
			
			case IMAGETYPE_GIF:
			
				$type			= "gif";
				
				$animated		= preg_match('#(\x00\x21\xF9\x04.{4}\x00\x2C.*){2,}#s', file_get_contents($uploadedFile)) ? true : false;
				
				break;
				
			case IMAGETYPE_JPEG:
			
				$type			= "jpg";
				
				break;
				
			case IMAGETYPE_PNG:
			
				$type			= "png";
				
				break;
				
		}
		
		$type					= $type ? $type : $extension;
		
		// create bigger thumbnail, for the banner form
		
		if ($type != "swf") {
			
			$vmaHelper->resizeImage($uploadedFile, $md5Hash, "thumbbig");
			
		}
		
		// get the corresponding size group, if existent
			
		$query 					= "SELECT `name` FROM #__vm_affiliate_size_groups WHERE `width` = '" . $width . "' AND `height` = '" . $height . "'";
		
		$database->setQuery($query);
		
		$sizeGroupName 			= $database->loadResult();
			
		// send back the md5 hash
		
		echo "|" . $md5Hash;
		
		// send back the temporary file location
		
		echo "|" . $extension;
		
		// send back the exact image type
		
		echo "|" . $type;
		
		// send back the animated status for gifs
		
		echo "|" . $animated;
		
		// send back the image width
		
		echo "|" . $width;
		
		// send back the image height
		
		echo "|" . $height;
		
		// send back the size group name
		
		echo "|" . $sizeGroupName;
		
		// prevent gzip issues
		
		ob_end_flush();
		
	}
	
	/**
	 * Method to export size groups for fetching by AJAX
	 */
	 
	function exportSizeGroups() {
		
		// initiate required variables
		
		$database	= &JFactory::getDBO();
		
		$request	= JRequest::getVar('type', 'banners');
		
		// make sure this is a secure request
		
		if (!$this->checkVMAToken()) {
			
			return false;
			
		}
		
		// get size group object
		
		$query		= "SELECT * FROM #__vm_affiliate_size_groups ";
		
		$query	   .= $request == "banners" ? "WHERE `width` != '0' AND `height` != '0' " : NULL;
		
		$query	   .= "ORDER BY `width` ASC";
		
		$database->setQuery($query);
		
		$sizeGroups	= $database->loadObjectList();
		
		// verify there is at least one size group

		if (count($sizeGroups) < 1) {
			
			return false;
			
		}

		// export the size groups into a javascript parsable format
		
		foreach ($sizeGroups as $sizeGroup) {
			
			echo $sizeGroup->size_group_id . "-" . $sizeGroup->width . "-" . $sizeGroup->height . "-" . $sizeGroup->name . "|";
			
		}
		
		// prevent gzip issues
		
		ob_end_flush();
		
	}
	
}