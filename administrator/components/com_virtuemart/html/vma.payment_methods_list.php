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

// initialize required variables

global $ps_vma;

$keyword	= &JRequest::getVar("keyword", "");

$link		= $ps_vma->getAdminLink();

require_once (CLASSPATH 				. "pageNavigation.class.php");

require_once (CLASSPATH 				. "htmlTools.class.php");

// prepare the queries

$query		= "SELECT methods.*, COUNT(fields.`field_id`) AS fields FROM #__vm_affiliate_methods methods "	. 

			  "LEFT JOIN #__vm_affiliate_method_fields fields ON methods.`method_id` = fields.`method_id` " . 
			  
			  "GROUP BY methods.`method_id`";

$navQuery	= "SELECT COUNT(*) FROM #__vm_affiliate_methods methods";

// add the search query if a keyword exists

if (!empty($keyword)) {
	
	// prepare the search query
	
	$searchIn 	= array("method_name", "method_enabled");
	
	$filter		= " WHERE " . $ps_vma->prepareSearch($searchIn, $keyword, "methods");
	
	// add the search query to the actual queries
	
	$query	   .= $filter;
	
	$navQuery  .= $filter;
			  
}

// run the navigation query

$ps_vma->_db->setQuery($navQuery);

$resultsNo	= $ps_vma->_db->loadResult();

// add navigation parameters to the results query

$limitstart = $resultsNo <= $limitstart ? 0 : $limitstart;

$query	   .= " LIMIT " . $limitstart . ", " . $limit;

// create the page navigation

$pageNav 	= new vmPageNav($resultsNo, $limitstart, $limit);

// create the list object with page navigation

$listObj 	= new listFactory($pageNav);

// prepare the table columns

$columns 	= array("#" 																					=> "style=\"width: 20px;\"", 

					"<input type=\"checkbox\" name=\"toggle\" onclick=\"checkAll(" . $resultsNo . ")\" />"	=> "style=\"width: 20px;\"",
				 
					JText::_("NAME")																		=> "",
					
					JText::_("FIELDS")																		=> "",
					
					JText::_("ENABLED")			 															=> "",
					
					JText::_("REMOVE")																		=> "");

// run the results query

$ps_vma->_db->setQuery($query);

$results	= $ps_vma->_db->loadObjectList();

?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminPaymentMethodsIcon">
    
        <h1 class="adminPanelTitle"><?php echo JText::_("PAYMENT_METHODS"); ?></h1>
        
    </div>
    
    <?php if (!empty($keyword) || $resultsNo > 0) { ?>
    
    <div class="affiliateSearchBox">
    
	<?php    
    
        // write the search field
    
        $listObj->writeSearchHeader(NULL, NULL, "vma", "payment_methods_list");
        
    ?>
        
    </div>
    
    <br />
    
    <div style="clear: both;"></div>
    
    <?php } ?>
    
    <?php 
	
	if ($resultsNo > 0) { 
	
	?>
    
    <div>
        
		<?php 
        
        // start the data table
        
        $listObj->startTable();
        
		// write the table header/columns
		
        $listObj->writeTableHeader($columns); 
        
        $i = 0;
        
		// process each row
		
        foreach ($results as $result) {

            // start a new row
            
            $listObj->newRow();
            
			// add row number
			
            $listObj->addCell($pageNav->rowNumber($i),							"style=\"width: 20px;\"");
            
			// add checkbox
			
            $listObj->addCell(vmCommonHTML::idBox($i, $i, false, "method_id"),	"style=\"width: 20px;\"");
            
			// add payment method name
			
            $listObj->addCell(($result->method_id > 1 ? "<a href=\"" . $link .	"page=vma.payment_methods_form&amp;method_id=" . $result->method_id . "\">" : NULL) . 
			
							  $result->method_name . 
							  
							  ($result->method_id > 1 ? "</a>" : NULL));

			// add number of fields
			
			$listObj->addCell($result->fields);
			
			// add affiliate enable/disable button
			
            $listObj->addCell($ps_vma->itemToggleButton($result->method_id, "paymentmethod", $result->method_enabled, $keyword, $limitstart));
            
			// add affiliate delete button
			
            $listObj->addCell(($result->method_id > 1 ? 
			
							  $ps_vma->itemDeleteButton($result->method_id, "paymentmethod", $keyword, $limitstart) : 
							  
							  "N/A"));
            
            $i++;
            
        }
        
        // write the data table
        
        $listObj->writeTable();
        
		// finish the table
		
        $listObj->endTable();
        
		// write the navigation footer
		
		$listObj->writeFooter("");
        
        ?>

	</div>
    
    <?php 
	
	}
	
	else {
		
		echo $ps_vma->displayNoResultsFound("vma.payment_methods_list");
		
	}
	
	?>
    
</div>