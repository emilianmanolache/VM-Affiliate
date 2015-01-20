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
 
// get vma settings

global $vmaSettings;
								
?>

<?php if ($this->type != "banners") { ?>

<div style="#position: relative; text-align: center; display: table; width: 600px; height: 450px; margin-left: auto; margin-right: auto;">

	<div style="#position: absolute; #top: 50%; #left: 50%; #zoom: 1; display: table-cell; vertical-align: middle;">
    
		<div style="#position: relative; #top: -50%; #left: -50%; 
		
		<?php if ($this->type == "textads") { ?>
        
        	text-align: left;
			
		<?php } ?>
        
        <?php if ($this->type == "productads" || $this->type == "categoryads") { ?>
        
        	display: table;	margin-left: auto; margin-right: auto;

		<?php } ?>
        
        ">
      
      	<?php } ?>
        
		<?php
        
		echo $this->item["content"];

		?>
        
        <?php if ($this->type != "banners") { ?>
        
		</div>
        
	</div>
    
</div>

<?php } ?>