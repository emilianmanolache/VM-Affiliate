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

JToolBarHelper::title(JText::_('TEXT_ADS'), 'head adminTextAdsIcon');

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
    
                <th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->textads); ?>')" /></th>
        
                <th style="text-align: left;"><?php echo JText::_("TITLE"); ?></th>
        
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

			if (count($this->textads) > 0) {
				
				$i = 0;

    			$k = 0;
				
				foreach ($this->textads as $key => $textad) {

					// process data
			
					$checked		= JHTML::_('grid.id', $i, $textad->textad_id);
					
					$hits 			= $vmaHelper->getHits("textad", $textad->textad_id);
			
					$hits 			= $hits ? $hits : "0";
			
					?>

		    		<tr class="row<?php echo $k ; ?>">

						<!-- add checkbox -->
                        
						<td style="text-align: center;"><?php echo $checked; ?></td>
                        
                        <!-- add title  -->
                        
                        <td><?php echo "<a href=\"" . JRoute::_($link . "_textads&amp;task=edit&amp;textad_id=" . $textad->textad_id) . "\">" . $textad->title . "</a>"; ?></td>
                              
						<!-- add size -->
                        
                        <td><?php echo ($textad->width || $textad->height ? 
			
							  (($textad->width ? $textad->width : "&#8734;") . "x" . ($textad->height ? $textad->height : "&#8734;")) : 
							  
							  "&#8734;") . ($textad->sizegroup ? " " . "(" . $textad->sizegroup . ")" : NULL); ?></td>
                              
						<!-- add hits -->
                        
                        <td><?php echo $hits; ?></td>
                              
                        <!-- add link -->
                        
                        <td><?php echo $vmaHelper->processLink($textad->link); ?></td>
                        
                        <!-- add preview link -->
                        
                        <td><?php echo "<a class=\"modal\" href=\"" 	. $vmaHelper->_website . "index.php?option=com_affiliate&amp;view=prev&amp;tmpl=component&amp;type=textads&amp;id=" . 
			
									  $textad->textad_id . "&amp;frontend=0&amp;format=raw\">" . 
									  
									  JText::_("PREVIEW") . 
																	  
									  "</a>"; ?></td>
                        
                        <!-- add toggle button -->
                        
                        <td style="text-align: center;"><?php echo $vmaHelper->itemToggleButton($textad->textad_id, "textad", $textad->published); ?></td>
                        
                        <!-- add delete button -->
                        
                        <td style="text-align: center;"><?php echo $vmaHelper->itemDeleteButton($textad->textad_id, "textad"); ?></td>
                        
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

	<input type="hidden" name="view" 			value="vma_textads"		/>

	<input type="hidden" name="boxchecked" 		value="0"				/>

	<?php echo JHTML::_('form.token'); ?>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>