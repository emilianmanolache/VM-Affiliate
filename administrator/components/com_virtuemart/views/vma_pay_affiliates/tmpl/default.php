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

JToolBarHelper::title(JText::_('PAY_AFFILIATES'), 'head adminPayIcon');

// initiate other variables

$link = $vmaHelper->getAdminLink();

// get payment method names

$methodsNames = $vmaHelper->getPaymentMethodsNames();

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
	
    <table class="adminlist" cellspacing="0" cellpadding="0">

        <thead>
    
            <tr>
    
                <th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->affiliates); ?>')" /></th>
        
                <th style="text-align: left;"><?php echo JText::_("AFFILIATE_ID"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("NAME"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("CLICKS"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("UNIQUE_CLICKS"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("SALES"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("PAYMENT_METHOD"); ?></th>
                
                <th style="text-align: left;"><?php echo JText::_("BALANCE"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("PAY"); ?></th>
    
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
			
					$checked		= JHTML::_('grid.id', $i, $affiliate->affiliate_id);

					?>

		    		<tr class="row<?php echo $k ; ?>">

						<!-- add checkbox -->
                        
						<td style="text-align: center;"><?php echo $checked; ?></td>
					
                    	<!-- add affiliate id -->
                        
                    	<td><?php echo $affiliate->affiliate_id; ?></td>
                        
                        <!-- add name -->
                        
                        <td><?php echo $affiliate->fname . " " . $affiliate->lname; ?></td>
						
                        <!-- add clicks -->
                        
                        <td><?php echo $clicks; ?></td>
                              
						<!-- add unique clicks -->
                        
                        <td><?php echo $uniqueClicks; ?></td>
                              
						<!-- add sales -->
                        
                        <td><?php echo $confirmedSales 	. "/" . $totalSales; ?></td>
                        
                        <!-- add payment method -->
                        
                        <td><?php echo $methodsNames[$affiliate->method]["name"]; ?></td>
                        
                        <!-- add balance -->
                        
                        <td style="text-align: right;"><?php echo $vmaHelper->formatAmount($affiliate->commissions); ?></td>
                        
                        <!-- add pay button -->
                        
                        <td style="text-align: center;"><?php echo "<a href=\"" . ($affiliate->method == "1" ? $vmaHelper->getPayPalLink($affiliate) : $link . 
						
							  "_pay_affiliates&amp;task=payAffiliate&amp;affiliate_id=" . $affiliate->affiliate_id) . "\" title=\"" . JText::_("PAY_AFFILIATE") . 
							  
							  " (" . $methodsNames[$affiliate->method]["name"] . ")" . "\">" . "<img src=\"" . $vmaHelper->_website . "components/com_affiliate/assets/images/pay_" . 
							  
							  $methodsNames[$affiliate->method]["image"] . ".png\" /></a>" ?></td>
                        
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

	<input type="hidden" name="task" 			value="" 				/>

	<input type="hidden" name="option" 			value="com_virtuemart"	/>

	<input type="hidden" name="view" 			value="pay_affiliates"	/>

	<input type="hidden" name="boxchecked" 		value="0"				/>

	<?php echo JHTML::_('form.token'); ?>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>