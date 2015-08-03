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

global $vmaSettings, $vmaHelper;

?>

<div class="affiliateTopMenu">
	
    <?php if ($this->activeAdsMenus["banners"]) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span>
            
                <?php 
                
                echo $this->section != "banners" ? "<a href=\"" . JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=banners&section=banners")) . "\">" : NULL;
                
                echo JText::_("BANNERS");
                
                echo $this->section != "banners" ? "</a>" : NULL;
                
                ?>
                
            </span>
            
        </div>
    
    <?php } ?>
    
    <?php if ($this->activeAdsMenus["banners"] && ($this->activeAdsMenus["textads"] || $this->activeAdsMenus["productads"] || $this->activeAdsMenus["categoryads"])) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span> | </span>
            
        </div>
        
	<?php } ?>
	
    <?php if ($this->activeAdsMenus["textads"]) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span>
                
                <?php 
                
                echo $this->section != "textads" ? "<a href=\"" . JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=banners&section=textads")) . "\">" : NULL;
                
                echo JText::_("TEXT_ADS");
                
                echo $this->section != "textads" ? "</a>" : NULL;
                
                ?>
                
            </span>
            
        </div>
        
	<?php } ?>
    
    <?php if ($this->activeAdsMenus["textads"] && ($this->activeAdsMenus["productads"] || $this->activeAdsMenus["categoryads"])) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span> | </span>
            
        </div>
	
    <?php } ?>

	<?php if ($this->activeAdsMenus["productads"]) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span>
                
                <?php 
                
                echo $this->section != "productads" && $this->section != "productadscategories" ? 
				
					 "<a href=\"" . JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=banners&section=" . $this->activeAdsMenus["productads"])) . "\">" : NULL;
                
                echo JText::_("PRODUCT_ADS");
                
                echo $this->section != "productads" && $this->section != "productadscategories" ? "</a>" : NULL;
                
                ?>
                
            </span>
            
        </div>
        
	<?php } ?>
    
    <?php if ($this->activeAdsMenus["productads"] && $this->activeAdsMenus["categoryads"]) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span> | </span>
            
        </div>
	
    <?php } ?>
        
	<?php if ($this->activeAdsMenus["categoryads"]) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span>
                
                <?php 
                
                echo $this->section != "categoryads" ? "<a href=\"" . JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=banners&section=categoryads")) . "\">" : NULL;
                
                echo JText::_("CATEGORY_ADS");
                
                echo $this->section != "categoryads" ? "</a>" : NULL;
                
                ?>
                
            </span>
            
        </div>
        
	<?php } ?>
        
</div>

<div style="clear: both;"></div>

<br />