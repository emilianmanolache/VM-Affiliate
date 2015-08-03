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
 
class AffiliateViewLogin extends JViewLegacy {
	
	/**
     * Method to display the template
     */
	 
    function display($tpl = null) {
		
		global $vmaSettings, $vmaHelper;
		
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
		
		// get specific login page parameters
		
		$generalRates	= $vmaHelper->getCommissionRates();
		
		$formattedRates = $vmaHelper->getFormattedCommissionRates();
		
		$initialBonus	= $vmaHelper->formatAmount($vmaSettings->initial_bonus);
		
		$payoutBalance	= $vmaHelper->formatAmount($vmaSettings->pay_balance);
		
		// set some default page parameters if not set
		
		$params->def( 'show_page_title', 1 );
		
		!$params->get( 'page_title') ? $params->set( 'page_title', JText::sprintf("WELCOME_MESSAGE", $config->get('sitename' ) )) : NULL;
		
		// set the pathway and page title

		$pathway->addItem( JText::_("JLOGIN"), JRoute::_($vmaHelper->vmaRoute('index.php?option=com_affiliate&view=login')) );

		$document->setTitle( $params->get( 'page_title') );
		
		// set the template parameters
		
		$this->assignRef( 'params', 		$params );
		
		$this->assignRef( 'generalRates', 	$generalRates );

		$this->assignRef( 'formattedRates', $formattedRates );
		
		$this->assignRef( 'initialBonus', 	$initialBonus );
		
		$this->assignRef( 'payoutBalance', 	$payoutBalance );
		
        parent::display($tpl);
		
    }
	
}
