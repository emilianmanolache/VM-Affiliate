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

// add validation functions

$this->loadTemplate("preferences_validation");

?>

<div id="affiliatePreferencesIcon" class="affiliatePanelIcon"><h2 class="affiliatePanelTitle"><?php echo JText::_("PREFERENCES"); ?></h2></div>

<br />
	
<div id="affiliateDetailsForm">

    <form action="<?php echo JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=preferences&task=paymentMethod")); ?>" method="post" id="affiliatePreferencesForm"
    
	onsubmit="if (!validatePaymentMethodForm()) { return false; }">

        <?php if (is_array($this->paymentMethods) && count($this->paymentMethods) > 0) { ?>
        
        	<div class="affiliateFormHeadline">
        
                <h2><?php echo JText::_("PAYMENT_METHOD"); ?></h2>
                
            </div>
        
			<?php foreach ($this->paymentMethods as $pm) { ?>
            
            	<?php if ($pm["name"]) { ?>
                
                    <div class="affiliateInputField">
                        
                        <input id="payment-method-<?php echo $pm["id"]; ?>" name="paymentmethod" type="radio" value="<?php echo $pm["id"]; ?>" 
						
							   <?php echo $this->affiliate->method == $pm["id"] ? "checked=\"checked\"" : NULL; ?> />
                        
                        <label for="payment-method-<?php echo $pm["id"]; ?>"><?php echo $pm["name"]; ?></label>
                        
                    </div>
            	
                <?php } ?>
                
                <div class="affiliateInputField">
					
                    <span>
                    
						<label for="payment-field-<?php echo $pm["fid"]; ?>"><?php echo $pm["fname"]; ?></label>
                    
                    </span>
                    
                    <input id="payment-field-<?php echo $pm["fid"]; ?>" name="payment-field-<?php echo $pm["fid"]; ?>" type="text" value="<?php echo $pm["value"]; ?>" />  
                    
                </div>
                
            <?php } ?>
            
            <div class="affiliateInputField">
                
                <br />
                
                <span>&nbsp;</span>
                
                <input type="submit" name="submit" class="button" value="<?php echo JText::_('SAVE'); ?>" />
                
                <?php echo JHTML::_( 'form.token' ); ?>
            
            </div>
        
        <?php } ?>
        
	</form>
    
    <form action="<?php echo JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=preferences&task=linkTo")); ?>" method="post"
    
	onsubmit="<?php if (!$this->affiliate->linkedto) { ?>if (!validateLinkToUserForm()) { return false; }<?php } ?>">
              
        <div class="affiliateFormHeadline">
        
            <h2><?php echo JText::_("LINK_TO_USER"); ?></h2>
            
        </div>
		
        <?php if (!$this->affiliate->linkedto) { ?>
        
            <div class="affiliateInputField">
             
                <p><?php echo JText::_("LINK_ACCOUNTS_DESCRIPTION"); ?></p>
                
                <br />
                
            </div>
            
            <div class="affiliateInputField">
                        
                <span>
                
                    <label for="affiliateLinkUsername"><?php echo JText::_("SITE_ACCOUNT_USERNAME"); ?></label>
                
                </span>
                
                <input id="affiliateLinkUsername" name="username" type="text" value="" />  
                
            </div>
                    
            <div class="affiliateInputField">
                        
                <span>
                
                    <label for="affiliateLinkPassword"><?php echo JText::_("SITE_ACCOUNT_PASSWORD"); ?></label>
                
                </span>
                
                <input id="affiliateLinkPassword" name="password" type="password" value="" />  
                
            </div>
                    
            <div class="affiliateInputField">
                
                <br />
                
                <span>&nbsp;</span>
                
                <input type="submit" name="submit" class="button" value="<?php echo JText::_('SAVE'); ?>" />
                
                <?php echo JHTML::_( 'form.token' ); ?>
            
            </div>
        
        <?php 
		
			} 
			
			else { 
        
		?>
        
			<div class="affiliateInputField">
             
                <p><?php echo JText::_("AFFILIATE_LINKED"); ?></p>
                
                <br />
                
            </div>
            
            <div class="affiliateInputField">
                        
                <span><?php echo JText::_("SITE_ACCOUNT_USERNAME"); ?>:</span>
                
                <span><strong><?php echo $this->affiliate->linkedto; ?></strong></span>
                
            </div>
            
            <div class="affiliateInputField">
                
                <br />
                
                <br />
                
                <span>&nbsp;</span>
                
                <input type="submit" name="submit" class="button" value="<?php echo JText::_('RESET'); ?>" />
                
                <?php echo JHTML::_( 'form.token' ); ?>
            
            </div>
            
        <?php } ?>
        
    </form>

</div>

<div style="clear: both;"></div>