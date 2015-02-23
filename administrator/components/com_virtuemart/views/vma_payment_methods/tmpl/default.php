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

JToolBarHelper::title(JText::_('PAYMENT_METHODS'), 'head adminPaymentMethodsIcon');

// initiate other variables

$link = $vmaHelper->getAdminLink();

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
    
                <th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->methods); ?>')" /></th>
        
                <th style="text-align: left;"><?php echo JText::_("NAME"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("FIELDS"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("ENABLED"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("REMOVE"); ?></th>
    
            </tr>
    
        </thead>
    
        <tbody>
        
        <?php

			if (count($this->methods) > 0) {
				
				$i = 0;

    			$k = 0;
				
				foreach ($this->methods as $key => $method) {

					// process data
			
					$checked		= JHTML::_('grid.id', $i, $method->method_id);

					?>

		    		<tr class="row<?php echo $k ; ?>">

						<!-- add checkbox -->
                        
						<td style="text-align: center;"><?php echo $checked; ?></td>
                        
                        <!-- add payment method name -->
                        
                        <td><?php echo ($method->method_id > 1 ? "<a href=\"" . $link .	"_payment_methods&amp;task=edit&amp;method_id=" . $method->method_id . "\">" : NULL) . 
			
										$method->method_name . 
										
										($method->method_id > 1 ? "</a>" : NULL); ?></td>
						
                        <!-- add number of fields -->
                        
                        <td><?php echo $method->fields; ?></td>
                        
                        <!-- add affiliate enable/disable button -->
                        
                        <td style="text-align: center;"><?php echo $vmaHelper->itemToggleButton($method->method_id, "paymentmethod", $method->method_enabled); ?></td>
                        
                        <!-- add affiliate delete button -->
                        
                        <td style="text-align: center;"><?php echo ($method->method_id > 1 ? 
						
																	$vmaHelper->itemDeleteButton($method->method_id, "paymentmethod") : 
																	
																	"N/A"); ?></td>
                        
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

	<input type="hidden" name="task" 			value="" 					/>

	<input type="hidden" name="option" 			value="com_virtuemart"		/>

	<input type="hidden" name="view" 			value="vma_payment_methods"	/>

	<input type="hidden" name="boxchecked" 		value="0"					/>

	<?php echo JHTML::_('form.token'); ?>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>