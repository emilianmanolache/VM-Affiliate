<?php

/**
 * @package   VM Affiliate
 * @version   4.5.0 May 2011
 * @author    Globacide Solutions http://www.globacide.com
 * @copyright Copyright (C) 2006 - 2011 Globacide Solutions
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access

defined( '_JEXEC' ) or die( 'Direct access to this location is not allowed.' );

// initialize required variables

global $ps_vma, $sess;

$document 			= &JFactory::getDocument();

$link				= $ps_vma->getAdminLink();

// load the modal box

JHTML::_('behavior.modal');
			
// get banner information

$bannerID			= &JRequest::getVar("banner_id", 	"");

if ($bannerID) {
	
	$query			= "SELECT banners.*, sizegroups.`name` AS sizegroup FROM #__vm_affiliate_banners banners LEFT JOIN #__vm_affiliate_size_groups sizegroups ON " . 
	
					  "banners.`banner_width` = sizegroups.`width` AND banners.`banner_height` = sizegroups.`height` WHERE banners.`banner_id` = '" . $bannerID . "'";
	
	$ps_vma->_db->setQuery($query);
	
	$banner			= $ps_vma->_db->loadObject();
	
	// determine image and thumbnail properties
	
	$bannerFilename	= $banner->banner_image . "." . $banner->banner_type;
	
	$thumbFilename	= "thumbbig_" 	. ($banner->banner_type == "swf" ? "swf.png" : $bannerFilename);
	
	$thumbnailImage	= JPATH_ROOT 	. DS . "components" . DS . "com_affiliate" . DS . "thumbs" 		. DS . $thumbFilename;
		
	$bannerLocation = JPATH_ROOT 	. DS . "components" . DS . "com_affiliate" . DS . "banners" 	. DS . $bannerFilename;
	
	list($width, $height) = getimagesize($bannerLocation);
	
	// generate the thumbnail if it doesn't exist
	
	if (!file_exists($thumbnailImage) && file_exists($bannerLocation)) {
		
		$ps_vma->resizeImage($bannerLocation, $banner->banner_image, "thumbbig");
		
	}

}

// initiate the ad form

$ps_vma->initiateAdForm("banners", (!$bannerID ? "add" : "edit"), (!$bannerID ? NULL : $banner));
	   
// include the banner form validation function

$validateBannerForm	= "// validate the banner form

					   function validateBannerForm() {
						  
						  if ($('affiliateImage') && (typeof(imageDetails) == 'undefined' || imageDetails.status != 'success')) {
							  
							  alert('" . JText::_("PROVIDE_BANNER_IMAGE", true) . "');
							  
							  return false;
							  
						  }
						  
						  if ($('affiliateName').value == '') {
							  
							  alert('" . JText::_("PROVIDE_BANNER_NAME", true) . "');
							  
							  return false;
						
						  }
						  
						  return true;
						  
					  }";
					  
$document->addScriptDeclaration($validateBannerForm);

?>
            
<form action="<?php echo $link . "page=vma.banners_list"; ?>" method="post" name="adminForm">
            
    <div class="affiliateAdminPage">
    
        <div class="adminPanelTitleIcon" id="adminBannersIcon">
        
            <h1 class="adminPanelTitle">
            
                <?php echo JText::_("BANNER_FORM") . ($bannerID ? ": " . $banner->banner_name : NULL); ?>
                
            </h1>
            
        </div>
        
        <div class="affiliateTopMenu">
            
            <div class="affiliateTopMenuLink">
            
                <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" 
                
                      class="affiliateTopMenuLinkItem">
                
                    <a href="<?php echo $link . "page=vma.banners_list"; ?>"><?php echo JText::_("BANNERS"); ?></a>
                    
                </span>
                
            </div>
            
        </div>
        
        <div style="clear: both;"></div>
    
        <br />
        
        <div>
            
			<div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label <?php echo (!$bannerID ? "for=\"affiliateImage\"" : NULL); ?>>
					
						<?php
                        
							echo JText::_("BANNER_IMAGE"); 
						
						?>
                        
					</label>
                    
                </div>
            
                <div class="affiliateDetailsValue affiliateLongerInputs" style="width: auto;"><?php
                
					if ($bannerID) {
						
						?><div id="imageThumbnail" style="clear: both;">
                        
                        		<img src="<?php echo $ps_vma->_website . "components/com_affiliate/thumbs/" . $thumbFilename; ?>" 
                                
                                	 alt="<?php echo JText::_("THUMB"); ?>" id="thumbnailImage" 
                                     
                                     style="clear: both; margin-bottom: 0px; <?php echo $banner->banner_type == "swf" ? "padding-left: 64px; padding-right: 64px;" : NULL; ?>" />
                                
						</div><?php
						
					}
					
					else {
						
						?><button id="affiliateImage" type="button" style="clear: both;"><?php echo JText::_("UPLOAD_BANNER"); ?></button><?php
						
					}
					
				?></div>
                
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateName"><?php echo JText::_("NAME"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue affiliateLongerInputs">
                
                    <input id="affiliateName" name="name" type="text" value="<?php echo $bannerID ? $banner->banner_name : NULL; ?>" />
                    
                </div>
            
            </div>
            
            <div class="affiliateFormRow">
            
                <div class="affiliateDetailsKey">
                
                    <label for="affiliateLink"><?php echo JText::_("LINK"); ?></label>
                    
                </div>
            
                <div class="affiliateDetailsValue affiliateLongerInputs">
                
                	<?php
					
						echo $ps_vma->getLinksRow("banners", (!$bannerID ? "add" : "edit"), (!$bannerID ? NULL : $banner));
						
					?>
                                        
                </div>
            
            </div>
            
            <div class="affiliateFormRow" id="affiliateSizeRow" <?php
            
				echo $bannerID ? NULL : 'style="display: none;"';
				
			?>>
            
            	<?php
				
					if ($bannerID) {
						
						?>
                        
                        	<div class="affiliateDetailsKey">
							
								<?php
                            
									echo JText::_("SIZE"); 
								
								?>
                                
							</div>
                            
                            <div class="affiliateDetailsValue affiliateLongerInputs">
                            
                            	<span style="font-weight: bold; line-height: 15px;">
                                
                                	<?php
									
										echo $banner->banner_width . "x" . $banner->banner_height . ($banner->sizegroup ? " " . "(" . $banner->sizegroup . ")" : NULL);
										
									?>
                                    
                                </span>
                                
                            </div>
                            
                        <?php
						
					}
					
				?>
            
            </div>
            
            <div class="affiliateFormRow">
            	
                <div class="affiliateDetailsKey">&nbsp;</div>
                
                <div class="affiliateDetailsValue" style="height: 36px; margin-top: 10px;">
                    
                    <span class="affiliateButton">
                    
                    	<input type="hidden" name="option" 					value="com_virtuemart" />
                        
                        <input type="hidden" name="pshop_mode" 				value="admin" />
                        
                        <input type="hidden" name="page" 					value="vma.banners_list" />
                        
                        <input type="hidden" name="func"					value="vmaBannerSave" />
                        
                        <input type="hidden" name="task"					value="" />
						
						<?php 
					
							if ($bannerID) { 
							
								?><input type="hidden" name="banner_id"		value="<?php echo $bannerID; ?>" /><?php 
								
							} 
						
						?>
                        
                        <input type="hidden" name="vmtoken"					value="<?php echo vmSpoofValue($sess->getSessionId()); ?>" />
                        
                        <input type="submit" 								value="<?php echo JText::_("SAVE"); ?>" style="width: auto; clear: both;" 
                    
                   			   class="affiliateButton affiliateSaveButton" 	onclick="if (!validateBannerForm()) { return false; }" /></span>
                               
				</div>
            
            </div>
            
            <div style="clear: both;"></div>
        
        </div>
        
    </div>

</form>