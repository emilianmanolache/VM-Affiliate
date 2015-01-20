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

global $ps_vma, $vmaSettings;

$link		= $ps_vma->getAdminLink();

// check if there are any affiliates that need to be paid

$payQuery	= "SELECT COUNT(*) FROM #__vm_affiliate WHERE `commissions` >= '" . $vmaSettings->pay_balance . "' AND `method` != '' AND `method` != 'N/A' AND `blocked` != '1'";

$ps_vma->_db->setQuery($payQuery);

$toBePaid	= $ps_vma->_db->loadResult();

?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminSummaryIcon">
    
        <h1 class="adminPanelTitle"><?php echo JText::_("AFFILIATE_PROGRAM_SUMMARY"); ?></h1>
        
    </div>
    
    <?php if ($toBePaid > 0) { ?>
    
    <br />
    
    <div style="text-align: center;">
    
    	<a href="<?php echo $link . "page=vma.pay_affiliates"; ?>" class="affiliatePayoutNotice">
            
			<?php 
			
			echo JText::sprintf("PAYOUT_NOTICE", $toBePaid, $ps_vma->formatAmount($vmaSettings->pay_balance), 
		
								$vmaSettings->pay_day, (date("j") > $vmaSettings->pay_day ? JText::_("NEXT") : JText::_("THIS"))); 
								
			?>
            
		</a>
        
    </div>
    
    <?php } ?>
    
    <br />
    
    <div id="adminPanel">
    
        <div id="adminPanelIcons">
        
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.affiliate_list"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/users.png"; ?>" alt="<?php echo JText::_("MANAGE_AFFILIATES"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.affiliate_list"); ?>">
                
                    <?php echo JText::_("MANAGE_AFFILIATES"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.pay_affiliates"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/pay.png"; ?>" alt="<?php echo JText::_("PAY_AFFILIATES"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.pay_affiliates"); ?>">
                
                    <?php echo JText::_("PAY_AFFILIATES"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.banners_list"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/banners.png"; ?>" alt="<?php echo JText::_("BANNERS"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.banners_list"); ?>">
                
                    <?php echo JText::_("BANNERS"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.textads_list"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/textads.png"; ?>" alt="<?php echo JText::_("TEXTADS"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.textads_list"); ?>">
                
                    <?php echo JText::_("TEXT_ADS"); ?>
                    
                </a>
                
            </div>
            
            <div style="clear: both"></div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.commission_rates_form"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/commissions.png"; ?>" alt="<?php echo JText::_("COMMISSION_RATES"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.commission_rates_form"); ?>">
                
                    <?php echo JText::_("COMMISSION_RATES"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.configuration_form"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/preferences.png"; ?>" alt="<?php echo JText::_("CONFIGURATION"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.configuration_form"); ?>">
                
                    <?php echo JText::_("CONFIGURATION"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.payment_methods_list"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/methods.png"; ?>" alt="<?php echo JText::_("PAYMENT_METHODS"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.payment_methods_list"); ?>">
                
                    <?php echo JText::_("PAYMENT_METHODS"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.email_affiliates"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/emails.png"; ?>" alt="<?php echo JText::_("EMAIL_AFFILIATES"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.email_affiliates"); ?>">
                
                    <?php echo JText::_("EMAIL_AFFILIATES"); ?>
                    
                </a>
                
            </div>
            
            <div style="clear: both"></div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.traffic"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/traffic.png"; ?>" alt="<?php echo JText::_("TRAFFIC"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.traffic"); ?>">
                
                    <?php echo JText::_("TRAFFIC"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.sales"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/sales.png"; ?>" alt="<?php echo JText::_("SALES"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.sales"); ?>">
                
                    <?php echo JText::_("SALES"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.payments"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/payments.png"; ?>" alt="<?php echo JText::_("PAYMENTS"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.payments"); ?>">
                
                    <?php echo JText::_("PAYMENTS"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.statistics"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/statistics.png"; ?>" alt="<?php echo JText::_("STATISTICS"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.statistics"); ?>">
                
                    <?php echo JText::_("STATISTICS"); ?>
                    
                </a>
                
            </div>
            
            <div style="clear: both"></div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "page=vma.about"); ?>">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/about.png"; ?>" alt="<?php echo JText::_("ABOUT"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "page=vma.about"); ?>">
                
                    <?php echo JText::_("ABOUT"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="http://www.globacide.com/virtuemart-affiliate/user-manual.html">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/manual.png"; ?>" alt="<?php echo JText::_("MANUAL"); ?>" /></a>
                
                <br />
                
                <a href="http://www.globacide.com/virtuemart-affiliate/user-manual.html">
                
                    <?php echo JText::_("MANUAL"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="http://www.globacide.com/virtuemart-affiliate/forum.html">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/forum.png"; ?>" alt="<?php echo JText::_("FORUM"); ?>" /></a>
                
                <br />
                
                <a href="http://www.globacide.com/virtuemart-affiliate/forum.html">
                
                    <?php echo JText::_("FORUM"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="http://www.globacide.com">
                
                    <img src="<?php echo $ps_vma->_website 		. "components/com_affiliate/assets/images/globacide.png"; ?>" alt="Globacide Solutions" /></a>
                
                <br />
                
                <a href="http://www.globacide.com">Globacide.com</a>
                
            </div>
        
        </div>
        
        <div id="affiliateProgramSummary">

            <div>
                
                <div>
                
                	<span><strong><?php echo JText::_("THIS_MONTH"); ?></strong></span>
                
                </div>
                
                <br />
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("AFFILIATES_REGISTERED_THIS_MONTH"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $ps_vma->getRegisteredAffiliates("thismonth"); ?></span>
                
                </div>
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("CONFIRMED_ORDERS_THIS_MONTH"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $ps_vma->getConfirmedOrders("thismonth"); ?></span>
                
                </div>
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("UNIQUE_VISITORS_THIS_MONTH"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $ps_vma->getUniqueVisitors("thismonth"); ?></span>
                    
				</div>
                
            </div>
            
            <br />
            
            <br />
            
            <div>
                
                <div>
                
                	<span><strong><?php echo JText::_("LAST_MONTH"); ?></strong></span>
                
                </div>
                
                <br />
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("AFFILIATES_REGISTERED_LAST_MONTH"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $ps_vma->getRegisteredAffiliates("lastmonth"); ?></span>
                
                </div>
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("CONFIRMED_ORDERS_LAST_MONTH"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $ps_vma->getConfirmedOrders("lastmonth"); ?></span>
                
                </div>
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("UNIQUE_VISITORS_LAST_MONTH"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $ps_vma->getUniqueVisitors("lastmonth"); ?></span>
                    
				</div>
                
            </div>
            
            <br />
            
            <br />
            
            <div>
                
                <div>
                
                	<span><strong><?php echo JText::_("OVERALL"); ?></strong></span>
                
                </div>
                
                <br />
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("TOTAL_AFFILIATES_REGISTERED"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $ps_vma->getRegisteredAffiliates(); ?></span>
                
                </div>
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("TOTAL_CONFIRMED_ORDERS"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $ps_vma->getConfirmedOrders(); ?></span>
                
                </div>
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("TOTAL_UNIQUE_VISITORS"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $ps_vma->getUniqueVisitors(); ?></span>
                    
				</div>
                
            </div>
            
        </div>
        
    </div>
    
    <div style="clear: both;"></div>

</div>