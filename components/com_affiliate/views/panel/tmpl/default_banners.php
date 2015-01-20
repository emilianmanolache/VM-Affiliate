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

<div id="affiliateBannersIcon" class="affiliatePanelIcon"><h2 class="affiliatePanelTitle"><?php echo JText::_("BANNERS_AND_ADS"); ?></h2></div>

<br />

<?php $this->display("banners_menu"); ?>

<div style="width: 100%; text-align: center;">

<?php $this->display("banners_" . $this->section); ?>

</div>