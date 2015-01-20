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
			
// get method information

$methodID			= &JRequest::getVar("method_id", 	"");

if ($methodID) {
	
	// get payment method's details
	
	$query			= "SELECT * FROM #__vm_affiliate_methods WHERE `method_id` = '" 		. $methodID . "'";
	
	$ps_vma->_db->setQuery($query);
	
	$method			= $ps_vma->_db->loadObject();
	
	// get payment method's fields
	
	$query			= "SELECT * FROM #__vm_affiliate_method_fields WHERE `method_id` = '" 	. $methodID . "' ORDER BY `field_id` ASC";
	
	$ps_vma->_db->setQuery($query);
	
	$fields			= $ps_vma->_db->loadObjectList();

}

else {
	
	$fields					= array(new stdClass());
	
	$fields[0]->field_id	= "s[]";
	
	$fields[0]->field_name	= NULL;
	
}

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
																	   
																	   		'src':		'" . $ps_vma->_website . "components/com_affiliate/assets/images/close.png',
																			
																			'alt':		'Remove Payment Method',
																			
																			'class':	'paymentMethodRemoveButton'
																			
																 		  })
																		  
																   )))
																   
						   );
																   
					   }";

$document->addScriptDeclaration($addMethodField);

?>
            
<form action="<?php echo $link . "page=vma.payment_methods_list"; ?>" method="post" name="adminForm">
            
    <div class="affiliateAdminPage">
    
        <div class="adminPanelTitleIcon" id="adminPaymentMethodsIcon">
        
            <h1 class="adminPanelTitle">
            
                <?php echo JText::_("PAYMENT_METHOD_FORM") . ($methodID ? ": " . $method->method_name : NULL); ?>
                
            </h1>
            
        </div>
        
        <div class="affiliateTopMenu">
            
            <div class="affiliateTopMenuLink">
            
                <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" 
                
                      class="affiliateTopMenuLinkItem">
                
                    <a href="<?php echo $link . "page=vma.payment_methods_list"; ?>"><?php echo JText::_("PAYMENT_METHODS"); ?></a>
                    
                </span>
                
            </div>
            
        </div>
        
        <div style="clear: both;"></div>
    
        <br />
        
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
                                                     
                                                     src="<?php echo $ps_vma->_website . "components/com_affiliate/assets/images/new.png"; ?>" />
                                                
                                            </a>
									
                                    <?php 
									
										} else {
											
									?>
                                            
                                            <a href="javascript:void(0)" onclick="$(this).getParent().getParent().remove();">
                                            
                                                <img alt="Remove Payment Method" class="paymentMethodRemoveButton" 
                                                     
                                                     src="<?php echo $ps_vma->_website . "components/com_affiliate/assets/images/close.png"; ?>" />
                                                
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
                        
                        <input type="hidden" name="pshop_mode" 				value="admin" />
                        
                        <input type="hidden" name="page" 					value="vma.payment_methods_form" />
                        
                        <input type="hidden" name="func"					value="vmaPaymentMethodSave" />
                        
                        <input type="hidden" name="task"					value="" />
						
						<?php 
					
							if ($methodID) { 
							
								?><input type="hidden" name="method_id"		value="<?php echo $methodID; ?>" /><?php 
								
							} 
						
						?>
                        
                        <input type="hidden" name="vmtoken"					value="<?php echo vmSpoofValue($sess->getSessionId()); ?>" />
                        
                        <input type="submit" 								value="<?php echo JText::_("SAVE"); ?>" style="width: auto; clear: both;" 
                    
                   			   class="affiliateButton affiliateSaveButton" 	onclick="if (!validateMethodForm()) { return false; }" /></span></div>
            
            </div>
            
            <div style="clear: both;"></div>
        
        </div>
        
    </div>

</form>