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

JToolBarHelper::title(JText::_('PRODUCT_ADS'), 'head adminProductAdsIcon');

// initiate other variables

$link = $vmaHelper->getAdminLink();

// load the modal box

JHTML::_("behavior.modal");

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
    
                <th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->productAds); ?>')" /></th>
        
                <th style="text-align: left;"><?php echo JText::_("PRODUCT_NAME"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("CATEGORY"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("PREVIEW"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("PUBLISHED"); ?></th>
    
            </tr>
    
        </thead>
    
        <tbody>
        
        <?php

			if (count($this->productAds) > 0) {
				
				$i = 0;

    			$k = 0;
				
				foreach ($this->productAds as $key => $productad) {

					// process data
			
					$checked		= JHTML::_('grid.id', $i, $productad->productID);
			
					?>

		    		<tr class="row<?php echo $k ; ?>">

						<!-- add checkbox -->
                        
						<td style="text-align: center;"><?php echo $checked; ?></td>
                        
                        <!-- add product name  -->
                        
                        <td><?php echo "<a href=\"index.php?option=com_virtuemart&amp;view=product&amp;task=edit&amp;virtuemart_product_id=" . $productad->productID . 
						
									   "&amp;product_parent_id=0\">" . $productad->productName . "</a>"; ?></td>
						
                        <!-- add category name -->
                        
                        <td><?php echo $productad->categoryName; ?></td>
                        
                        <!-- add preview link -->
                        
                        <td><?php echo "<a class=\"modal\" href=\"" . $vmaHelper->_website . "index.php?option=com_affiliate&amp;view=prev&amp;tmpl=component&amp;type=productads&amp;id=" . 
			
								  $productad->productID . "&amp;frontend=0&amp;format=raw\">" . 
								  
								  JText::_("PREVIEW") . 
																  
								  "</a>"; ?></td>
                              
                        <!-- add toggle button -->
                        
                        <td style="text-align: center;"><?php echo $vmaHelper->itemToggleButton($productad->productID, "productad", ($productad->productPublished == '0' ? 0 : 1)); ?></td>
                        
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

	<input type="hidden" name="view" 			value="vma_product_ads" />

	<input type="hidden" name="boxchecked" 		value="0"				/>

	<?php echo JHTML::_('form.token'); ?>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>