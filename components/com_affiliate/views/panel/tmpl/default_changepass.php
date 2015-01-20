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
 
// get vma settings

global $vmaSettings, $ps_vma;

// get current document

$document = &JFactory::getDocument();

// add validation function

$this->loadTemplate("changepass_validation");

?>

<div id="affiliatePasswordIcon" class="affiliatePanelIcon"><h2 class="affiliatePanelTitle"><?php echo JText::_("CHANGE_PASSWORD"); ?></h2></div>

<br />
	
<div id="affiliateDetailsForm">

    <form action="<?php echo JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&task=changePassword")); ?>" method="post" 
    
	onsubmit="if (!validateAffiliateForm()) { return false; }">
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateOldPassword"><?php echo JText::_("OLD_PASSWORD"); ?></label>
                
            </span>
        
            <input id="affiliateOldPassword" name="oldpassword" type="password" value="" />*
            
        </div>
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateNewPassword"><?php echo JText::_("NEW_PASSWORD"); ?></label>
                
            </span>
        
            <input id="affiliateNewPassword" name="newpassword" type="password" value="" />*
            
        </div>
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateVerifyPassword"><?php echo JText::_("VERIFY_PASSWORD"); ?></label>
                
            </span>
        
            <input id="affiliateVerifyPassword" name="verifypassword" type="password" value="" />*
            
        </div>
                    
        <div class="affiliateInputField">
            
            <br />
            
            <span>&nbsp;</span>
            
            <input type="submit" name="submit" class="button" value="<?php echo JText::_('CHANGE_PASSWORD'); ?>" />
            
            <?php echo JHTML::_( 'form.token' ); ?>
        
        </div>
        
    </form>

</div>

<div style="clear: both;"></div>