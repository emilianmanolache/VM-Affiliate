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

// initiate mootools

JHTML::_('behavior.mootools');

// get pagination links

$pagination = $this->pagination->getPagesLinks();

// insert referral link expansion function

$document	= &JFactory::getDocument();

$document->addScriptDeclaration("function expandAffiliateReferrerLink(id) {
	
										var a 									= document.getElementById(id).title;
										
										var b 									= document.getElementById(id).innerHTML;
										
										document.getElementById(id).innerHTML   = a;
										
										document.getElementById(id).innerHTML   = document.getElementById(id).innerHTML.replace(/&amp;/i,	'&');
										
										document.getElementById(id).innerHTML   = document.getElementById(id).innerHTML.replace(/§ion/i,	'&amp;section');
										
										document.getElementById(id).title 		= b;
										
								 }");
								 
?>

<div id="affiliateTrafficIcon" class="affiliatePanelIcon"><h2 class="affiliatePanelTitle"><?php echo JText::_("TRAFFIC"); ?></h2></div>

<br />

<?php $this->display("traffic_menu"); ?>

<div>

<?php

if (is_array($this->traffic)) { 

	?>

    <table style="width: 100%;">
    
        <tr style="text-align: left;">
        
            <td>
            
                <strong>#</strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("IP_ADDRESS"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("REFERRING_URL"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("DATE"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("TIME"); ?></strong>
                
            </td>
            
        </tr>
        
    <?php 
    
    foreach ($this->traffic as $traffic) {

        echo "<tr style=\"text-align: left;\" class=\"affiliateDataRow\">";
            
        echo "<td>" . $traffic["index"]		. "</td>";
        
        echo "<td>" . $traffic["ip"] 		. "</td>";

		echo "<td>" . $traffic["referrer"] 	. "</td>";
		
        echo "<td>" . $traffic["date"] 		. "</td>";
        
        echo "<td>" . $traffic["time"]		. "</td>";
        
        echo "</tr>";
            
    }
    
    ?>
    
    </table>
    
    <br />
    
    <div style="text-align: center;"><?php echo $this->pagination->getResultsCounter(); ?></div>
    
    <?php if (!empty($pagination)) { ?>
    
	    <br />
        
        <div style="text-align: center;"><?php echo $pagination; ?></div>
    
    <?php } ?>
    
	<?php

}

else {
	
	echo JText::_("NO_RESULTS");
	
}

?>

</div>