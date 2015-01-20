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
        
                echo !$this->paid ? "<a href=\"" . JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=sales&paid=1&confirmed=" . $this->confirmed)) . "\">" : NULL; 
            
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
        
                echo $this->paid ? "<a href=\"" . JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=sales&paid=0&confirmed=" . $this->confirmed)) . "\">" : NULL; 
            
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
        
                echo $this->confirmed ? "<a href=\"" . JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=sales&confirmed=0&paid=" . $this->paid)) . "\">" : NULL; 
            
                echo ucwords(JText::_("ALL"));
                
                echo $this->confirmed ? "</a>" : NULL;
                
            ?>
        
        </span>
            
	</div>
    
    <div class="affiliateTopMenuLink">
    
        <span>/</span>
    
    </div>
    
    <div class="affiliateTopMenuLink">
            
        <span>
        
            <?php
        
                echo !$this->confirmed ? "<a href=\"" . JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=sales&confirmed=1&paid=" . $this->paid)) . "\">" : NULL; 
            
                echo JText::_("CONFIRMED"); 
                
                echo !$this->confirmed ? "</a>" : NULL;
                
            ?>
        
        </span>
        
    </div>
    
</div>

<div style="clear: both;"></div>

<br />