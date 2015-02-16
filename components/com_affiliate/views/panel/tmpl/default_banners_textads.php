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

// initiate squeezebox for the ad previews
					
JHTML::_("behavior.modal", "a.affiliateModal");
			
if (count($this->textads) > 0) { 

	?>

    <table style="width: 100%;">
    
        <tr style="text-align: left;">
        
            <td>
            
                <strong>#</strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("TITLE"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("SIZE"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("HITS"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("HTML_CODE"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("PREVIEW"); ?></strong>
                
            </td>
            
        </tr>
        
    <?php 
    
    foreach ($this->textads as $textad) {

        echo "<tr style=\"text-align: left;\" class=\"affiliateDataRow\">";
            
        echo "<td>" . $textad["index"]		. "</td>";
        
        echo "<td>" . $textad["title"] 		. "</td>";
        
        echo "<td>" . $textad["size"]		. "</td>";
        
        echo "<td>" . $textad["hits"] 		. "</td>";
        
        echo "<td>" . $textad["html"] 		. "</td>";
        
        echo "<td>" . $textad["prev"]		. "</td>";
        
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