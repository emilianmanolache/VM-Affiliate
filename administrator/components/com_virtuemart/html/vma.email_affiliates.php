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

$language 			= &JFactory::getLanguage();

$editor				= &JFactory::getEditor();

$link				= $ps_vma->getAdminLink();

$email				= &JRequest::getVar("email");

// get com_massmail component's language

$language->load("com_massmail", JPATH_ROOT . DS . "administrator");

// get affiliates

$query				= "SELECT `affiliate_id`, `fname`, `lname`, `mail` FROM #__vm_affiliate WHERE `blocked` = '0'";

$ps_vma->_db->setQuery($query);

$affiliates			= $ps_vma->_db->loadObjectList();

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

// validate the e-mail form

$validateEmailForm	= "// validate the e-mail form

					   function validateEmailForm() {
						   
						   if ($('affiliateSubject').value == '' || ($('affiliateHTML').checked == false && $('affiliateMessageText').value == '')) {
							   
							   alert('" . JText::_("FILL_IN_ALL_FIELDS", true) 	. "!');
							   
							   return false;
							   
						   }
						   
						   return true;
						   
					   }";

$document->addScriptDeclaration($validateEmailForm);

?>

<form action="<?php echo $link . "page=vma.email_affiliates"; ?>" method="post">

    <div class="affiliateAdminPage">
    
        <div class="adminPanelTitleIcon" id="adminEmailAffiliatesIcon">
        
            <h1 class="adminPanelTitle"><?php echo JText::_("EMAIL_AFFILIATES"); ?></h1>
            
        </div>
        
        <div style="clear: both;"></div>
        
        <br />
    	
        <div class="affiliateFormRow">
            
            <div class="affiliateDetailsKey">
            
                <label for="affiliateRecipients"><?php echo JText::_("RECIPIENTS"); ?></label>
                
            </div>
        
            <div class="affiliateDetailsValue affiliateLongerInputs">
            
            	<select id="affiliateRecipients" name="recipients">
                
                	<option value="0"><?php 
					
						echo ucwords(JText::_("ALL")); 
						
					?></option>
                    
                    <option value="" disabled="disabled"></option>
                    
                    <optgroup label="<?php echo JText::_("SELECT"); ?>:">
                    
					<?php
                    
                        foreach ($affiliates as $affiliate) {
                            
                            echo '<option value="' . $affiliate->mail . '" ' . ($affiliate->mail == $email ? 'selected="selected"' : NULL) . '>' . 
							
								 $affiliate->fname . ' ' . $affiliate->lname . 
								 
								 '</option>';
                            
                        }
                    
                    ?>
                    
                    </optgroup>
                    
                </select>
                
            </div>
        
        </div>
        
        <div class="affiliateFormRow">
            
            <div class="affiliateDetailsKey">
            
                <label for="affiliateSubject"><?php echo JText::_("SUBJECT"); ?></label>
                
            </div>
        
            <div class="affiliateDetailsValue affiliateLongerInputs">
            
            	<input id="affiliateSubject" name="subject" type="text" />
                
            </div>
        
        </div>
        
        <div class="affiliateFormRow">
            
            <div class="affiliateDetailsKey">
            
                <label for="affiliateHTML"><?php echo JText::_("SEND IN HTML MODE"); ?></label>
                
            </div>
        
            <div class="affiliateDetailsValue affiliateCommissionInputs">
            
            	<input id="affiliateHTML" name="html" type="checkbox" onchange="switchEditor();" />
                
            </div>
        
        </div>
        
        <div class="affiliateFormRow">
            
            <div class="affiliateDetailsKey">
            
                <label for="affiliateMessageText"><?php echo JText::_("MESSAGE"); ?></label>
                
            </div>
        
            <div class="affiliateDetailsValue affiliateCommissionInputs">
            
            	<textarea id="affiliateMessageText" name="affiliateMessageText" style="width: 300px; height: 100px;"></textarea>
                
                <div id="affiliateMessageHTMLContainer" style="display: none;">
                
                	<?php echo $editor->display('affiliateMessageHTML', NULL, '300', '100', '60', '20', false); ?>
                    
                </div>
                
            </div>
        
        </div>
        
        <div class="affiliateFormRow">
            
            <div class="affiliateDetailsKey">&nbsp;</div>
                
			<div class="affiliateDetailsValue affiliateCommissionInputs" style="margin-top: 10px;">
                
                <span class="affiliateButton">
                	
                    <input type="hidden" name="option" 				   value="com_virtuemart" />
        
        			<input type="hidden" name="pshop_mode" 			   value="admin" />
        
        			<input type="hidden" name="page" 				   value="vma.email_affiliates" />
                    
                    <input type="hidden" name="func"				   value="vmaMassEmailSend" />
                    
                    <input type="hidden" name="vmtoken"				   value="<?php echo vmSpoofValue($sess->getSessionId()); ?>" />
        
                    <input type="submit" 							   value="<?php echo ucwords(JText::_("SEND_MASS_EMAIL")); ?>" style="width: auto; clear: both;" 
                    
                    	   class="affiliateButton affiliateMailButton" onclick="if (!validateEmailForm()) { return false; }" /></span></div>
            
        	</div>
            
            <div style="clear: both;"></div>
        
        </div>
        
    </div>
    
</form>