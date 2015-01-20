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

// get pagination links

$pagination = $this->pagination->getPagesLinks();

// initiate squeezebox for the ad previews
					
JHTML::_("behavior.modal", "a.affiliateModal");
			
if (count($this->productadscategories) > 0) { 

	?>

    <table style="width: 100%;">
    
        <tr style="text-align: left;">
        
            <td>
            
                <strong>#</strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("CATEGORY"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("PRODUCT_ADS"); ?></strong>
                
            </td>
            
        </tr>
        
    <?php 
    
    foreach ($this->productadscategories as $category) {

        echo "<tr style=\"text-align: left;\" class=\"affiliateDataRow\">";
            
        echo "<td>" . $category["index"]		. "</td>";
        
        echo "<td>" . $category["name"]			. "</td>";
        
        echo "<td>" . $category["products"] 	. "</td>";
        
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