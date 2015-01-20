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
			
// add form validation function

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

<form action="<?php echo $link . "page=vma.affiliate_form"; ?>" method="post" name="adminForm">

    <div class="affiliateAdminPage">
    
        <div class="adminPanelTitleIcon" id="adminPreferencesIcon">
        
            <h1 class="adminPanelTitle">
			
				<?php echo JText::_("CHANGE_PASSWORD") . ": " . $affiliate->fname . " " . $affiliate->lname; ?>
                
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
            
                        <input type="hidden" name="pshop_mode" 		value="admin" />
            
                        <input type="hidden" name="page" 			value="vma.affiliate_form" />
                        
                        <input type="hidden" name="func"			value="vmaAffiliatePasswordUpdate" />
                        
                        <input type="hidden" name="task"			value="" />
                        
                        <input type="hidden" name="affiliate_id"	value="<?php echo $affiliate->affiliate_id; ?>" />
                        
                        <input type="hidden" name="vmtoken"			value="<?php echo vmSpoofValue($sess->getSessionId()); ?>" />
            
                        <input type="submit" value="<?php echo JText::_("SAVE"); ?>" style="width: auto;" 
                        
                        class="affiliateButton affiliateSaveButton" onclick="if (!validateAffiliateForm()) { return false; }" /></span>
                    
                </div>
                
            </div>
        
        	<div style="clear: both;"></div>
        
        </div>
        
    </div>
    
</form>