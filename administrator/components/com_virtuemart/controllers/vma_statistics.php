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

// import the component controller library

jimport('joomla.application.component.controller');

/**
 * VM Affiliate backend controller
 */

class VirtuemartControllerVma_statistics extends JControllerLegacy {
	
	/**
	 * Method to display the view
	 */
	 
	function display() {

		$document 	= JFactory::getDocument();
		
		$viewName 	= JRequest::getWord('view', '');
		
		$viewType 	= $document->getType();
		
		$view 		= $this->getView($viewName, $viewType);

		parent::display();
		
	}
	
}