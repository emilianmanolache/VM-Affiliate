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
 
// import joomla component view application

jimport( 'joomla.application.component.view');
 
/**
 * View file for VM Affiliate's Affiliate Panel component
 */
 
class AffiliateViewPrev extends JView {
	
	/**
     * Method to display the template
     */
	 
    function display($tpl = null) {
		
		// get application
	
		$mainframe	= &JFactory::getApplication();
		
		// initialize variables
		
		$config 		= &JFactory::getConfig();
		
		$document		= &JFactory::getDocument();
		
		$user			= &JFactory::getUser();
		
		$session		= &JFactory::getSession();
		
		$pathway		= &$mainframe->getPathway();

		$menu   		= &JSite::getMenu();
		
		$item   		= $menu->getActive();
		
		$params			= isset($item->id) ? $menu->getParams($item->id): $menu->getParams(0);
			
        $model 			= $this->getModel();
		
		$type			= JRequest::getVar('type', 'banners');
		
		// set some default page parameters if not set
		
		$params->def( 'show_page_title', 1 );
		
		!$params->get( 'page_title') ? $params->set( 'page_title', JText::_("PREVIEW") ) : NULL;
		
		// set the pathway and page title

		$pathway->addItem( JText::_("PREVIEW"), '' );

		$document->setTitle( $params->get( 'page_title') );
		
		// get the preview item and assign it to the template
			
		$item = $session->get("item");
		
		$this->assignRef( "item", 	$item );
		
		$this->assignRef( 'type', 	$type );
		
		$this->assignRef( 'params', $params );
		
        parent::display($tpl);
		
    }
	
}
