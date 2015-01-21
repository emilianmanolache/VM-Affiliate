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

// import the installer

jimport('joomla.installer.installer');

jimport('joomla.filesystem.folder');

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

$query		= "DELETE FROM #__virtuemart_modules WHERE `module_name` = 'vma'";

$database->setQuery($query);

$database->query();

// remove the vma functions

$query		= "DELETE FROM #__virtuemart_adminmenuentries WHERE `module_id` = '255'";

$database->setQuery($query);

$database->query();

// get offline tracking name field id

$query		= "SELECT `fieldid` FROM #__virtuemart_userfields WHERE `name` = 'vm_partnersname'";

$database->setQuery($query);

$pnFieldID	= $database->loadResult();

// remove offline tracking fields

$query		= "DELETE FROM #__virtuemart_userfields WHERE `name` = 'vm_partnersname' OR `name` = 'vm_partnersusername'";

$database->setQuery($query);

$database->query();

// remove offline tracking names

$query		= "DELETE FROM #__virtuemart_userfield_values WHERE `fieldid` = '" . $pnFieldID . "'";

$database->setQuery($query);

$database->query();

// remove offline tracking user info fields

$userInfo	= $database->getTableFields("#__virtuemart_userinfos");
	
if (in_array("vm_partnersusername",	$userInfo)) {
	
	$query				= "ALTER TABLE `#__virtuemart_userinfos` DROP `vm_partnersusername`";
	
	$database->setQuery($query);
	
	$database->query();
	
}

if (in_array("vm_partnersname", 	$userInfo)) {
	
	$query				= "ALTER TABLE `#__virtuemart_userinfos` DROP `vm_partnersname`";
	
	$database->setQuery($query);
	
	$database->query();
	
}

$userInfo	= $database->getTableFields("#__virtuemart_order_userinfos");
	
if (in_array("vm_partnersusername",	$userInfo)) {
	
	$query				= "ALTER TABLE `#__virtuemart_order_userinfos` DROP `vm_partnersusername`";
	
	$database->setQuery($query);
	
	$database->query();
	
}

if (in_array("vm_partnersname", 	$userInfo)) {
	
	$query				= "ALTER TABLE `#__virtuemart_order_userinfos` DROP `vm_partnersname`";
	
	$database->setQuery($query);
	
	$database->query();
	
}

// remove all vm affiliate related files

$path		= JPATH_ROOT . DS . "administrator" . DS . "components" . DS . "com_virtuemart";

@unlink($path . DS . "vmaInstall.xml");

@unlink($path . DS . "controllers" 	. DS . "vma.php");

@unlink($path . DS . "controllers" 	. DS . "vma_about.php");

@unlink($path . DS . "controllers" 	. DS . "vma_affiliates.php");

@unlink($path . DS . "controllers" 	. DS . "vma_banners.php");

@unlink($path . DS . "controllers" 	. DS . "vma_category_ads.php");

@unlink($path . DS . "controllers" 	. DS . "vma_commission_rates.php");

@unlink($path . DS . "controllers" 	. DS . "vma_configuration.php");

@unlink($path . DS . "controllers" 	. DS . "vma_email_affiliates.php");

@unlink($path . DS . "controllers" 	. DS . "vma_pay_affiliates.php");

@unlink($path . DS . "controllers" 	. DS . "vma_payment_methods.php");

@unlink($path . DS . "controllers" 	. DS . "vma_payments.php");

@unlink($path . DS . "controllers" 	. DS . "vma_product_ads.php");

@unlink($path . DS . "controllers" 	. DS . "vma_sales.php");

@unlink($path . DS . "controllers" 	. DS . "vma_sizegroups.php");

@unlink($path . DS . "controllers" 	. DS . "vma_statistics.php");

@unlink($path . DS . "controllers" 	. DS . "vma_textads.php");

@unlink($path . DS . "controllers" 	. DS . "vma_traffic.php");

@unlink($path . DS . "models" 		. DS . "vma_about.php");

@unlink($path . DS . "models" 		. DS . "vma_affiliates.php");

@unlink($path . DS . "models" 		. DS . "vma_banners.php");

@unlink($path . DS . "models" 		. DS . "vma_category_ads.php");

@unlink($path . DS . "models" 		. DS . "vma_commission_rates.php");

@unlink($path . DS . "models" 		. DS . "vma_configuration.php");

@unlink($path . DS . "models" 		. DS . "vma_email_affiliates.php");

@unlink($path . DS . "models" 		. DS . "vma_pay_affiliates.php");

@unlink($path . DS . "models" 		. DS . "vma_payment_methods.php");

@unlink($path . DS . "models" 		. DS . "vma_payments.php");

@unlink($path . DS . "models" 		. DS . "vma_product_ads.php");

@unlink($path . DS . "models" 		. DS . "vma_sales.php");

@unlink($path . DS . "models" 		. DS . "vma_sizegroups.php");

@unlink($path . DS . "models" 		. DS . "vma_statistics.php");

@unlink($path . DS . "models" 		. DS . "vma_textads.php");

@unlink($path . DS . "models" 		. DS . "vma_traffic.php");

@unlink($path . DS . "helpers" 		. DS . "vma.php");

@JFolder::delete($path . DS . "views" 	. DS . "vma");

@JFolder::delete($path . DS . "views" 	. DS . "vma_about");

@JFolder::delete($path . DS . "views" 	. DS . "vma_affiliates");

@JFolder::delete($path . DS . "views" 	. DS . "vma_banners");

@JFolder::delete($path . DS . "views" 	. DS . "vma_category_ads");

@JFolder::delete($path . DS . "views" 	. DS . "vma_commission_rates");

@JFolder::delete($path . DS . "views" 	. DS . "vma_configuration");

@JFolder::delete($path . DS . "views" 	. DS . "vma_email_affiliates");

@JFolder::delete($path . DS . "views" 	. DS . "vma_pay_affiliates");

@JFolder::delete($path . DS . "views" 	. DS . "vma_payment_methods");

@JFolder::delete($path . DS . "views" 	. DS . "vma_payments");

@JFolder::delete($path . DS . "views" 	. DS . "vma_product_ads");

@JFolder::delete($path . DS . "views" 	. DS . "vma_sales");

@JFolder::delete($path . DS . "views" 	. DS . "vma_sizegroups");

@JFolder::delete($path . DS . "views" 	. DS . "vma_statistics");

@JFolder::delete($path . DS . "views" 	. DS . "vma_textads");

@JFolder::delete($path . DS . "views" 	. DS . "vma_traffic");

?>