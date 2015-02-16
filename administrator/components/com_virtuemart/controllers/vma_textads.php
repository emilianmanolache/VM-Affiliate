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

class VirtuemartControllerVma_textads extends JController {
	
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
	
	/**
	 * Method to save a text ad
	 */
	 
	function save() {
		
		global $vmaHelper;
		
		$database						= JFactory::getDBO();
		
		// get text ad's id
		
		$textAdID						= JRequest::getVar("textad_id", 					"");
		
		// get text ad form
		
		$textAd							= array();
		
		$textAd["title"] 				= JRequest::getString("title", 				"", 	"post");
		
		$textAd["title"]				= $database->getEscaped($textAd["title"]);
		
		$textAd["link"] 				= JRequest::getString("link", 				"", 	"post");
		
		$textAd["size"] 				= JRequest::getString("size", 				"", 	"post");
		
		$textAd["text"] 				= $_POST["affiliateText"];
		
		$textAd["link"]					= str_replace("&amp;", "&", 				$textAd["link"]);
		
		// validate the text ad form
		
		if (!$textAd["title"]) {
			
			vmError(JText::_("PROVIDE_TITLE"));
				
			$this->display();
				
			return false;
				
		}
		
		if (!$textAd["text"]) {
				
			vmError(JText::_("PROVIDE_CONTENT"));
			
			$this->display();
			
			return false;
			
		}
		
		if (!$textAd["size"]) {
				
			vmError(JText::_("FILL_IN_ALL_FIELDS"));
			
			$this->display();
			
			return false;
			
		}
		
		// get the size group's parameters
		
		$query		= "SELECT `width`, `height` FROM #__vm_affiliate_size_groups WHERE `size_group_id` = '" . $textAd["size"] . "'";
		
		$database->setQuery($query);
		
		$sizeGroup 	= $database->loadObject();
		
		// determine query
		
		$query	= $textAdID ? 
				  
				  "UPDATE #__vm_affiliate_textads SET `title` = '" . $textAd["title"] . "', `content` = '" . $textAd["text"] . "', `width` = '" . 
				  
				  $sizeGroup->width . "', `height` = '" . $sizeGroup->height . "', `link` = '" . $textAd["link"] . "' WHERE `textad_id` = '" . $textAdID . "'" :
				  
				  "INSERT INTO #__vm_affiliate_textads VALUES ('', '" . $textAd["title"] . "', '" . $textAd["text"] . "', '1', " . 
				  
				  "'" . $textAd["link"] . "', '" . $sizeGroup->width . "', '" . $sizeGroup->height . "')";
		
		$database->setQuery($query);
		
		$database->query();
		
		// show the confirmation message
		
		vmInfo(JText::_("TEXT_AD_SAVED"));
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
	/**
	 * Method to toggle a textad's status between enabled and disabled
	 */

	function toggle() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$database		= &JFactory::getDBO();
		
		$id				= &JRequest::getVar("id");
		
		$type			= &JRequest::getVar("type");
		
		$published		= &JRequest::getVar("published");
		
		// perform the toggle query
		
		$query			= "UPDATE #__vm_affiliate_textads SET `published` = 1 - published WHERE `textad_id` = '" . $id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
	/**
	 * Method to delete a textad
	 */

	function delete() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$database		= &JFactory::getDBO();
		
		$id				= &JRequest::getVar("id");
		
		$type			= &JRequest::getVar("type");
		
		// perform the delete query
		
		$query			= "DELETE FROM #__vm_affiliate_textads WHERE `textad_id` = '" . $id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
}