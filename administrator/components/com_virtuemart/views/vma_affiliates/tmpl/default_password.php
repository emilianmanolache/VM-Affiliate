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

JToolBarHelper::title(JText::_("CHANGE_PASSWORD") . ": " . $this->affiliate->fname . " " . $this->affiliate->lname, 'head adminPreferencesIcon');

// initiate other variables

$link = $vmaHelper->getAdminLink();

// add form validation function

$document = &JFactory::getDocument();

$validationFunction = 'function validateAffiliateForm() {
						  
						  if (document.getElementById("affiliateNewPassword").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_PASSWORD", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateVerifyPassword").value == "") {
							  
							  alert( "' . JText::_("RETYPE_PASSWORD", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateNewPassword").value != document.getElementById("affiliateVerifyPassword").value) {
							  
							  alert( "' . JText::_("PASSWORDS_DIFFER", true) . '" );
							  
							  return false;
							  
						  }
						  
						  return true; }';
						  
$document->addScriptDeclaration($validationFunction);

?>

<form action="<?php echo $link . "_affiliates"; ?>" method="post" name="adminForm">

	<div class="affiliateAdminPage">
    
    	<div class="affiliateTopMenu">
            
            <div class="affiliateTopMenuLink">
            
                <span style="background: url(<?php echo $vmaHelper->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" 
                
                      class="affiliateTopMenuLinkItem">
                
                    <a href="<?php echo $link . "_affiliates&amp;task=edit&amp;affiliate_id=" . $this->affiliate->affiliate_id; ?>"><?php echo JText::_("EDIT_DETAILS"); ?></a>
                    
                </span>
                
            </div>
            
        </div>
        
        <div style="clear: both;"></div>

        <br />
    
        <div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateNewPassword"><?php echo JText::_("NEW_PASSWORD"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue">
                
                    <input id="affiliateNewPassword" name="newpassword" type="password" value="" />*
                    
                </div>
            
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateVerifyPassword"><?php echo JText::_("VERIFY_PASSWORD"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue">
                
                    <input id="affiliateVerifyPassword" name="verifypassword" type="password" value="" />*
                    
                </div>
            
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">&nbsp;</div>
                
                <div class="affiliateDetailsValue" style="margin-top: 10px;">
                
                    <span class="affiliateButton">
                        
                        <input type="hidden" name="option" 			value="com_virtuemart" />
            
                        <input type="hidden" name="view" 			value="vma_affiliates" />
                        
                        <input type="hidden" name="task"			value="affiliatePasswordUpdate" />
                        
                        <input type="hidden" name="affiliate_id"	value="<?php echo $this->affiliate->affiliate_id; ?>" />
            
                        <input type="submit" value="<?php echo JText::_("JAPPLY"); ?>" style="width: auto;" 
                        
                        class="affiliateButton affiliateSaveButton" onclick="if (!validateAffiliateForm()) { return false; }" /></span>
                        
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