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

// import the installer

jimport('joomla.installer.installer');

$installer	= &JInstaller::getInstance();

// get database object

$database	= &JFactory::getDBO();

// uninstall the plugin

$query		= "SELECT `id` FROM #__plugins WHERE `element` = 'vma'";

$database->setQuery($query);

$pluginID	= $database->loadResult();

if ($pluginID) {
	
	$installer->uninstall("plugin", $pluginID);
	
}

// remove all vm affiliate databases

$query		= "DROP TABLE `#__vm_affiliate`, 		`#__vm_affiliate_banners`, 			`#__vm_affiliate_banners_hits`, `#__vm_affiliate_clicks`, 

						  `#__vm_affiliate_links`, 	`#__vm_affiliate_links_categories`, `#__vm_affiliate_methods`, 		`#__vm_affiliate_method_fields`, 
						  
						  `#__vm_affiliate_orders`, `#__vm_affiliate_orders_pretrack`,	`#__vm_affiliate_payments`, 	`#__vm_affiliate_payment_details`, 
						  
						  `#__vm_affiliate_rates`,	`#__vm_affiliate_settings`, 		`#__vm_affiliate_size_groups`, 	`#__vm_affiliate_textads`, 
						  
						  `#__vm_affiliate_textads_hits`";

$database->setQuery($query);

$database->query();

// remove vma menus

$query		= "DELETE FROM #__menu WHERE `link` LIKE '%option=com_affiliate%'";

$database->setQuery($query);

$database->query();

// remove the vma module

$query		= "DELETE FROM #__vm_module WHERE `module_name` = 'vma'";

$database->setQuery($query);

$database->query();

// remove the vma functions

$query		= "DELETE FROM #__vm_function WHERE `function_class` = 'ps_vma'";

$database->setQuery($query);

$database->query();

// get offline tracking name field id

$query		= "SELECT `fieldid` FROM #__vm_userfield WHERE `name` = 'vm_partnersname'";

$database->setQuery($query);

$pnFieldID	= $database->loadResult();

// remove offline tracking fields

$query		= "DELETE FROM #__vm_userfield WHERE `name` = 'vm_partnersname' OR `name` = 'vm_partnersusername'";

$database->setQuery($query);

$database->query();

// remove offline tracking names

$query		= "DELETE FROM #__vm_userfield_values WHERE `fieldid` = '" . $pnFieldID . "'";

$database->setQuery($query);

$database->query();

// remove offline tracking user info fields

$userInfo	= $database->getTableFields("#__vm_user_info");
	
if (in_array("vm_partnersusername",	$userInfo)) {
	
	$query				= "ALTER TABLE `#__vm_user_info` DROP `vm_partnersusername`";
	
	$database->setQuery($query);
	
	$database->query();
	
}

if (in_array("vm_partnersname", 	$userInfo)) {
	
	$query				= "ALTER TABLE `#__vm_user_info` DROP `vm_partnersname`";
	
	$database->setQuery($query);
	
	$database->query();
	
}

$userInfo	= $database->getTableFields("#__vm_order_user_info");
	
if (in_array("vm_partnersusername",	$userInfo)) {
	
	$query				= "ALTER TABLE `#__vm_order_user_info` DROP `vm_partnersusername`";
	
	$database->setQuery($query);
	
	$database->query();
	
}

if (in_array("vm_partnersname", 	$userInfo)) {
	
	$query				= "ALTER TABLE `#__vm_order_user_info` DROP `vm_partnersname`";
	
	$database->setQuery($query);
	
	$database->query();
	
}

// remove all vm affiliate related files

$path		= JPATH_ROOT . DS . "administrator" . DS . "components" . DS . "com_virtuemart";

@unlink($path . DS . "classes" 	. DS . "ps_vma.php");

@unlink($path . DS . "html" 	. DS . "vmaInstall.xml");

@unlink($path . DS . "html" 	. DS . "vma.affiliate_password_form.php");

@unlink($path . DS . "html" 	. DS . "vma.affiliate_password_list.php");

@unlink($path . DS . "html" 	. DS . "vma.affiliate_preferences_form.php");

@unlink($path . DS . "html" 	. DS . "vma.affiliate_preferences_list.php");

@unlink($path . DS . "html" 	. DS . "vma.banners_form.php");

@unlink($path . DS . "html" 	. DS . "vma.banners_list.php");

@unlink($path . DS . "html" 	. DS . "vma.category_ads.php");

@unlink($path . DS . "html" 	. DS . "vma.commission_rates_form.php");

@unlink($path . DS . "html" 	. DS . "vma.commission_rates_list.php");

@unlink($path . DS . "html" 	. DS . "vma.configuration_form.php");

@unlink($path . DS . "html" 	. DS . "vma.configuration_list.php");

@unlink($path . DS . "html" 	. DS . "vma.email_affiliates.php");

@unlink($path . DS . "html" 	. DS . "vma.pay_affiliate.php");

@unlink($path . DS . "html" 	. DS . "vma.pay_affiliates.php");

@unlink($path . DS . "html" 	. DS . "vma.payment_methods_form.php");

@unlink($path . DS . "html" 	. DS . "vma.payment_methods_list.php");

@unlink($path . DS . "html" 	. DS . "vma.payments.php");

@unlink($path . DS . "html" 	. DS . "vma.product_ads.php");

@unlink($path . DS . "html" 	. DS . "vma.sales.php");

@unlink($path . DS . "html" 	. DS . "vma.size_groups_form.php");

@unlink($path . DS . "html" 	. DS . "vma.size_groups_list.php");

@unlink($path . DS . "html" 	. DS . "vma.statistics.php");

@unlink($path . DS . "html" 	. DS . "vma.summary.php");

@unlink($path . DS . "html" 	. DS . "vma.textads_form.php");

@unlink($path . DS . "html" 	. DS . "vma.textads_list.php");

@unlink($path . DS . "html" 	. DS . "vma.traffic.php");

@unlink($path . DS . "html" 	. DS . "vma.about.php");

@unlink($path . DS . "html" 	. DS . "vma.affiliate_details.php");

@unlink($path . DS . "html" 	. DS . "vma.affiliate_form.php");

@unlink($path . DS . "html" 	. DS . "vma.affiliate_list.php");

?>