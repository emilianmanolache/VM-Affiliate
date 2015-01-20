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

global $mainframe, $ps_vma, $sess;

$document 			= &JFactory::getDocument();

$link				= $ps_vma->getAdminLink();

// get affiliate's information

$affiliateID		= &JRequest::getVar("affiliate_id", "");

if (!$affiliateID) {
	
	$mainframe->redirect(str_replace("&amp;", "&", $link . "page=vma.affiliate_list"));
	
}

$query				= "SELECT * FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";

$ps_vma->_db->setQuery($query);

$affiliate			= $ps_vma->_db->loadObject();

// get payment methods
			
$paymentMethods 	= $ps_vma->getPaymentMethods($affiliate->affiliate_id);
			
// add preferences form validation function

$validationFunction = 'function validatePaymentMethodForm() {
						  
						  var paymentMethodSet		= false;
						  
						  var emailProvided			= true;
						  
						  for (var i = 0; i < document.getElementById(\'affiliatePaymentForm\').paymentmethod.length; i++) {
							  
							 if (document.getElementById(\'affiliatePaymentForm\').paymentmethod[i].checked) {
								 
								 paymentMethodSet	= true;
							
							 }
							 
							 if (document.getElementById(\'affiliatePaymentForm\').paymentmethod[i].value == \'1\' &&
						  
								 document.getElementById(\'affiliatePaymentForm\').paymentmethod[i].checked &&
								  
								 document.getElementById(\'payment-field-1\').value == \'\') {
								  
								 emailProvided		= false;
								  
							 }
							 
						  }
						  
						  if (typeof(document.getElementById(\'affiliatePaymentForm\').paymentmethod.length) == \'undefined\') {
							  
							  if (document.getElementById(\'affiliatePaymentForm\').paymentmethod.checked) {
								  
								  paymentMethodSet = true;
								  
							  }
							  
							  if (document.getElementById(\'affiliatePaymentForm\').paymentmethod.value == \'1\' &&
						  
								 document.getElementById(\'affiliatePaymentForm\').paymentmethod.checked &&
								  
								 document.getElementById(\'payment-field-1\').value == \'\') {
								  
								 emailProvided		= false;
								  
							 }
							   
						  }
						  
						  if (!paymentMethodSet) {
							  
							  alert( "' . JText::_("PROVIDE_PAYMENT_METHOD", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (!emailProvided) {
							  
							  alert( "' . JText::_("PROVIDE_EMAIL_ADDRESS", true) . '" );
							  
							  return false;
							  
						  }
						  
						  return true; 
						  
					  }';
						  
$document->addScriptDeclaration($validationFunction);

// add link form validation function

$validationFunction = 'function validateLinkToUserForm() {
						  
						  if (document.getElementById("affiliateLinkUsername").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_USERNAME", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateLinkPassword").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_PASSWORD", true) . '" );
							  
							  return false;
							  
						  }
						  
						  return true; 
						  
					  }';
						  
$document->addScriptDeclaration($validationFunction);

?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminPreferencesIcon">
    
        <h1 class="adminPanelTitle">
        
            <?php echo JText::_("PREFERENCES") . ": " . $affiliate->fname . " " . $affiliate->lname; ?>
            
        </h1>
        
    </div>
    
    <div class="affiliateTopMenu">
        
        <div class="affiliateTopMenuLink">
        
            <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" 
            
                  class="affiliateTopMenuLinkItem">
            
                <a href="<?php echo $link . "page=vma.affiliate_form&amp;affiliate_id=" . $affiliateID; ?>"><?php echo JText::_("EDIT_DETAILS"); ?></a>
                
            </span>
            
        </div>
        
    </div>
    
    <div style="clear: both;"></div>

    <br />

    <div>
        
        <form action="<?php echo $link . "page=vma.affiliate_preferences_form"; ?>" method="post" name="adminForm" id="affiliatePaymentForm">
    
            <?php if (is_array($paymentMethods) && count($paymentMethods) > 0) { ?>
            
                <div class="affiliateDetailsDescription" style="padding-top: 0px;">
                
                    <h2><strong><?php echo JText::_("PAYMENT_METHOD"); ?></strong></h2>
                    
                </div>
                
                    <?php foreach ($paymentMethods as $pm) { ?>
                    
                        <?php if ($pm["name"]) { ?>
                        
                            <div class="affiliateFormRow">
                            
                                <div class="affiliateDetailsKey">
                                
                                    <input id="payment-method-<?php echo $pm["id"]; ?>" name="paymentmethod" type="radio" value="<?php echo $pm["id"]; ?>" 
                                    
                                           <?php echo $affiliate->method == $pm["id"] ? "checked=\"checked\"" : NULL; ?> />
                                    
                                    <label for="payment-method-<?php echo $pm["id"]; ?>"><?php echo $pm["name"]; ?></label>
                                    
                                </div>
                            
                                <div class="affiliateDetailsValue"></div>
                            
                            </div>
                            
                        <?php } ?>
                    
                        <div class="affiliateFormRow">
                        
                            <div class="affiliateDetailsKey">
                            
                                <label for="payment-field-<?php echo $pm["fid"]; ?>"><?php echo $pm["fname"]; ?></label>
                                
                            </div>
                        
                            <div class="affiliateDetailsValue">
                            
                                <input id="payment-field-<?php echo $pm["fid"]; ?>" name="payment-field-<?php echo $pm["fid"]; ?>" type="text" value="<?php echo $pm["value"]; ?>" />
                                
                            </div>
                        
                        </div>
                    
                    <?php } ?>
                    
                    <div class="affiliateFormRow">
                    
                        <div class="affiliateDetailsKey">&nbsp;</div>
                        
                        <div class="affiliateDetailsValue" style="margin-top: 10px;">
                        
                            <span class="affiliateButton">
                                
                                <input type="hidden" name="option" 			value="com_virtuemart" />
                    
                                <input type="hidden" name="pshop_mode" 		value="admin" />
                    
                                <input type="hidden" name="page" 			value="vma.affiliate_preferences_form" />
                                
                                <input type="hidden" name="func"			value="vmaAffiliatePreferencesUpdate" />
                                
                                <input type="hidden" name="task"			value="" />
                                
                                <input type="hidden" name="affiliate_id"	value="<?php echo $affiliate->affiliate_id; ?>" />
                                
                                <input type="hidden" name="vmtoken"			value="<?php echo vmSpoofValue($sess->getSessionId()); ?>" />
                    
                                <input type="submit" value="<?php echo JText::_("SAVE"); ?>" style="width: auto;" 
                                
                                class="affiliateButton affiliateSaveButton" onclick="if (!validatePaymentMethodForm()) { return false; }" /></span>
                            
                        </div>
                        
                    </div>
            
                <?php } ?>
        
            </form>
        
        </div>
        
        <div class="affiliateDetailsDescription">
        
            <h2><strong><?php echo JText::_("LINK_TO_USER"); ?></strong></h2>
            
        </div>
        
        <div>
        
            <form action="<?php echo $link . "page=vma.affiliate_preferences_form"; ?>" method="post" id="linkToForm">
            
                <?php if (!$affiliate->linkedto) { ?>
                
                    <div class="affiliateFormRow">
                    
                        <div class="affiliateDetailsKey">
                        
                            <label for="affiliateLinkUsername"><?php echo JText::_("SITE_ACCOUNT_USERNAME"); ?></label>
                            
                        </div>
                    
                        <div class="affiliateDetailsValue">
                        
                            <input id="affiliateLinkUsername" name="username" type="text" value="" />
                            
                        </div>
                    
                    </div>
                    
                    <div class="affiliateFormRow">
                    
                        <div class="affiliateDetailsKey">
                        
                            <label for="affiliateLinkPassword"><?php echo JText::_("SITE_ACCOUNT_PASSWORD"); ?></label>
                            
                        </div>
                    
                        <div class="affiliateDetailsValue">
                        
                            <input id="affiliateLinkPassword" name="password" type="password" value="" />
                            
                        </div>
                    
                    </div>
                    
                <?php 
                
                    }
                    
                    else {
                        
                ?>
        
                    <div>
                    
                        <p><?php echo JText::_("LINKING_SUCCESSFUL"); ?>!</p>
                    
                    </div>
                    
                    <div class="affiliateDetailsRow">
                    
                        <div style="float: left; width: 30%;"><?php echo JText::_("SITE_ACCOUNT_USERNAME"); ?>:</div>
                    
                        <div style="float: left; width: 30%;"><strong><?php echo $affiliate->linkedto; ?></strong></div>
                    
                    </div>
                
                <?php } ?>
                
                <div class="affiliateFormRow">
                
                    <div class="affiliateDetailsKey">&nbsp;</div>
                    
                    <div class="affiliateDetailsValue" style="margin-top: 10px;">
                    
                        <span class="affiliateButton">
                            
                            <input type="hidden" name="option" 			value="com_virtuemart" />
                
                            <input type="hidden" name="pshop_mode" 		value="admin" />
                
                            <input type="hidden" name="page" 			value="vma.affiliate_preferences_form" />
                            
                            <input type="hidden" name="func"			value="vmaAffiliate<?php echo $affiliate->linkedto ? "Unlink" : "Link"; ?>" />
                            
                            <input type="hidden" name="task"			value="" />
                            
                            <input type="hidden" name="affiliate_id"	value="<?php echo $affiliate->affiliate_id; ?>" />
                            
                            <input type="hidden" name="vmtoken"			value="<?php echo vmSpoofValue($sess->getSessionId()); ?>" />
                
                            <input type="submit" value="<?php echo $affiliate->linkedto ? JText::_("RESET") : JText::_("SAVE"); ?>" 
                            
                            class="affiliateButton affiliate<?php echo $affiliate->linkedto ? "Reset" : "Save"; ?>Button" style="width: auto;" 
                            
                            <?php echo !$affiliate->linkedto ? "onclick=\"if (!validateLinkToUserForm()) { return false; }\"" : NULL; ?> /></span>
                        
                    </div>
                    
                </div>
            
            </form>
            
        </div>
    
        <div style="clear: both;"></div>
    
    </div>
    
</div>