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

// initialize required variables

global $ps_vma, $sess, $vmaSettings;

$document 			= &JFactory::getDocument();

$link				= $ps_vma->getAdminLink();

// get affiliate's information

$affiliateID		= &JRequest::getVar("affiliate_id", 	"");

if ($affiliateID) {
	
	$query			= "SELECT * FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
	
	$ps_vma->_db->setQuery($query);
	
	$affiliate		= $ps_vma->_db->loadObject();

}

// get tier parameter

$tier				= &JRequest::getInt("tier",				1);

$tier 				= $tier > $vmaSettings->tier_level ? $vmaSettings->tier_level : $tier;

// get commission rates

$commissionRates	= $ps_vma->getCommissionRates($affiliateID ? $affiliateID : NULL);

$commissionRates	= $tier > 1 	? $commissionRates["tiers"][$tier - 2] 		: $commissionRates["affiliate"];

if ($affiliateID) {
	
	$generalRates	= $ps_vma->getCommissionRates();
	
	$generalRates	= $generalRates["affiliate"];
	
}

// get discount rate

if ($vmaSettings->offline_tracking && $vmaSettings->offline_type == 3 && $tier == 1) {
	
	$discountRate		= $ps_vma->getDiscountRate($affiliateID ? $affiliateID : NULL);
	
	$generalRate		= $ps_vma->getDiscountRate();

}

// get currency symbol

$currencySymbol		= $ps_vma->getCurrencySymbol();

// determine page title

if ($affiliateID) {
	
	$pageTitle		= ucwords(JText::_("COMMISSION_RATES_FOR") 	. " " . $affiliate->fname 		. " " . $affiliate->lname);
	
}

else if ($tier > 1) {
	
	$pageTitle		= ucwords(JText::_("COMMISSION_RATES") 		. " " . JText::_("FROM_TIER") 	. " " . $tier);
	
}

else {
	
	$pageTitle		= JText::_("GENERAL_COMMISSION_RATES");
	
}

// add discount symbol update function

if ($vmaSettings->offline_tracking && $vmaSettings->offline_type == 3 && $tier == 1) {
	
	$discountSymbol	= 'function updateDiscountSymbol(type) {
							
							$("affiliateDiscountSymbol").innerHTML = type == 2 ? "%" : "' . $currencySymbol . '";
							
					   }';
	
	$document->addScriptDeclaration($discountSymbol);
					
}

// add defaults checkbox toggle function

if ($affiliateID) {
	
	$checkboxToggle = 'function toggleDefaults() {

							// get defaults checkbox status
							
							var useDefaults 														= document.getElementById("affiliateUseDefaults").checked;
							
							// store previously edited values
							
							if (useDefaults) {
								
								window.affiliateCommissions											= new Array();
								
								window.affiliateCommissions.per_click_fixed 						= document.getElementById("affiliatePerClick").value;
								
								window.affiliateCommissions.per_unique_click_fixed 					= document.getElementById("affiliatePerUniqueClick").value;
								
								window.affiliateCommissions.per_sale_fixed 							= document.getElementById("affiliatePerSaleFixed").value;
								
								window.affiliateCommissions.per_sale_percentage 					= document.getElementById("affiliatePerSalePercentage").value;
							
								'
								
									. (($vmaSettings->offline_tracking && $vmaSettings->offline_type == 3 && $tier == 1) ? 
									
								'
								
								window.affiliateDiscount											= new Array();
								
								window.affiliateDiscount.type										= document.getElementById("affiliateDiscountType").value;
								
								window.affiliateDiscount.amount										= document.getElementById("affiliateDiscountAmount").value;
								
								' 
								
									: NULL) .
								
								'
								
							}
							
							// change input values
							
							if (useDefaults) {
								
								document.getElementById("affiliatePerClick").value					= "' . $generalRates["per_click_fixed"] 			. '";
								
								document.getElementById("affiliatePerUniqueClick").value			= "' . $generalRates["per_unique_click_fixed"] 		. '";
								
								document.getElementById("affiliatePerSaleFixed").value				= "' . $generalRates["per_sale_fixed"] 				. '";
								
								document.getElementById("affiliatePerSalePercentage").value			= "' . $generalRates["per_sale_percentage"] 		. '";
								
								'
								
									. (($vmaSettings->offline_tracking && $vmaSettings->offline_type == 3 && $tier == 1) ? 
									
								'
								
								document.getElementById("affiliateDiscountAmount").value			= "' . $generalRate["discount_amount"]				. '";
								
								document.getElementById("affiliateDiscountType").options["' . $generalRate["discount_type"] . '" == "1"	? 0 : 1].selected = true;
								
								' 
								
									: NULL) .
								
								'
								
							}
							
							else {
								
								// restore previous values

								if (typeof(window.affiliateCommissions) != "undefined") {
									
									document.getElementById("affiliatePerClick").value				= window.affiliateCommissions.per_click_fixed;
								
									document.getElementById("affiliatePerUniqueClick").value		= window.affiliateCommissions.per_unique_click_fixed;
									
									document.getElementById("affiliatePerSaleFixed").value			= window.affiliateCommissions.per_sale_fixed;
									
									document.getElementById("affiliatePerSalePercentage").value		= window.affiliateCommissions.per_sale_percentage;
									
								}
								
								// restore affiliates commission rates
								
								else {
									
									document.getElementById("affiliatePerClick").value				= "' . $affiliate->per_click_fixed 					. '";
									
									document.getElementById("affiliatePerUniqueClick").value		= "' . $affiliate->per_unique_click_fixed 			. '";
									
									document.getElementById("affiliatePerSaleFixed").value			= "' . $affiliate->per_sale_fixed 					. '";
									
									document.getElementById("affiliatePerSalePercentage").value		= "' . $affiliate->per_sale_percentage 				. '";
									
								}
								
								'
								
									. (($vmaSettings->offline_tracking && $vmaSettings->offline_type == 3 && $tier == 1) ? 
									
								'
								
								if (typeof(window.affiliateDiscount) != "undefined") {
									
									document.getElementById("affiliateDiscountAmount").value		= window.affiliateDiscount.amount;
								
									document.getElementById("affiliateDiscountType").options[window.affiliateDiscount.type == 1	? 0 : 1].selected = true;
								
								}
								
								else {
									
									document.getElementById("affiliateDiscountAmount").value		= "' . $affiliate->discount_amount 					. '";
									
									document.getElementById("affiliateDiscountType").options["'			 . $affiliate->discount_type . '" == "1" ? 0 : 1].selected = true;
									
								}
								
								' 
								
									: NULL) .
								
								'
								
							}
							
							// toggle inputs statuses
							
							document.getElementById("affiliatePerClick").disabled					= useDefaults;
							
							document.getElementById("affiliatePerUniqueClick").disabled				= useDefaults;
							
							document.getElementById("affiliatePerSaleFixed").disabled				= useDefaults;
							
							document.getElementById("affiliatePerSalePercentage").disabled 			= useDefaults;
							
							'
								
								. (($vmaSettings->offline_tracking && $vmaSettings->offline_type == 3 && $tier == 1) ? 
								
							'
							
							updateDiscountSymbol(document.getElementById("affiliateDiscountType").value);
							
							document.getElementById("affiliateDiscountType").disabled				= useDefaults;
							
							document.getElementById("affiliateDiscountAmount").disabled 			= useDefaults;
							
							' 
							
								: NULL) .
							
							'
							
					  }';
					  
	$document->addScriptDeclaration($checkboxToggle);

}

// add commission rates form validation function

$numberValidation = $ps_vma->numberValidationFunction("float");
						  
$document->addScriptDeclaration($numberValidation);

?>
            
<form action="<?php echo $link . "page=vma.commission_rates_form"; ?>" method="post" name="adminForm">
            
    <div class="affiliateAdminPage">
    
        <div class="adminPanelTitleIcon" id="adminCommissionRatesIcon">
        
            <h1 class="adminPanelTitle">
            
                <?php echo $pageTitle; ?>
                
            </h1>
            
        </div>
        
        <div class="affiliateTopMenu">
            
            <div class="affiliateTopMenuLink">
            
                <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" 
                
                      class="affiliateTopMenuLinkItem">
                
                    <a href="<?php echo $link . "page=vma.affiliate_list"; ?>"><?php echo JText::_("MANAGE_AFFILIATES"); ?></a>
                    
                </span>
                
            </div>
            
        </div>
        
        <div style="clear: both;"></div>
    
        <br />
        
        <?php if (!$affiliateID && $vmaSettings->multi_tier) { ?>
        
            <div style="text-align: center;">
            
                <span><?php 
                
                    if ($tier > 1) { 
                    
                        ?><a href="<?php echo $link . "page=vma.commission_rates_form"; ?>"><?php 
                    
                    } 
                    
                    echo JText::_("GENERAL_COMMISSION_RATES"); 
                    
                    if ($tier > 1) { 
                    
                        ?></a><?php 
                        
                    }
                    
                ?></span><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><span><?php 
                
                    if ($tier < 2) { 
                    
                        ?><a href="<?php echo $link . "page=vma.commission_rates_form&amp;tier=2"; ?>"><?php 
                        
                    }
                    
                    echo ucwords(JText::_("FROM_TIER"));
                    
                    if ($tier < 2) { 
                    
                        ?></a><?php 
                        
                    } 
                    
                ?></span><span>:&nbsp;&nbsp;</span><?php for ($i = 2; $i <= $vmaSettings->tier_level; $i++) { 
                
                          ?><span><?php 
                      
                          if ($tier <> $i) { 
                          
                              ?><a href="<?php echo $link . "page=vma.commission_rates_form&amp;tier=" . $i; ?>"><?php 
                              
                          } 
                          
                          echo $i; 
                          
                          if ($tier <> $i) { 
                          
                              ?></a><?php 
                              
                          } 
                          
                          ?></span><?php 
                          
                          if ($i < $vmaSettings->tier_level) { 
                          
                              ?><span>&nbsp;&nbsp;/&nbsp;</span><?php 
                              
                          } ?>
                    
                <?php } ?></span>
            
            </div>
        
        <?php } ?>
        
        <div>
			
            <?php if ($affiliateID) { ?>
            
                <div>
                    
                    <span>
                    
						<input type="checkbox" name="use_defaults" value="1" id="affiliateUseDefaults" onmouseup="setTimeout('toggleDefaults()', 100);" 
						
							   onkeyup="setTimeout('toggleDefaults()', 100);" <?php echo $affiliate->use_defaults ? "checked=\"checked\"" : NULL; ?> />
                    
                    </span>
                    
                    <label for="affiliateUseDefaults"><?php echo JText::_("USE_GENERAL_COMMISSION_RATES"); ?></label>
                    
                </div>
                
                <br />
            
            <?php } ?>
            
            <div class="affiliateDetailsDescription" style="padding-top: 0px;">
            
                <h2><strong><?php echo JText::_("PER_CLICK"); ?></strong></h2>
                
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliatePerClick"><?php echo JText::_("PER_CLICK"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue affiliateCommissionInputs">
                
                    <input id="affiliatePerClick" name="per_click_fixed" type="text" value="<?php echo $commissionRates["per_click_fixed"]; ?>" 
                    
                    	   onkeydown="return validateNumberInput(event, this);" <?php echo @$affiliate->use_defaults ? "disabled=\"disabled\"" : NULL; ?> />&nbsp;<strong><?php
					
					echo $currencySymbol; ?></strong>
                    
                </div>
            
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliatePerUniqueClick"><?php echo JText::_("PER_UNIQUE_CLICK"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue affiliateCommissionInputs">
                
                    <input id="affiliatePerUniqueClick" name="per_unique_click_fixed" type="text" 
                    
                    	   value="<?php echo $commissionRates["per_unique_click_fixed"]; ?>" 
                    
                    	   onkeydown="return validateNumberInput(event, this);" <?php echo @$affiliate->use_defaults ? "disabled=\"disabled\"" : NULL; ?> />&nbsp;<strong><?php
					
					echo $currencySymbol; ?></strong>
                    
                </div>
            
            </div>
        	
            <div class="affiliateDetailsDescription">
            
                <h2><strong><?php echo JText::_("PER_SALE"); ?></strong></h2>
                
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliatePerSaleFixed"><?php echo JText::_("FIXED_RATE"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue affiliateCommissionInputs">
                
                    <input id="affiliatePerSaleFixed" name="per_sale_fixed" type="text" value="<?php echo $commissionRates["per_sale_fixed"]; ?>" 
                    
                    	   onkeydown="return validateNumberInput(event, this);" <?php echo @$affiliate->use_defaults ? "disabled=\"disabled\"" : NULL; ?> />&nbsp;<strong><?php
					
					echo $currencySymbol; ?></strong>
                    
                </div>
            
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliatePerSalePercentage"><?php echo JText::_("PERCENTAGE"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue affiliateCommissionInputs">
                
                    <input id="affiliatePerSalePercentage" name="per_sale_percentage" type="text" 
                    
                    	   value="<?php echo $commissionRates["per_sale_percentage"]; ?>" onkeydown="return validateNumberInput(event, this);" 
                    
                    	   <?php echo @$affiliate->use_defaults ? "disabled=\"disabled\"" : NULL; ?> />&nbsp;<strong>%</strong>
                    
                </div>
            
            </div>
            
            <?php 
			
				if ($vmaSettings->offline_tracking && $vmaSettings->offline_type == 3 && $tier == 1) {
					
			?>
            
                <div class="affiliateDetailsDescription">
                
                    <h2><strong><?php echo JText::_("CUSTOMER_DISCOUNT"); ?></strong></h2>
                    
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateDiscountType"><?php echo JText::_("TYPE"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                    	<select id="affiliateDiscountType" name="discount_type" onchange="updateDiscountSymbol(this.value);" <?php 
						
							echo @$affiliate->use_defaults ? "disabled=\"disabled\"" : NULL; ?>>
                        
                        	<option value="1" <?php echo $discountRate["discount_type"] == 1 ? "selected=\"selected\"" : NULL; ?>><?php echo JText::_("FIXED"); ?></option>
                            
                            <option value="2" <?php echo $discountRate["discount_type"] == 2 ? "selected=\"selected\"" : NULL; ?>><?php echo JText::_("PERCENTAGE"); ?></option>
                            	
                        </select>
                        
                    </div>
                
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateDiscountAmount"><?php echo JText::_("AMOUNT"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue affiliateCommissionInputs">
                    
                        <input id="affiliateDiscountAmount" name="discount_amount" type="text" 
                        
                               value="<?php echo $discountRate["discount_amount"]; ?>" onkeydown="return validateNumberInput(event, this);" 
                        
                               <?php echo @$affiliate->use_defaults ? "disabled=\"disabled\"" : NULL; ?> />&nbsp;<strong><span id="affiliateDiscountSymbol"><?php
                               
							   		 echo $discountRate["discount_type"] == 2 ? "%" : $currencySymbol; ?></span></strong>
                        
                    </div>
                
                </div>
            
            <?php
			
				}
				
			?>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">&nbsp;</div>
                
                <div class="affiliateDetailsValue affiliateCommissionInputs" style="margin-top: 10px;">
                
                    <span class="affiliateButton">
                        
                        <input type="hidden" name="option" 			value="com_virtuemart" />
            
                        <input type="hidden" name="pshop_mode" 		value="admin" />
            
                        <input type="hidden" name="page" 			value="vma.commission_rates_form" />
                        
                        <input type="hidden" name="func"			value="vmaCommissionRatesUpdate" />
                        
                        <input type="hidden" name="task"			value="" />
                        
                        <?php if ($affiliateID) { ?>
                        
                            <input type="hidden" name="affiliate_id"	value="<?php echo $affiliate->affiliate_id; ?>" />
                        
                        <?php } ?>
                        
                        <input type="hidden" name="tier"			value="<?php echo $tier; ?>" />
                        
                        <input type="hidden" name="vmtoken"			value="<?php echo vmSpoofValue($sess->getSessionId()); ?>" />
            
                        <input type="submit" value="<?php echo JText::_("SAVE"); ?>" style="width: 84px;" 
                        
                        class="affiliateButton affiliateSaveButton" onclick="if (!validatePaymentMethodForm()) { return false; }" /></span>
                    
                </div>
            
            </div>
        
        </div>
    
        <div style="clear: both;"></div>
        
    </div>

</form>