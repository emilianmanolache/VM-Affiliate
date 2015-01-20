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

global $ps_vma, $sess;

$document 			= &JFactory::getDocument();

$link				= $ps_vma->getAdminLink();

// check if this is an edit form

$affiliateID		= &JRequest::getVar("affiliate_id", "");

$formType			= $affiliateID ? "Update" : "Add";

if ($formType == "Update") {
	
	$query			= "SELECT * FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
	
	$ps_vma->_db->setQuery($query);
	
	$affiliate		= $ps_vma->_db->loadObject();

}

// add form validation function

$validationFunction = 'function validateAffiliateForm() {';

if ($formType == "Add") {
							
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

<form action="<?php echo $link . "page=vma.affiliate_list"; ?>" method="post" name="adminForm">

    <div class="affiliateAdminPage">
    
        <div class="adminPanelTitleIcon" id="adminAffiliate<?php echo $formType; ?>Icon">
        
            <h1 class="adminPanelTitle">
			
				<?php echo $formType == "Add" ? JText::_("ADD_AFFILIATE") : JText::_("EDIT_DETAILS") . ": " . $affiliate->fname . " " . $affiliate->lname; ?>
                
			</h1>
            
        </div>
        
        <?php if ($formType == "Update") { ?>
        
            <div class="affiliateTopMenu">
                
                <div class="affiliateTopMenuLink">
                
                    <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/views/panel/tmpl/images/password_small.png) no-repeat left top;" 
                    
                          class="affiliateTopMenuLinkItem">
                    
                        <a href="<?php echo $link . "page=vma.affiliate_password_form&amp;affiliate_id=" 		. $affiliateID; ?>"><?php echo JText::_("CHANGE_PASSWORD"); ?></a>
                        
                    </span>
                    
                </div>
                
                <div class="affiliateTopMenuLink">
                
                    <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/assets/images/preferences_small.png) no-repeat left top;" 
                    
                          class="affiliateTopMenuLinkItem">
                    
                        <a href="<?php echo $link . "page=vma.affiliate_preferences_form&amp;affiliate_id=" 	. $affiliateID; ?>"><?php echo JText::_("PREFERENCES"); ?></a>
                        
                    </span>
                    
                </div>
                
                <div class="affiliateTopMenuLink">
                
                    <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/assets/images/commissions_small.png) no-repeat left top;" 
                    
                          class="affiliateTopMenuLinkItem">
                    
                        <a href="<?php echo $link . "page=vma.commission_rates_form&amp;affiliate_id=" 			. $affiliateID; ?>"><?php echo JText::_("COMMISSION_RATES"); ?></a>
                        
                    </span>
                    
                </div>
                
                <div class="affiliateTopMenuLink">
                
                    <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" 
                    
                          class="affiliateTopMenuLinkItem">
                    
                        <a href="<?php echo $link . "page=vma.affiliate_list"; ?>"><?php echo JText::_("MANAGE_AFFILIATES"); ?></a>
                        
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
            
            <?php if ($formType == "Add") { ?>
            
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateUsername"><?php echo JText::_("USERNAME"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliateUsername" type="text" name="username" />*
                        
                    </div>
                
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliatePassword"><?php echo JText::_("PASSWORD"); ?></label>
                        
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
            
            <?php if ($formType == "Update") { ?>
            
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateFirstName"><?php echo JText::_("FIRST_NAME"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliateFirstName" type="text" name="fname" value="<?php echo @$affiliate->fname; ?>" />*
                        
                    </div>
                
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateLastName"><?php echo JText::_("LAST_NAME"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliateLastName" type="text" name="lname" value="<?php echo @$affiliate->lname; ?>" />*
                        
                    </div>
                
                </div>
            
            <?php } ?>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateEmail"><?php echo JText::_("EMAIL_ADDRESS"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue">
                
                    <input id="affiliateEmail" type="text" name="mail" value="<?php echo @$affiliate->mail; ?>" />*
                    
                </div>
            
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateWebsite"><?php echo JText::_("WEBSITE"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue">
                
                    <input id="affiliateWebsite" type="text" name="website" value="<?php echo @$affiliate->website; ?>" />
                    
                </div>
            
            </div>
            
            <?php if ($formType == "Update") { ?>
            
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliatePhoneNo"><?php echo JText::_("PHONE_NUMBER"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliatePhoneNo" type="text" name="phoneno" value="<?php echo @$affiliate->phoneno; ?>" />
                        
                    </div>
                
                </div>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">
                    
                        <label for="affiliateTaxSSN"><?php echo JText::_("TAX_SSN"); ?></label>
                        
                    </div>
                
                    <div class="affiliateDetailsValue">
                    
                        <input id="affiliateTaxSSN" type="text" name="taxssn" value="<?php echo @$affiliate->taxssn; ?>" />
                        
                    </div>
                
                </div>
            
            <?php } ?>
            
            <div class="affiliateDetailsDescription">
            
            	<h2><strong><?php echo $formType == "add" ? JText::_("AFFILIATE_DETAILS") : JText::_("ADDRESS"); ?></strong></h2>
                
			</div>
            
            <?php if ($formType == "Add") { ?>
            
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
				
					<input id="affiliateStreet" type="text" name="street" value="<?php echo @$affiliate->street; ?>" />*
                    
				</div>
            
            </div>
            
            <div class="affiliateFormRow">
            
            	<div class="affiliateDetailsKey">
				
					<label for="affiliateCity"><?php echo JText::_("CITY"); ?></label>
                    
				</div>
            
            	<div class="affiliateDetailsValue">
				
					<input id="affiliateCity" type="text" name="city" value="<?php echo @$affiliate->city; ?>" />*
                    
				</div>
            
            </div>
            
            <div class="affiliateFormRow">
            
            	<div class="affiliateDetailsKey">
				
					<label for="affiliateState"><?php echo JText::_("STATE"); ?></label>
                    
				</div>
            
            	<div class="affiliateDetailsValue">
				
					<input id="affiliateState" type="text" name="state" value="<?php echo @$affiliate->state; ?>" />
                    
				</div>
            
            </div>
            
            <div class="affiliateFormRow">
            
            	<div class="affiliateDetailsKey">
				
					<label for="affiliateCountry"><?php echo JText::_("COUNTRY"); ?></label>
                    
				</div>
            
            	<div class="affiliateDetailsValue">
				
					<input id="affiliateCountry" type="text" name="country" value="<?php echo @$affiliate->country; ?>" />*
                    
				</div>
            
            </div>
            
            <div class="affiliateFormRow">
            
            	<div class="affiliateDetailsKey">
				
					<label for="affiliateZipCode"><?php echo JText::_("ZIPCODE"); ?></label>
                    
				</div>
            
            	<div class="affiliateDetailsValue">
				
					<input id="affiliateZipCode" type="text" name="zipcode" value="<?php echo @$affiliate->zipcode; ?>" />*
                    
				</div>
            
            </div>
            
            <?php if ($formType == "Add") { ?>
                        
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
            
                        <input type="hidden" name="pshop_mode" 				value="admin" />
            
                        <input type="hidden" name="page" 					value="vma.affiliate_list" />
                        
                        <input type="hidden" name="func"					value="vmaAffiliate<?php echo $formType; ?>" />
                        
                        <input type="hidden" name="task"					value="" />
                        
                        <?php if ($formType == "Update") { ?>
                        
                            <input type="hidden" name="affiliate_id"		value="<?php echo $affiliate->affiliate_id; ?>" />
                        
                        <?php } ?>
                        
                        <input type="hidden" name="vmtoken"					value="<?php echo vmSpoofValue($sess->getSessionId()); ?>" />
            
                        <input type="submit" value="<?php echo $formType == "Add" ? JText::_("ADD_AFFILIATE") : JText::_("SAVE"); ?>" style="width: auto;" 
                        
                               class="affiliateButton affiliateSaveButton" 	onclick="if (!validateAffiliateForm()) { return false; }" /></span>
                    
				</div>
                
            </div>
            
            <div style="clear: both;"></div>
            
        </div>
        
    </div>
    
</form>