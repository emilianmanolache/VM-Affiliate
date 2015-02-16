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

// import the joomla component model application

jimport( 'joomla.application.component.model' );
 
/**
 * Model file for VM Affiliate's Affiliate Panel component
 */
 
class AffiliateModelTerms extends JModel {
	
	/**
	 * Method to load required data
	 */
	 
	function getTerms() {
		
		// initiate variables
		
		$database	= &JFactory::getDBO();
		
		// build the query
		
		$query		= "SELECT `setting`, `aff_terms` FROM #__vm_affiliate_settings WHERE `setting` = '1'";
		
        $database->setQuery($query);
		
		$result		= $database->loadObject();

        return $result->aff_terms;
		
	}
	
}
