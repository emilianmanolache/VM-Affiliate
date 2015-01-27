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

JToolBarHelper::title(JText::_('SIZE_GROUPS'), 'head adminSizeGroupsIcon');

// initiate other variables

$link 		= $vmaHelper->getAdminLink();

$modal		= &JRequest::getVar("modal", 	"");

$type		= &JRequest::getVar("type", 	"");

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

	<?php if ($modal) { ?>

    <div class="affiliateAdminPage" style="border: 0 none;">
    
        <div class="adminPanelTitleIcon adminSizeGroupsIcon" id="adminSizeGroupsIcon">
        
            <div style="float: left;">
            
                <h1 class="adminPanelTitle" style="height: 64px; overflow: hidden; width: 420px;">
                
                    <?php echo JText::_("SIZE_GROUPS"); ?>
                    
                </h1>
                
            </div>
                    
            <div style="float: right; text-align: center; margin-top: 10px; margin-right: 10px;">
            
                <a class="toolbar" href="javascript:void(0);" id="affiliateNewSizeGroup">
                
                    <div class="icon-32-new" type="Standard" style="height: 32px; width: 32px;"></div><?php 
                    
                        echo JText::_("JTOOLBAR_NEW"); 
                        
                    ?>
                    
                </a>
                
            </div>
            
        </div>
        
        <?php } ?>
       
        <table class="adminlist" cellspacing="0" cellpadding="0">
    
            <thead>
        
                <tr>
        
                    <?php if (!$modal) { ?>
                    
                        <th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->sizegroups); ?>')" /></th>
                    
                    <?php } ?>
                    
                    <th style="text-align: left;"><?php echo JText::_("NAME"); ?></th>
            
                    <th style="text-align: left;"><?php echo JText::_("WIDTH"); ?></th>
            
                    <th style="text-align: left;"><?php echo JText::_("HEIGHT"); ?></th>
            
                    <th style="text-align: left;"><?php echo JText::_("REMOVE"); ?></th>
        
                </tr>
        
            </thead>
        
            <tbody>
            
            <?php
    
                if (count($this->sizegroups) > 0) {
                    
                    $i = 0;
    
                    $k = 0;
                    
                    foreach ($this->sizegroups as $key => $sizegroup) {
    
                        // process data
                
                        $checked		= JHTML::_('grid.id', $i, $sizegroup->size_group_id);
                
                        ?>
    
                        <tr class="row<?php echo $k ; ?>">
    
                            <!-- add checkbox -->
                            
                            <?php if (!$modal) { ?>
                            
                                <td style="text-align: center;"><?php echo $checked; ?></td>
                            
                            <?php } ?>
                            
                            <!-- add name  -->
                            
                            <td><?php echo "<a href=\"" . JRoute::_($link . "_sizegroups&amp;task=edit&amp;size_group_id=" . $sizegroup->size_group_id . 
							
										   ($modal ? "&amp;modal=true" : NULL)) . "&type=" . $type . "\">" . ($sizegroup->name ? $sizegroup->name : "N/A") . "</a>"; ?></td>
                                  
                            <!-- add width -->
                            
                            <td><?php echo $sizegroup->width 	? $sizegroup->width 	: "&#8734;"; ?></td>
                                  
                            <!-- add height -->
                            
                            <td><?php echo $sizegroup->height 	? $sizegroup->height 	: "&#8734;"; ?></td>
                            
                            <!-- add delete button -->
                            
                            <td style="text-align: center;"><?php echo $vmaHelper->itemDeleteButton($sizegroup->size_group_id, "sizegroup") . 
			
							  								"<div style=\"display: none;\">" . $sizegroup->size_group_id . "</div>"; ?></td>
                            
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
    
        <input type="hidden" name="view" 			value="vma_sizegroups"	/>
    
        <input type="hidden" name="boxchecked" 		value="0"				/>
    
        <?php echo JHTML::_('form.token'); ?>

	<?php if ($modal) { ?>
    
	</div>
    
    <?php } ?>
    
</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>