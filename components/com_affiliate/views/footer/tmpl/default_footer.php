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

// add the separator horizontal rule style

$document	= &JFactory::getDocument();

$document->addStyleDeclaration("div.affiliateHR {
	
									border-bottom:	2px solid #CCCCCC; 
									
									margin:			0px 0px 10px 0px;
									
									padding:		5px 0px 5px 0px;
									
									width:			100%;
									
									#padding:		0px 0px 0px 0px;
									
								}");

// get site name

$config		= &JFactory::getConfig();

$siteName	= $config->get('sitename');

?>

<div class="affiliateHR"></div>

<div style="text-align: center;">

    <em>

        <strong><?php echo $siteName; ?></strong> Affiliate Program
    
    </em>
    
</div>

<br />