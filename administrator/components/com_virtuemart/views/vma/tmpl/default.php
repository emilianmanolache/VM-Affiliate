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

JToolBarHelper::title(JText::_('AFFILIATE_PROGRAM_SUMMARY'), 'head adminSummaryIcon');

// get link

$link		= $vmaHelper->getAdminLink();

// check if there are any affiliates that need to be paid

$payQuery	= "SELECT COUNT(*) FROM #__vm_affiliate WHERE `commissions` >= '" . $vmaSettings->pay_balance . "' AND `method` != '' AND `method` != 'N/A' AND `blocked` != '1'";

$vmaHelper->_db->setQuery($payQuery);

$toBePaid	= $vmaHelper->_db->loadResult();

?>

<div class="affiliateAdminPage">

	<?php if ($toBePaid > 0) { ?>
    
    <br />
    
    <div style="text-align: center;">
    
    	<a href="<?php echo $link . "_pay_affiliates"; ?>" class="affiliatePayoutNotice">
            
			<?php 
			
			echo JText::sprintf("PAYOUT_NOTICE", $toBePaid, $vmaHelper->formatAmount($vmaSettings->pay_balance), 
		
								$vmaSettings->pay_day, (date("j") > $vmaSettings->pay_day ? JText::_("NEXT") : JText::_("THIS"))); 
								
			?>
            
		</a>
        
    </div>
    
    <?php } ?>
    
    <br />
    
    <div id="adminPanel">
    
        <div id="adminPanelIcons">
        
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_affiliates"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/users.png"; ?>" alt="<?php echo JText::_("MANAGE_AFFILIATES"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_affiliates"); ?>">
                
                    <?php echo JText::_("MANAGE_AFFILIATES"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_pay_affiliates"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/pay.png"; ?>" alt="<?php echo JText::_("PAY_AFFILIATES"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_pay_affiliates"); ?>">
                
                    <?php echo JText::_("PAY_AFFILIATES"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_banners"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/banners.png"; ?>" alt="<?php echo JText::_("BANNERS"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_banners"); ?>">
                
                    <?php echo JText::_("BANNERS"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_textads"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/textads.png"; ?>" alt="<?php echo JText::_("TEXTADS"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_textads"); ?>">
                
                    <?php echo JText::_("TEXT_ADS"); ?>
                    
                </a>
                
            </div>
            
            <div style="clear: both"></div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_commission_rates"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/commissions.png"; ?>" alt="<?php echo JText::_("COMMISSION_RATES"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_commission_rates"); ?>">
                
                    <?php echo JText::_("COMMISSION_RATES"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_configuration"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/preferences.png"; ?>" alt="<?php echo JText::_("CONFIGURATION"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_configuration"); ?>">
                
                    <?php echo JText::_("CONFIGURATION"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_payment_methods"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/methods.png"; ?>" alt="<?php echo JText::_("PAYMENT_METHODS"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_payment_methods"); ?>">
                
                    <?php echo JText::_("PAYMENT_METHODS"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_email_affiliates"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/emails.png"; ?>" alt="<?php echo JText::_("EMAIL_AFFILIATES"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_email_affiliates"); ?>">
                
                    <?php echo JText::_("EMAIL_AFFILIATES"); ?>
                    
                </a>
                
            </div>
            
            <div style="clear: both"></div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_traffic"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/traffic.png"; ?>" alt="<?php echo JText::_("TRAFFIC"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_traffic"); ?>">
                
                    <?php echo JText::_("TRAFFIC"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_sales"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/sales.png"; ?>" alt="<?php echo JText::_("SALES"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_sales"); ?>">
                
                    <?php echo JText::_("SALES"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_payments"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/payments.png"; ?>" alt="<?php echo JText::_("PAYMENTS"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_payments"); ?>">
                
                    <?php echo JText::_("PAYMENTS"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_statistics"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/statistics.png"; ?>" alt="<?php echo JText::_("STATISTICS"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_statistics"); ?>">
                
                    <?php echo JText::_("STATISTICS"); ?>
                    
                </a>
                
            </div>
            
            <div style="clear: both"></div>
            
            <div class="adminPanelIcon">
                
                <a href="<?php echo JRoute::_($link . "_about"); ?>">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/about.png"; ?>" alt="<?php echo JText::_("ABOUT"); ?>" /></a>
                
                <br />
                
                <a href="<?php echo JRoute::_($link . "_about"); ?>">
                
                    <?php echo JText::_("ABOUT"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="http://www.globacide.com/virtuemart-affiliate/user-manual.html">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/manual.png"; ?>" alt="<?php echo JText::_("MANUAL"); ?>" /></a>
                
                <br />
                
                <a href="http://www.globacide.com/virtuemart-affiliate/user-manual.html">
                
                    <?php echo JText::_("MANUAL"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="http://www.globacide.com/virtuemart-affiliate/forum.html">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/forum.png"; ?>" alt="<?php echo JText::_("FORUM"); ?>" /></a>
                
                <br />
                
                <a href="http://www.globacide.com/virtuemart-affiliate/forum.html">
                
                    <?php echo JText::_("FORUM"); ?>
                    
                </a>
                
            </div>
            
            <div class="adminPanelIcon">
                
                <a href="http://www.globacide.com">
                
                    <img src="<?php echo $vmaHelper->_website 		. "components/com_affiliate/assets/images/globacide.png"; ?>" alt="Globacide Solutions" /></a>
                
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
                    
                    <span class="affiliateSummaryValue"><?php echo $vmaHelper->getRegisteredAffiliates("thismonth"); ?></span>
                
                </div>
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("CONFIRMED_ORDERS_THIS_MONTH"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $vmaHelper->getConfirmedOrders("thismonth"); ?></span>
                
                </div>
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("UNIQUE_VISITORS_THIS_MONTH"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $vmaHelper->getUniqueVisitors("thismonth"); ?></span>
                    
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
                    
                    <span class="affiliateSummaryValue"><?php echo $vmaHelper->getRegisteredAffiliates("lastmonth"); ?></span>
                
                </div>
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("CONFIRMED_ORDERS_LAST_MONTH"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $vmaHelper->getConfirmedOrders("lastmonth"); ?></span>
                
                </div>
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("UNIQUE_VISITORS_LAST_MONTH"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $vmaHelper->getUniqueVisitors("lastmonth"); ?></span>
                    
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
                    
                    <span class="affiliateSummaryValue"><?php echo $vmaHelper->getRegisteredAffiliates(); ?></span>
                
                </div>
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("TOTAL_CONFIRMED_ORDERS"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $vmaHelper->getConfirmedOrders(); ?></span>
                
                </div>
                
                <div class="affiliateSummaryRow">
                
                	<span class="affiliateSummaryTopic"><?php echo JText::_("TOTAL_UNIQUE_VISITORS"); ?>:</span>
                    
                    <span class="affiliateSummaryValue"><?php echo $vmaHelper->getUniqueVisitors(); ?></span>
                    
				</div>
                
            </div>
            
        </div>
        
    </div>
    
    <div style="clear: both;"></div>
    
</div>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>