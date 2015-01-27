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

// get pagination links

$pagination = $this->pagination->getPagesLinks();
								 
?>

<div id="affiliatePaymentsIcon" class="affiliatePanelIcon"><h2 class="affiliatePanelTitle"><?php echo JText::_("PAYMENTS"); ?></h2></div>

<br />

<div>

<?php

if (is_array($this->payments)) { 

	?>

    <table style="width: 100%;">
    
        <tr style="text-align: left;">
        
            <td>
            
                <strong>#</strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("PAYMENT_METHOD"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("AMOUNT"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("CONFIRMED"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("DATE"); ?></strong>
                
            </td>
            
        </tr>
        
    <?php 
    
    foreach ($this->payments as $payment) {

        echo "<tr style=\"text-align: left;\" class=\"affiliateDataRow\">";
            
        echo "<td>" . $payment["index"]		. "</td>";
        
        echo "<td>" . $payment["method"] 	. "</td>";

		echo "<td>" . $payment["amount"] 	. "</td>";
		
		echo "<td>" . $payment["status"] 	. "</td>";
		
        echo "<td>" . $payment["date"] 		. "</td>";
        
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