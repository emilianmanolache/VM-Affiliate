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
						  
						  if (document.getElementById("affiliateOldPassword").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_PASSWORD", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateNewPassword").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_PASSWORD", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateVerifyPassword").value == "") {
							  
							  alert( "' . JText::_("RETYPE_PASSWORD", true) . '" );
							  
							  return false;
							  
						  }
						  
						  if (document.getElementById("affiliateNewPassword").value != document.getElementById("affiliateVerifyPassword").value) {
							  
							  alert( "' . JText::_("PASSWORDS_DIFFER", true) . '" );
							  
							  return false;
							  
						  }
						  
						  return true; }';
						  
$document->addScriptDeclaration($validationFunction);

?>