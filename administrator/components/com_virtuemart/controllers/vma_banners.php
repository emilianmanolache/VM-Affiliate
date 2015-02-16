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

class VirtuemartControllerVma_banners extends JController {
	
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
	 * Method to save a banner
	 */
	 
	function save() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$database		 	= JFactory::getDBO();
		
		$bannerID			= JRequest::getVar('banner_id', 	'');
		
		$bannerName			= JRequest::getVar('name',			'');
		
		$bannerName			= $database->getEscaped($bannerName);
		
		$bannerLink			= JRequest::getVar('link',			'');
		
		$bannerDetails		= JRequest::getVar('bannerDetails', '');
	
		$bannerSize			= JRequest::getVar('size',			'');
		
		$bannerLink			= str_replace("&amp;", "&", 		$bannerLink);
		
		// validate the banner form
		
		if (!$bannerName) {
			
			vmError(JText::_("PROVIDE_BANNER_NAME"));
			
			$this->display();
			
			return false;
				
		}
		
		if (!$bannerID && !$bannerDetails) {
				
			vmError(JText::_("PROVIDE_BANNER_IMAGE"));
			
			$this->display();
			
			return false;
			
		}
			
		// add the new banner
		
		if (!$bannerID) {
			
			// prepare required variables
			
			list($bannerImage, $bannerOldExtension, $bannerFileType) = explode("|", $bannerDetails);
		
			$bannerPath		= JPATH_ROOT 	. DS . "components" . DS . "com_affiliate" 	. DS 	. "banners";
			
			$temporaryFile	= $bannerPath	. DS . "temp_" 		. $bannerImage 			. "." 	. $bannerOldExtension;
			
			// preserve the image size
			
			if (!$bannerSize || $bannerSize == "preserve") {
				
				// determine banner dimensions
				
				list($bannerWidth, $bannerHeight) = @getimagesize($temporaryFile);
				
				// move uploaded file
				
				if (!@rename($temporaryFile, $bannerPath . DS . $bannerImage . "." . $bannerFileType)) {
					
					vmError(JText::_("BANNER_MOVING_FAILURE"));
					
					$this->display();
					
					return false;
					
				}
				
			}
			
			// resize the image
			
			else {
					
				// get the size group width and height
				
				$query			= "SELECT `width`, `height` FROM #__vm_affiliate_size_groups WHERE `size_group_id` = '" . $bannerSize . "'";
				
				$database->setQuery($query);
				
				$size			= $database->loadObject();
				
				// determine banner dimensions
				
				$bannerWidth	= $size->width;
				
				$bannerHeight	= $size->height;
				
				// resize the banner image
				
				$vmaHelper->resizeImage($temporaryFile, $bannerImage, $type = "banner", $bannerWidth, $bannerHeight);
				
				// remove the temporary image
				
				@unlink($temporaryFile);
				
			}
			
			// insert the banner in the database
			
			$query = "INSERT INTO #__vm_affiliate_banners VALUES ('', '" . $bannerName 		. "', '" . $bannerImage . "', '" . 
			
					 $bannerFileType . "', '" . $bannerWidth 	. "', '" . $bannerHeight 	. "', '" . $bannerLink 	. "', '1')";
			
			$database->setQuery($query);
			
			$database->query();
			
		}
		
		// save the edited banner
		
		else {
			
			$query = "UPDATE #__vm_affiliate_banners SET `banner_name` = '" . $bannerName . "', `banner_link` = '" . $bannerLink . "' WHERE `banner_id` = '" . $bannerID . "'";
			
			$database->setQuery($query);
			
			$database->query();
			
		}
		
		// show the confirmation message
		
		vmInfo(JText::_("BANNER_SUCCESSFULLY_SAVED"));
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
	/**
	 * Method to toggle a banner's status between enabled and disabled
	 */

	function toggle() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$database		= &JFactory::getDBO();
		
		$id				= &JRequest::getVar("id");
		
		$type			= &JRequest::getVar("type");
		
		$published		= &JRequest::getVar("published");
		
		// perform the toggle query
		
		$query			= "UPDATE #__vm_affiliate_banners SET `published` = 1 - published WHERE `banner_id` = '" . $id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
	/**
	 * Method to delete a banner
	 */

	function delete() {
		
		global $vmaHelper;
		
		// initiate required variables
		
		$database		= &JFactory::getDBO();
		
		$id				= &JRequest::getVar("id");
		
		$type			= &JRequest::getVar("type");
			
		// get banner filename
	
		$query		= "SELECT `banner_image`, `banner_type` FROM #__vm_affiliate_banners WHERE `banner_id` = '" . $id . "'";
		
		$database->setQuery($query);
		
		$bannerInfo = $database->loadObject();
		
		// determine paths and filename
		
		$commonPath	= JPATH_ROOT . DS . "components" . DS . "com_affiliate";
		
		$fileName	= $bannerInfo->banner_image . "." . $bannerInfo->banner_type;
		
		// remove all related files
		
		@unlink($commonPath . DS . "banners" 	. DS 				. $fileName);
		
		@unlink($commonPath . DS . "thumbs" 	. DS . "thumb_" 	. $fileName);
		
		@unlink($commonPath . DS . "thumbs" 	. DS . "thumbbig_" 	. $fileName);
		
		// perform the toggle query
		
		$query			= "DELETE FROM #__vm_affiliate_banners WHERE `banner_id` = '" . $id . "'";
		
		$database->setQuery($query);
		
		$database->query();
		
		// confirm the operation
		
		$this->display();
		
		return true;
		
	}
	
}