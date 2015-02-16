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

// get vma settings

global $vmaSettings;

// get current document

$document 			= &JFactory::getDocument();

// add preferences form validation function

$validationFunction = 'function validatePaymentMethodForm() {
						  
						  var paymentMethodSet		= false;
						  
						  var emailProvided			= true;
						  
						  for (var i = 0; i < document.getElementById(\'affiliatePreferencesForm\').paymentmethod.length; i++) {
							  
							 if (document.getElementById(\'affiliatePreferencesForm\').paymentmethod[i].checked) {
								 
								 paymentMethodSet	= true;
							
							 }
							 
							 if (document.getElementById(\'affiliatePreferencesForm\').paymentmethod[i].value == \'1\' &&
						  
								 document.getElementById(\'affiliatePreferencesForm\').paymentmethod[i].checked &&
								  
								 document.getElementById(\'payment-field-1\').value == \'\') {
								  
								 emailProvided		= false;
								  
							 }
							 
						  }
						  
						  if (typeof(document.getElementById(\'affiliatePreferencesForm\').paymentmethod.length) == \'undefined\') {
							  
							  if (document.getElementById(\'affiliatePreferencesForm\').paymentmethod.checked) {
								  
								  paymentMethodSet = true;
								  
							  }
							  
							  if (document.getElementById(\'affiliatePreferencesForm\').paymentmethod.value == \'1\' &&
						  
								 document.getElementById(\'affiliatePreferencesForm\').paymentmethod.checked &&
								  
								 document.getElementById(\'payment-field-1\').value == \'\') {
								  
								 emailProvided		= false;
								  
							 }
							   
						  }
						  
						  if (!paymentMethodSet) {
							  
							  alert( "' . JText::_("PROVIDE_PAYMENT_METHOD", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (!emailProvided) {
							  
							  alert( "' . JText::_("PROVIDE_EMAIL_ADDRESS", true) . '" );
							  
							  return false;
							  
						  }
						  
						  return true; 
						  
					  }';
						  
$document->addScriptDeclaration($validationFunction);

// add link form validation function

$validationFunction = 'function validateLinkToUserForm() {
						  
						  if (document.getElementById("affiliateLinkUsername").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_USERNAME", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateLinkPassword").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_PASSWORD", true) . '" );
							  
							  return false;
							  
						  }
						  
						  return true; 
						  
					  }';
						  
$document->addScriptDeclaration($validationFunction);

?>