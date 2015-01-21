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

$document->addStyleSheet(JURI::base() . "components/com_affiliate/views/register/tmpl/css/style.css");

// add validation function

$this->loadTemplate("validation");

?>

<?php if ($this->params->get( 'show_page_title', 1)) { ?>

<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">

	<?php echo $this->escape($this->params->get('page_title')); ?>
    
</div>

<?php } ?>

<div id="affiliatePanel">
	
    <div id="affiliateRegisterForm">
    
        <form action="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=login&task=register")); ?>" method="post" onsubmit="if (!validateAffiliateForm()) { return false; }">
            
            <div class="affiliateFormHeadline">
            
                <div id="affiliateAccountIcon"></div>
                
                <h2><?php echo JText::_("ACCOUNT_INFO"); ?></h2>
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                    
                    <label for="affiliateUsername"><?php echo JText::_("USERNAME"); ?></label>
                    
                </span>
                
                <input id="affiliateUsername" name="username" type="text" />*
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliatePassword"><?php echo JText::_("PASSWORD"); ?></label>
                    
                </span>
            
                <input id="affiliatePassword" name="password" type="password" />*
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliateVPassword"><?php echo JText::_("VERIFY_PASSWORD"); ?></label>
                    
                </span>
            
                <input id="affiliateVPassword" name="vpassword" type="password" />*
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliateEmail"><?php echo JText::_("EMAIL_ADDRESS"); ?></label>
                    
                </span>
            
                <input id="affiliateEmail" name="mail" type="text" />*
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliateWebsite"><?php echo JText::_("WEBSITE"); ?></label>
                    
                </span>
            
                <input id="affiliateWebsite" name="website" type="text" />
                
            </div>
            
            <div class="affiliateFormHeadline">
                
                <div id="affiliateDetailsIcon"></div>
                
                <h2><?php echo JText::_("AFFILIATE_DETAILS") ?></h2>
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliateFirstName"><?php echo JText::_("FIRST_NAME"); ?></label>
                    
                </span>
            
                <input id="affiliateFirstName" name="fname" type="text" />*
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliateLastName"><?php echo JText::_("LAST_NAME"); ?></label>
                    
                </span>
            
                <input id="affiliateLastName" name="lname" type="text" />*
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliateStreet"><?php echo JText::_("STREET_ADDRESS"); ?></label>
                    
                </span>
            
                <input id="affiliateStreet" name="street" type="text" />*
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliateCity"><?php echo JText::_("CITY"); ?></label>
                    
                </span>
            
                <input id="affiliateCity" name="city" type="text" />*
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliateState"><?php echo JText::_("STATE"); ?></label>
                    
                </span>
            
                <input id="affiliateState" name="state" type="text" />
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliateCountry"><?php echo JText::_("COUNTRY"); ?></label>
                    
                </span>
            
                <input id="affiliateCountry" name="country" type="text" />*
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliateZipCode"><?php echo JText::_("ZIPCODE"); ?></label>
                    
                </span>
            
                <input id="affiliateZipCode" name="zipcode" type="text" />*
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliatePhoneNo"><?php echo JText::_("PHONE_NUMBER"); ?></label>
                    
                </span>
            
                <input id="affiliatePhoneNo" name="phoneno" type="text" />
                
            </div>
            
            <div class="affiliateInputField">
            
                <span>
                
                    <label for="affiliateTaxSSN"><?php echo JText::_("TAX_SSN"); ?></label>
                    
                </span>
            
                <input id="affiliateTaxSSN" name="taxssn" type="text" />
                
            </div>
            
            <?php if ($vmaSettings->must_agree) { ?>
			
            	<div class="affiliateInputField">
					
                    <br />
                    
                    <span>&nbsp;</span>
                    
                    <input id="affiliateTermsAgreement" name="agreed_terms" value="1" type="checkbox" />
                    
                    <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=terms&tmpl=component")); ?>" class="affiliateModal">
                            
						<?php echo JText::_("I_AGREE_TO_TERMS"); ?>
                            
                    </a>
                    
                </div>
            
			<?php } ?>
                        
            <div class="affiliateInputField">
                
                <br />
                
                <span>&nbsp;</span>
                
                <input type="submit" name="submit" class="button" value="<?php echo JText::_("REGISTER_NOW"); ?>" />
                
                <?php echo JHTML::_( 'form.token' ); ?>
            
            </div>
            
        </form>
    
    </div>
    
    <div style="clear: both;"></div>
	
    <?php $this->display("footer"); ?>
    
</div>