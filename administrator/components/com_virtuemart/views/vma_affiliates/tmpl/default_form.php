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

// display the title

JToolBarHelper::title($this->formType == "Add" ? JText::_("ADD_AFFILIATE") : JText::_("EDIT_DETAILS") . ": " . 

					  $this->affiliate->fname . " " . $this->affiliate->lname, 'head adminAffiliate' . $this->formType . 'Icon');

// initiate other variables

$link = $vmaHelper->getAdminLink();

// add form validation function

$document = &JFactory::getDocument();

$validationFunction = 'function validateAffiliateForm() {';

if ($this->formType == "Add") {
							
	$validationFunction .= 'if (document.getElementById("affiliateUsername").value == "") {
								  
								alert( "' . JText::_("PROVIDE_USERNAME", true) . '" );
								
								return false;
								
							} 
							
							if (document.getElementById("affiliatePassword").value == "") {
								
								alert( "' . JText::_("PROVIDE_PASSWORD", true) . '" );
								
								return false;
								
							}
							
							if (document.getElementById("affiliateVPassword").value == "") {
								
								alert( "' . JText::_("RETYPE_PASSWORD", true) . '" );
								
								return false;
								
							}
							
							if (document.getElementById("affiliatePassword").value != document.getElementById("affiliateVPassword").value) {
								
								 alert( "' . JText::_("PASSWORDS_DIFFER", true) . '" );
								 
								 return false;
								 
							}';
							  
}
						  
$validationFunction .= 'if (document.getElementById("affiliateEmail").value == "") {
							  
							alert( "' . JText::_("PROVIDE_EMAIL_ADDRESS", true) . '" );
							
							return false;
							
						}
						
						if (document.getElementById("affiliateFirstName").value == "") {
							
							alert( "' . JText::_("PROVIDE_FIRST_NAME", true) . '" );
							
							return false;
							
						}
						
						if (document.getElementById("affiliateLastName").value == "") {
							
							alert( "' . JText::_("PROVIDE_LAST_NAME", true) . '" );
							
							return false;
							
						}
						
						if (document.getElementById("affiliateStreet").value == "") {
							
							alert( "' . JText::_("PROVIDE_ADDRESS", true) . '" );
							
							return false;
							
						}
						
						if (document.getElementById("affiliateCity").value == "") {
							
							alert( "' . JText::_("PROVIDE_CITY", true) . '" );
							
							return false;
							
						}
						
						if (document.getElementById("affiliateCountry").value == "") {
							
							alert( "' . JText::_("PROVIDE_COUNTRY", true) . '" );
							
							return false;
							
						}
						
						if (document.getElementById("affiliateZipCode").value == "") {
							
							alert( "' . JText::_("PROVIDE_ZIPCODE", true) . '" );
							
							return false;
							
						}
						
						return true; 
						
					}';
						  
$document->addScriptDeclaration($validationFunction);

?>

<form action="<?php echo $link . "_affiliates"; ?>" method="post" name="adminForm">

    <div class="affiliateAdminPage">
        
        <?php if ($this->formType == "Update") { ?>
        
            <div class="affiliateTopMenu">
                
                <div class="affiliateTopMenuLink">
                
                    <span style="background: url(<?php echo $vmaHelper->_website; ?>components/com_affiliate/views/panel/tmpl/images/password_small.png) no-repeat left top;" 
                    
                          class="affiliateTopMenuLinkItem">
                    
                        <a href="<?php echo $link . "_affiliates&amp;task=password&amp;affiliate_id=" 			. $this->affiliate->affiliate_id; ?>"><?php echo JText::_("CHANGE_PASSWORD"); ?></a>
                        
                    </span>
                    
                </div>
                
                <div class="affiliateTopMenuLink">
                
                    <span style="background: url(<?php echo $vmaHelper->_website; ?>components/com_affiliate/assets/images/preferences_small.png) no-repeat left top;" 
                    
                          class="affiliateTopMenuLinkItem">
                    
                        <a href="<?php echo $link . "_affiliates&amp;task=preferences&amp;affiliate_id=" 		. $this->affiliate->affiliate_id; ?>"><?php echo JText::_("PREFERENCES"); ?></a>
                        
                    </span>
                    
                </div>
                
                <div class="affiliateTopMenuLink">
                
                    <span style="background: url(<?php echo $vmaHelper->_website; ?>components/com_affiliate/assets/images/commissions_small.png) no-repeat left top;" 
                    
                          class="affiliateTopMenuLinkItem">
                    
                        <a href="<?php echo $link . "_commission_rates&amp;affiliate_id=" 	. $this->affiliate->affiliate_id; ?>"><?php echo JText::_("COMMISSION_RATES"); ?></a>
                        
                    </span>
                    
                </div>
                
                <div class="affiliateTopMenuLink">
                
                    <span style="background: url(<?php echo $vmaHelper->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" 
                    
                          class="affiliateTopMenuLinkItem">
                    
                        <a href="<?php echo $link . "_affiliates"; ?>"><?php echo JText::_("MANAGE_AFFILIATES"); ?></a>
                        
                    </span>
                    
                </div>
                
            </div>
            
            <div style="clear: both;"></div>
        
        <?php } ?>
        
        <br />
    
        <div>
        
            <div class="affiliateDetailsDescription" style="padding-top: 0px;">
            
            	<h2><strong><?php echo JText::_("ACCOUNT_INFO"); ?></strong></h2>
                
			</div>
            
            <?php if ($this->formType == "Add") { ?>
            
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateUsername"><?php echo JText::_("JGLOBAL_USERNAME"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliateUsername" type="text" name="username" />*
                        
                    </div>
                
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliatePassword"><?php echo JText::_("JGLOBAL_PASSWORD"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliatePassword" type="password" name="password" />*
                        
                    </div>
                
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateVPassword"><?php echo JText::_("VERIFY_PASSWORD"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliateVPassword" type="password" name="vpassword" />*
                        
                    </div>
                
                </div>
            
            <?php } ?>
            
            <?php if ($this->formType == "Update") { ?>
            
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateFirstName"><?php echo JText::_("FIRST_NAME"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliateFirstName" type="text" name="fname" value="<?php echo @$this->affiliate->fname; ?>" />*
                        
                    </div>
                
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateLastName"><?php echo JText::_("LAST_NAME"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliateLastName" type="text" name="lname" value="<?php echo @$this->affiliate->lname; ?>" />*
                        
                    </div>
                
                </div>
            
            <?php } ?>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateEmail"><?php echo JText::_("EMAIL_ADDRESS"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue">
                
                    <input id="affiliateEmail" type="text" name="mail" value="<?php echo @$this->affiliate->mail; ?>" />*
                    
                </div>
            
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateWebsite"><?php echo JText::_("WEBSITE"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue">
                
                    <input id="affiliateWebsite" type="text" name="website" value="<?php echo @$this->affiliate->website; ?>" />
                    
                </div>
            
            </div>
            
            <?php if ($this->formType == "Update") { ?>
            
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliatePhoneNo"><?php echo JText::_("PHONE_NUMBER"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliatePhoneNo" type="text" name="phoneno" value="<?php echo @$this->affiliate->phoneno; ?>" />
                        
                    </div>
                
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateTaxSSN"><?php echo JText::_("TAX_SSN"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliateTaxSSN" type="text" name="taxssn" value="<?php echo @$this->affiliate->taxssn; ?>" />
                        
                    </div>
                
                </div>
            
            <?php } ?>
            
            <div class="affiliateDetailsDescription">
            
            	<h2><strong><?php echo $this->formType == "Add" ? JText::_("AFFILIATE_DETAILS") : JText::_("ADDRESS"); ?></strong></h2>
                
			</div>
            
            <?php if ($this->formType == "Add") { ?>
            
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateFirstName"><?php echo JText::_("FIRST_NAME"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliateFirstName" type="text" name="fname" />*
                        
                    </div>
                
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateLastName"><?php echo JText::_("LAST_NAME"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliateLastName" type="text" name="lname" />*
                        
                    </div>
                
                </div>
            
            <?php } ?>
            
            <div class="affiliateFormRow">
            
            	<div class="affiliateDetailsKey">
				
					<label for="affiliateStreet"><?php echo JText::_("STREET_ADDRESS"); ?></label>
                    
				</div>
            
            	<div class="affiliateDetailsValue">
				
					<input id="affiliateStreet" type="text" name="street" value="<?php echo @$this->affiliate->street; ?>" />*
                    
				</div>
            
            </div>
            
            <div class="affiliateFormRow">
            
            	<div class="affiliateDetailsKey">
				
					<label for="affiliateCity"><?php echo JText::_("CITY"); ?></label>
                    
				</div>
            
            	<div class="affiliateDetailsValue">
				
					<input id="affiliateCity" type="text" name="city" value="<?php echo @$this->affiliate->city; ?>" />*
                    
				</div>
            
            </div>
            
            <div class="affiliateFormRow">
            
            	<div class="affiliateDetailsKey">
				
					<label for="affiliateState"><?php echo JText::_("STATE"); ?></label>
                    
				</div>
            
            	<div class="affiliateDetailsValue">
				
					<input id="affiliateState" type="text" name="state" value="<?php echo @$this->affiliate->state; ?>" />
                    
				</div>
            
            </div>
            
            <div class="affiliateFormRow">
            
            	<div class="affiliateDetailsKey">
				
					<label for="affiliateCountry"><?php echo JText::_("COUNTRY"); ?></label>
                    
				</div>
            
            	<div class="affiliateDetailsValue">
				
					<input id="affiliateCountry" type="text" name="country" value="<?php echo @$this->affiliate->country; ?>" />*
                    
				</div>
            
            </div>
            
            <div class="affiliateFormRow">
            
            	<div class="affiliateDetailsKey">
				
					<label for="affiliateZipCode"><?php echo JText::_("ZIPCODE"); ?></label>
                    
				</div>
            
            	<div class="affiliateDetailsValue">
				
					<input id="affiliateZipCode" type="text" name="zipcode" value="<?php echo @$this->affiliate->zipcode; ?>" />*
                    
				</div>
            
            </div>
            
            <?php if ($this->formType == "Add") { ?>
                        
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliatePhoneNo"><?php echo JText::_("PHONE_NUMBER"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliatePhoneNo" type="text" name="phoneno" />
                        
                    </div>
                
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateTaxSSN"><?php echo JText::_("TAX_SSN"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliateTaxSSN" type="text" name="taxssn" />
                        
                    </div>
                
                </div>
            
            <?php } ?>
            
            <div class="affiliateFormRow">
            
            	<div class="affiliateDetailsKey">&nbsp;</div>
                
                <div class="affiliateDetailsValue" style="margin-top: 10px;">
                
                    <span class="affiliateButton">
                        
                        <input type="hidden" name="option" 					value="com_virtuemart" />
            
                        <input type="hidden" name="view" 					value="vma_affiliates" />
                        
                        <input type="hidden" name="task"					value="affiliate<?php echo $this->formType; ?>" />
                        
                        <?php if ($this->formType == "Update") { ?>
                        
                            <input type="hidden" name="affiliate_id"		value="<?php echo $this->affiliate->affiliate_id; ?>" />
                        
                        <?php } ?>
            
                        <input type="submit" value="<?php echo $this->formType == "Add" ? JText::_("ADD_AFFILIATE") : JText::_("JAPPLY"); ?>" style="width: auto;" 
                        
                               class="affiliateButton affiliateSaveButton" 	onclick="if (!validateAffiliateForm()) { return false; }" /></span>
                    	
                        <?php echo JHTML::_('form.token'); ?>
                        
				</div>
                
            </div>
            
            <div style="clear: both;"></div>
            
        </div>
        
    </div>
    
</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>