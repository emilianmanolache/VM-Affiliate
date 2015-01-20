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

global $vmaSettings;

// get current document

$document 			= &JFactory::getDocument();

// add form validation function

$validationFunction = 'function validateAffiliateForm() {
	
						  if (document.getElementById("affiliateUsername").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_USERNAME", true) . '" );
							  
							  return false;
							  
						  } 
						  
						  if (document.getElementById("affiliatePassword").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_PASSWORD", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateVPassword").value == "") {
							  
							  alert( "' . JText::_("RETYPE_PASSWORD", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliatePassword").value != document.getElementById("affiliateVPassword").value) {
							  
							   alert( "' . JText::_("PASSWORDS_DIFFER", true) . '" );
							   
							   return false;
							   
						  }
						  
						  if (document.getElementById("affiliateEmail").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_EMAIL_ADDRESS", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateFirstName").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_FIRST_NAME", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateLastName").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_LAST_NAME", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateStreet").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_ADDRESS", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateCity").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_CITY", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateCountry").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_COUNTRY", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateZipCode").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_ZIPCODE", true) . '" );
							  
							  return false;
							  
						  }';
						  
if ($vmaSettings->must_agree) {
	
	$validationFunction .= 'if (document.getElementById("affiliateTermsAgreement").checked == false) {
							  
								alert( "' . JText::_("YOU_MUST_AGREE", true) . '" );
								
								return false;
								
							}';
	
}

$validationFunction .= "return true; }";
						  
$document->addScriptDeclaration($validationFunction);

?>