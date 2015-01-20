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

$link				= $ps_vma->getAdminLink();

$affiliateID		= &JRequest::getVar("affiliate_id", "");

// get affiliate's details

if (!$affiliateID) {
	
	$mainframe->redirect(str_replace("&amp;", "&", $link . "page=vma.affiliate_list"));
	
}

$query				= "SELECT * FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";

$ps_vma->_db->setQuery($query);

$affiliate			= $ps_vma->_db->loadObject();

// get payment method details
	
$paymentDetails		= $ps_vma->getPaymentMethods($affiliate->affiliate_id, $affiliate->method);

$paymentNames		= $ps_vma->getPaymentMethodsNames();

// get commission rates

$commissionRates	= $ps_vma->getCommissionRates($affiliate->affiliate_id);

$formattedRates		= $ps_vma->getFormattedCommissionRates($affiliate->affiliate_id);

?>

<form action="<?php echo $link . "page=vma.pay_affiliates"; ?>" method="post">

    <div class="affiliateAdminPage">
    
        <div class="adminPanelTitleIcon" id="adminPayIcon">
        
            <h1 class="adminPanelTitle"><?php echo JText::_("PAY_AFFILIATE") . ": " . $affiliate->fname . " " . $affiliate->lname; ?></h1>
            
        </div>
        
        <div class="affiliateTopMenu">
            
            <div class="affiliateTopMenuLink">
            
                <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" class="affiliateTopMenuLinkItem">
                
                    <a href="<?php echo $link . "page=vma.pay_affiliates"; ?>"><?php echo JText::_("PAY_AFFILIATES"); ?></a>
                    
                </span>
                
            </div>
            
        </div>
        
        <div style="clear: both;"></div>
        
        <br />
    
        <div id="affiliateDetailsPanel">
        
            <div class="affiliateDetailsColumn">
                
                <?php 
                
                if ($affiliate->method && $affiliate->method != "N/A") { 
                    
                    ?>
                    
                    <div class="affiliateDetailsDescription" style="padding-top: 0px;">
                
                        <strong><?php echo JText::_("PAYMENT_INFO"); ?></strong>
                        
                    </div>
                    
                    <div class="affiliateDetailsRow">
                
                        <div class="affiliateDetailsKey"><?php echo JText::_("AMOUNT"); ?>:</div>
                    
                        <div class="affiliateDetailsValue"><?php echo $ps_vma->formatAmount($affiliate->commissions); ?></div>
                    
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
                
                    <div class="affiliateDetailsValue"><?php echo $affiliate->fname; ?></div>
                
                </div>
                
                <div class="affiliateDetailsRow">
                
                    <div class="affiliateDetailsKey"><?php echo JText::_("LAST_NAME"); ?>:</div>
                
                    <div class="affiliateDetailsValue"><?php echo $affiliate->lname; ?></div>
                
                </div>
                
                <div class="affiliateDetailsRow">
                
                    <div class="affiliateDetailsKey"><?php echo JText::_("EMAIL_ADDRESS"); ?>:</div>
                
                    <div class="affiliateDetailsValue"><?php echo $affiliate->mail; ?></div>
                
                </div>
                
                <?php if ($affiliate->phoneno) { ?>
                
                <div class="affiliateDetailsRow">
                
                    <div class="affiliateDetailsKey"><?php echo JText::_("PHONE_NUMBER"); ?>:</div>
                
                    <div class="affiliateDetailsValue"><?php echo $affiliate->phoneno; ?></div>
                
                </div>
                
                <?php } ?>
                
                <div class="affiliateDetailsRow">
                
                    <div class="affiliateDetailsKey"><?php echo JText::_("ADDRESS"); ?>:</div>
                
                    <div class="affiliateDetailsValue"><?php echo $affiliate->street 	. ", "; ?></div>
                    
				</div>
                
                <div class="affiliateDetailsRow">
                    
                    <div class="affiliateDetailsKey">&nbsp;</div>
                
                    <div class="affiliateDetailsValue"><?php echo $affiliate->city		. ", " . ($affiliate->state	? $affiliate->state 		. ", " : NULL); ?></div>
                    
                </div>
                
                <div class="affiliateDetailsRow">
                    
                    <div class="affiliateDetailsKey">&nbsp;</div>
                
                    <div class="affiliateDetailsValue"><?php echo $affiliate->country 	. ", " . $affiliate->zipcode; ?></div>										  
                
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
        
        			<input type="hidden" name="pshop_mode" 		value="admin" />
        
        			<input type="hidden" name="page" 			value="vma.pay_affiliates" />
                    
                    <input type="hidden" name="func"			value="vmaAffiliatePay" />
                    
                    <input type="hidden" name="affiliate_id"	value="<?php echo $affiliate->affiliate_id; ?>" />
                    
                    <input type="hidden" name="vmtoken"			value="<?php echo vmSpoofValue($sess->getSessionId()); ?>" />
        
                    <input type="submit" value="<?php echo JText::_("MARK_AS_PAID");?>" class="affiliateButton payment<?php echo ucwords($paymentNames[$affiliate->method]["image"]); ?>" /></span>
                
            </div>
            
        </div>
    
        <div style="clear: both;"></div>
        
    </div>
    
</form>