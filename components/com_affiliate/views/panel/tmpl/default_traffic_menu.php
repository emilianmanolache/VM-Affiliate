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

global $ps_vma;

?>

<div class="affiliateTopMenu">
            
    <div class="affiliateTopMenuLink">
        
        <span>
        
            <?php
        
                echo !$this->paid ? "<a href=\"" . JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=traffic&paid=1&unique=" . $this->unique)) . "\">" : NULL; 
            
                echo JText::_("OVERALL"); 
                
                echo !$this->paid ? "</a>" : NULL;
                
            ?>
        
        </span>
            
	</div>
    
    <div class="affiliateTopMenuLink">
    
        <span>/</span>
    
    </div>
    
    <div class="affiliateTopMenuLink">
        
        <span>
        
            <?php
        
                echo $this->paid ? "<a href=\"" . JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=traffic&paid=0&unique=" . $this->unique)) . "\">" : NULL; 
            
                echo JText::_("CURRENT"); 
                
                echo $this->paid ? "</a>" : NULL;
                
            ?>
        
        </span>
            
	</div>
    
    <div class="affiliateTopMenuLink">
        
        <span>|</span>
        
	</div>
    
    <div class="affiliateTopMenuLink">
            
        <span>
        
            <?php
        
                echo $this->unique ? "<a href=\"" . JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=traffic&unique=0&paid=" . $this->paid)) . "\">" : NULL; 
            
                echo JText::_("CLICKS"); 
                
                echo $this->unique ? "</a>" : NULL;
                
            ?>
        
        </span>
            
	</div>
    
    <div class="affiliateTopMenuLink">
    
        <span>/</span>
    
    </div>
    
    <div class="affiliateTopMenuLink">
            
        <span>
        
            <?php
        
                echo !$this->unique ? "<a href=\"" . JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=traffic&unique=1&paid=" . $this->paid)) . "\">" : NULL; 
            
                echo JText::_("UNIQUE_CLICKS"); 
                
                echo !$this->unique ? "</a>" : NULL;
                
            ?>
        
        </span>
        
    </div>
    
</div>

<div style="clear: both;"></div>

<br />