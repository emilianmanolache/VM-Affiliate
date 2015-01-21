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

JToolBarHelper::title(JText::_('CATEGORY_ADS'), 'head adminCategoryAdsIcon');

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
    
                    <?php echo ShopFunctions::displayDefaultViewSearch('', $this->search); ?>
    
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
    
                <th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->categoryAds); ?>')" /></th>
        
                <th style="text-align: left;"><?php echo JText::_("NAME"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("PRODUCTS"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("PREVIEW"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("PUBLISHED"); ?></th>
    
            </tr>
    
        </thead>
    
        <tbody>
        
        <?php

			if (count($this->categoryAds) > 0) {
				
				$i = 0;

    			$k = 0;
				
				foreach ($this->categoryAds as $key => $categoryad) {

					// process data
			
					$checked		= JHTML::_('grid.id', $i, $categoryad->categoryID);
			
					?>

		    		<tr class="row<?php echo $k ; ?>">

						<!-- add checkbox -->
                        
						<td style="text-align: center;"><?php echo $checked; ?></td>
                        
                        <!-- add category name  -->
                        
                        <td><?php echo "<a href=\"index.php?option=com_virtuemart&amp;view=category&amp;task=edit&amp;cid=" . $categoryad->categoryID . "\">" . 
						
									   $categoryad->categoryName . "</a>"; ?></td>
						
                        <!-- add products count -->
                        
                        <td><?php echo $categoryad->productsNo; ?></td>
                        
                        <!-- add preview link -->
                        
                        <td><?php echo "<a class=\"modal\" href=\"" . $vmaHelper->_website . "index.php?option=com_affiliate&amp;view=prev&amp;tmpl=component&amp;type=categoryads&amp;id=" . 
			
									  $categoryad->categoryID . "&amp;frontend=0&amp;format=raw\">" . 
									  
									  JText::_("PREVIEW") . 
																	  
									  "</a>"; ?></td>
                              
                        <!-- add toggle button -->
                        
                        <td style="text-align: center;"><?php echo $vmaHelper->itemToggleButton($categoryad->categoryID, "categoryad", ($categoryad->categoryPublished == '0' ? 0 : 1)); ?></td>
                        
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

	<input type="hidden" name="view" 			value="vma_category_ads" />

	<input type="hidden" name="boxchecked" 		value="0"				/>

	<?php echo JHTML::_('form.token'); ?>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>