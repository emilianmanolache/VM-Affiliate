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

// get current document

$document 			= &JFactory::getDocument();

// add form validation function

$validationFunction = 'function validateAffiliateForm() {
	
						  if (document.getElementById("affiliateUsername").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_USERNAME", true) . '" );
							  
							  return false;
							  
						  } 
						  
						  if (document.getElementById("affiliateEmail").value == "") {
							  
							  alert( "' . JText::_("PROVIDE_EMAIL_ADDRESS", true) . '" );
							  
							  return false;
							  
						  }
						  
						  return true;
						  
					  }';
						  
$document->addScriptDeclaration($validationFunction);

?>