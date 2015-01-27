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

$editor				= JFactory::getEditor();

$textadID			= &JRequest::getVar("textad_id", 	"");

if ($textadID) {
	
	$query			= "SELECT * FROM #__vm_affiliate_textads WHERE `textad_id` = '" . $textadID . "'";
	
	$database->setQuery($query);
	
	$textad			= $database->loadObject();

}		

// display the title

JToolBarHelper::title(JText::_("TEXT_AD_FORM") . ($textadID ? ": " . $textad->title : NULL), 'head adminTextAdsIcon');

// initiate other variables

$link = $vmaHelper->getAdminLink();

// load the modal box

JHTML::_('behavior.modal');

// build the link/size rows

$vmaHelper->initiateAdForm("textads", (!$textadID ? "add" : "edit"), (!$textadID ? NULL : $textad));

// include the text ad form validation function

$validateTextAdForm	= "// validate the text ad form

					   function validateTextAdForm() {
						  
						  if ($('affiliateTitle').value == '') {
							  
							  alert('" 	. JText::_("PROVIDE_TITLE", 		true) . "');
							  
							  return false;
							  
						  }
						  
						  if ($('affiliateSize').value == '') {
							  
							  alert('" 	. JText::_("FILL_IN_ALL_FIELDS", 	true) . "');
							  
							  return false;
							  
						  }
						  
						  return true;
						  
					  }";
					  
$document->addScriptDeclaration($validateTextAdForm);

?>

<form action="<?php echo $link . "_textads"; ?>" method="post" name="adminForm">

    <div class="affiliateAdminPage">
        
        <div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateTitle"><?php echo JText::_("TITLE"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue affiliateLongerInputs">
                
                    <input id="affiliateTitle" name="title" type="text" value="<?php echo $textadID ? $textad->title : NULL; ?>" />
                    
                </div>
            
            </div>
            
            <div class="affiliateFormRow">
            
            	<div class="affiliateDetailsKey">
                
                	<label for="affiliateText"><?php echo JText::_("TEXT"); ?></label>
                    
                </div>
                
                <div class="affiliateDetailsValue affiliateLongerInputs">
                
                	<?php echo $editor->display('affiliateText', ($textadID ? $textad->content : NULL), '380', '100', '60', '20', false); ?>
                    
                </div>
                
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateLink"><?php echo JText::_("LINK"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue affiliateLongerInputs">
                
                	<?php
					
						echo $vmaHelper->getLinksRow("textads", (!$textadID ? "add" : "edit"), (!$textadID ? NULL : $textad));
						
					?>
                    
				</div>
            
            </div>
            
            <div class="affiliateFormRow" id="affiliateSizeRow" style="display: none;"></div>
            
            <div class="affiliateFormRow">
            	
                <div class="affiliateDetailsKey">&nbsp;</div>
                
                <div class="affiliateDetailsValue" style="height: 36px; margin-top: 10px;">
                    
                    <span class="affiliateButton">
                    
                    	<input type="hidden" name="option" 					value="com_virtuemart" />
                        
                        <input type="hidden" name="view" 					value="vma_textads" />
                        
                        <input type="hidden" name="task"					value="save" />
						
						<?php 
					
							if ($textadID) { 
							
								?><input type="hidden" name="textad_id"		value="<?php echo $textadID; ?>" /><?php 
								
							} 
						
						?>
                        
                        <input type="submit" 								value="<?php echo JText::_("JAPPLY"); ?>" style="width: auto; clear: both;" 
                    
                   			   class="affiliateButton affiliateSaveButton" 	onclick="if (!validateTextAdForm()) { return false; }" />
						
                        <?php echo JHTML::_('form.token'); ?>
                        
					</span></div>
            
            </div>
            
            <div style="clear: both;"></div>
        
        </div>
        
    </div>
    
</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>