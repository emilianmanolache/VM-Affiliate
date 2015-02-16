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

// get current document

$document = &JFactory::getDocument();

// add the component style

$document->addStyleSheet(JURI::base() . "components/com_affiliate/views/panel/tmpl/css/style.css");

$document->addCustomTag('<!--[if lte IE 7]>'				.

							'<link rel="stylesheet" href="' . JURI::base() . 'components/com_affiliate/views/panel/tmpl/css/style.ie7.css" type="text/css" />' . 
						
						'<![endif]-->');
								
?>

<?php if ($this->params->get( 'show_page_title', 1)) { ?>

<div class="componentheading<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">

	<?php echo $this->escape($this->params->get('page_title')); ?>
    
</div>

<?php } ?>

<div id="affiliatePanel">

	<?php $this->display("topmenu"); ?>
    
    <?php 
	
	if ($this->subview == "home") {
		
		$this->display("mainlinks"); 
		
	} 
	
	?>
	
	<?php $this->display($this->subview); ?>
    
    <?php $this->display("footer"); ?>
    
</div>