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

// add form validation function

$validationFunction = 'function validateAffiliateForm() {
						  
						  if (document.getElementById("affiliateEmail").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_EMAIL", true) . '" );
							  
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
							  
						  }
						  
						  return true; }';
						  
$document->addScriptDeclaration($validationFunction);

?>