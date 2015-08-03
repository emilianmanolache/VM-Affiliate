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

JToolBarHelper::title(JText::_('EMAIL_AFFILIATES'), 'head adminEmailAffiliatesIcon');

// initiate other variables

$link 				= $vmaHelper->getAdminLink();

$document			= &JFactory::getDocument();

$language 			= &JFactory::getLanguage();

$editor				= &JFactory::getEditor();

$email				= &JRequest::getVar("email");

// get com_users component's language

$language->load("com_users", JPATH_ROOT . DS . "administrator");

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

<form action="<?php echo $link . "_email_affiliates"; ?>" method="post" name="adminForm" id="adminForm">

	<div class="affiliateAdminPage">
     
    <div class="affiliateFormRow">
            
            <div class="affiliateDetailsKey">
            
                <label for="affiliateRecipients"><?php echo JText::_("RECIPIENTS"); ?></label>
                
            </div>
        
            <div class="affiliateDetailsValue affiliateLongerInputs">
            
            	<select id="affiliateRecipients" name="recipients">
                
                	<option value="0"><?php 
					
						echo ucwords(JText::_("JALL")); 
						
					?></option>
                    
                    <option value="" disabled="disabled"></option>
                    
                    <optgroup label="<?php echo JText::_("SELECT"); ?>:">
                    
					<?php
                    
                        foreach ($this->affiliates as $affiliate) {
                            
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
            
                <label for="affiliateHTML"><?php echo JText::_("COM_USERS_MAIL_FIELD_SEND_IN_HTML_MODE_LABEL"); ?></label>
                
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
        
        			<input type="hidden" name="view" 				   value="vma_email_affiliates" />
                    
                    <input type="hidden" name="task"				   value="send" />
        
                    <input type="submit" 							   value="<?php echo ucwords(JText::_("SEND_MASS_EMAIL")); ?>" style="width: auto; clear: both;" 
                    
                    	   class="affiliateButton affiliateMailButton" onclick="if (!validateEmailForm()) { return false; }" /></span></div>
            
            		<?php echo JHTML::_('form.token'); ?>
                    
        	</div>
            
            <div style="clear: both;"></div>
        
        </div>
        
    </div>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>