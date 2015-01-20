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

global $ps_vma;

$link			= $ps_vma->getAdminLink();

// get active statistics sections

$ps_vma->getActiveStatsSection();

// get refreshed variables

$type 			= &JRequest::getVar("type", 	"traffic");
			
$period 		= &JRequest::getVar("period", 	"month");

$request 		= $type . $period;

$link1			= $link . "page=vma.statistics&amp;type=" 		. $type 	. "&amp;period=";

$link2			= $link . "page=vma.statistics&amp;period=" 	. $period 	. "&amp;type=";

// get active statistics menus

$activeMenus	= $ps_vma->getActiveStatsMenus();

// get statistics data

$statsData		= $ps_vma->getStatisticsData($request);

?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminStatisticsIcon">
    
        <h1 class="adminPanelTitle"><?php echo JText::_("STATISTICS"); ?></h1>
        
    </div>
    
    <div style="clear: both;"></div>
    
    <br />
    
    <?php 
	
		if ($statsData) {
			
	?>
    
		<div class="affiliateTopMenu affiliateLogsMenu">
              
			<div class="affiliateTopMenuLink">
              
				<?php if ($activeMenus["trafficmonth"] || $activeMenus["trafficyear"]) { ?>
                      
                    <span class="affiliateFilterMenu">
                      
                        <?php 
                          
                        echo ($type != "traffic" ? "<a href=\"" . JRoute::_($link2 . "traffic") . "\">" : NULL) . JText::_("TRAFFIC") . ($type != "traffic" ? "</a>" : NULL);
                          
                        ?>
                          
                    </span>
              
				<?php } ?>
              
				<?php if (($activeMenus["trafficmonth"] || $activeMenus["trafficyear"]) && ($activeMenus["salesmonth"] 	|| $activeMenus["salesyear"])) { ?>
                  
					<span class="affiliateFilterMenu">
                    	
                        <span>|</span>
                        
                    </span>
                  
				<?php } ?>
              
				<?php if ($activeMenus["salesmonth"] || $activeMenus["salesyear"]) { ?>
                  
                    <span class="affiliateFilterMenu">
                        
                        <?php 
                        
                        echo ($type != "sales" ? "<a href=\"" . JRoute::_($link2 . "sales") . "\">" : NULL) . JText::_("SALES") . ($type != "sales" ? "</a>" : NULL);
                        
                        ?>
                        
                    </span>
                  
				<?php } ?>
                
                <?php if ($activeMenus["trafficmonth"] || $activeMenus["trafficyear"] || $activeMenus["salesmonth"] || $activeMenus["salesyear"]) { ?>
              
                    <span class="affiliateFilterMenu">
                    
                        <span>/</span>
                        
                    </span>
                  
				<?php } ?>
            
                <?php if ($activeMenus["trafficmonth"] || $activeMenus["salesmonth"]) { ?>
                    
                    <span class="affiliateFilterMenu">
                    
                        <?php 
                        
                        echo ($period != "month" ? "<a href=\"" . JRoute::_($link1 . "month") . "\">" : NULL) . JText::_("THIS_MONTH") . ($period != "month" ? "</a>" : NULL);
                        
                        ?>
                        
                    </span>
                
                <?php } ?>
              
				<?php if (($activeMenus["trafficmonth"] || $activeMenus["salesmonth"]) && ($activeMenus["trafficyear"] 	|| $activeMenus["salesyear"])) { ?>
                
                    <span class="affiliateFilterMenu">
                    
                        <span>|</span>
                        
                    </span>
                    
                <?php } ?>
                
                <?php if ($activeMenus["trafficyear"] || $activeMenus["salesyear"]) { ?>
                
                    <span class="affiliateFilterMenu">
                            
						<?php 
                        
                        echo ($period != "year" ? "<a href=\"" . JRoute::_($link1 . "year") . "\">" : NULL) . JText::_("THIS_YEAR") . ($period != "year" ? "</a>" : NULL);
                        
                        ?>
                        
                    </span>
                    
                <?php } ?>
               
			</div>
               
		</div>
          
		<div style="clear: both;"></div>
    
    <?php
	
		}
		
	?>
    
    <br />
    
	<?php

    if ($statsData) {
        
        ?>
        
        <div id="affiliateStatisticsGraph">
        
            <img src="<?php echo $ps_vma->_website . "components/com_affiliate/statistics/" . md5("admin") . $type . $period; ?>.png" alt="<?php echo JText::_("STATISTICS"); ?>" />
             
        </div>
        
        <?php
        
    }
    
    else {
        
        echo "<div style=\"text-align: center;\">" . JText::_("NO_RESULTS") . "</div>";
        
    }
    
    ?>
    
    <div style="clear: both;"></div>

</div>