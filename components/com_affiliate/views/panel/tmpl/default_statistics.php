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

global $vmaSettings;

?>

<div id="affiliateStatisticsIcon" class="affiliatePanelIcon"><h2 class="affiliatePanelTitle"><?php echo JText::_("STATISTICS"); ?></h2></div>

<br />

<?php $this->display("statistics_menu"); ?>

<div style="width: 100%; text-align: center;">

	<?php
	
	if ($this->data) {
		
		?>
        
        <div id="affiliateStatisticsGraph">
        
            <img src="<?php echo JURI::base() . "components/com_affiliate/statistics/" . md5($this->affiliate->affiliate_id) . 
			
			$this->type . $this->period; ?>.png" alt="<?php echo JText::_("STATISTICS"); ?>" />
             
		</div>
        
        <?php
		
	}
	
    else {
        
        echo JText::_("NO_RESULTS");
        
    }
	
	?>
    
</div>