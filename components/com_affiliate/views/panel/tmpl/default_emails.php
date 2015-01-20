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
 
// get vma settings

global $vmaSettings, $ps_vma;

$document 			= &JFactory::getDocument();

// add editor switching function

$editorSwitching	= "// switch the editor between text and html

					  function switchEditor() {
						  
						  var html = $('affiliateHTML').checked;

						  $('affiliateMessageText').style.display			= html ? 'none'		: 'block';
						  
						  $('affiliateMessageHTMLContainer').style.display	= html ? 'block'	: 'none';
						  
					  }";
					  
$document->addScriptDeclaration($editorSwitching);

// uncheck the html checkbox on page load

$uncheckHTML		= "// uncheck the html checkbox when the page has loaded

					   window.addEvent('domready', function() {
						   
						   $('affiliateHTML').checked = false;
						   
					   });";

$document->addScriptDeclaration($uncheckHTML);

?>

<div id="affiliateEmailsIcon" class="affiliatePanelIcon"><h2 class="affiliatePanelTitle"><?php echo JText::_("EMAIL_CAMPAIGN"); ?></h2></div>

<br />

<div>

	<form action="<?php echo JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=emails&task=emails")); ?>" method="post">
    
	<div class="affiliateEmailsRow">
    
    	<div class="affiliateEmailsLabel">
        
        	<label for="affiliateEmails"><?php echo JText::_("RECIPIENTS"); ?></label>
            
		</div>
        
        <div class="affiliateEmailsField">
        
        	<textarea name="recipients" id="affiliateEmails" rows="4" cols="30"></textarea>
            
            <br />
            
            <?php echo JText::sprintf("INPUT_CAMPAIGN_RECIPIENTS", "<br />"); ?>
            
		</div>
        
    </div>
    
    <div style="clear: both; height: 1px;"></div>
    
    <div class="affiliateEmailsRow">
    
    	<div class="affiliateEmailsLabel">
        
        	<label for="affiliateSubject"><?php echo JText::_("SUBJECT"); ?></label>
            
		</div>
        
        <div class="affiliateEmailsField">
        
        	<input type="text" name="subject" id="affiliateSubject" />
            
		</div>
        
    </div>
    
    <div style="clear: both;"></div>
    
    <div class="affiliateEmailsRow">
    
    	<div class="affiliateEmailsLabel">
        
        	<label for="affiliateHTML"><?php echo JText::_("SEND IN HTML MODE"); ?></label>
            
		</div>
        
        <div class="affiliateEmailsField">
        
        	<input id="affiliateHTML" name="html" type="checkbox" onchange="switchEditor();" style="margin-left: 0px; padding-left: 0px; width: auto;" />
            
		</div>
        
    </div>
    
    <div style="clear: both;"></div>
    
    <div class="affiliateEmailsRow">
    
    	<div class="affiliateEmailsLabel">
        
        	<label for="affiliateMessageText"><?php echo JText::_("MESSAGE"); ?></label>
            
		</div>
        
        <div class="affiliateEmailsField">
        
        	<textarea name="messageText" id="affiliateMessageText" rows="4" cols="30"></textarea>
            
            <div id="affiliateMessageHTMLContainer" style="display: none;">
                
				<?php echo $this->editor->display('messageHTML', NULL, '300', '100', '60', '20', false); ?>
                
            </div>
                
            <br />
            
            <?php echo JText::sprintf("INPUT_CAMPAIGN_MESSAGE", "<br /><strong><a href=\"javascript:document.getElementById('affiliateMessageText').value += '" 	. 
			
									  $this->affiliateLink . "'; document.getElementById('affiliateMessageText').focus();\">{afflink}</a></strong>"); ?>
            
		</div>
        
    </div>
    
    <div style="clear: both;"></div>
    
    <div class="affiliateEmailsRow">
    
    	<div class="affiliateEmailsLabel">&nbsp;</div>
        
        <div>
        
        	<input type="submit" value="<?php echo JText::_("SEND_MASS_EMAIL"); ?>" />
            
		</div>
        
    </div>
    
    </form>
    
</div>

<div style="clear: both;"></div>