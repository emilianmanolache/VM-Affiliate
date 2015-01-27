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

// get helper and settings

global $vmaHelper, $vmaSettings;

// start the virtuemart administration area

AdminUIHelper::startAdminArea($this); 

// initiate other variables

$link 				= $vmaHelper->getAdminLink();

$document			= &JFactory::getDocument();

$editor 			= &JFactory::getEditor();

$database			= &JFactory::getDBO();

// display the title

JToolBarHelper::title(JText::_("CONFIGURATION"), 'head adminPreferencesIcon');

// load the tooltip plugin

JHTML::_('behavior.tooltip');

$tooltipImage			= $vmaHelper->_website . "components/com_affiliate/assets/images/info.png";

// reload the global settings object

$query					= "SELECT * FROM #__vm_affiliate_settings WHERE `setting` = '1'";

$database->setQuery($query);

$GLOBALS["vmaSettings"] = $vmaSettings = $database->loadObject();

// get currency symbol

$currencySymbol			= $vmaHelper->getCurrencySymbol();

// add tabs function

$tabsFunctions		= 'tabCaption										= new Array();
					  
					  tabCaption["affiliateGeneralTab"]					= "' . JText::_("GENERAL", true) 					. '";
					  
					  tabCaption["affiliateTrackingTab"]				= "' . JText::_("TRACKING", true) 					. '";
					  
					  tabCaption["affiliateTermsTab"] 					= "' . JText::_("AFFILIATE_PROGRAM_TERMS", true) 	. '";
					  
					  function focusTab(id) {
						  
						  // get all containers
							
						  var containers = $$("div.affiliateTab");
						  
						  // hide all containers
						  
						  containers.each(function(container) {
							  
							  container.setStyle("display", "none");
							  
						  });
						  
						  // show this container
						  
						  $(id + "Container").setStyle("display", "block");
						  
						  // get all tab titles
							
						  var tabs = new Array($("affiliateGeneralTab"), $("affiliateTrackingTab"), $("affiliateTermsTab"));
						  
						  // make all tab titles links
						  
						  tabs.each(function(tab) {
							  
							  tab.empty();
							  
							  var newTabTitle = new Element("a", {
								  
								  					"href": "javascript:void(0);",
													
													"events": {
														
														"click": function() {
															
															focusTab(this.getParent().id);
															
														}
														
													}
													
							  					}).appendText(tabCaption[tab.id]).injectInside(tab);
							  
						  });
							
						  // empty this tab title
						  
						  $(id).empty();
						  
						  // make this tab title normal text
						  
						  $(id).appendText(tabCaption[id]);
						  
					  }
					  
					  window.addEvent("domready", function() {
						  
						  focusTab("affiliateGeneralTab");
						  
					  });';

$document->addScriptDeclaration($tabsFunctions);
	  
// add amount validation function

$amountValidation 	= $vmaHelper->numberValidationFunction("float");
						  
$document->addScriptDeclaration($amountValidation);

// add number validation function

$numberValidation 	= $vmaHelper->numberValidationFunction("integer");

$document->addScriptDeclaration($numberValidation);

// add calendar class

$document->addScript($vmaHelper->_website 		. "components/com_affiliate/assets/js/calendar.js");

// add calendar stylesheet

$document->addStyleSheet($vmaHelper->_website 	. "components/com_affiliate/assets/css/calendar.css");

// configure the calendar

$dayCalendar		= 'window.addEvent("domready", function() {
	
							new Calendar({ "affiliatePaymentDay": "j" }, { navigation: 0, tweak: { x: 6, y: 1 } });
							
						});';

$document->addScriptDeclaration($dayCalendar);

// add configuration form validation

$formValidation		= 'function validateConfigurationForm() {
	
							if (document.getElementById("affiliateLinkFeedString").value == "") {
								
								alert("' . JText::_("PROVIDE_LINK_FEED_STRING", true) . '");
								
								return false;
								
							}
							
							if (document.getElementById("affiliateReferralCookieLifetime").value == "") {
								
								alert("' . JText::_("PROVIDE_COOKIE_LIFETIME", true) . '");
								
								return false;
								
							}
							
							return true;
							
					  }';
					  
$document->addScriptDeclaration($formValidation);

?>

<form action="<?php echo $link . "_configuration"; ?>" method="post" name="adminForm" id="adminForm">

	<div class="affiliateAdminPage">
     
    	<div style="text-align: center;">
            
            <span id="affiliateGeneralTab"><?php 
			
				echo JText::_("GENERAL"); 
				
			?></span><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><span id="affiliateTrackingTab"><a href="javascript:void(0);"><?php 
				
				echo JText::_("TRACKING");
				                
            ?></a></span><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><span id="affiliateTermsTab"><a href="javascript:void(0);"><?php
            
				echo JText::_("AFFILIATE_PROGRAM_TERMS");
				
			?></a></span>
        
        </div>
            
        <div id="affiliateDetailsPanel" class="affiliateConfigurationForm">
    
    		<div id="affiliateGeneralTabContainer" class="affiliateTab">
            
            	<div class="affiliateDetailsDescription" style="padding-top: 0px;">
            
                    <h2><strong><?php echo JText::_("REGISTRATION"); ?></strong></h2>
                    
                </div>
                        
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                    	<div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("ALLOW_AFFILIATE_REGISTRATIONS_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                        
                        <label for="affiliateAllowRegistrations"><?php echo JText::_("ALLOW_AFFILIATE_REGISTRATIONS"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <input id="affiliateAllowRegistrations" name="allow_signups" type="checkbox" value="1" 
                        
                               <?php echo $vmaSettings->allow_signups ? "checked=\"checked\"" : NULL; ?> />
                        
                    </div>
                
                </div>
                
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                    	<div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("AUTO_APPROVE_AFFILIATES_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                    
                        <label for="affiliateAutomaticallyApproveAffiliates"><?php echo JText::_("AUTO_APPROVE_AFFILIATES"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <input id="affiliateAutomaticallyApproveAffiliates" name="auto_block" type="checkbox" value="1" 
                        
                               <?php echo !$vmaSettings->auto_block ? "checked=\"checked\"" : NULL; ?> />
                        
                    </div>
                    
                </div>
                
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                    	<div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("INITIAL_BONUS_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                    
                        <label for="affiliateInitialBonus"><?php echo JText::_("INITIAL_BONUS"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <input id="affiliateInitialBonus" name="initial_bonus" type="text" value="<?php echo $vmaSettings->initial_bonus; ?>" 
                        
                               onkeydown="return validateNumberInput(event, this);" style="text-align: right;" />&nbsp;<strong><?php
                               
                               echo $currencySymbol; ?></strong>
                        
                    </div>
                    
                </div>
				
                <div class="affiliateDetailsDescription">
            
                    <h2><strong><?php echo JText::_("PAYOUT_SETTINGS"); ?></strong></h2>
                    
                </div>
                        
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                    	<div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("MINIMUM_BALANCE_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                        
                        <label for="affiliateMinimumBalance"><?php echo JText::_("MINIMUM_BALANCE"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <input id="affiliateMinimumBalance" name="pay_balance" type="text" value="<?php echo $vmaSettings->pay_balance; ?>" 
                        
                               onkeydown="return validateNumberInput(event, this);" style="text-align: right;" />&nbsp;<strong><?php
                               
                               echo $currencySymbol; ?></strong>
                        
                    </div>
                    
                </div>
                
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                    	<div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("PAYOUT_DAY_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                    
                        <label for="affiliatePaymentDay"><?php echo JText::_("PAYOUT_DAY"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <input id="affiliatePaymentDay" name="pay_day" type="text" value="<?php echo $vmaSettings->pay_day; ?>" style="text-align: right;" />
                        
                    </div>
                    
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">&nbsp;</div>
                    
                    <div class="affiliateDetailsValue affiliateCommissionInputs" style="margin-top: 10px;">
                    
                        <span class="affiliateButton">

                            <input type="submit" value="<?php echo JText::_("JAPPLY"); ?>" style="width: 84px;" 
                            
                            class="affiliateButton affiliateSaveButton" onclick="if (!validateConfigurationForm()) { return false; }" /></span>
                        
                    </div>
                
                </div>
                
            </div>
        
        	<div style="clear: both;"></div>
        
            <div id="affiliateTrackingTabContainer" class="affiliateTab">
            
            	<div class="affiliateDetailsDescription" style="padding-top: 0px;">
            
                    <h2><strong><?php echo JText::_("ONLINE"); ?></strong></h2>
                    
                </div>
                        
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                    	<div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("TRACK_WHICH_REFERRER_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                    
                        <label for="affiliateTrackWhichReferrer"><?php echo JText::_("TRACK_WHICH_REFERRER"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <select id="affiliateTrackWhichReferrer" name="track_who">
                        
                            <option value="1" <?php echo $vmaSettings->track_who == 1 ? "selected=\"selected\"" : NULL; ?>><?php echo JText::_("THE_FIRST"); ?></option>
                            
                            <option value="2" <?php echo $vmaSettings->track_who == 2 ? "selected=\"selected\"" : NULL; ?>><?php echo JText::_("THE_LAST"); ?></option>
                            
                        </select>
                        
                    </div>
                    
                </div>
                
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                    	<div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("LINK_FEED_STRING_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                    
                        <label for="affiliateLinkFeedString"><?php echo JText::_("LINK_FEED_STRING"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <input id="affiliateLinkFeedString" name="link_feed" type="text" value="<?php echo $vmaSettings->link_feed; ?>" style="text-align: left;" />
                        
                    </div>
                    
                </div>
                
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                    	<div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("REFERRAL_COOKIE_LIFETIME_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                    
                        <label for="affiliateReferralCookieLifetime"><?php echo JText::_("REFERRAL_COOKIE_LIFETIME"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <input id="affiliateReferralCookieLifetime" name="cookie_time" type="text" value="<?php echo $vmaSettings->cookie_time; ?>" 
                        
                               onkeydown="return validateNumber(event, this, 20);" style="text-align: left;" />
                        
                    </div>
                    
                </div>
				
                <div class="affiliateDetailsDescription">
            
                    <h2><strong><?php echo JText::_("OFFLINE"); ?></strong></h2>
                    
                </div>
                        
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                    	<div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("ENABLE_OFFLINE_TRACKING_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                    
                        <label for="affiliateEnableOfflineTracking"><?php echo JText::_("ENABLE_OFFLINE_TRACKING"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <input id="affiliateEnableOfflineTracking" name="offline_tracking" type="checkbox" value="1" 
                        
                               <?php echo $vmaSettings->offline_tracking ? "checked=\"checked\"" : NULL; ?> />
                        
                    </div>
                    
                </div>
                
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                    	<div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("OFFLINE_TRACKING_TYPE_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                    
                        <label for="affiliateOfflineTrackingType"><?php echo JText::_("OFFLINE_TRACKING_TYPE"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <select id="affiliateOfflineTrackingType" name="offline_type">
                        
                            <option value="1" <?php echo $vmaSettings->offline_type == 1 ? "selected=\"selected\"" : NULL; ?>><?php echo JText::_("JGLOBAL_USERNAME"); ?></option>
                            
                            <option value="2" <?php echo $vmaSettings->offline_type == 2 ? "selected=\"selected\"" : NULL; ?>><?php echo JText::_("NAME"); ?></option>
                            
                            <option value="3" <?php echo $vmaSettings->offline_type == 3 ? "selected=\"selected\"" : NULL; ?>><?php echo JText::_("DISCOUNT_COUPON"); ?></option>
                            
                        </select>
                        
                    </div>
                    
                </div>
                
                <div class="affiliateDetailsDescription">
            
                    <h2><strong><?php echo JText::_("MULTI_TIER"); ?></strong></h2>
                    
                </div>
                        
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    	
                        <div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("ENABLE_MULTI_TIER_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                    
                        <label for="affiliateEnableMultiTierTracking"><?php echo JText::_("ENABLE_MULTI_TIER"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <input id="affiliateEnableMultiTierTracking" name="multi_tier" type="checkbox" value="1" 
                        
                               <?php echo $vmaSettings->multi_tier ? "checked=\"checked\"" : NULL; ?>  />
                        
                    </div>
                    
                </div>
                
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                    	<div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("MAXIMUM_TIERS_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                    
                        <label for="affiliateMaximumTiers"><?php echo JText::_("MAXIMUM_TIERS"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <select id="affiliateMaximumTiers" name="tier_level">
                        
                            <option value="2" <?php echo $vmaSettings->tier_level == 2 ? "selected=\"selected\"" : NULL; ?>>2</option>
                            
                            <option value="3" <?php echo $vmaSettings->tier_level == 3 ? "selected=\"selected\"" : NULL; ?>>3</option>
                            
                            <option value="4" <?php echo $vmaSettings->tier_level == 4 ? "selected=\"selected\"" : NULL; ?>>4</option>
                            
                            <option value="5" <?php echo $vmaSettings->tier_level == 5 ? "selected=\"selected\"" : NULL; ?>>5</option>
                            
                        </select>
                        
                    </div>
                    
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">&nbsp;</div>
                    
                    <div class="affiliateDetailsValue affiliateCommissionInputs" style="margin-top: 10px;">
                    
                        <span class="affiliateButton">

                            <input type="submit" value="<?php echo JText::_("JAPPLY"); ?>" style="width: 84px;" 
                            
                            class="affiliateButton affiliateSaveButton" onclick="if (!validateConfigurationForm()) { return false; }" /></span>
                        
                    </div>
                
                </div>
                
            </div>
    
            <div style="clear: both;"></div>
            
            <div id="affiliateTermsTabContainer" class="affiliateTab">
                
                <div class="affiliateDetailsDescription" style="padding-top: 0px;">
            
                    <h2><strong><?php echo JText::_("AFFILIATE_PROGRAM_TERMS"); ?></strong></h2>
                    
                </div>
                        
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                    	<div class="affiliateInfoIcon">
                    
							<?php echo JHTML::tooltip(JText::_("MUST_AGREE_TO_TERMS_DESC"), "", $tooltipImage); ?>
                            
                        </div>
                    
                        <label for="affiliateMustAgreeToTerms"><?php echo JText::_("MUST_AGREE_TO_TERMS"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <input id="affiliateMustAgreeToTerms" name="must_agree" type="checkbox" value="1" 
                        
                               <?php echo $vmaSettings->must_agree ? "checked=\"checked\"" : NULL; ?> />
                        
                    </div>
                    
                </div>
                
                <div class="affiliateFormRow">
    
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateProgramTerms"><?php echo JText::_("CONTENT"); ?>:</label>
                        
                    </div>
                    
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
					
						<?php echo $editor->display('affiliateProgramTerms', $vmaSettings->aff_terms, '444', '300', '60', '20', false); ?>
                        
					</div>
                
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">&nbsp;</div>
                    
                    <div class="affiliateDetailsValue affiliateCommissionInputs" style="margin-top: 10px;">
                        
                        <span class="affiliateButton">
                            
                            <input type="hidden" name="option" 			value="com_virtuemart" />
                
                            <input type="hidden" name="view" 			value="vma_configuration" />
                            
                            <input type="hidden" name="task"			value="update" />
                
                            <input type="submit" value="<?php echo JText::_("JAPPLY"); ?>" style="width: 84px;" 
                            
                            class="affiliateButton affiliateSaveButton" onclick="if (!validateConfigurationForm()) { return false; }" /></span>
                        
                        	<?php echo JHTML::_('form.token'); ?>
                            
                    </div>
                
                </div>
				
            </div>
        
        </div>
        
		<div style="clear: both;"></div>
        
    </div>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>