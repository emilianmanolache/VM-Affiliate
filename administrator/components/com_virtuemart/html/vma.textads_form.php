<?php

/**
 * @package   VM Affiliate
 * @version   4.5.0 May 2011
 * @author    Globacide Solutions http://www.globacide.com
 * @copyright Copyright (C) 2006 - 2011 Globacide Solutions
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access

defined( '_JEXEC' ) or die( 'Direct access to this location is not allowed.' );

// initialize required variables

global $ps_vma, $sess;

$document 			= &JFactory::getDocument();

$editor				= &JFactory::getEditor();

$link				= $ps_vma->getAdminLink();

// load the modal box

JHTML::_('behavior.modal');
			
// get text ad information

$textadID			= &JRequest::getVar("textad_id", 	"");

if ($textadID) {
	
	$query			= "SELECT * FROM #__vm_affiliate_textads WHERE `textad_id` = '" . $textadID . "'";
	
	$ps_vma->_db->setQuery($query);
	
	$textad			= $ps_vma->_db->loadObject();

}		

// build the link/size rows

$ps_vma->initiateAdForm("textads", (!$textadID ? "add" : "edit"), (!$textadID ? NULL : $textad));

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
            
<form action="<?php echo $link . "page=vma.textads_list"; ?>" method="post" name="adminForm">
            
    <div class="affiliateAdminPage">
    
        <div class="adminPanelTitleIcon" id="adminTextAdsIcon">
        
            <h1 class="adminPanelTitle">
            
                <?php echo JText::_("TEXT_AD_FORM") . ($textadID ? ": " . $textad->title : NULL); ?>
                
            </h1>
            
        </div>
        
        <div class="affiliateTopMenu">
            
            <div class="affiliateTopMenuLink">
            
                <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" 
                
                      class="affiliateTopMenuLinkItem">
                
                    <a href="<?php echo $link . "page=vma.textads_list"; ?>"><?php echo JText::_("TEXT_ADS"); ?></a>
                    
                </span>
                
            </div>
            
        </div>
        
        <div style="clear: both;"></div>
    
        <br />
        
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
					
						echo $ps_vma->getLinksRow("textads", (!$textadID ? "add" : "edit"), (!$textadID ? NULL : $textad));
						
					?>
                    
				</div>
            
            </div>
            
            <div class="affiliateFormRow" id="affiliateSizeRow" style="display: none;"></div>
            
            <div class="affiliateFormRow">
            	
                <div class="affiliateDetailsKey">&nbsp;</div>
                
                <div class="affiliateDetailsValue" style="height: 36px; margin-top: 10px;">
                    
                    <span class="affiliateButton">
                    
                    	<input type="hidden" name="option" 					value="com_virtuemart" />
                        
                        <input type="hidden" name="pshop_mode" 				value="admin" />
                        
                        <input type="hidden" name="page" 					value="vma.textads_list" />
                        
                        <input type="hidden" name="func"					value="vmaTextAdSave" />
                        
                        <input type="hidden" name="task"					value="" />
						
						<?php 
					
							if ($textadID) { 
							
								?><input type="hidden" name="textad_id"		value="<?php echo $textadID; ?>" /><?php 
								
							} 
						
						?>
                        
                        <input type="hidden" name="vmtoken"					value="<?php echo vmSpoofValue($sess->getSessionId()); ?>" />
                        
                        <input type="submit" 								value="<?php echo JText::_("SAVE"); ?>" style="width: auto; clear: both;" 
                    
                   			   class="affiliateButton affiliateSaveButton" 	onclick="if (!validateTextAdForm()) { return false; }" />
                               
					</span></div>
            
            </div>
            
            <div style="clear: both;"></div>
        
        </div>
        
    </div>

</form>