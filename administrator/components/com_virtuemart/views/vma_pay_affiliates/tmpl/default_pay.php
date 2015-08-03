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

JToolBarHelper::title(JText::_("PAY_AFFILIATE") . ": " . $this->affiliate->fname . " " . $this->affiliate->lname, 'head adminPayIcon');

// initiate other variables

$link = $vmaHelper->getAdminLink();

// get payment method details
	
$paymentDetails		= $vmaHelper->getPaymentMethods($this->affiliate->affiliate_id, $this->affiliate->method);

$paymentNames		= $vmaHelper->getPaymentMethodsNames();

// get commission rates

$commissionRates	= $vmaHelper->getCommissionRates($this->affiliate->affiliate_id);

$formattedRates		= $vmaHelper->getFormattedCommissionRates($this->affiliate->affiliate_id);

?>

<form action="<?php echo $link . "_pay_affiliates"; ?>" method="post" name="adminform">

<div class="affiliateAdminPage">
    
    <div class="affiliateTopMenu">
            
        <div class="affiliateTopMenuLink">
        
            <span style="background: url(<?php echo $vmaHelper->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" class="affiliateTopMenuLinkItem">
            
                <a href="<?php echo $link . "_pay_affiliates"; ?>"><?php echo JText::_("PAY_AFFILIATES"); ?></a>
                
            </span>
            
        </div>
        
    </div>
    
    <div style="clear: both;"></div>
    
    <br />

    <div id="affiliateDetailsPanel">
        
        <div class="affiliateDetailsColumn">
            
            <?php 
            
            if ($this->affiliate->method && $this->affiliate->method != "N/A") { 
                
                ?>
                
                <div class="affiliateDetailsDescription" style="padding-top: 0px;">
            
                    <strong><?php echo JText::_("PAYMENT_INFO"); ?></strong>
                    
                </div>
                
                <div class="affiliateDetailsRow">
            
                    <div class="affiliateDetailsKey"><?php echo JText::_("AMOUNT"); ?>:</div>
                
                    <div class="affiliateDetailsValue"><?php echo $vmaHelper->formatAmount($this->affiliate->commissions); ?></div>
                
                </div>
            
                <div class="affiliateDetailsRow">
                    
                    <div class="affiliateDetailsKey"><?php echo JText::_("PAYMENT_METHOD"); ?>:</div>
                
                    <div class="affiliateDetailsValue"><?php echo $paymentDetails[0]["name"]; ?></div>
                
                </div>
                    
                <?php
                
                foreach ($paymentDetails as $paymentDetail) {
                    
                    if ($paymentDetail["value"]) {
                        
                        ?>
                        
                        <div class="affiliateDetailsRow">
                        
                            <div class="affiliateDetailsKey"><?php echo $paymentDetail["fname"]; ?>:</div>
                        
                            <div class="affiliateDetailsValue"><?php echo $paymentDetail["value"]; ?></div>
                        
                        </div>
        
                        <?php
                    
                    }
                
                }
                
            } 
            
            ?>
            
        </div>
        
        <div class="affiliateDetailsColumn">
        
            <div class="affiliateDetailsDescription" style="padding-top: 0px;">
            
                <strong><?php echo JText::_("ACCOUNT_INFO"); ?></strong>
                
            </div>
            
            <div class="affiliateDetailsRow">
            
                <div class="affiliateDetailsKey"><?php echo JText::_("FIRST_NAME"); ?>:</div>
            
                <div class="affiliateDetailsValue"><?php echo $this->affiliate->fname; ?></div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
                <div class="affiliateDetailsKey"><?php echo JText::_("LAST_NAME"); ?>:</div>
            
                <div class="affiliateDetailsValue"><?php echo $this->affiliate->lname; ?></div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
                <div class="affiliateDetailsKey"><?php echo JText::_("EMAIL_ADDRESS"); ?>:</div>
            
                <div class="affiliateDetailsValue"><?php echo $this->affiliate->mail; ?></div>
            
            </div>
            
            <?php if ($this->affiliate->phoneno) { ?>
            
            <div class="affiliateDetailsRow">
            
                <div class="affiliateDetailsKey"><?php echo JText::_("PHONE_NUMBER"); ?>:</div>
            
                <div class="affiliateDetailsValue"><?php echo $this->affiliate->phoneno; ?></div>
            
            </div>
            
            <?php } ?>
            
            <div class="affiliateDetailsRow">
            
                <div class="affiliateDetailsKey"><?php echo JText::_("ADDRESS"); ?>:</div>
            
                <div class="affiliateDetailsValue"><?php echo $this->affiliate->street 	. ", "; ?></div>
                
            </div>
            
            <div class="affiliateDetailsRow">
                
                <div class="affiliateDetailsKey">&nbsp;</div>
            
                <div class="affiliateDetailsValue"><?php echo $this->affiliate->city		. ", " . ($this->affiliate->state	? $this->affiliate->state 		. ", " : NULL); ?></div>
                
            </div>
            
            <div class="affiliateDetailsRow">
                
                <div class="affiliateDetailsKey">&nbsp;</div>
            
                <div class="affiliateDetailsValue"><?php echo $this->affiliate->country 	. ", " . $this->affiliate->zipcode; ?></div>										  
            
            </div>
            
        </div>
        
        <div style="clear: both;"></div>
        
        <div style="padding-left: 15px; font-style: italic; font-weight: bold;">
            
            <?php echo JText::_("PAYMENT_NOTE"); ?>
            
        </div>
        
        <br />
        
        <div style="padding-left: 20px; width: 200px; margin-left: auto; margin-right: auto; text-align: center;">
        
            <span class="affiliateButton">
                
                <input type="hidden" name="option" 			value="com_virtuemart" />
    
                <input type="hidden" name="view" 			value="vma_pay_affiliates" />
                
                <input type="hidden" name="task"			value="pay" />
                
                <input type="hidden" name="affiliate_id"	value="<?php echo $this->affiliate->affiliate_id; ?>" />
    
                <input type="submit" value="<?php echo JText::_("MARK_AS_PAID");?>" class="affiliateButton payment<?php echo ucwords($paymentNames[$this->affiliate->method]["image"]); ?>" /></span>
            
            	<?php echo JHTML::_('form.token'); ?>
                
        </div>
        
    </div>

    <div style="clear: both;"></div>
    
</div>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>