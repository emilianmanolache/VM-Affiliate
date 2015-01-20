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

    <?php if ($this->activeStatisticsMenus["trafficmonth"] || $this->activeStatisticsMenus["trafficyear"]) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span>
            
                <?php 
                
                echo ($this->type != "traffic" ? "<a href=\"" . JRoute::_($ps_vma->vmaRoute($this->link2 . "traffic")) . "\">" : NULL) . 
				
					  JText::_("TRAFFIC") . ($this->type != "traffic" ? "</a>" : NULL);
                
                ?>
                
            </span>
            
        </div>
    
    <?php } ?>
    
    <?php if (($this->activeStatisticsMenus["trafficmonth"] || $this->activeStatisticsMenus["trafficyear"]) && 
	
			  ($this->activeStatisticsMenus["salesmonth"] 	|| $this->activeStatisticsMenus["salesyear"])) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span> | </span>
            
        </div>
        
	<?php } ?>
	
    <?php if ($this->activeStatisticsMenus["salesmonth"] || $this->activeStatisticsMenus["salesyear"]) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span>
                
                <?php 
                
				echo ($this->type != "sales" ? "<a href=\"" . JRoute::_($ps_vma->vmaRoute($this->link2 . "sales")) . "\">" : NULL) . 
				
					  JText::_("SALES") . ($this->type != "sales" ? "</a>" : NULL);
                
                ?>
                
            </span>
            
        </div>
        
	<?php } ?>
    
    <?php if ($this->activeStatisticsMenus["trafficmonth"] || $this->activeStatisticsMenus["trafficyear"] ||
	
			  $this->activeStatisticsMenus["salesmonth"] || $this->activeStatisticsMenus["salesyear"]) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span> / </span>
            
        </div>
        
	<?php } ?>

	<?php if ($this->activeStatisticsMenus["trafficmonth"] || $this->activeStatisticsMenus["salesmonth"]) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span>
            
                <?php 
                
                echo ($this->period != "month" ? "<a href=\"" . JRoute::_($ps_vma->vmaRoute($this->link1 . "month")) . "\">" : NULL) . 
				
					  JText::_("THIS_MONTH") . ($this->period != "month" ? "</a>" : NULL);
                
                ?>
                
            </span>
            
        </div>
    
    <?php } ?>
    
    <?php if (($this->activeStatisticsMenus["trafficmonth"] || $this->activeStatisticsMenus["salesmonth"]) && 
	
			  ($this->activeStatisticsMenus["trafficyear"] 	|| $this->activeStatisticsMenus["salesyear"])) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span> | </span>
            
        </div>
        
	<?php } ?>
	
    <?php if ($this->activeStatisticsMenus["trafficyear"] || $this->activeStatisticsMenus["salesyear"]) { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span>
                
                <?php 
                
				echo ($this->period != "year" ? "<a href=\"" . JRoute::_($ps_vma->vmaRoute($this->link1 . "year")) . "\">" : NULL) . 
				
					  JText::_("THIS_YEAR") . ($this->period != "year" ? "</a>" : NULL);
                
                ?>
                
            </span>
            
        </div>
        
	<?php } ?>
        
</div>

<div style="clear: both;"></div>

<br />