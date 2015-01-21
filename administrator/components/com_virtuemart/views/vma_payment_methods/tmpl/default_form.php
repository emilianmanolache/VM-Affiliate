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

// get method information

$database			= &JFactory::getDBO();

$methodID			= &JRequest::getVar("method_id", 	"");

if ($methodID) {
	
	// get payment method's details
	
	$query			= "SELECT * FROM #__vm_affiliate_methods WHERE `method_id` = '" 		. $methodID . "'";
	
	$database->setQuery($query);
	
	$method			= $database->loadObject();
	
	// get payment method's fields
	
	$query			= "SELECT * FROM #__vm_affiliate_method_fields WHERE `method_id` = '" 	. $methodID . "' ORDER BY `field_id` ASC";
	
	$database->setQuery($query);
	
	$fields			= $database->loadObjectList();

}

else {
	
	$fields					= array(new stdClass());
	
	$fields[0]->field_id	= "s[]";
	
	$fields[0]->field_name	= NULL;
	
}

// display the title

JToolBarHelper::title(JText::_("PAYMENT_METHOD_FORM") . ($methodID ? ": " . $method->method_name : NULL), 'head adminPaymentMethodsIcon');

// initiate other variables

$link = $vmaHelper->getAdminLink();

// add form validation function

$document = &JFactory::getDocument();

// include the payment method form validation function

$validateMethodForm	= "// validate the method form

					   function validateMethodForm() {
						  
						  if ($('affiliateMethodName').value == '') {
							  
							  alert('" 	. JText::_("PROVIDE_PAYMENT_METHOD_NAME", 		true) . "');
							  
							  return false;
							  
						  }
						  
						  return true;
						  
					  }";
					  
$document->addScriptDeclaration($validateMethodForm);

// include the payment method field add function

$addMethodField		= "// add a payment method field

					   function addPaymentMethodField() {
						   
						   $('paymentMethodFieldsContainer').adopt(new Element('div', {
							   
							   											'class': 'affiliateFormRow'
																		
						   										   })
																   
																   .adopt(new Element('div', {
														
																		'class': 'affiliateDetailsKey'
																		
																   })
																   
																   .setHTML('&nbsp;'),
																   
																   new Element('div', {
																	   
																	   'class':  'affiliateDetailsValue affiliateLongerInputs'
																	   
																   })
																   
																   .adopt(new Element('input', {
																		
																		'name': 	'fields[]',
																		
																		'type':		'text',
																		
																		'value':	''
																		
						   										   }),
																   
																   new Element('a',	{
																	   
																	   'href':		'javascript:void(0)',
																	   
																	   'events':	{
																		   
																		   'click': function() {
																			   
																			   this.getParent().getParent().remove();
																		
																		   }
																		   
																	   }
																	   
																   })
																   
																   .appendText(' ')
																   
																   .adopt(new Element('img', {
																	   
																	   		'src':		'" . $vmaHelper->_website . "components/com_affiliate/assets/images/close.png',
																			
																			'alt':		'Remove Payment Method',
																			
																			'class':	'paymentMethodRemoveButton'
																			
																 		  })
																		  
																   )))
																   
						   );
																   
					   }";

$document->addScriptDeclaration($addMethodField);

?>

<form action="<?php echo $link . "_payment_methods"; ?>" method="post" name="adminForm">

    <div class="affiliateAdminPage">
        
        <div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateMethodName"><?php echo JText::_("NAME"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue affiliateLongerInputs">
                
                    <input id="affiliateMethodName" name="name" type="text" value="<?php echo $methodID ? $method->method_name : NULL; ?>" />
                    
                </div>
            
            </div>
            
            <div id="paymentMethodFieldsContainer">
                    
				<?php
                        
                    for ($i = 0; $i < count($fields); $i++) {

                        ?>
                            
                            <div class="affiliateFormRow">
                                
                                <div class="affiliateDetailsKey">
                                
									<?php

                                        echo $i == 0 ? "<label for=\"affiliateFirstField\">" . JText::_("FIELDS") . "</label>" : "&nbsp;";
                                        
                                    ?>
                            
                                </div>
                                
                                <div class="affiliateDetailsValue affiliateLongerInputs">
                                	
                                    <input type="text" 
                                            
                                           name="field<?php 	echo $fields[$i]->field_id; ?>" 
                                           
                                           value="<?php 		echo $fields[$i]->field_name; ?>"
                                           
                                           <?php 				echo $i == 0 ? ' id="affiliateFirstField" ' : NULL; ?> />
                                                   
                                    <?php 
									
										if ($i == 0) {
											
									?>
                                            
                                            <a href="javascript:void(0)" onclick="addPaymentMethodField();">
                                            
                                                <img alt="New Payment Method" class="paymentMethodAddButton" 
                                                     
                                                     src="<?php echo $vmaHelper->_website . "components/com_affiliate/assets/images/new.png"; ?>" />
                                                
                                            </a>
									
                                    <?php 
									
										} else {
											
									?>
                                            
                                            <a href="javascript:void(0)" onclick="$(this).getParent().getParent().remove();">
                                            
                                                <img alt="Remove Payment Method" class="paymentMethodRemoveButton" 
                                                     
                                                     src="<?php echo $vmaHelper->_website . "components/com_affiliate/assets/images/close.png"; ?>" />
                                                
                                            </a>

                                    <?php 
									
										}
										
									?>
                                    
                                </div>
                                
                            </div>
                            
                        <?php
                        
                    }
                
                ?>
            
            </div>
            
            <div class="affiliateFormRow">
            	
                <div class="affiliateDetailsKey">&nbsp;</div>
                
                <div class="affiliateDetailsValue" style="margin-top: 10px;">
                    
                    <span class="affiliateButton">
                    
                    	<input type="hidden" name="option" 					value="com_virtuemart" />
                        
                        <input type="hidden" name="view" 					value="vma_payment_methods" />
                        
                        <input type="hidden" name="task"					value="save" />
						
						<?php 
					
							if ($methodID) { 
							
								?><input type="hidden" name="method_id"		value="<?php echo $methodID; ?>" /><?php 
								
							} 
						
						?>
                        
                        <input type="submit" 								value="<?php echo JText::_("SAVE"); ?>" style="width: auto; clear: both;" 
                    
                   			   class="affiliateButton affiliateSaveButton" 	onclick="if (!validateMethodForm()) { return false; }" /></span></div>
            			
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