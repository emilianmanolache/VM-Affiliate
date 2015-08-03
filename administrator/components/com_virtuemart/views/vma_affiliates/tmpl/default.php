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

JToolBarHelper::title(JText::_('MANAGE_AFFILIATES'), 'head adminAffiliatesIcon');

// initiate other variables

$link 			= $vmaHelper->getAdminLink();

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
    
    <div id="header">

        <div id="filterbox" >
    
            <table>
    
            <tr>
    
                <td align="left" width="100%">
    
                    <?php echo $this->displayDefaultViewSearch(); ?>
    
                </td>
    
            </tr>
    
            </table>
    
        </div>
    
        <div id="resultscounter">
        
            <?php echo $this->pagination->getResultsCounter(); ?>
            
        </div>

    </div>
	
    <?php if ($this->toBePaid > 0) { ?>
    
    <div style="text-align: center; background-color: #F0F0F0; height: 48px; line-height: 48px;">
    
    	<a href="<?php echo $link . "_pay_affiliates"; ?>" class="affiliatePayoutNotice">
            
			<?php 
			
			echo JText::sprintf("PAYOUT_NOTICE", $this->toBePaid, $vmaHelper->formatAmount($vmaSettings->pay_balance), 
		
								$vmaSettings->pay_day, (date("j") > $vmaSettings->pay_day ? JText::_("NEXT") : JText::_("THIS"))); 
								
			?>
            
		</a>
        
    </div>
    
    <?php } ?>

    <div id="editcell">

    <table class="adminlist jgrid table table-striped" cellspacing="0" cellpadding="0">

        <thead>
    
            <tr>
    
                <th class="admin-checkbox"><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->affiliates); ?>')" /></th>
        
                <th style="text-align: left;"><?php echo JText::_("AFFILIATE_ID"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("NAME"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("CLICKS"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("UNIQUE_CLICKS"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("SALES"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("BALANCE"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("ENABLED"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("RATES"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("JTOOLBAR_EDIT"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("REMOVE"); ?></th>
    
            </tr>
    
        </thead>
    
        <tbody>
        
        <?php

			if (count($this->affiliates) > 0) {
				
				$i = 0;

    			$k = 0;
				
				foreach ($this->affiliates as $key => $affiliate) {

					// process data
            
            		$confirmedSales = $vmaHelper->getSales($affiliate->affiliate_id, true, true);
            
            		$totalSales		= $confirmedSales + $vmaHelper->getSales($affiliate->affiliate_id, true, false);
            
					$clicks			= $vmaHelper->getClicks($affiliate->affiliate_id, true, false);
			
					$uniqueClicks	= $vmaHelper->getClicks($affiliate->affiliate_id, true, true);
			
					$mustBePaid		= $affiliate->commissions >= $vmaSettings->pay_balance && $affiliate->method && $affiliate->method != "N/A" ? true : false;
			
					$checked		= JHTML::_('grid.id', $i, $affiliate->affiliate_id);

					?>

		    		<tr class="row<?php echo $k ; ?>">

						<!-- add checkbox -->
                        
						<td class="admin-checkbox"><?php echo $checked; ?></td>
					
                    	<!-- add affiliate id -->
                        
                    	<td><?php echo $affiliate->affiliate_id; ?></td>
                        
                        <!-- add name (attaching link which leads to affiliate's info page) -->
                        
                        <td><?php echo "<a href=\"" 											. $link 				. "_affiliates&amp;task=details&amp;affiliate_id=" 	. 
			
										$affiliate->affiliate_id . "\">" 						. $affiliate->fname 	. " " . $affiliate->lname . 
										
										"</a>"; ?></td>
						
                        <!-- add clicks (attaching link which leads to affiliate's traffic page) -->
                        
                        <td><?php echo ($clicks > 0 ? "<a href=\"" 								. $link 				. "_traffic&amp;affiliate_id=" 				. 
			
							  $affiliate->affiliate_id . "&amp;paid=0&amp;unique=0\">" : NULL)	. $clicks 				. ($clicks > 0 ? "</a>" : NULL); ?></td>
                              
						<!-- add unique clicks (attaching link which leads to affiliate's unique traffic page) -->
                        
                        <td><?php echo ($uniqueClicks > 0 ? "<a href=\"" 						. $link 				. "_traffic&amp;affiliate_id=" 				. 
			
							  $affiliate->affiliate_id . "&amp;paid=0&amp;unique=1\">" : NULL)	. $uniqueClicks 		. ($uniqueClicks > 0 ? "</a>" : NULL); ?></td>
                              
						<!-- add sales (attaching link which leads to affiliate's sales page) -->
                        
                        <td><?php echo ($confirmedSales > 0 || $totalSales > 0 ? "<a href=\"" 	. $link 			. "_sales&amp;affiliate_id=" 				. 
			
							  $affiliate->affiliate_id . "&amp;paid=0\">" : NULL) 				. $confirmedSales 	. "/" . $totalSales . 
							  
							  ($confirmedSales > 0 || $totalSales > 0 ? "</a>" : NULL); ?></td>
                              
                        <!-- add balance -->
                        
                        <td><?php echo ($mustBePaid ? "<a style=\"float: left;\" href=\"" 	. ($affiliate->method == "1" ? $vmaHelper->getPayPalLink($affiliate) : $link 				.
							  
							  "_pay_affiliate&amp;affiliate_id=" 	. $affiliate->affiliate_id) 	. "\" title=\"" 	. JText::_("PAY_AFFILIATE") . " (" 							. 
							  
							  $this->methodsNames[$affiliate->method]["name"] . ")" 	. "\">" . "<img src=\""		. $vmaHelper->_website . "components/com_affiliate/assets/images/pay_"	. 
							  
							  $this->methodsNames[$affiliate->method]["image"] . ".png\" " 	. "alt=\"Pay\" /></a>" : NULL) . "<span style=\"vertical-align: middle; float: left; " 	.
							  
							  ($mustBePaid ? "margin-top: 2px; margin-right: 6px; font-weight: bold;" : NULL) . "\">" . $vmaHelper->formatAmount($affiliate->commissions) . "</span>"; 
							  
							  ?></td>
                        
                        <!-- add affiliate enable/disable button -->
                        
                        <td><?php echo $vmaHelper->itemToggleButton($affiliate->affiliate_id, "affiliate", $affiliate->blocked); ?></td>
                        
                        <!-- add affiliate commission rates button -->
                        
                        <td><?php echo $vmaHelper->affiliateRatesButton($affiliate->affiliate_id); ?></td>
                        
                        <!-- add affiliate edit button -->
                        
                        <td><?php echo $vmaHelper->affiliateEditButton($affiliate->affiliate_id); ?></td>
                        
                        <!-- add affiliate delete button -->
                        
                        <td><?php echo $vmaHelper->itemDeleteButton($affiliate->affiliate_id, "affiliate"); ?></td>
                        
                    </tr>
                    
                    <?php
					
					$k = 1 - $k;

					$i++;
				
				}
				
			}
			
		?>
        
        </tbody>
        
        <tfoot>

	    	<tr>

				<td colspan="11">

					<?php echo $this->pagination->getListFooter(); ?>

				</td>

	    	</tr>

		</tfoot>
        
	</table>

    </div>

	<input type="hidden" name="task" 			value="" 				/>

	<input type="hidden" name="option" 			value="com_virtuemart"	/>

	<input type="hidden" name="view" 			value="vma_affiliates"	/>

	<input type="hidden" name="boxchecked" 		value="0"				/>

	<?php echo JHTML::_('form.token'); ?>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>