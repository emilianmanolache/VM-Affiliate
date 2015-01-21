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

$document->addStyleSheet(JURI::base() . "components/com_affiliate/views/lostpassword/tmpl/css/style.css");

// add validation function

$this->loadTemplate("validation");

?>

<?php if ($this->params->get( 'show_page_title', 1)) { ?>

<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">

	<?php echo $this->escape($this->params->get('page_title')); ?>
    
</div>

<?php } ?>

<div id="affiliatePanel">
	
    <div id="affiliateLostPasswordForm">
    
        <form action="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=login&task=lostPassword")); ?>" method="post" 
        
        onsubmit="if (!validateAffiliateForm()) { return false; }">
            
            <div class="affiliateFormHeadline">
            
            	<div id="affiliatePasswordIcon"></div>
                
			</div>
            
            <div class="affiliateInputField">
            
            	<p><?php echo JText::sprintf("FORGOT_YOUR_PASSWORD_NOTE", "<br />"); ?></p>
                
                <br />
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                    
                    <label for="affiliateUsername"><?php echo JText::_("USERNAME"); ?></label>
                    
                </span>
                
                <input id="affiliateUsername" name="username" type="text" />*
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliateEmail"><?php echo JText::_("EMAIL_ADDRESS"); ?></label>
                    
                </span>
            
                <input id="affiliateEmail" name="mail" type="text" />*
                
            </div>
            
            <div class="affiliateInputField">
            
            	<br />
                
               	<span>&nbsp;</span>
                
                <input type="submit" name="submit" class="button" value="<?php echo JText::_("SEND_PASSWORD"); ?>" />
                
                <?php echo JHTML::_( 'form.token' ); ?>
            
            </div>
 
        </form>
    
    </div>
    
    <div style="clear: both;"></div>
	
    <?php $this->display("footer"); ?>
    
</div>