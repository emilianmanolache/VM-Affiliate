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

JToolBarHelper::title(JText::_('BANNERS'), 'head adminBannersIcon');

// initiate other variables

$link = $vmaHelper->getAdminLink();

// load the modal box

JHTML::_("behavior.modal", "a.affiliateModal");

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
    
                <th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->banners); ?>')" /></th>
        
                <th style="text-align: left;"><?php echo JText::_("NAME"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("TYPE"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("SIZE"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("HITS"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("LINK"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("PREVIEW"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("PUBLISHED"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("REMOVE"); ?></th>
    
            </tr>
    
        </thead>
    
        <tbody>
        
        <?php

			if (count($this->banners) > 0) {
				
				$i = 0;

    			$k = 0;
				
				foreach ($this->banners as $key => $banner) {

					// process data
			
					$checked		= JHTML::_('grid.id', $i, $banner->banner_id);
					
					$hits 			= $vmaHelper->getHits("banner", $banner->banner_id);
			
					$hits 			= $hits ? $hits : "0";
			
					?>

		    		<tr class="row<?php echo $k ; ?>">

						<!-- add checkbox -->
                        
						<td style="text-align: center;"><?php echo $checked; ?></td>
                        
                        <!-- add name  -->
                        
                        <td><?php echo "<a href=\"" . JRoute::_($link . "_banners&amp;task=edit&amp;banner_id=" . $banner->banner_id) . "\">" . $banner->banner_name . "</a>"; ?></td>
						
                        <!-- add type -->
                        
                        <td><?php echo strtoupper($banner->banner_type); ?></td>
                              
						<!-- add size -->
                        
                        <td><?php echo $banner->banner_width . "x" . $banner->banner_height . ($banner->sizegroup ? " " . "(" . $banner->sizegroup . ")" : NULL); ?></td>
                              
						<!-- add hits -->
                        
                        <td><?php echo $hits; ?></td>
                              
                        <!-- add link -->
                        
                        <td><?php echo $vmaHelper->processLink($banner->banner_link); ?></td>
                        
                        <!-- add preview link -->
                        
                        <td><?php echo "<a class=\"affiliateModal\" href=\"" . $vmaHelper->_website . "index.php?option=com_affiliate&amp;view=prev&amp;tmpl=component&amp;type=banners&amp;id=" . 
			
									  $banner->banner_id . "&amp;frontend=0&amp;format=raw\" rel=\"{size: {x: " . $banner->banner_width . ", y: " . $banner->banner_height . 
									  
									  "}, classWindow: 'affiliatePreviewWindow'}\">" . JText::_("PREVIEW") . 
																	  
									  "</a>"; ?></td>
                        
                        <!-- add toggle button -->
                        
                        <td style="text-align: center;"><?php echo $vmaHelper->itemToggleButton($banner->banner_id, "banner", $banner->published); ?></td>
                        
                        <!-- add delete button -->
                        
                        <td style="text-align: center;"><?php echo $vmaHelper->itemDeleteButton($banner->banner_id, "banner"); ?></td>
                        
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

	<input type="hidden" name="view" 			value="vma_banners"		/>

	<input type="hidden" name="boxchecked" 		value="0"				/>

	<?php echo JHTML::_('form.token'); ?>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>