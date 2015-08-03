<?php

/**
 * @package   VM Affiliate
 * @version   4.5.2.7 January 2012
 * @author    Globacide Solutions http://www.globacide.com
 * @copyright Copyright (C) 2006 - 2012 Globacide Solutions
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access

defined( '_JEXEC' ) or die( 'Direct access to this location is not allowed.' );

/**
 * VM Affiliate custom installer class
 */
 
class com_affiliateInstallerScript {
	
	/**
	 * VM Affiliate pre-installer function
	 */
	 
	public function preflight() {
		
		// import the installer
		
		jimport('joomla.installer.installer');
		
		$installer	= &JInstaller::getInstance();
		
		// get database object
		
		$database	= &JFactory::getDBO();
		
		// verify that virtuemart is installed
		
		$query		= "SELECT `extension_id` FROM #__extensions WHERE `element` = 'com_virtuemart' AND `enabled` = '1'";
		
		$database->setQuery($query);
		
		$virtueMart	= $database->loadResult();
			
		if (!$virtueMart) {
			
			$installer->set('message', NULL);
			
			@$installer->abort("VirtueMart is not installed! Please install VirtueMart first, then install VM Affiliate.");
			
			return false;
			
		}
		
		// remove previous module, if installed
		
		$query		= "SELECT `extension_id` FROM #__extensions WHERE `element` = 'mod_vmaffiliate'";
		
		$database->setQuery($query);
		
		$moduleID	= $database->loadResult();
		
		if ($moduleID) {
			
			$moduleInstaller = new JInstaller();
			
			$moduleInstaller->uninstall("module", $moduleID);
			
		}
		
		// remove any previous plugin, if installed
		
		$query		= "SELECT `extension_id` FROM #__extensions WHERE `element` = 'vma'";
		
		$database->setQuery($query);
		
		$pluginID	= $database->loadResult();
		
		if ($pluginID) {
			
			$pluginInstaller = new JInstaller();
			
			$pluginInstaller->uninstall("plugin", $pluginID);
			
		}
		
	}
	
	/**
	 * VM Affiliate post-installer function
	 */
	
	public function postflight() {
		
		// get application
		
		$mainframe	= &JFactory::getApplication();
		
		$uri 		= &JFactory::getURI();
		
		// import the installer
		
		jimport('joomla.installer.installer');
		
		$installer	= &JInstaller::getInstance();
		
		// get database object
		
		$database	= &JFactory::getDBO();
		
		// load language
				
		$language	= &JFactory::getLanguage();
		
		$language->load("com_affiliate", JPATH_ROOT);
		
		// remove backend link
		
		$this->deleteMenuItem("`title` = 'com_affiliate' AND `client_id` = '1'");
		
		// make this a frontend-only component
		
		$query		= "UPDATE #__extensions SET `client_id` = '0' WHERE `element` = 'com_affiliate'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// determine plugin path
		
		$sourcePath	= $installer->getPath("source");
		
		$pluginPath	= $sourcePath . DIRECTORY_SEPARATOR . "plugin";
		
		// install and publish the plugin
			
		if ($data	= JInstallerHelper::unpack($pluginPath . DIRECTORY_SEPARATOR . "plg_vma.zip")) {
			
			// install the plugin
			
			$pluginInstaller	= new JInstaller();
			
			$pluginInstalled	= $pluginInstaller->install($data["extractdir"]);
			
			// publish the plugin
		
			$query				= "UPDATE #__extensions SET `enabled` = '1' WHERE `element` = 'vma'";
			
			$database->setQuery($query);
			
			$database->query();
			
		}
		
		else {
			
			$pluginInstalled	= false;
			
		}
		
		// make sure the plugin was installed
		
		if (!$pluginInstalled) {
			
			$installer->set('message', NULL);
			
			@$installer->abort("The VM Affiliate Tracking Plugin could not be installed. Please contact Globacide Solutions @ http://www.globacide.com for support.");
			
			return false;
			
		}
		
		// copy the other required files, into virtuemart
		
		$vmFiles			= array();
		
		$vmSourcePath		= $sourcePath 						. DIRECTORY_SEPARATOR . "backend";
		
		$vmDestinationPath	= JPATH_ROOT						. DIRECTORY_SEPARATOR . "administrator"	. DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_virtuemart";
							  
		$vmFiles			= array(0	=>	
		
									array("src"		=> $vmSourcePath		,
									
										  "dest"	=> $vmDestinationPath	,
										  
										  "type"	=> "folder"));
		
		$copiedVMFiles		= $installer->copyFiles($vmFiles, true);
		
		// make sure the virtuemart files were successfully copied
		
		if ($copiedVMFiles != 1) {
			
			$installer->set('message', NULL);
			
			@$installer->abort("Unable to copy backend files. Please make sure VirtueMart's administration directory is writable.");
			
			return false;
			
		}
		
		// version management
		
		$componentXML 			= JApplicationHelper::parseXMLInstallFile($installer->getPath('manifest'));
		
		$versionXML				= $vmDestinationPath . DIRECTORY_SEPARATOR . "vmaInstall.xml";
		
		if (file_exists($versionXML)) {
			
			$oldComponentXML	= JApplicationHelper::parseXMLInstallFile($versionXML);
			
		}
		
		$installer->copyFiles(array(0 => array("src" => $installer->getPath('manifest'), "dest" => $versionXML, "type" => "file")));
		
		$upgrade				= isset($oldComponentXML) && $componentXML["version"] != $oldComponentXML["version"] ? true : false;
		
		$reinstall				= isset($oldComponentXML) && ($componentXML["version"] == $oldComponentXML["version"]) ? true : false;
		
		$previousVersion		= $upgrade ? $oldComponentXML["version"] : NULL;
		
		// check if no existing records exist

        $query                  = "SHOW TABLES LIKE '#__vm_affiliate'";

        $database->setQuery($query);

        $database->execute($query);

        $tableExists            = $database->getNumRows() > 0;

        $existingRecords        = false;

        if ($tableExists) {

            $query = "SELECT `affiliate_id` FROM #__vm_affiliate";

            $database->setQuery($query);

            $existingRecords = $database->loadResult();

        }

		$upgradeFromVM20		= !$upgrade && !$reinstall && $existingRecords ? true : false;
		
		// install or update the database
			
		$installDatabase		= $upgrade ? $this->updateDatabase($previousVersion) : (!$reinstall && !$upgradeFromVM20 ? $this->installDatabase() : NULL);

		// get component id
		
		$query					= "SELECT `extension_id` FROM #__extensions WHERE `element` = 'com_affiliate'";
		
		$database->setQuery($query);
		
		$componentID			= $database->loadResult();
		
		// detect any affiliate program links, and migrate them
		
		$query					= "UPDATE #__menu SET `link` = 'index.php?option=com_affiliate&view=login', `type` = 'component', `published` = '1', `component_id` = '" . 
		
								  $componentID . "' WHERE `link` LIKE '%option=com_affiliate%' AND `client_id` = 0";
								  
		$database->setQuery($query);
		
		$database->query();
		
		$updatedMenus			= $database->getAffectedRows();
		
		// if no affiliate program link found, create one
		
		if (!$updatedMenus) {
			
			$this->insertMenuItem("INSERT INTO #__menu VALUES ('', 'mainmenu', 'Affiliates', 'affiliates', '', 'affiliates', 'index.php?option=com_affiliate&view=login', 'component', 1, 1, 1, '" . $componentID . "', 0, '0000-00-00 00:00:00', 0, 1, '', 0, '', __LFT__, __RGT__, 0, '*', 0)");
			
		}
		
		// remove any previous vma module, and create a new one
		
		$query					= "DELETE FROM #__virtuemart_modules WHERE `module_name` = 'affiliate' OR `module_name` = 'vma'";
		
		$database->setQuery($query);
		
		$database->query();
		
		$query					= "INSERT INTO #__virtuemart_modules VALUES (255, 'vma', 'VM Affiliate module. Manages the Affiliate Program''s details, configuration, affiliates, etc.', 'admin,storeadmin', '1', '1', 7)";
		
		$database->setQuery($query);
		
		$database->query();
		
		// remove any previous vma menu items, and create the new ones
		
		$query					= "DELETE FROM #__virtuemart_adminmenuentries WHERE `module_id` = '255'";
		
		$database->setQuery($query);
		
		$database->query();
		
		$query					= "ALTER TABLE `#__virtuemart_adminmenuentries` CHANGE COLUMN `id` `id` INT(255) NOT NULL AUTO_INCREMENT;
		
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'SUMMARY', '', '', ' vmicon vmicon-16-info', 0, 1, NULL, 'vma', NULL);
		
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'MANAGE_AFFILIATES', '', '', 'vmicon vmicon-16-user', 1, 1, NULL, 'vma_affiliates', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'EMAIL_AFFILIATES', '', '', 'vmicon vmicon-16-content', 2, 1, NULL, 'vma_email_affiliates', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'PAY_AFFILIATES', '', '', 'vmicon vmicon-16-content', 3, 1, NULL, 'vma_pay_affiliates', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'COMMISSION_RATES', '', '', 'vmicon vmicon-16-install', 4, 1, NULL, 'vma_commission_rates', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'CONFIGURATION', '', '', 'vmicon vmicon-16-config', 5, 1, NULL, 'vma_configuration', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'PAYMENT_METHODS', '', '', 'vmicon vmicon-16-content', 6, 1, NULL, 'vma_payment_methods', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'BANNERS', '', '', 'vmicon vmicon-16-media', 7, 1, NULL, 'vma_banners', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'TEXT_ADS', '', '', 'vmicon vmicon-16-article', 8, 1, NULL, 'vma_textads', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'PRODUCT_ADS', '', '', 'vmicon vmicon-16-content', 9, 1, NULL, 'vma_product_ads', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'CATEGORY_ADS', '', '', 'vmicon vmicon-16-content', 10, 1, NULL, 'vma_category_ads', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'STATISTICS', '', '', 'vmicon vmicon-16-info', 11, 1, NULL, 'vma_statistics', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'TRAFFIC', '', '', 'vmicon vmicon-16-content', 12, 1, NULL, 'vma_traffic', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'SALES', '', '', 'vmicon vmicon-16-content', 13, 1, NULL, 'vma_sales', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'PAYMENTS', '', '', 'vmicon vmicon-16-content', 14, 1, NULL, 'vma_payments', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'ABOUT', '', '', 'vmicon vmicon-16-content', 15, 1, NULL, 'vma_about', NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'MANUAL', 'http://www.globacide.com/virtuemart-affiliate/user-manual.html', '', 'vmicon vmicon-16-content', 16, 1, NULL, NULL, NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'FORUM', 'http://www.globacide.com/virtuemart-affiliate/forum.html', '', 'vmicon vmicon-16-help', 17, 1, NULL, NULL, NULL);
								  
								  INSERT INTO `#__virtuemart_adminmenuentries` VALUES ('', 255, 0, 'Globacide.com', 'http://www.globacide.com', '', 'vmicon vmicon-16-content', 18, 1, NULL, NULL, NULL);
							  
							  	  UPDATE `#__virtuemart_adminmenuentries` SET `link` = '' WHERE `link` = '0';";
		
		$queries				= $database->splitSql($query);
		
		foreach ($queries as $query) {
			
			$database->setQuery($query);
			
			$database->query();
				
		}
		
		// insert offline tracking fields and values, only on new installs or upgrades from vm 2.0
		
		if ((!$upgrade && !$reinstall) || $upgradeFromVM20) {
			
			// get fax field's ordering number
			
			$query				= "SELECT `ordering` FROM #__virtuemart_userfields WHERE `name` = 'fax'";
			
			$database->setQuery($query);
			
			$faxOrdering		= $database->loadResult();
			
			// increase ordering of higher order fields
			
			$query				= "UPDATE #__virtuemart_userfields SET ordering = ordering + 2 WHERE `ordering` > '" . $faxOrdering . "'";
			
			$database->setQuery($query);
			
			$database->query();
			
			// insert the new fields
			
			$query				= "INSERT INTO #__virtuemart_userfields (`virtuemart_userfield_id`, `virtuemart_vendor_id`, `name`, " . 
			
								  "`title`, `description`, `type`, `maxlength`, `size`, `required`, `cols`, `rows`, `value`, `default`, `registration`, " . 
								  
								  "`shipment`, `account`, `readonly`, `calculated`, `sys`, `ordering`, `shared`, `published`, " .
								  
								  "`created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) " . 
								  
								  "VALUES ('', '1', 'vm_partnersusername', 'Partner', '', 'text', 50, 30, 0, '', '', '', '', '1', '0', '1', '0', '0', '0', '" . ($faxOrdering + 1) . "', 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0)";
	
			$database->setQuery($query);
			
			$database->query();
			
			$query				= "INSERT INTO #__virtuemart_userfields (`virtuemart_userfield_id`, `virtuemart_vendor_id`, `name`, " . 
			
								  "`title`, `description`, `type`, `maxlength`, `size`, `required`, `cols`, `rows`, `value`, `default`, `registration`, " . 
								  
								  "`shipment`, `account`, `readonly`, `calculated`, `sys`, `ordering`, `shared`, `published`, " .
								  
								  "`created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) " . 
								  
								  "VALUES ('', '1', 'vm_partnersname', 'Partner', '', 'select', 0, 0, 0, '', '', '', '', '1', '0', '1', '0', '0', '0', '" . ($faxOrdering + 2) . "', 0, 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0)";
	
			$database->setQuery($query);
			
			$database->query();
			
			// get the partner's name field id
			
			$partnersNameID		= $database->insertid();
			
			// add all published affiliates as partner names
			
			$query				= "SELECT `affiliate_id`, CONCAT(`fname`, ' ', `lname`) AS name FROM #__vm_affiliate WHERE `blocked` = '0'";
			
			$database->setQuery($query);
			
			$affiliates			= $database->loadObjectList();
			
			$query				= "INSERT INTO #__virtuemart_userfield_values VALUES ('', '" . $partnersNameID . "', ' ', '0', 0, '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0')";
									  
			$database->setQuery($query);
			
			$database->query();
					
			if (is_array($affiliates)) {
				
				foreach ($affiliates as $affiliate) {
					
					$query			= "INSERT INTO #__virtuemart_userfield_values VALUES ('', '" . $partnersNameID . "', '" . $affiliate->name . "', '" . 
					
									  $affiliate->affiliate_id . "', 0, '" . $affiliate->affiliate_id . "', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0', '0000-00-00 00:00:00', '0')";
									  
					$database->setQuery($query);
					
					$database->query();
					
				}
			
			}
			
		}
		
		// insert the offline tracking user info fields
		
		$userInfoFields			= $database->getTableFields("#__virtuemart_userinfos");
		
		if (!in_array("vm_partnersusername",	$userInfoFields)) {
			
			$query				= "ALTER TABLE `#__virtuemart_userinfos` ADD `vm_partnersusername` VARCHAR( 255 ) NOT NULL";
			
			$database->setQuery($query);
			
			$database->query();
			
		}
		
		if (!in_array("vm_partnersname", 		$userInfoFields)) {
			
			$query				= "ALTER TABLE `#__virtuemart_userinfos` ADD `vm_partnersname` VARCHAR( 255 ) NOT NULL";
			
			$database->setQuery($query);
			
			$database->query();
			
		}
		
		$userInfoFields			= $database->getTableFields("#__virtuemart_order_userinfos");
		
		if (!in_array("vm_partnersusername",	$userInfoFields)) {
			
			$query				= "ALTER TABLE `#__virtuemart_order_userinfos` ADD `vm_partnersusername` VARCHAR( 255 ) NOT NULL";
			
			$database->setQuery($query);
			
			$database->query();
			
		}
		
		if (!in_array("vm_partnersname", 		$userInfoFields)) {
			
			$query				= "ALTER TABLE `#__virtuemart_order_userinfos` ADD `vm_partnersname` VARCHAR( 255 ) NOT NULL";
			
			$database->setQuery($query);
			
			$database->query();
			
		}
		
		// draw confirmation box
		
		$installer->set('message', "<table style=\"width: 100%; text-align: center; padding: 10px;\">" . 
										
										"<tr style=\"text-align: center; margin-top: 10px;\">" . 
										
											"<td colspan=\"3\">" . 
											
												"<h1 style=\"text-align: center;\">Thank you for " . ($upgrade ? "upgrading" : "choosing") . " VM Affiliate!</h1>" .
													
												"<h3 style=\"text-align: center;\">The only truly integrated affiliate system for Joomla!&trade; &amp; VirtueMart!</h3>" . 
												
												"<h4 style=\"text-align: center; color: green;\">You have just " . ($upgrade ? "upgraded from version <strong>" . $previousVersion . "</strong> to" : 
												
												($reinstall ? "re-installed" : "installed")) . " version <strong>" . $componentXML["version"]. "</strong> of VM Affiliate.</h4>" . 
											
											"</td>" . 
											
										"</tr>" . 
										
										"<tr><td colspan=\"3\"><hr style=\"height: 3px; border: none; background: transparent; border-top: 3px; border-top-style: dashed; border-top-color: #CCCCCC;\" /></td></tr>" . 
										
										"<tr style=\"text-align: center; margin-top: 10px;\">" . 
										
											"<td style=\"width: 102px;\">" . 
													
												"<img src=\"" . $uri->root() . "/components/com_affiliate/assets/images/install_vmabox.png\" />" . 
												
											"</td>" . 
											
											"<td style=\"margin-left: 30px; margin-right: 30px; margin-bottom: 10px; vertical-align: middle;\">" . 
												
												"<p>VM Affiliate is a complex affiliate system, with powerful yet easy-to-use features.</p>" . 
												
												"<p>Its integration with VirtueMart saves your time and money otherwise spent on maintenance needs, as standard affiliate programs require. It's highly stable, automatic, customizable and efficient.</p>" .
											
												"<p>VM Affiliate has got this far thanks to the wonderful community and constant feedback from its users. We would love to hear about your experience with VMA! Your suggestion will be treasured, and carefully considered.</p>" . 
												
											"</td>" . 
											
											"<td style=\"width: 200px;\">" . 
											
												"<img src=\"" . $uri->root() . "/components/com_affiliate/assets/images/install_gslogo.png\" />" . 
											
											"</td>" . 
										
										"</tr>" . 
										
										"<tr><td colspan=\"3\" style=\"text-align: center;\"><a href=\"" . $uri->root() . "index.php?option=com_affiliate&view=login\">VMA Frontend</a><span style=\"margin-left: 5px; margin-right: 5px; color: #CCCCCC;\">&nbsp;|&nbsp;</span><a href=\"" . $uri->root() . "administrator/index.php?option=com_virtuemart&pshop_mode=admin&page=vma.summary\">VMA Administration</a></td></tr>" . 
										
										"<tr><td colspan=\"3\"><hr style=\"height: 3px; border: none; background: transparent; border-top: 3px; border-top-style: dashed; border-top-color: #CCCCCC;\" /></td></tr>" . 
										
										"<tr>" . 
										
											"<td colspan=\"3\" align=\"center\">" . 
											
												"<table align=\"center\">" . 
												
													"<tr align=\"center\">" . 
														
														"<td style=\"line-height: 32px; background-image: url(" . $uri->root() . "/components/com_affiliate/assets/images/install_featurelist.png); background-repeat: no-repeat; padding-left: 38px;\"><a target=\"_new\" href=\"http://www.globacide.com/virtuemart-affiliate/features.html\">Feature List</a></td>"	. 
														
														"<td style=\"line-height: 32px; margin-left: 15px; margin-right: 15px; color: #CCCCCC;\">&nbsp;|&nbsp;</td>" . 
														
														"<td style=\"line-height: 32px; background-image: url(" . $uri->root() . "/components/com_affiliate/assets/images/install_manual.png); background-repeat: no-repeat; padding-left: 38px;\"><a target=\"_new\" href=\"http://www.globacide.com/virtuemart-affiliate/user-manual.html\">User Manual</a></td>"		. 
														
														"<td style=\"line-height: 32px; margin-left: 15px; margin-right: 15px; color: #CCCCCC;\">&nbsp;|&nbsp;</td>" . 
														
														"<td style=\"line-height: 32px; background-image: url(" . $uri->root() . "/components/com_affiliate/assets/images/install_forum.png); background-repeat: no-repeat; padding-left: 38px;\"><a target=\"_new\" href=\"http://www.globacide.com/virtuemart-affiliate/forum.html\">Support Forums</a></td>"			. 
														
														"<td style=\"line-height: 32px; margin-left: 15px; margin-right: 15px; color: #CCCCCC;\">&nbsp;|&nbsp;</td>" . 
														
														"<td style=\"line-height: 32px; background-image: url(" . $uri->root() . "/components/com_affiliate/assets/images/install_website.png); background-repeat: no-repeat; padding-left: 38px;\"><a target=\"_new\" href=\"http://www.globacide.com\">Globacide Solutions</a></td>"									. 
													
													"</tr>" . 
													
												"</table>" . 
												
											"</td>" . 
											
										"</tr>" . 
										
									"</table>");
	
	}
	
	/**
	 * Method to install the database from scratch
	 */
	 
	function installDatabase() {
		
		// get application
		
		$mainframe	= &JFactory::getApplication();
		
		$uri 		= &JFactory::getURI();
		
		// initialize required variables
		
		$database	= &JFactory::getDBO();
		
		$config		= &JFactory::getConfig();
	
		$siteName	= $config->get("sitename");
		
		$siteNameAC	= strtoupper($config->get("sitename"));
		
		$siteURL	= $uri->root();
		
		$utf		= $database->hasUTF();
		
		$utfString	= $utf ? " DEFAULT CHARSET=utf8" : NULL;
		
		// load language
				
		$language	= &JFactory::getLanguage();
		
		$language->load("com_affiliate", JPATH_ROOT);
		
		// write query
		
		$query		= "DROP TABLE IF EXISTS 
		
							`#__vm_affiliate`, 
							
							`#__vm_affiliate_banners`, 
							
							`#__vm_affiliate_banners_hits`, 
							
							`#__vm_affiliate_clicks`, 
							
							`#__vm_affiliate_links`, 
							
							`#__vm_affiliate_links_categories`, 
							
							`#__vm_affiliate_methods`, 
							
							`#__vm_affiliate_method_fields`, 
							
							`#__vm_affiliate_orders`, 
							
							`#__vm_affiliate_orders_pretrack`, 
							
							`#__vm_affiliate_payments`, 
							
							`#__vm_affiliate_payment_details`, 
							
							`#__vm_affiliate_rates`, 
							
							`#__vm_affiliate_settings`, 
							
							`#__vm_affiliate_size_groups`, 
							
							`#__vm_affiliate_textads`, 
							
							`#__vm_affiliate_textads_hits`;
		
					   CREATE TABLE IF NOT EXISTS `#__vm_affiliate` (
		
						`affiliate_id` int(11) NOT NULL auto_increment,
						
						`username` varchar(20) NOT NULL default '',
						
						`password` varchar(255) NOT NULL,
						
						`fname` varchar(15) NOT NULL default '',
						
						`lname` varchar(15) NOT NULL default '',
						
						`mail` varchar(50) NOT NULL default '',
						
						`website` varchar(50) NOT NULL default 'N/A',
						
						`commissions` float(255,4) NOT NULL default '0.0000',
						
						`street` varchar(50) NOT NULL default '',
						
						`city` varchar(30) NOT NULL default '',
						
						`state` varchar(30) NOT NULL default '',
						
						`country` varchar(30) NOT NULL default '',
						
						`zipcode` varchar(10) NOT NULL default '',
						
						`phoneno` varchar(20) NOT NULL default '',
						
						`taxssn` varchar(30) NOT NULL default '',
						
						`method` varchar(20) NOT NULL default 'N/A',
						
						`per_sale_percentage` decimal(10,2) NOT NULL default '0.00',
						
						`per_click_fixed` decimal(10,4) NOT NULL default '0.0000',
						
						`per_unique_click_fixed` decimal(10,4) NOT NULL default '0.0000',
						
						`per_sale_fixed` decimal(10,4) NOT NULL default '0.0000',
						
						`discount_type` int(255) NOT NULL default '2',
						
						`discount_amount` float(255,2) NOT NULL default '0.00',
						
						`use_defaults` int(1) NOT NULL default '1',
						
						`linkedto` varchar(100) NOT NULL,
						
						`blocked` binary(1) NOT NULL default '0',
						
						`referred` int(255) default NULL,
						
						`date` date NOT NULL,
						
						PRIMARY KEY  (`affiliate_id`)
						
					  )" . $utfString . ";
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_banners` (
					  
						`banner_id` int(255) NOT NULL auto_increment,
						
						`banner_name` varchar(255) NOT NULL,
						
						`banner_image` varchar(255) NOT NULL,
						
						`banner_type` varchar(255) NOT NULL default 'jpg',
						
						`banner_width` int(255) NOT NULL default '0',
						
						`banner_height` int(255) NOT NULL default '0',
						
						`banner_link` varchar(255) NOT NULL,
						
						`published` binary(1) NOT NULL,
						
						PRIMARY KEY  (`banner_id`)
						
					  )" . $utfString . ";
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_banners_hits` (
					  
						`banhit_id` int(255) NOT NULL auto_increment,
						
						`banner_id` int(255) NOT NULL,
						
						`hits` int(255) NOT NULL,
						
						`affiliate_id` int(255) NOT NULL,
						
						PRIMARY KEY  (`banhit_id`)
						
					  )" . $utfString . ";
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_clicks` (
					  
						`ClickID` int(10) unsigned NOT NULL auto_increment,
						
						`AffiliateID` int(10) unsigned NOT NULL default '0',
						
						`UnixTime` int(11) NOT NULL default '0',
						
						`RemoteAddress` varchar(16) NOT NULL default 'none',
						
						`RefURL` longtext NOT NULL,
						
						`Browser` varchar(100) NOT NULL default 'ID',
						
						`paid` binary(1) NOT NULL default '0',
						
						`date` date NOT NULL,
						
						PRIMARY KEY  (`ClickID`)
						
					  )" . $utfString . ";
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_links` (
					  
						`link_id` int(255) NOT NULL auto_increment,
						
						`product_id` int(255) NOT NULL,
						
						`published` binary(1) NOT NULL,
						
						PRIMARY KEY  (`link_id`)
						
					  )" . $utfString . ";
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_links_categories` (
					  
						`link_id` int(255) NOT NULL auto_increment,
						
						`category_id` int(255) NOT NULL,
						
						`published` binary(1) NOT NULL,
						
						PRIMARY KEY  (`link_id`)
						
					  )" . $utfString . ";
					  
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_method_fields` (
					  
						`field_id` int(255) NOT NULL auto_increment,
						
						`method_id` int(255) NOT NULL,
						
						`field_name` varchar(255) NOT NULL,
						
						PRIMARY KEY  (`field_id`)
						
					  )" . $utfString . ";
					  
					  INSERT INTO `#__vm_affiliate_method_fields` (`field_id`, `method_id`, `field_name`) VALUES (1, 1, '" . ($utf ? JText::_("PAYPAL_EMAIL") : "PayPal E-mail") . "');
					  
					  INSERT INTO `#__vm_affiliate_method_fields` (`field_id`, `method_id`, `field_name`) VALUES (3, 3, '" . ($utf ? JText::_("PAYABLE_TO") : "Payable To") . "');
					  
					  INSERT INTO `#__vm_affiliate_method_fields` (`field_id`, `method_id`, `field_name`) VALUES (4, 4, '" . ($utf ? JText::_("ACCOUNT_HOLDER") : "Account Holder") . "');
					  
					  INSERT INTO `#__vm_affiliate_method_fields` (`field_id`, `method_id`, `field_name`) VALUES (5, 4, '" . ($utf ? JText::_("BANK_NAME") : "Bank Name") . "');
					  
					  INSERT INTO `#__vm_affiliate_method_fields` (`field_id`, `method_id`, `field_name`) VALUES (6, 4, '" . ($utf ? JText::_("ACCOUNT_NUMBER") : "Account Number") . "');
					  
					  INSERT INTO `#__vm_affiliate_method_fields` (`field_id`, `method_id`, `field_name`) VALUES (7, 4, '" . ($utf ? JText::_("BANK_ADDRESS") : "Bank Address") . "');
					  
					  INSERT INTO `#__vm_affiliate_method_fields` (`field_id`, `method_id`, `field_name`) VALUES (11, 4, '" . ($utf ? JText::_("SWIFT") : "S.W.I.F.T. Code") . "');
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_methods` (
					  
						`method_id` int(255) NOT NULL auto_increment,
						
						`method_name` varchar(255) NOT NULL,
						
						`method_enabled` binary(1) NOT NULL default '1',
						
						`method_type` varchar(255) NOT NULL default 'Manual',
						
						`built_in` binary(1) NOT NULL default '0',
						
						PRIMARY KEY  (`method_id`)
						
					  )" . $utfString . ";
					  
					  INSERT INTO `#__vm_affiliate_methods` (`method_id`, `method_name`, `method_enabled`, `method_type`, `built_in`) VALUES (1, 'PayPal', '1', 'Automatic', '1');
					  
					  INSERT INTO `#__vm_affiliate_methods` (`method_id`, `method_name`, `method_enabled`, `method_type`, `built_in`) VALUES (3, '" . ($utf ? JText::_("CHECK") : "Check") . "', '1', 'Manual', '1');
					  
					  INSERT INTO `#__vm_affiliate_methods` (`method_id`, `method_name`, `method_enabled`, `method_type`, `built_in`) VALUES (4, '" . ($utf ? JText::_("BANK_TRANSFER") : "Bank Transfer") . "', '1', 'Manual', '1');
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_orders` (
					  
						`aff_order_id` int(255) NOT NULL auto_increment,
						
						`affiliate_id` int(255) NOT NULL,
						
						`order_id` int(255) NOT NULL,
						
						`order_status` varchar(1) NOT NULL default 'P',
						
						`paid` binary(1) NOT NULL default '0',
						
						`date` date NOT NULL,
						
						PRIMARY KEY  (`aff_order_id`)
						
					  )" . $utfString . ";
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_payment_details` (
					  
						`affiliate_id` int(255) NOT NULL,
						
						`field_id` int(255) NOT NULL,
						
						`field_value` varchar(255) NOT NULL
						
					  )" . $utfString . ";
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_payments` (
					  
						`payment_id` int(30) NOT NULL auto_increment,
						
						`affiliate_id` int(30) NOT NULL default '0',
						
						`username` varchar(30) NOT NULL default '',
						
						`method` varchar(30) NOT NULL default '',
						
						`amount` float(255,2) NOT NULL,
						
						`date` date NOT NULL default '0000-00-00',
						
						`status` varchar(255) NOT NULL default 'P',
						
						`transaction` varchar(255) NOT NULL default '0',
						
						PRIMARY KEY  (`payment_id`)
						
					  )" . $utfString . ";
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_rates` (
					  
						`rate_id` int(255) NOT NULL auto_increment,
						
						`rate` varchar(10) NOT NULL default '1',
						
						`per_sale_percentage` decimal(10,2) NOT NULL default '0.00',
						
						`per_click_fixed` decimal(10,4) NOT NULL default '0.0000',
						
						`per_unique_click_fixed` decimal(10,4) NOT NULL default '0.0000',
						
						`per_sale_fixed` decimal(10,4) NOT NULL default '0.0000',
						
						PRIMARY KEY  (`rate_id`)
						
					  )" . $utfString . ";
					  
					  INSERT INTO `#__vm_affiliate_rates` (`rate_id`, `rate`, `per_sale_percentage`, `per_click_fixed`, `per_unique_click_fixed`, `per_sale_fixed`) VALUES (1, '1', '15.00', '0.0000', '0.0000', '0.0000');
					  
					  INSERT INTO `#__vm_affiliate_rates` (`rate_id`, `rate`, `per_sale_percentage`, `per_click_fixed`, `per_unique_click_fixed`, `per_sale_fixed`) VALUES (2, '2', '5.00', '0.0000', '0.0000', '0.0000');
					  
					  INSERT INTO `#__vm_affiliate_rates` (`rate_id`, `rate`, `per_sale_percentage`, `per_click_fixed`, `per_unique_click_fixed`, `per_sale_fixed`) VALUES (3, '3', '3.00', '0.0000', '0.0000', '0.0000');
					  
					  INSERT INTO `#__vm_affiliate_rates` (`rate_id`, `rate`, `per_sale_percentage`, `per_click_fixed`, `per_unique_click_fixed`, `per_sale_fixed`) VALUES (4, '4', '2.00', '0.0000', '0.0000', '0.0000');
					  
					  INSERT INTO `#__vm_affiliate_rates` (`rate_id`, `rate`, `per_sale_percentage`, `per_click_fixed`, `per_unique_click_fixed`, `per_sale_fixed`) VALUES (5, '5', '1.00', '0.0000', '0.0000', '0.0000');
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_settings` (
					  
						`setting` int(255) NOT NULL default '1',
						
						`cookie_time` int(255) NOT NULL default '31536000',
						
						`allow_signups` binary(1) NOT NULL default '1',
						
						`link_feed` varchar(20) NOT NULL default 'aff_id',
						
						`auto_block` binary(1) NOT NULL default '0',
						
						`must_agree` binary(1) NOT NULL default '0',
						
						`aff_terms` longtext NOT NULL,
						
						`multi_tier` binary(1) NOT NULL,
						
						`tier_level` int(255) NOT NULL,
						
						`pay_balance` float(255,4) NOT NULL default '50.0000',
						
						`pay_day` int(255) NOT NULL default '1',
						
						`initial_bonus` float(255,4) NOT NULL default '0.0000',
						
						`track_who` int(255) NOT NULL default '2',
						
						`offline_tracking` binary(1) NOT NULL default '0',
						
						`offline_type` int(255) NOT NULL default '2',
						
						`discount_type` int(255) NOT NULL default '2',
						
						`discount_amount` float(255,2) NOT NULL default '5.00'
						
					  )" . $utfString . ";
					  
					  INSERT INTO `#__vm_affiliate_settings` (`setting`, `cookie_time`, `allow_signups`, `link_feed`, `auto_block`, `must_agree`, `aff_terms`, `multi_tier`, `tier_level`, `pay_balance`, `pay_day`, `initial_bonus`, `track_who`, `offline_tracking`, `offline_type`, `discount_type`, `discount_amount`) VALUES ('1', 31536000, '1', 'aff_id', '0', '0', '" . 
					  
					  $database->getEscaped(str_replace("CURRENTDATE", date("F j, Y"), str_replace("WEBSITENAME", $siteName, str_replace("WEBSITENAMECAPITAL", $siteNameAC, str_replace("WEBSITEADDRESS", $siteURL, "<div id=\"maincontent\">\r\n<h1>General terms and conditions for participation in WEBSITENAME's Affiliate Program</h1>\r\n<p>CURRENTDATE</p>\r\n<p>NOTE: PLEASE THOROUGHLY REVIEW THESE GENERAL TERMS AND CONDITIONS FOR PARTICIPATION IN WEBSITENAMECAPITAL'S AFFILIATE PROGRAM (THE \"GENERAL TERMS AND CONDITIONS\") BEFORE YOU COMMENCE YOUR PARTICIPATION IN WEBSITENAMECAPITAL'S AFFILIATE PROGRAM. IF YOU DO NOT AGREE TO BE BOUND BY THESE GENERAL TERMS AND CONDITIONS, WEBSITENAMECAPITAL IS NOT WILLING TO APPOINT YOU AS AN AFFILIATE AND YOU ARE NOT ALLOWED TO PARTICIPATE IN THE WEBSITENAMECAPITAL AFFILIATE PROGRAM. PLEASE NOTE THAT THE COMMENCEMENT OF ANY OF THE ACTIVITIES THAT ARE ALLOWED UNDER THE WEBSITENAMECAPITAL AFFILIATE PROGRAM WILL BE CONSIDERED AS AN ACCEPTANCE OF THESE GENERAL TERMS AND CONDITIONS.</p>\r\n<h2>1. BACKGROUND</h2>\r\n<p>WEBSITENAME has established an affiliate program, under which approved affiliates are entitled to promote WEBSITENAME and its products subject to the terms and conditions set out in these General Terms and Conditions (the \"WEBSITENAME Affiliate Program\").</p>\r\n<h2>2. DEFINITIONS</h2>\r\n<p>2.1 The following capitalized terms will have the meanings ascribed to them below, unless the context would obviously require otherwise.</p>\r\n<p><span style=\"text-decoration: underline;\">\"Affiliate\"</span> means an affiliate whose Affiliate Application has been accepted by WEBSITENAME by e-mail or otherwise in writing and who accepts these General Terms and Conditions.</p>\r\n<p><span style=\"text-decoration: underline;\">\"Affiliate Application\"</span> means the application form that must be submitted to WEBSITENAME in connection with the application to become a WEBSITENAME Affiliate.</p>\r\n<p><span style=\"text-decoration: underline;\">\"Affiliate Revenue Share\"</span> shall mean the revenue share that the Affiliate is entitled to, if any, in relation to sales of WEBSITENAME products that are tracked by the WEBSITENAME Tracking Functionality as sales that results from the Affiliate Promotional Activities performed hereunder.</p>\r\n<p><span style=\"text-decoration: underline;\">\"Affiliate Mailers\"</span> means newsletters and other mailers that the Affiliate issues from time to time to customers and other individuals who have accepted to receive such mailers from the Affiliate.</p>\r\n<p><span style=\"text-decoration: underline;\">\"Affiliate Promotional Activities\"</span> shall mean the promotional activities that the Affiliate performs hereunder in order to promote WEBSITENAME and the WEBSITENAME products as further specified in Section 3.1 hereto.</p>\r\n<p><span style=\"text-decoration: underline;\">\"Affiliate Resource Page\"</span> means the resource page that the Affiliate is granted access to when it has been approved as an Affiliate by WEBSITENAME, which specifies the WEBSITENAME Materials that the Affiliate is entitled to use whilst performing the Affiliate Promotional Activities hereunder.</p>\r\n<p><span style=\"text-decoration: underline;\">\"Affiliate Sites\"</span> means websites that are owned and controlled by the Affiliate.</p>\r\n<p><span style=\"text-decoration: underline;\">\"Affiliate User Account\"</span> means the Affiliate's user account for the WEBSITENAME Affiliate Program that: (i) specifies the contact details for the Affiliate; (ii) enables the Affiliate to use WEBSITENAME Tracking Functionality so that sales that directly relate to the Affiliate Promotional Activities can be properly tracked; (iii) specifies the Affiliate Revenue Share, if any.</p>\r\n<p><span style=\"text-decoration: underline;\">\"Effective Date\"</span> means the date when the Affiliate commences with any of the Affiliate Promotional Activities.</p>\r\n<p><span style=\"text-decoration: underline;\">\"WEBSITENAME Guidelines and Policies\"</span> means all other guidelines and policies of WEBSITENAME, its licensors and cooperation partners, including any updates of any of the foregoing, which WEBSITENAME makes available to the Affiliate on the WEBSITENAME website or otherwise in writing hereunder (including e-mails).</p>\r\n<p><span style=\"text-decoration: underline;\">\"WEBSITENAME Marks\"</span> means the name, trademarks, service marks, trade names, logos and/or other designations of origin of WEBSITENAME as specified on the WEBSITENAME website.</p>\r\n<p><span style=\"text-decoration: underline;\">\"WEBSITENAME Materials\"</span> means documents, texts, banners, graphics, logotypes, photos, screenshots, box shots and any and all other materials provided to the Affiliate on the Affiliate Resource Page or by any other means.</p>\r\n<p><span style=\"text-decoration: underline;\">\"WEBSITENAME Site\"</span> means WEBSITEADDRESS including the shopping carts linked thereto.</p>\r\n<p><span style=\"text-decoration: underline;\">\"WEBSITENAME Products\"</span> shall mean all products that are subject to commerce on WEBSITENAME Site and feature WEBSITENAME Tracking Functionality.</p>\r\n<p><span style=\"text-decoration: underline;\">\"WEBSITENAME Tracking Functionality\"</span> means WEBSITENAME tracking functionality which tracks whether a sale of a WEBSITENAME product relates to the Affiliate Promotional Activities performed hereunder.</p>\r\n<p><span style=\"text-decoration: underline;\"> </span></p>\r\n<h2>3.	LICENSE RIGHTS AND RESTRICTIONS</h2>\r\n<p>3.1	Subject to the Affiliate's compliance with these General Terms and Conditions (including but not limited to the requirements set out in Section 4.1 below), WEBSITENAME grants the Affiliate, during the term hereof, a non-exclusive, non-transferable, non-sublicensable and limited license, to:</p>\r\n<ol style=\"list-style-type: lower-roman; list-style-image: url();\">\r\n<li style=\"list-style-type: lower-roman; list-style-image: url();\">use the WEBSITENAME Marks and the WEBSITENAME Materials on the Affiliate Site(s) to provide links from the Affiliate Sites to the WEBSITENAME Site;</li>\r\n<li style=\"list-style-type: lower-roman; list-style-image: url();\">use the WEBSITENAME Marks and the WEBSITENAME Materials in Affiliate Mailers to provide links to the WEBSITENAME Site;</li>\r\n<li style=\"list-style-type: lower-roman; list-style-image: url();\">subject to WEBSITENAME's prior written approval, use the WEBSITENAME Marks, the WEBSITENAME Materials and the WEBSITENAME Products for any other promotional activities.</li>\r\n</ol>\r\n<p>3.2 The Affiliate may not assign, delegate, sublicense, pledge or otherwise transfer the rights or licenses set out herein, or any of its obligations hereunder to any third party other than as expressly granted under these General Terms and Conditions.</p>\r\n<p>3.3 The Affiliate may not alter, change, modify or adapt the WEBSITENAME Marks or the WEBSITENAME Materials, unless otherwise approved by WEBSITENAME in writing (in each specific case).</p>\r\n<p>3.4 The WEBSITENAME Marks, the WEBSITENAME Materials and the WEBSITENAME Products and any and all patents, copyrights, trademarks, design rights and any and all other intellectual property rights associated therewith, are the exclusive property of WEBSITENAME and its licensors. All rights in and to the WEBSITENAME Marks, the WEBSITENAME Materials and the WEBSITENAME Products are reserved by WEBSITENAME and its licensors and the Affiliate obtains no other rights than the limited license rights explicitly granted under Section 3.1 above.</p>\r\n<h2>4.	THE AFFILIATE'S OBLIGATIONS</h2>\r\n<p>4.1 The Affiliate undertakes to:</p>\r\n<ol style=\"list-style-type: lower-roman; list-style-image: url();\">\r\n<li style=\"list-style-type: lower-roman; list-style-image: url();\">place links on the Affiliate Sites to the WEBSITENAME Site through use of the WEBSITENAME Marks and the WEBSITENAME Materials;</li>\r\n<li style=\"list-style-type: lower-roman; list-style-image: url();\">ensure that the Affiliate Sites do not contain any content that is illegal or which can be considered as defamatory, racist, abusive or fraudulent;</li>\r\n<li style=\"list-style-type: lower-roman; list-style-image: url();\">ensure that the Affiliate Mailers that include WEBSITENAME Marks, WEBSITENAME Materials or links to WEBSITENAME Products cannot be considered as spam or unsolicited e-mails;</li>\r\n<li style=\"list-style-type: lower-roman; list-style-image: url();\">ensure that all use of the WEBSITENAME Marks or the WEBSITENAME Materials is made in compliance with the WEBSITENAME Guidelines and Policies and WEBSITENAME's instructions from time to time;</li>\r\n<li style=\"list-style-type: lower-roman; list-style-image: url();\">upon WEBSITENAME's request, promptly remove from any Affiliate Sites or any Affiliate Mailer any WEBSITENAME Marks, WEBSITENAME Materials or any text relating to WEBSITENAME or the WEBSITENAME products;</li>\r\n<li style=\"list-style-type: lower-roman; list-style-image: url();\">ensure that the use of the WEBSITENAME Marks, the WEBSITENAME Materials and the WEBSITENAME Products are made in compliance with any and all applicable laws and regulations, including but not limited to export control laws, privacy laws, marketing laws and consumer protection laws;</li>\r\n<li style=\"list-style-type: lower-roman; list-style-image: url();\">ensure that the contact information in the Affiliate User Account is complete, correct and at all times accurate; and</li>\r\n<li style=\"list-style-type: lower-roman; list-style-image: url();\">ensure that no third party is given access to the password of the Affiliate User Account and immediately inform WEBSITENAME of any suspected misuse of the afore-mentioned password.</li>\r\n</ol>\r\n<p>4.2	The Affiliate shall, during the term hereof and thereafter, indemnify WEBSITENAME against any and all liabilities, damages, losses, costs and expenses (including reasonable legal fees and expenses) suffered or incurred by WEBSITENAME due to: (i) the Affiliate's breach of these General Terms and Conditions; or (ii) otherwise due to the Affiliate's use of the WEBSITENAME Marks or the WEBSITENAME Materials.</p>\r\n<h2>5. WEBSITENAMECAPITAL'S OBLIGATION</h2>\r\n<p>WEBSITENAME shall pay any Affiliate Revenue Share that the Affiliate is entitled to in connection with the Affiliate Promotional Activities hereunder, if any, without undue delay. Any Affiliate Revenue Share percentage shall be calculated on the end user purchase price of the applicable WEBSITENAME product exclusive of any and all taxes and shipping fees (if applicable).</p>\r\n<p>The Affiliate will only be entitled to Affiliate Revenue Share on sales of WEBSITENAME products which can be tracked by the WEBSITENAME Tracking Functionality.</p>\r\n<h2>6. DISCLAIMER</h2>\r\n<p>TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE MANDATORY LAW, THE WEBSITENAMECAPITAL MARKS, THE WEBSITENAMECAPITAL MATERIALS AND THE WEBSITENAMECAPITAL PRODUCTS (AND ANY AND ALL PARTS THEREOF) ARE PROVIDED \"AS IS\" AND WEBSITENAMECAPITAL MAKES NO REPRESENTATIONS OR WARRANTIES WHATSOEVER WITH RESPECT TO THE WEBSITENAMECAPITAL MARKS, THE WEBSITENAMECAPITAL MATERIALS OR THE WEBSITENAMECAPITAL PRODUCTS, INCLUDING BUT NOT LIMITED TO, IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT OF THIRD PARTIES' INTELLECTUAL PROPERTY RIGHTS.</p>\r\n<h2>7. LIMITATION OF LIABILITY</h2>\r\n<p>7.1 EXCEPT AS EXPRESSLY SET FORTH IN THESE GENERAL TERMS AND CONDITIONS AND TO THE MAXIMUM EXTENT PERMITTED BY APPLICABLE MANDATORY LAW, IN NO EVENT SHALL EITHER PARTY BE LIABLE FOR: (I) INCIDENTAL, INDIRECT, SPECIAL, EXEMPLARY OR CONSEQUENTIAL DAMAGES; NOR FOR (II) DAMAGE TO PROPERTY; LOSS OF USE OR DATA; LOSS OF PRODUCTION; OR LOST PROFITS, SAVINGS OR REVENUES OF ANY KIND (WHETHER DIRECT, INDIRECT OR CONSEQUENTIAL); NO MATTER WHAT THEORY OF LIABILITY, EVEN IF SUCH PARTY HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH DAMAGES.</p>\r\n<p>7.2 Notwithstanding anything to the contrary herein, WEBSITENAME's total accumulated liability hereunder shall not exceed the payments made to the Affiliate hereunder during the period of time the Affiliate operated in the interest of the WEBSITENAME Affiliate Program, and under WEBSITENAME approval.</p>\r\n<p>7.3 Notwithstanding the above, the limitation of liabilities set out in Section 7.1 above shall not apply in case of gross negligence or willful misconduct or in case of a breach of, or in relation to, Section 3, Section 4.1 (ii)-(x), Section 4.2 and Section 8.</p>\r\n<p>7.4 No action, regardless of form, arising out of any alleged breach of these General Terms and Conditions may be brought by either party more than twelve (12) months after the cause of action occurred or became known to the claiming party, whichever is later.</p>\r\n<h2>8. CONFIDENTIALITY</h2>\r\n<p>The Affiliate undertakes to keep and treat as confidential and not disclose to any third party any information relating to WEBSITENAME's business and trade secrets received in connection with the Affiliate's participation in the WEBSITENAME Affiliate Program (including but not limited to product related and commercial information) nor make use of such information for any purpose whatsoever other than for the purposes of its participation in the WEBSITENAME Affiliate Program. The confidentiality undertaking shall last during the term of the Affiliate's participation in the WEBSITENAME Affiliate Program and for a period of five (5) years thereafter.</p>\r\n<h2>9. TERM AND TERMINATION</h2>\r\n<p>9.1 WEBSITENAME may further prematurely terminate the Affiliate's participation in the WEBSITENAME Affiliate Program upon written notice to the Affiliate (including e-mail), with immediate effect: (i) if the Affiliate, in WEBSITENAME's reasonable discretion, is in material breach of these General Terms and Conditions; (ii) if the Affiliate's use of the WEBSITENAME Marks, in WEBSITENAME's reasonable discretion, dilutes or tarnish the WEBSITENAME Marks or harms the reputation of WEBSITENAME in any way; (iii) if the Affiliate becomes insolvent or enter into liquidation, bankruptcy or other procedure due to its inability to pay its debts; or (iv) in the event of a change of control of the WEBSITENAME.</p>\r\n<p>9.2 Upon termination or expiration of the Affiliate's participation in the WEBSITENAME Affiliate Program, all rights granted to the Affiliate hereunder shall terminate and the Affiliate shall: (i) cease any and all use of the WEBSITENAME Marks, the WEBSITENAME Materials and the WEBSITENAME Products; and (ii) destroy any Confidential Information of WEBSITENAME received hereunder.</p>\r\n<p>9.3 Sections which by their nature are intended to survive the termination or expiration of these General Terms and Conditions, shall so survive the expiration or termination hereof, including but not limited to Section 4.2, Section 6, Section 7, Section 8 and Section 12.</p>\r\n<h2>10.	REVISIONS OF THESE GENERAL TERMS AND CONDITIONS</h2>\r\n<p>WEBSITENAME reserves the right to revise these General Terms and Conditions upon at least thirty (30) days prior written notice to the Affiliate (including e-mail). If the Affiliate does not accept the revised General Terms and Conditions, the Affiliate is entitled to terminate its participation in the WEBSITENAME Affiliate Program with immediate effect upon written notice to WEBSITENAME (including e-mail).</p>\r\n<h2>11.	MISCELLANEOUS</h2>\r\n<p>11.1	All notices required or permitted to be given by either party under these General Terms and Conditions shall be in writing and may be delivered by courier, registered mail, facsimile or e-mail and shall be deemed given upon dispatch of such notice. A notice to the Affiliate shall be made to the contact details specified in the Affiliate User Account, and notices to WEBSITENAME shall be made to the contact details specified on WEBSITENAME website.</p>\r\n<p>11.2	The Affiliate may not assign any of its rights or obligation under these General Terms and Conditions to a third party without the prior written consent of WEBSITENAME.</p>\r\n<p>11.3	The failure by WEBSITENAME or the Affiliate to enforce any provisions of these General Terms and Conditions or to exercise any right in respect thereto shall not be construed as constituting a waiver of its rights thereof.</p>\r\n<p>11.4	If any provision of these General Terms and Conditions is unenforceable, such provision will be changed and interpreted to accomplish the objectives of such provision to the greatest extent possible under applicable law and the remaining provisions will continue in full force and effect.</p>\r\n<p>11.5	The Affiliate's participation in the WEBSITENAME Affiliate Program is not intended to establish any partnership, agency, joint venture, employment, or other relationship between the WEBSITENAME and the Affiliate except that of independent contractors.</p>\r\n<p>11.6	These General Terms and Conditions contains the entire agreement between the parties on the subject matter hereof and supersedes all undertakings and agreements previously made between the parties with respect to the subject matter of these General Terms and Conditions.</p>\r\n<p>11.7	Except as set out in Section 10 above, these General Terms and Conditions may be modified only by a written document duly signed by both parties and referencing these General Terms and Conditions.</p>\r\n<h2>12.	GOVERNING LAW AND ARBITRATION</h2>\r\n<p>Notwithstanding the above, nothing in these General Terms and Conditions shall prevent either party from seeking any interim or final injunctive relief by a court if competent jurisdiction.</p>\r\n</div>"))))) . 
					  
					  "', '0', 2, 50.0000, 1, 0.0000, 2, '0', 2, 2, 5.00);
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_size_groups` (
					  
						`size_group_id` int(255) NOT NULL auto_increment,
						
						`width` int(255) NOT NULL default '0',
						
						`height` int(255) NOT NULL default '0',
						
						`name` varchar(255) NOT NULL,
						
						PRIMARY KEY  (`size_group_id`)
						
					  )" . $utfString . ";
					  
					  INSERT INTO `#__vm_affiliate_size_groups` (`size_group_id`, `width`, `height`, `name`) VALUES (22, 468, 60,	'Full Banner');
					  
					  INSERT INTO `#__vm_affiliate_size_groups` (`size_group_id`, `width`, `height`, `name`) VALUES (23, 234, 60,	'Half Banner');
					  
					  INSERT INTO `#__vm_affiliate_size_groups` (`size_group_id`, `width`, `height`, `name`) VALUES (24, 88, 31,	'Micro Bar');
					  
					  INSERT INTO `#__vm_affiliate_size_groups` (`size_group_id`, `width`, `height`, `name`) VALUES (25, 120, 240, 'Vertical Banner');
					  
					  INSERT INTO `#__vm_affiliate_size_groups` (`size_group_id`, `width`, `height`, `name`) VALUES (26, 728, 90,	'Leaderboard');
					  
					  INSERT INTO `#__vm_affiliate_size_groups` (`size_group_id`, `width`, `height`, `name`) VALUES (27, 160, 600, 'Wide Skyscraper');
					  
					  INSERT INTO `#__vm_affiliate_size_groups` (`size_group_id`, `width`, `height`, `name`) VALUES (28, 120, 600, 'Skyscraper');
					  
					  INSERT INTO `#__vm_affiliate_size_groups` (`size_group_id`, `width`, `height`, `name`) VALUES (29, 300, 600, 'Half Page Ad');
					  
					  INSERT INTO `#__vm_affiliate_size_groups` (`size_group_id`, `width`, `height`, `name`) VALUES (30, 300, 0,	'300px Fluid Height');
					  
					  INSERT INTO `#__vm_affiliate_size_groups` (`size_group_id`, `width`, `height`, `name`) VALUES (31, 0, 0,		'Fluid');
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_textads` (
					  
						`textad_id` int(255) NOT NULL auto_increment,
						
						`title` varchar(255) NOT NULL,
						
						`content` varchar(255) NOT NULL,
						
						`published` binary(1) NOT NULL,
						
						`link` varchar(255) NOT NULL,
						
						`width` int(255) NOT NULL,
						
						`height` int(255) NOT NULL,
						
						PRIMARY KEY  (`textad_id`)
						
					  )" . $utfString . ";
					  
					  CREATE TABLE IF NOT EXISTS `#__vm_affiliate_textads_hits` (
					  
						`texthit_id` int(255) NOT NULL auto_increment,
						
						`textad_id` int(255) NOT NULL,
						
						`hits` int(255) NOT NULL,
						
						`affiliate_id` int(255) NOT NULL,
						
						PRIMARY KEY  (`texthit_id`)
						
					  )" . $utfString . ";";
		
		$queries	= $database->splitSql($query);
		
		foreach ($queries as $query) {
			
			$database->setQuery($query);
			
			$database->query();
				
		}
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to update the database
	 */
	
	function updateDatabase($oldVersion) {
		
		// initialize required variables
		
		$database	= &JFactory::getDBO();
		
		$utf		= $database->hasUTF();
		
		$utfString	= $utf ? " DEFAULT CHARSET=utf8" : NULL;
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to uninstall VM Affiliate
	 */
	 
	function uninstall() {
		
		// import the installer
	
		jimport('joomla.installer.installer');

		$installer	= &JInstaller::getInstance();
		
		// get database object
		
		$database	= &JFactory::getDBO();
		
		// uninstall the plugin
		
		$query		= "SELECT `extension_id` FROM #__extensions WHERE `element` = 'vma'";
		
		$database->setQuery($query);
		
		$pluginID	= $database->loadResult();

		if ($pluginID) {
			
			$pluginInstaller = new JInstaller();
			
			$pluginInstaller->uninstall("plugin", $pluginID);
			
		}

		// remove all vm affiliate databases
		
		$query		= "DROP TABLE `#__vm_affiliate`, 		`#__vm_affiliate_banners`, 			`#__vm_affiliate_banners_hits`, `#__vm_affiliate_clicks`, 
		
								  `#__vm_affiliate_links`, 	`#__vm_affiliate_links_categories`, `#__vm_affiliate_methods`, 		`#__vm_affiliate_method_fields`, 
								  
								  `#__vm_affiliate_orders`, `#__vm_affiliate_orders_pretrack`,	`#__vm_affiliate_payments`, 	`#__vm_affiliate_payment_details`, 
								  
								  `#__vm_affiliate_rates`,	`#__vm_affiliate_settings`, 		`#__vm_affiliate_size_groups`, 	`#__vm_affiliate_textads`, 
								  
								  `#__vm_affiliate_textads_hits`";
		
		$database->setQuery($query);
		
		$database->query();
		
		// get component id
		
		$query					= "SELECT `extension_id` FROM #__extensions WHERE `element` = 'com_affiliate'";
		
		$database->setQuery($query);
		
		$componentID			= $database->loadResult();
		
		// add backend link to prevent error
		
		$this->insertMenuItem("INSERT INTO #__menu VALUES ('', 'main', 'com_affiliate', 'comaffiliate', '', 'comaffiliate', 'index.php?option=com_affiliate', 'component', 1, 1, 1, '" . $componentID . "', 0, '0000-00-00 00:00:00', 0, 1, 'class:component', 0, '', __LFT__, __RGT__, 0, '', 1)");
		
		// remove vma menus
		
		$this->deleteMenuItem("`link` LIKE '%option=com_affiliate%' AND `client_id` = 0");
		
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
		
		jimport('joomla.filesystem.folder');
		
		$path		= JPATH_ROOT . DIRECTORY_SEPARATOR . "administrator" . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_virtuemart";
		
		@unlink($path . DIRECTORY_SEPARATOR . "vmaInstall.xml");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_about.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_affiliates.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_banners.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_category_ads.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_commission_rates.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_configuration.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_email_affiliates.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_pay_affiliates.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_payment_methods.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_payments.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_product_ads.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_sales.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_sizegroups.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_statistics.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_textads.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "controllers" 	. DIRECTORY_SEPARATOR . "vma_traffic.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_about.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_affiliates.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_banners.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_category_ads.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_commission_rates.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_configuration.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_email_affiliates.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_pay_affiliates.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_payment_methods.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_payments.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_product_ads.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_sales.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_sizegroups.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_statistics.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_textads.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "models" 		. DIRECTORY_SEPARATOR . "vma_traffic.php");
		
		@unlink($path . DIRECTORY_SEPARATOR . "helpers" 		. DIRECTORY_SEPARATOR . "vma.php");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_about");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_affiliates");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_banners");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_category_ads");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_commission_rates");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_configuration");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_email_affiliates");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_pay_affiliates");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_payment_methods");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_payments");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_product_ads");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_sales");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_sizegroups");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_statistics");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_textads");
		
		@JFolder::delete($path . DIRECTORY_SEPARATOR . "views" 	. DIRECTORY_SEPARATOR . "vma_traffic");
		
	}

	/**
	 * Method to delete a menu item, in a nested-set compatible manner
	 */

	public function deleteMenuItem($initialQuery) {
		
		$database	= &JFactory::getDBO();
		
		$query		= "SELECT * FROM #__menu WHERE " . $initialQuery;
		
		$database->setQuery($query);
		
		$menuItems	= $database->loadObjectList();
		
		if ($menuItems) {
			
			foreach ($menuItems as $menuItem) {
				
				// delete item
				
				$query	= "DELETE FROM #__menu WHERE `id` = '" . $menuItem->id . "'";
				
				$database->setQuery($query);
				
				$database->query();
				
				$itemRemoved = $database->getAffectedRows();
				
				// adjust nested set accordingly
				
				if ($itemRemoved) {
					
					// decrement parent width
				
					$query = "UPDATE #__menu SET `rgt` = rgt - 2 WHERE `id` = '" . $menuItem->parent_id . "'";
					
					$database->setQuery($query);
					
					$database->query();
					
					// adjust lft of following siblings
					
					$query = "UPDATE #__menu SET `lft` = lft - 2 WHERE `lft` > '" . $menuItem->rgt . "'";
					
					$database->setQuery($query);
					
					$database->query();
					
					// adjust rgt of following siblings
					
					$query = "UPDATE #__menu SET `rgt` = rgt - 2 WHERE `rgt` > '" . $menuItem->rgt . "'";
					
					$database->setQuery($query);
					
					$database->query();
					
				}
				
			}
			
		}
		
		// confirm the operation
		
		return true;
		
	}
	
	/**
	 * Method to insert a menu item, in a nested-set compatible manner
	 */
	 
	public function insertMenuItem($initialQuery) {
		
		$database	= &JFactory::getDBO();
		
		// get highest rgt of root
		
		$query		= "SELECT MAX(`rgt`) FROM `#__menu` WHERE `id` > 1";
		
		$database->setQuery($query);
		
		$highestRGT = $database->loadResult();
		
		// fill variables in the query
		
		$newLFT		= $highestRGT + 1;
		
		$newRGT		= $highestRGT + 2;
		
		$newQuery	= str_replace("__LFT__", $newLFT, $initialQuery);
		
		$newQuery	= str_replace("__RGT__", $newRGT, $newQuery);
		
		// insert the new item
		
		$database->setQuery($newQuery);
		
		$database->query();
		
		$inserted	= $database->insertid();
		
		// increment parent rgt
		
		if ($inserted) {
			
			$query	= "UPDATE #__menu SET `rgt` = rgt + 2 WHERE `id` = 1";
			
			$database->setQuery($query);
			
			$database->query();
			
		}
		
		// confirm the operation
		
		return true;
		
	}
	
}

?>