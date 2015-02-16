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

?>

<div id="affiliateSummaryIcon" class="affiliatePanelIcon"><h2 class="affiliatePanelTitle"><?php echo JText::_("SUMMARY"); ?></h2></div>

<br />

<div class="affiliateSummaryContainer">

    <table class="affiliateSummaryContainer">
        
        <tr>
        
            <td colspan="2"><strong><?php echo JText::_("CURRENT"); ?></strong></td>
            
        </tr>
        
        <tr class="affiliateDataRow">
        
            <td class="affiliateSummaryKey"><?php echo JText::_("CLICKS"); ?></td>
            
            <td class="affiliateSummaryValue">
				
				<?php if ($this->currentClicks) { ?>
				
					<a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=traffic&paid=0&unique=0")); ?>">
					
				<?php } ?>
				
							<?php echo $this->currentClicks; ?>
                            
                <?php if ($this->currentClicks) { ?>
                
                	</a>
                    
                <?php } ?>
                
			</td>
            
        </tr>
        
        <tr class="affiliateDataRow">
        
            <td class="affiliateSummaryKey"><?php echo JText::_("UNIQUE_CLICKS"); ?></td>
            
            <td class="affiliateSummaryValue">
				
				<?php if ($this->currentUClicks) { ?>
				
					<a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=traffic&paid=0&unique=1")); ?>">
					
				<?php } ?>
				
							<?php echo $this->currentUClicks; ?>
                            
                <?php if ($this->currentUClicks) { ?>
                
                	</a>
                    
                <?php } ?>
                
			</td>
            
        </tr>
        
        <tr class="affiliateDataRow">
        
            <td class="affiliateSummaryKey"><?php echo JText::_("APPROVED_SALES"); ?></td>
            
            <td class="affiliateSummaryValue">
				
				<?php if ($this->currentASales) { ?>
				
					<a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=sales&paid=0&confirmed=1")); ?>">
					
				<?php } ?>
				
							<?php echo $this->currentASales; ?>
                            
                <?php if ($this->currentASales) { ?>
                
                	</a>
                    
                <?php } ?>
                
			</td>
            
        </tr>
        
        <tr class="affiliateDataRow">
        
            <td class="affiliateSummaryKey"><?php echo JText::_("PENDING_SALES"); ?></td>
            
            <td class="affiliateSummaryValue"><?php echo $this->pendingSales; ?></td>
            
        </tr>
        
        <?php if ($vmaSettings->multi_tier) { ?>
        
            <tr class="affiliateDataRow">
            
                <td class="affiliateSummaryKey"><?php echo JText::_("REFERRED_AFFILIATES"); ?></td>
                
                <td class="affiliateSummaryValue"><?php echo $this->referredAffs; ?></td>
                
            </tr>
        
        <?php } ?>
        
        <tr class="affiliateDataRow">
        
            <td class="affiliateSummaryKey"><em><?php echo JText::_("BALANCE"); ?></em></td>
            
            <td class="affiliateSummaryValue"><em><?php echo $this->currentBalance; ?></em></td>
            
        </tr>
        
    </table>
    
    <table class="affiliateSummaryContainer">
        
        <tr>
        
            <td colspan="2"><strong><?php echo JText::_("COMMISSION_RATES"); ?></strong></td>
            
        </tr>
    
    	<?php if ($this->generalRates["affiliate"]["per_click_fixed"] > 0) { ?>
        
            <tr class="affiliateDataRow">
            
                <td class="affiliateSummaryKey"><?php echo JText::_("PER_CLICK"); ?></td>
                
                <td class="affiliateSummaryValue"><?php echo $this->formattedRates["affiliate"]["per_click_fixed"]; ?></td>
                
            </tr>
        
        <?php } ?>
        
        <?php if ($this->generalRates["affiliate"]["per_unique_click_fixed"] > 0) { ?>
        
            <tr class="affiliateDataRow">
            
                <td class="affiliateSummaryKey"><?php echo JText::_("PER_UNIQUE_CLICK"); ?></td>
                
                <td class="affiliateSummaryValue"><?php echo $this->formattedRates["affiliate"]["per_unique_click_fixed"]; ?></td>
                
            </tr>
        
        <?php } ?>
        
        <?php if ($this->generalRates["affiliate"]["per_sale_fixed"] > 0) { ?>
        
        <tr class="affiliateDataRow">
        
            <td class="affiliateSummaryKey"><?php echo JText::_("PER_SALE") . " " . "(" . JText::_("FIXED_RATE") . ")"; ?></td>
            
            <td class="affiliateSummaryValue"><?php echo $this->formattedRates["affiliate"]["per_sale_fixed"]; ?></td>
            
        </tr>
        
        <?php } ?>
        
        <?php if ($this->generalRates["affiliate"]["per_sale_percentage"] > 0) { ?>
        
        <tr class="affiliateDataRow">
        
            <td class="affiliateSummaryKey"><?php echo JText::_("PER_SALE") . " " . "(" . JText::_("PERCENTAGE") . ")"; ?></td>
            
            <td class="affiliateSummaryValue"><?php echo $this->generalRates["affiliate"]["per_sale_percentage"]; ?>%</td>
            
        </tr>
        
        <?php } ?>
        
        <?php if ($vmaSettings->offline_tracking && $vmaSettings->offline_type == 3 && $this->discountRate["discount_amount"] > 0) { ?>
        
        <tr class="affiliateDataRow">
        
            <td class="affiliateSummaryKey"><?php echo JText::_("CUSTOMER_DISCOUNT"); ?></td>
            
            <td class="affiliateSummaryValue"><?php echo $this->formattedRate; ?></td>
            
        </tr>
        
        <?php } ?>
        
    </table>
    
</div>

<div class="affiliateSummaryContainer">
    
    <table class="affiliateSummaryContainer">
    
        <tr>
            
            <td colspan="2"><strong><?php echo JText::_("OVERALL"); ?></strong></td>
            
        </tr>
        
        <tr class="affiliateDataRow">
            
            <td class="affiliateSummaryKey"><?php echo JText::_("CLICKS"); ?></td>
            
            <td class="affiliateSummaryValue">
				
				<?php if ($this->overallClicks) { ?>
				
					<a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=traffic&paid=1&unique=0")); ?>">
					
				<?php } ?>
				
							<?php echo $this->overallClicks; ?>
                            
                <?php if ($this->overallClicks) { ?>
                
                	</a>
                    
                <?php } ?>
                
			</td>
            
        </tr>
        
        <tr class="affiliateDataRow">
            
            <td class="affiliateSummaryKey"><?php echo JText::_("UNIQUE_CLICKS"); ?></td>
            
            <td class="affiliateSummaryValue">
				
				<?php if ($this->overallUClicks) { ?>
				
					<a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=traffic&paid=1&unique=1")); ?>">
					
				<?php } ?>
				
							<?php echo $this->overallUClicks; ?>
                            
                <?php if ($this->overallUClicks) { ?>
                
                	</a>
                    
                <?php } ?>
                
			</td>
            
        </tr>
        
        <tr class="affiliateDataRow">
            
            <td class="affiliateSummaryKey"><?php echo JText::_("APPROVED_SALES"); ?></td>
            
            <td class="affiliateSummaryValue">
				
				<?php if ($this->overallASales) { ?>
				
					<a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=sales&paid=1&confirmed=1")); ?>">
					
				<?php } ?>
				
							<?php echo $this->overallASales; ?>
                            
                <?php if ($this->overallASales) { ?>
                
                	</a>
                    
                <?php } ?>
                
			</td>
            
        </tr>
        
        <tr class="affiliateDataRow">
            
            <td class="affiliateSummaryKey"><em><?php echo JText::_("TOTAL_COMMISSIONS_EARNED"); ?></em></td>
            
            <td class="affiliateSummaryValue"><em><?php echo $this->overallBalance; ?></em></td>
            
        </tr>
        
    </table>
    
    <table class="affiliateSummaryContainer">
    	
        <?php 
		
		if ($vmaSettings->multi_tier) {
			
			?>
            
			<tr>
        
                <td colspan="2"><strong><?php echo JText::_("TIER_EARNING_RATES"); ?></strong></td>
                
            </tr>
        
        	<?php
			
			for ($i = 0; $i < $vmaSettings->tier_level - 1; $i++) {
				
				if ($this->generalRates["tiers"][$i]["per_click_fixed"] > 0 ||
				
					$this->generalRates["tiers"][$i]["per_unique_click_fixed"] > 0 ||
					
					$this->generalRates["tiers"][$i]["per_sale_fixed"] > 0 ||
					
					$this->generalRates["tiers"][$i]["per_sale_percentage"] > 0) {
						
					?>
                
                    <tr>
            
                        <td colspan="2"><em><?php echo JText::_("FROM_TIER") . " " . ($i + 2) . ":"; ?></em></td>
                        
                    </tr>
        			
                    <?php if ($this->generalRates["tiers"][$i]["per_click_fixed"] > 0) { ?>
        
                        <tr class="affiliateDataRow">
                        
                            <td class="affiliateSummaryKey"><?php echo JText::_("PER_CLICK"); ?></td>
                            
                            <td class="affiliateSummaryValue"><?php echo $this->formattedRates["tiers"][$i]["per_click_fixed"]; ?></td>
                            
                        </tr>
                    
                    <?php } ?>
                    
                    <?php if ($this->generalRates["tiers"][$i]["per_unique_click_fixed"] > 0) { ?>
                    
                        <tr class="affiliateDataRow">
                        
                            <td class="affiliateSummaryKey"><?php echo JText::_("PER_UNIQUE_CLICK"); ?></td>
                            
                            <td class="affiliateSummaryValue"><?php echo $this->formattedRates["tiers"][$i]["per_unique_click_fixed"]; ?></td>
                            
                        </tr>
                    
                    <?php } ?>
                    
                    <?php if ($this->generalRates["tiers"][$i]["per_sale_fixed"] > 0) { ?>
                    
                    <tr class="affiliateDataRow">
                    
                        <td class="affiliateSummaryKey"><?php echo JText::_("PER_SALE") . " " . "(" . JText::_("FIXED_RATE") . ")"; ?></td>
                        
                        <td class="affiliateSummaryValue"><?php echo $this->formattedRates["tiers"][$i]["per_sale_fixed"]; ?></td>
                        
                    </tr>
                    
                    <?php } ?>
                    
                    <?php if ($this->generalRates["tiers"][$i]["per_sale_percentage"] > 0) { ?>
                    
                    <tr class="affiliateDataRow">
                    
                        <td class="affiliateSummaryKey"><?php echo JText::_("PER_SALE") . " " . "(" . JText::_("PERCENTAGE") . ")"; ?></td>
                        
                        <td class="affiliateSummaryValue"><?php echo $this->generalRates["tiers"][$i]["per_sale_percentage"]; ?>%</td>
                        
                    </tr>
                    
                    <?php } ?>
        
                	<?php
					
				}
				
			}
        
		}
		
		?>
        
    </table>
    
</div>

<div style="clear: both;"></div>