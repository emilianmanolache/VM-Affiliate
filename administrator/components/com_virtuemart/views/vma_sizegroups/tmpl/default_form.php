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

// get text ad information

$database			= JFactory::getDBO();

$document			= JFactory::getDocument();

$sizeGroupID		= &JRequest::getVar("size_group_id",	"");

$modal				= &JRequest::getVar("modal", 			"");

$type				= &JRequest::getVar("type", 			"");

// get the size group's details

if ($sizeGroupID) {
	
	$query			= "SELECT * FROM #__vm_affiliate_size_groups WHERE `size_group_id` = '" . $sizeGroupID . "'";
	
	$database->setQuery($query);
	
	$sizeGroup		= $database->loadObject();

}
	

// display the title

JToolBarHelper::title(JText::_("SIZE_GROUP_FORM") . ($sizeGroupID ? ": " . ($sizeGroup->name ? $sizeGroup->name : $sizeGroup->width . "x" . $sizeGroup->height) : NULL), 'head adminSizeGroupsEditIcon');

// initiate other variables

$link = $vmaHelper->getAdminLink();

// include number validation function

$numberValidationFunction		= $vmaHelper->numberValidationFunction("integer");

$document->addScriptDeclaration($numberValidationFunction);

?>

<form action="<?php echo $link . "_sizegroups"; ?>" method="post" name="adminForm">

    <div class="affiliateAdminPage" <?php 

		if ($modal) {
			
			echo "style=\"border: 0 none; width: 600px;\"";
			
		}
		
		?>>
    
        <div class="adminPanelTitleIcon adminSizeGroupsEditIcon" id="adminSizeGroupsEditIcon">
        
            <div style="float: left;">
            
                <h1 class="adminPanelTitle" <?php 
				
					echo $modal ? "style=\"height: 64px; overflow: hidden; width: 420px;\"" : NULL; 
					
					?>>
                
                    <?php echo JText::_("SIZE_GROUP_FORM") . ($sizeGroupID ? ": " . ($sizeGroup->name ? $sizeGroup->name : $sizeGroup->width . "x" . $sizeGroup->height) : NULL); ?>
                    
                </h1>
			
            </div>
                        
            <?php 
		
			if ($modal) {
				
				?>
                
                <div>
                
                    <div style="float: right; text-align: center; margin-top: 10px;">
                    
                        <a class="toolbar" href="javascript:void(0);" id="affiliateCancelSizeGroup">
                        
                            <div class="icon-32-cancel" type="Standard" style="height: 32px; width: 32px;"></div><?php 
                            
                                echo JText::_("JCANCEL");
                                
                            ?>
                            
                        </a>
                        
                    </div>
                    
                    <div style="float: right; text-align: center; margin-top: 10px; margin-right: 20px;">
                    
                        <a class="toolbar" href="javascript:void(0);" id="affiliateSaveSizeGroup">
                        
                            <div class="icon-32-save" type="Standard" style="height: 32px; width: 32px;"></div><?php 
                            
                                echo JText::_("JAPPLY");
                                
                            ?>
                            
                        </a>
                        
                    </div>
				
                </div>
                        
        		<?php
				
			}
			
		?>
        
        </div>
        
        <div class="affiliateTopMenu">
            
            <div class="affiliateTopMenuLink <?php
            
				if ($modal) {
					
					echo "affiliateSizeGroupMenuModal";
					
				}
				
				?>">
            
                <span style="background: url(<?php echo $vmaHelper->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" 
                
                      class="affiliateTopMenuLinkItem">
                
                    <a href="<?php echo $link . "_sizegroups&amp;type=" . $type; ?>" id="affiliateReturnSizeGroup"><?php echo JText::_("SIZE_GROUPS"); ?></a>
                    
                </span>
                
            </div>
            
        </div>
        
        <div style="clear: both;"></div>
        
        <br />
    
        <div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateSizeGroupName"><?php echo JText::_("NAME"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue">
                
                    <input id="affiliateSizeGroupName" type="text" name="name" value="<?php echo @$sizeGroup->name; ?>" />
                    
                </div>
            
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateSizeGroupWidth"><?php echo JText::_("WIDTH"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue">
                
                    <input id="affiliateSizeGroupWidth" type="text" name="width" value="<?php echo @$sizeGroup->width; ?>" 
                    
                    	   onkeydown="return validateNumber(event, this, 4);" />&nbsp;<strong>px</strong>
                    
                </div>
            
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateSizeGroupHeight"><?php echo JText::_("HEIGHT"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue">
                
                    <input id="affiliateSizeGroupHeight" type="text" name="height" value="<?php echo @$sizeGroup->height; ?>" 
                    
                    	   onkeydown="return validateNumber(event, this, 4);" />&nbsp;<strong>px</strong>
                    
                </div>
            
            </div>
            
            <div class="affiliateFormRow">
            
            	<div class="affiliateDetailsKey">&nbsp;</div>
                
                <div class="affiliateDetailsValue" style="margin-top: 10px;">
                
                    <span class="affiliateButton">
                        
                        <input type="hidden" name="option" 					value="com_virtuemart" />
            
                        <input type="hidden" name="view" 					value="vma_sizegroups" />
                        
                        <input type="hidden" name="task"					value="save" />
                        
                        <input type="hidden" name="type"					value="<?php echo $type; ?>"  />
                        
                        <input type="hidden" name="size_group_id"		value="<?php echo @$sizeGroup->size_group_id; ?>" />
            
                        <input type="submit" value="<?php echo JText::_("JAPPLY"); ?>" style="width: auto;" 
                        
                               class="affiliateButton affiliateSaveButton" id="affiliateSizeGroupSaveButton" />
                        
                        <img src="<?php echo $vmaHelper->_website . "components/com_affiliate/assets/images/spinner.gif"; ?>" alt="Spinner" style="display: none;" id="sizeGroupSpinner" />
                        
                        <?php echo JHTML::_('form.token'); ?>
                        
                    </span>
                    
				</div>
                
            </div>
            
        </div>
    
    	<?php 
		
			if (!$modal) {
				
			?>
            
        		<div style="clear: both;"></div>
        
        	<?php
			
			}
			
		?>
        
    </div>
    
</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>