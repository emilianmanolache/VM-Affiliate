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

global $mainframe, $ps_vma, $vmaSettings;

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

if ($affiliate->method && $affiliate->method != "N/A") {
	
	$paymentDetails	= $ps_vma->getPaymentMethods($affiliate->affiliate_id, $affiliate->method);
	
}

// get commission rates

$commissionRates	= $ps_vma->getCommissionRates($affiliate->affiliate_id);

$formattedRates		= $ps_vma->getFormattedCommissionRates($affiliate->affiliate_id);

// get discount rate

$discountRate		= $ps_vma->getDiscountRate($affiliate->affiliate_id);

?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminDetailsIcon">
    
        <h1 class="adminPanelTitle"><?php echo $affiliate->fname . " " . $affiliate->lname; ?></h1>
        
    </div>
    
    <div class="affiliateTopMenu">
        
        <div class="affiliateTopMenuLink">
            
            <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/assets/images/edit_bigger.png) no-repeat left top;" class="affiliateTopMenuLinkItem">
            
                <a href="<?php echo $link . "page=vma.affiliate_form" . "&amp;" . "affiliate_id" . "=" . $affiliate->affiliate_id; ?>"><?php echo JText::_("EDIT_DETAILS"); ?></a>
                
            </span>
        
        </div>
        
        <div class="affiliateTopMenuLink">
        
            <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" class="affiliateTopMenuLinkItem">
            
                <a href="<?php echo $link . "page=vma.affiliate_list"; ?>"><?php echo JText::_("MANAGE_AFFILIATES"); ?></a>
                
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
            
            	<div class="affiliateDetailsValue"><?php echo $affiliate->affiliate_id; ?></div>
            
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
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("USERNAME"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $affiliate->username; ?></div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("EMAIL_ADDRESS"); ?>:</div>
            
            	<div class="affiliateDetailsValue">
                
                	<a href="<?php echo $link; ?>page=vma.email_affiliates&amp;email=<?php echo $affiliate->mail; ?>">
					
						<?php echo $affiliate->mail; ?>
                        
					</a>
                    
				</div>
            
            </div>
            
			<?php if ($affiliate->website) { ?>
            
            <div class="affiliateDetailsRow">
            
                <div class="affiliateDetailsKey"><?php echo JText::_("WEBSITE"); ?>:</div>
            
                <div class="affiliateDetailsValue">
                
                	<a href="<?php echo $ps_vma->parseURL($affiliate->website); ?>">
					
						<?php echo $ps_vma->parseURL($affiliate->website); ?>
                        
					</a>
                    
				</div>
            
            </div>
            
            <?php } ?>
            
            <?php if ($affiliate->phoneno) { ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("PHONE_NUMBER"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $affiliate->phoneno; ?></div>
            
            </div>
            
            <?php } ?>
            
            <?php if ($affiliate->taxssn) { ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("TAX_SSN"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $affiliate->taxssn; ?></div>
            
            </div>
            
            <?php } ?>
            
            <div class="affiliateDetailsDescription">
            
            	<strong><?php echo JText::_("ADDRESS"); ?></strong>
                
			</div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("STREET_ADDRESS"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $affiliate->street; ?></div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("CITY"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $affiliate->city; ?></div>
            
            </div>
            
            <?php if ($affiliate->state) { ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("STATE"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $affiliate->state; ?></div>
            
            </div>
            
            <?php } ?>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("COUNTRY"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $affiliate->country; ?></div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("ZIPCODE"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $affiliate->zipcode; ?></div>
            
            </div>
            
            <?php 
			
			if ($affiliate->method && $affiliate->method != "N/A") { 
            	
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
            
            	<div class="affiliateDetailsValue"><?php echo $ps_vma->formatAmount($affiliate->commissions); ?></div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("CLICKS"); ?>:</div>
            
            	<div class="affiliateDetailsValue">
				
					<?php if ($currentClicks = $ps_vma->getClicks($affiliate->affiliate_id, true, false)) { ?>
                
                		<a href="<?php echo $link . "page=vma.traffic&amp;affiliate_id=" . $affiliate->affiliate_id . "&amp;paid=0&amp;unique=0"; ?>">
					
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
                
                	<?php if ($currentUniqueClicks = $ps_vma->getClicks($affiliate->affiliate_id, true, true)) { ?>
                    
                    	<a href="<?php echo $link . "page=vma.traffic&amp;affiliate_id=" . $affiliate->affiliate_id . "&amp;paid=0&amp;unique=1"; ?>">
					
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
                
                	<?php if ($currentApprovedSales = $ps_vma->getSales($affiliate->affiliate_id, true, true)) { ?>
                    
                		<a href="<?php echo $link . "page=vma.sales&amp;affiliate_id=" . $affiliate->affiliate_id . "&amp;paid=0&amp;confirmed=1"; ?>">
					
                    <?php } ?>
                    
								<?php echo $currentApprovedSales; ?>
                        
					<?php if ($currentApprovedSales) { ?>
                    
                    	</a>
						
					<?php } ?>
                    
				</div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("PENDING_SALES"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $ps_vma->getSales($affiliate->affiliate_id, true, false); ?></div>
            
            </div>
            
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("REFERRED_AFFILIATES"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $ps_vma->getReferredAffiliates($affiliate->affiliate_id); ?></div>
            
            </div>
            
            <div class="affiliateDetailsDescription">
            
            	<strong><?php echo JText::_("OVERALL"); ?></strong>
                
			</div>
                        
            <div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("TOTAL_COMMISSIONS_EARNED"); ?>:</div>
            
            	<div class="affiliateDetailsValue"><?php echo $ps_vma->getOverallBalance($affiliate->affiliate_id, $affiliate->commissions); ?></div>
            
            </div>
            
			<div class="affiliateDetailsRow">
            
            	<div class="affiliateDetailsKey"><?php echo JText::_("CLICKS"); ?>:</div>
            
            	<div class="affiliateDetailsValue">
                
                	<?php if ($overallClicks = $ps_vma->getClicks($affiliate->affiliate_id, false, false)) { ?>
                    
                		<a href="<?php echo $link . "page=vma.traffic&amp;affiliate_id=" . $affiliate->affiliate_id . "&amp;paid=1&amp;unique=0"; ?>">
					
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
                
                	<?php if ($overallUniqueClicks = $ps_vma->getClicks($affiliate->affiliate_id, false, true)) { ?>
                    
                		<a href="<?php echo $link . "page=vma.traffic&amp;affiliate_id=" . $affiliate->affiliate_id . "&amp;paid=1&amp;unique=1"; ?>">
					
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
                
                	<?php if ($overallApprovedSales = $ps_vma->getSales($affiliate->affiliate_id, false, true)) { ?>
                    
                		<a href="<?php echo $link . "page=vma.sales&amp;affiliate_id=" . $affiliate->affiliate_id . "&amp;paid=1&amp;confirmed=1"; ?>">
					
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
				
															  $ps_vma->formatAmount($discountRate["discount_amount"]) : 
															  
															  $discountRate["discount_amount"] . "%"; ?></div>
            
            </div>
            
            <?php } ?>
            
        </div>
        
    </div>

	<div style="clear: both;"></div>
    
</div>