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

// import the component controller library

if (!class_exists('VmController')) require(VMPATH_ADMIN . DS . 'helpers' . DS . 'vmcontroller.php');

/**
 * VM Affiliate backend controller
 */

class VirtuemartControllerVma_configuration extends VmController {

    /**
     * Method to display the view
     */

    public function __construct() {

        parent::__construct();

    }
	 
	/**
	 * Method to save the configuration values
	 */

	function update() {
		
		global $vmaHelper;
		
		$database			= JFactory::getDBO();
		
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
		
		$affTerms			= $database->getEscaped(JRequest::getString("affiliateProgramTerms",	"", "POST", JREQUEST_ALLOWHTML));
		
		// validate input
		
		if (!$linkFeed) {
			
			vmError(JText::_("PROVIDE_LINK_FEED_STRING"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$cookieTime) {
			
			vmError(JText::_("PROVIDE_COOKIE_LIFETIME"));
			
			$this->display();
			
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
							  
		$database->setQuery($query);
		
		$database->query();
		
		// update the offline tracking custom fields
		
		$query				= "UPDATE #__virtuemart_userfields SET `published` = '0' WHERE `name` = 'vm_partnersusername' OR `name` = 'vm_partnersname'";
		
		$database->setQuery($query);
		
		$database->query();
		
		if ($offlineTracking && $offlineType != 3) {
			
			$query				= "UPDATE #__virtuemart_userfields SET `published` = '1' WHERE `name` = '" . ($offlineType == 1 ? "vm_partnersusername" : "vm_partnersname") . "'";
			
			$database->setQuery($query);
		
			$database->query();
			
		}
		
		// update the global settings object
		
		$query				= "SELECT * FROM #__vm_affiliate_settings WHERE `setting` = '1'";
		
		$database->setQuery($query);
		
		$GLOBALS["vmaSettings"] = $database->loadObject();
		
		// show the confirmation message
		
		vmInfo(JText::_("CONFIGURATION_SAVED"), false);
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
}