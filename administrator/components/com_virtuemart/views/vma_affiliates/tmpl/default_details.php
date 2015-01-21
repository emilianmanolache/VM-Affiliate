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

$vmaHelper->startAdminArea($this);

// display the title

JToolBarHelper::title($this->affiliate->fname . " " . $this->affiliate->lname, 'head adminDetailsIcon');

// initiate other variables

$link = $vmaHelper->getAdminLink();

// get payment method details

if ($this->affiliate->method && $this->affiliate->method != "N/A") {
	
	$paymentDetails	= $vmaHelper->getPaymentMethods($this->affiliate->affiliate_id, $this->affiliate->method);
	
}

// get commission rates

$commissionRates	= $vmaHelper->getCommissionRates($this->affiliate->affiliate_id);

$formattedRates		= $vmaHelper->getFormattedCommissionRates($this->affiliate->affiliate_id);

// get discount rate

$discountRate		= $vmaHelper->getDiscountRate($this->affiliate->affiliate_id);

?>

<div class="affiliateAdminPage">
    
    <div class="affiliateTopMenu">
        
        <div class="affiliateTopMenuLink">
            
            <span style="background: url(<?php echo $vmaHelper->_website; ?>components/com_affiliate/assets/images/edit_bigger.png) no-repeat left top;" class="affiliateTopMenuLinkItem">
            
                <a href="<?php echo $link . "_affiliates" . "&amp;" . "task=edit" . "&amp;" . "affiliate_id" . "=" . $this->affiliate->affiliate_id; ?>"><?php echo JText::_("EDIT_DETAILS"); ?></a>
                
            </span>
        
        </div>
        
        <div class="affiliateTopMenuLink">
        
            <span style="background: url(<?php echo $vmaHelper->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" class="affiliateTopMenuLinkItem">
            
                <a href="<?php echo $link . "_affiliates"; ?>"><?php echo JText::_("MANAGE_AFFILIATES"); ?></a>
                
            </span>
            
        </div>
        
    </div>
    
    <div style="clear: both;"></div>
    
    <br />

    <div id="affiliateDetailsPanel">
    
    	<div class="affiliateDetailsColumn">
        
        	<div class="affiliateDetailsDescription" style="padding-top: 0px;">
            
            	<strong><?php echo JText::_("ACCOUNT_INFO"); ?></strong>
                
			</div>
                        
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("AFFILIATE_ID"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $this->affiliate->affiliate_id; ?></div>
            
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
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("USERNAME"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $this->affiliate->username; ?></div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("EMAIL_ADDRESS"); ?>:</div>
            
            	<div class="affiliateDetailsValue">
                
                	<a href="<?php echo $link; ?>_email_affiliates&amp;email=<?php echo $this->affiliate->mail; ?>">
					
						<?php echo $this->affiliate->mail; ?>
                        
					</a>
                    
				</div>
            
            </div>
            
			<?php if ($this->affiliate->website) { ?>
            
            <div class="affiliateDetailsRow">
            
                <div class="affiliateDetailsKey"><?php echo JText::_("WEBSITE"); ?>:</div>
            
                <div class="affiliateDetailsValue">
                
                	<a href="<?php echo $vmaHelper->parseURL($this->affiliate->website); ?>">
					
						<?php echo $vmaHelper->parseURL($this->affiliate->website); ?>
                        
					</a>
                    
				</div>
            
            </div>
            
            <?php } ?>
            
            <?php if ($this->affiliate->phoneno) { ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("PHONE_NUMBER"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $this->affiliate->phoneno; ?></div>
            
            </div>
            
            <?php } ?>
            
            <?php if ($this->affiliate->taxssn) { ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("TAX_SSN"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $this->affiliate->taxssn; ?></div>
            
            </div>
            
            <?php } ?>
            
            <div class="affiliateDetailsDescription">
            
            	<strong><?php echo JText::_("ADDRESS"); ?></strong>
                
			</div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("STREET_ADDRESS"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $this->affiliate->street; ?></div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("CITY"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $this->affiliate->city; ?></div>
            
            </div>
            
            <?php if ($this->affiliate->state) { ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("STATE"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $this->affiliate->state; ?></div>
            
            </div>
            
            <?php } ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("COUNTRY"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $this->affiliate->country; ?></div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("ZIPCODE"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $this->affiliate->zipcode; ?></div>
            
            </div>
            
            <?php 
			
			if ($this->affiliate->method && $this->affiliate->method != "N/A") { 
            	
				?>
                
                <div class="affiliateDetailsDescription">
            
            		<strong><?php echo JText::_("PAYMENT_METHOD"); ?></strong>
                    
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
            
            	<strong><?php echo JText::_("CURRENT"); ?></strong>
                
			</div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("BALANCE"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $vmaHelper->formatAmount($this->affiliate->commissions); ?></div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("CLICKS"); ?>:</div>
            
            	<div class="affiliateDetailsValue">
				
					<?php if ($currentClicks = $vmaHelper->getClicks($this->affiliate->affiliate_id, true, false)) { ?>
                
                		<a href="<?php echo $link . "_traffic&amp;affiliate_id=" . $this->affiliate->affiliate_id . "&amp;paid=0&amp;unique=0"; ?>">
					
                    <?php } ?>
                    
								<?php echo $currentClicks; ?>
                        
					<?php if ($currentClicks) { ?>
                    
                    	</a>
						
					<?php } ?>
                    
				</div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("UNIQUE_CLICKS"); ?>:</div>
            
            	<div class="affiliateDetailsValue">
                
                	<?php if ($currentUniqueClicks = $vmaHelper->getClicks($this->affiliate->affiliate_id, true, true)) { ?>
                    
                    	<a href="<?php echo $link . "_traffic&amp;affiliate_id=" . $this->affiliate->affiliate_id . "&amp;paid=0&amp;unique=1"; ?>">
					
                    <?php } ?>
                    
								<?php echo $currentUniqueClicks; ?>
                        
					<?php if ($currentUniqueClicks) { ?>
                    
                    	</a>
						
					<?php } ?>
                    
				</div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("APPROVED_SALES"); ?>:</div>
            
            	<div class="affiliateDetailsValue">
                
                	<?php if ($currentApprovedSales = $vmaHelper->getSales($this->affiliate->affiliate_id, true, true)) { ?>
                    
                		<a href="<?php echo $link . "_sales&amp;affiliate_id=" . $this->affiliate->affiliate_id . "&amp;paid=0&amp;confirmed=1"; ?>">
					
                    <?php } ?>
                    
								<?php echo $currentApprovedSales; ?>
                        
					<?php if ($currentApprovedSales) { ?>
                    
                    	</a>
						
					<?php } ?>
                    
				</div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("PENDING_SALES"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $vmaHelper->getSales($this->affiliate->affiliate_id, true, false); ?></div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("REFERRED_AFFILIATES"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $vmaHelper->getReferredAffiliates($this->affiliate->affiliate_id); ?></div>
            
            </div>
            
            <div class="affiliateDetailsDescription">
            
            	<strong><?php echo JText::_("OVERALL"); ?></strong>
                
			</div>
                        
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("TOTAL_COMMISSIONS_EARNED"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $vmaHelper->getOverallBalance($this->affiliate->affiliate_id, $this->affiliate->commissions); ?></div>
            
            </div>
            
			<div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("CLICKS"); ?>:</div>
            
            	<div class="affiliateDetailsValue">
                
                	<?php if ($overallClicks = $vmaHelper->getClicks($this->affiliate->affiliate_id, false, false)) { ?>
                    
                		<a href="<?php echo $link . "_traffic&amp;affiliate_id=" . $this->affiliate->affiliate_id . "&amp;paid=1&amp;unique=0"; ?>">
					
                    <?php } ?>
                    
								<?php echo $overallClicks; ?>
                        
					<?php if ($overallClicks) { ?>
                    
                    	</a>
						
					<?php } ?>
                    
				</div>
            
            </div>

            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("UNIQUE_CLICKS"); ?>:</div>
            
            	<div class="affiliateDetailsValue">
                
                	<?php if ($overallUniqueClicks = $vmaHelper->getClicks($this->affiliate->affiliate_id, false, true)) { ?>
                    
                		<a href="<?php echo $link . "_traffic&amp;affiliate_id=" . $this->affiliate->affiliate_id . "&amp;paid=1&amp;unique=1"; ?>">
					
                    <?php } ?>
                    
								<?php echo $overallUniqueClicks; ?>
                        
					<?php if ($overallUniqueClicks) { ?>
                    
                    	</a>
						
					<?php } ?>
                    
				</div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("APPROVED_SALES"); ?>:</div>
            
            	<div class="affiliateDetailsValue">
                
                	<?php if ($overallApprovedSales = $vmaHelper->getSales($this->affiliate->affiliate_id, false, true)) { ?>
                    
                		<a href="<?php echo $link . "_sales&amp;affiliate_id=" . $this->affiliate->affiliate_id . "&amp;paid=1&amp;confirmed=1"; ?>">
					
                    <?php } ?>
                    
								<?php echo $overallApprovedSales; ?>
                        
					<?php if ($overallApprovedSales) { ?>
                    
                    	</a>
						
					<?php } ?>
                    
				</div>
            
            </div>
            
            <div class="affiliateDetailsDescription">
            
            	<strong><?php echo JText::_("COMMISSION_RATES"); ?></strong>
                
			</div>
            
            <?php if ($commissionRates["affiliate"]["per_click_fixed"] > 0) { ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("PER_CLICK"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $formattedRates["affiliate"]["per_click_fixed"]; ?></div>
            
            </div>
            
            <?php } ?>
            
            <?php if ($commissionRates["affiliate"]["per_unique_click_fixed"] > 0) { ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("PER_UNIQUE_CLICK"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $formattedRates["affiliate"]["per_unique_click_fixed"]; ?></div>
            
            </div>
            
            <?php } ?>
            
            <?php if ($commissionRates["affiliate"]["per_sale_fixed"] > 0) { ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("PER_SALE") . " " . "(" . JText::_("FIXED_RATE") . ")"; ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $formattedRates["affiliate"]["per_sale_fixed"]; ?></div>
            
            </div>
            
            <?php } ?>
            
            <?php if ($commissionRates["affiliate"]["per_sale_percentage"] > 0) { ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("PER_SALE") . " " . "(" . JText::_("PERCENTAGE") . ")"; ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $commissionRates["affiliate"]["per_sale_percentage"]; ?>%</div>
            
            </div>
            
            <?php } ?>
            
            <?php if ($vmaSettings->offline_tracking && $vmaSettings->offline_type == 3 && $discountRate["discount_amount"] > 0) { ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("CUSTOMER_DISCOUNT"); ?>:</div>
            
            	<div class="affiliateDetailsValue">-<?php echo $discountRate["discount_type"] == 1 ? 
				
															  $vmaHelper->formatAmount($discountRate["discount_amount"]) : 
															  
															  $discountRate["discount_amount"] . "%"; ?></div>
            
            </div>
            
            <?php } ?>
            
        </div>
        
    </div>

	<div style="clear: both;"></div>
    
</div>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>