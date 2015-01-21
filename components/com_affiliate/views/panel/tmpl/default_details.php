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

// add validation function

$this->loadTemplate("details_validation");

?>

<div id="affiliateDetailsIcon" class="affiliatePanelIcon"><h2 class="affiliatePanelTitle"><?php echo JText::_("EDIT_DETAILS"); ?></h2></div>

<br />
	
<div id="affiliateDetailsForm">

    <form action="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=details&task=details")); ?>" method="post" 
    
	onsubmit="if (!validateAffiliateForm()) { return false; }">
        
        <div class="affiliateFormHeadline">
            
            <h2><?php echo JText::_("ACCOUNT_INFO"); ?></h2>
            
        </div>
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateFirstName"><?php echo JText::_("FIRST_NAME"); ?></label>
                
            </span>
        
            <input id="affiliateFirstName" name="fname" type="text" value="<?php echo $this->affiliate->fname; ?>" />*
            
        </div>
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateLastName"><?php echo JText::_("LAST_NAME"); ?></label>
                
            </span>
        
            <input id="affiliateLastName" name="lname" type="text" value="<?php echo $this->affiliate->lname; ?>" />*
            
        </div>
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateEmail"><?php echo JText::_("EMAIL_ADDRESS"); ?></label>
                
            </span>
        
            <input id="affiliateEmail" name="mail" type="text" value="<?php echo $this->affiliate->mail; ?>" />*
            
        </div>
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateWebsite"><?php echo JText::_("WEBSITE"); ?></label>
                
            </span>
        
            <input id="affiliateWebsite" name="website" type="text" value="<?php echo $this->affiliate->website; ?>" />
            
        </div>
        
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliatePhoneNo"><?php echo JText::_("PHONE_NUMBER"); ?></label>
                
            </span>
        
            <input id="affiliatePhoneNo" name="phoneno" type="text" value="<?php echo $this->affiliate->phoneno; ?>" />
            
        </div>
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateTaxSSN"><?php echo JText::_("TAX_SSN"); ?></label>
                
            </span>
        
            <input id="affiliateTaxSSN" name="taxssn" type="text" value="<?php echo $this->affiliate->taxssn; ?>" />
            
        </div>
        
        <div class="affiliateFormHeadline">
            
            <h2><?php echo JText::_("ADDRESS"); ?></h2>
            
        </div>
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateStreet"><?php echo JText::_("STREET_ADDRESS"); ?></label>
                
            </span>
        
            <input id="affiliateStreet" name="street" type="text" value="<?php echo $this->affiliate->street; ?>" />*
            
        </div>
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateCity"><?php echo JText::_("CITY"); ?></label>
                
            </span>
        
            <input id="affiliateCity" name="city" type="text" value="<?php echo $this->affiliate->city; ?>" />*
            
        </div>
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateState"><?php echo JText::_("STATE"); ?></label>
                
            </span>
        
            <input id="affiliateState" name="state" type="text" value="<?php echo $this->affiliate->state; ?>" />
            
        </div>
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateCountry"><?php echo JText::_("COUNTRY"); ?></label>
                
            </span>
        
            <input id="affiliateCountry" name="country" type="text" value="<?php echo $this->affiliate->country; ?>" />*
            
        </div>
        
        <div class="affiliateInputField">
        
            <span>
            
                <label for="affiliateZipCode"><?php echo JText::_("ZIPCODE"); ?></label>
                
            </span>
        
            <input id="affiliateZipCode" name="zipcode" type="text" value="<?php echo $this->affiliate->zipcode; ?>" />*
            
        </div>
                    
        <div class="affiliateInputField">
            
            <br />
            
            <span>&nbsp;</span>
            
            <input type="submit" name="submit" class="button" value="<?php echo JText::_('SAVE'); ?>" />
            
            <?php echo JHTML::_( 'form.token' ); ?>
        
        </div>
        
    </form>

</div>

<div style="clear: both;"></div>