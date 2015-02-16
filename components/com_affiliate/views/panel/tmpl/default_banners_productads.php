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

global $vmaSettings, $vmaHelper;

// get pagination links

$pagination = $this->pagination->getPagesLinks();

// initiate squeezebox for the ad previews
					
JHTML::_("behavior.modal", "a.affiliateModal");
			
if (count($this->productads) > 0) { 

	?>
	
    <?php 
	
	if (isset($this->categoryName) && $this->categoryName) {
		
		?>
        
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=banners&section=productadscategories")); ?>">
        
            &lt;&lt; <?php echo JText::_("CATEGORIES"); ?></a>
            
		<span>&nbsp;|&nbsp;</span>
        
        <strong><?php echo JText::_("CATEGORY"); ?></strong>: <em><?php echo $this->categoryName; ?></em>
        
        <br />
        
        <br />

        <?php
		
	}
	
	?>
    
    <table style="width: 100%;">
    
        <tr style="text-align: left;">
        
            <td>
            
                <strong>#</strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("THUMB"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("NAME"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("HTML_CODE"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("PREVIEW"); ?></strong>
                
            </td>
            
        </tr>
        
    <?php 
    
    foreach ($this->productads as $productad) {

        echo "<tr style=\"text-align: left;\" class=\"affiliateDataRow\">";
            
        echo "<td>" . $productad["index"]		. "</td>";
        
        echo "<td>" . $productad["thumb"] 		. "</td>";
        
        echo "<td>" . $productad["name"]		. "</td>";
        
        echo "<td>" . $productad["html"] 		. "</td>";
        
        echo "<td>" . $productad["prev"]		. "</td>";
        
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