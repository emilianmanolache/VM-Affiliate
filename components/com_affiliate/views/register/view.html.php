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
 
class AffiliateViewRegister extends JViewLegacy {
	
	/**
     * Method to display the template
     */
	 
    function display($tpl = null) {
		
		global $vmaHelper;
		
		// get application
		
		$mainframe		= &JFactory::getApplication();
		
		// only load footer
		
		if ($tpl == "footer") {
			
			parent::display($tpl);
			
			return true;
			
		}
		
		// initialize variables
		
		$config 		= &JFactory::getConfig();
		
		$document		= &JFactory::getDocument();
		
		$user			= &JFactory::getUser();
		
		$pathway		= &$mainframe->getPathway();

		$menu   		= &JSite::getMenu();
		
		$item   		= $menu->getActive();
		
		$params			= isset($item->id) ? $menu->getParams($item->id): $menu->getParams(0);
			
        $model 			= $this->getModel();
		
		$this->_addPath('template', $this->_basePath . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . "footer" . DIRECTORY_SEPARATOR . 'tmpl');
		
		// fix squeezebox issue on mootools 1.2
				
		$vmaHelper->fixSqueezeBox();
		
		// initiate squeezebox
		
		JHTML::_("behavior.modal", "a.affiliateModal");
		
		// set some default page parameters if not set
		
		$params->def( 'show_page_title', 1 );
		
		!$params->get( 'page_title') ? $params->set( 'page_title', JText::_("AFFILIATE_PANEL") . " - " . JText::_("SIGN_UP_TEXT") ) : NULL;
		
		// set the pathway and page title

		$pathway->addItem( JText::_("SIGN_UP_TEXT"), JRoute::_($vmaHelper->vmaRoute('index.php?option=com_affiliate&view=register')) );

		$document->setTitle( $params->get( 'page_title') );
		
		// set the template parameters
		
		$this->assignRef( 'params', 		$params );
		
        parent::display($tpl);
		
    }
	
}
