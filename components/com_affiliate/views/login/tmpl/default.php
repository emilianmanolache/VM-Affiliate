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
 
// get vma settings

global $vmaSettings, $vmaHelper;

// get current document

$document = &JFactory::getDocument();

// add the component style

$document->addStyleSheet(JURI::base() . "components/com_affiliate/views/login/tmpl/css/style.css");

?>

<?php if ($this->params->get( 'show_page_title', 1)) { ?>

<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">

	<?php echo $this->escape($this->params->get('page_title')); ?>
    
</div>

<?php } ?>

<div id="affiliatePanel">

	<div id="affiliateLoginForm">
        
		<div id="affiliateLoginIcon"></div>
        
        <br />
        
        <form action="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=login&task=login")); ?>" method="post">
        
            <div class="affiliateInputField">
            
				<span>
                
                	<label for="affiliateUsername"><?php echo JText::_("JGLOBAL_USERNAME"); ?></label>
                    
				</span>
                
                <input id="affiliateUsername" name="username" type="text" />
                
            </div>
            
            <div class="affiliateInputField">
            
            	<span>
                
	            	<label for="affiliatePassword"><?php echo JText::_("JGLOBAL_PASSWORD"); ?></label>
                    
				</span>
            
            	<input id="affiliatePassword" name="passwd" type="password" />
                
            </div>
            
            <div class="affiliateInputField">
            	
                <span>&nbsp;</span>
                
                <input type="submit" name="submit" class="button" value="<?php echo JText::_("JLOGIN"); ?>" />
                
            </div>
            
            <div>
                
                <br />
                
                <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=lostpassword")); ?>"><?php echo JText::_("FORGOT_YOUR_PASSWORD"); ?></a>
                    
                <?php if ($vmaSettings->allow_signups) { ?>
                
                    <br />
                    
                    <?php echo JText::_("NO_ACCOUNT"); ?>
                    
                    <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=register")); ?>"><?php echo JText::_("SIGN_UP_TEXT"); ?>!</a>
                
                <?php }	?>
                
                <?php echo JHTML::_( 'form.token' ); ?>
            
            </div>
            
        </form>
            
    </div>
    
    <div id="affiliateProgramDetails">
    	
        <div id="affiliateMoneyIcon"></div>
        
		<br />
        
        <?php echo JText::_("AFFILIATE_PROGRAM_OFFER_MESSAGE"); ?>:
        
        <br />
            
        <?php 
    
            echo $this->generalRates["affiliate"]["per_sale_percentage"] 	> 0 ? "<strong>" . $this->generalRates["affiliate"]["per_sale_percentage"] 		. "%</strong> - " 	. 
            
                 JText::_("PERCENTAGE") . " " . JText::_("PER_SALE") . "<br />" : "";
                  
            echo $this->generalRates["affiliate"]["per_sale_fixed"] 		> 0 ? "<strong>" . $this->formattedRates["affiliate"]["per_sale_fixed"] 		. "</strong> - " 	. 
            
                 JText::_("FIXED_RATE") . " " . JText::_("PER_SALE") . "<br />" : "";
                  
            echo $this->generalRates["affiliate"]["per_click_fixed"]		> 0 ? "<strong>" . $this->formattedRates["affiliate"]["per_click_fixed"] 		. "</strong> - " 	. 
            
                 JText::_("PER_CLICK") . "<br />" : "";
                  
            echo $this->generalRates["affiliate"]["per_unique_click_fixed"] > 0 ? "<strong>" . $this->formattedRates["affiliate"]["per_unique_click_fixed"] . "</strong> - " 	. 
            
                 JText::_("PER_UNIQUE_CLICK") : "";
                 
        ?>
              
        <br /><br />
    
        <?php 
        
            if ($vmaSettings->initial_bonus > 0) {
                
                echo JText::sprintf("INITIAL_BONUS_MESSAGE", "<strong>" . $this->initialBonus . "</strong>") . "<br />";
                
            }
        
        ?>
        
        <?php
        
            echo JText::sprintf("MINIMUM_BALANCE_MESSAGE", "<strong>" . $this->payoutBalance . "</strong>") . ".";
            
        ?>
        
        <br />
        
        <?php
        
            echo JText::sprintf("PAYOUT_DAY_MESSAGE", "<strong>" . $vmaSettings->pay_day . "</strong>");
            
        ?>
    
    </div>
    
    <div style="clear: both;"></div>
    
    <?php $this->display("footer"); ?>
    
</div>