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
								 
?>

<div id="affiliateSalesIcon" class="affiliatePanelIcon"><h2 class="affiliatePanelTitle"><?php echo JText::_("SALES"); ?></h2></div>

<br />

<?php $this->display("sales_menu"); ?>

<div>

<?php

if (is_array($this->sales)) { 

	?>

    <table style="width: 100%;">
    
        <tr style="text-align: left;">
        
            <td>
            
                <strong>#</strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("ORDER_ID"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("ORDER_SUBTOTAL"); ?></strong>
                
            </td>
            
            <?php 
			
				if (!$this->confirmed) { 
				
			?>
            
                <td>
                
                    <strong><?php echo JText::_("CONFIRMED"); ?></strong>
                    
                </td>
            
            <?php 
			
				} 
				
			?>
            
            <?php 
			
				if ($this->paid) { 
				
			?>
            
                <td>
                
                    <strong><?php echo JText::_("PAID"); ?></strong>
                    
                </td>
            
            <?php 
			
				} 
				
			?>
            
            <td>
            
                <strong><?php echo JText::_("DATE"); ?></strong>
                
            </td>
            
            <td>
            
                <strong><?php echo JText::_("TIME"); ?></strong>
                
            </td>
            
        </tr>
        
    <?php 
    
    foreach ($this->sales as $sale) {

        echo "<tr style=\"text-align: left;\" class=\"affiliateDataRow\">";
            
        echo "<td>" . $sale["index"]		. "</td>";
        
        echo "<td>" . $sale["id"] 			. "</td>";

		echo "<td>" . $sale["subtotal"] 	. "</td>";
		
        echo !$this->confirmed ? 
		
			 "<td>" . $sale["status"] 		. "</td>" : 
			 
			 NULL;
        
        echo $this->paid ?
		
			 "<td>" . $sale["paid"]			. "</td>" :
			 
			 NULL;
		
		echo "<td>" . $sale["date"]			. "</td>";
		
		echo "<td>" . $sale["time"]			. "</td>";
        
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