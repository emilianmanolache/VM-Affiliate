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

$keyword	= &JRequest::getVar("keyword", 				"");

$link		= $ps_vma->getAdminLink();

require_once (CLASSPATH 							. 	"pageNavigation.class.php");

require_once (CLASSPATH 							. 	"htmlTools.class.php");

// load the modal box

JHTML::_("behavior.modal");

// prepare the queries

$join		= "LEFT JOIN #__vm_affiliate_links productAds ON product.`product_id` = productAds.`product_id`, #__vm_category category, #__vm_product_category_xref xref";

$condition	= "product.`product_publish` = 'Y' AND category.`category_id` = xref.`category_id` AND category.`category_publish` = 'Y' " 				. 

			  "AND product.`product_parent_id` = '0' AND product.`product_thumb_image` != '' AND product.`product_id` = xref.`product_id`";

$query		= "SELECT product.`product_id` AS productID, product.`product_name` AS productName, productAds.`published` AS productPublished, "		. 

			  "GROUP_CONCAT(DISTINCT(category.`category_name`) SEPARATOR ', ') AS categoryName FROM #__vm_product product "									. $join;

$navQuery	= "SELECT COUNT(DISTINCT product.`product_id`) FROM #__vm_product product "																. $join;

$query	   .= " WHERE " . $condition;

$navQuery  .= " WHERE " . $condition;

// add the search query if a keyword exists

if (!empty($keyword)) {
	
	// prepare the search query
	
	$searchIn 	= array("product_name");
	
	$filter		= " AND " . $ps_vma->prepareSearch($searchIn, $keyword, "product");
	
	// add the search query to the actual queries
	
	$query	   .= $filter;
	
	$navQuery  .= $filter;
			  
}

// run the navigation query

$ps_vma->_db->setQuery($navQuery);

$resultsNo	= $ps_vma->_db->loadResult();

// add ordering parameters to the results query

$query	   .= " GROUP BY xref.`product_id` ORDER BY product.`product_name` ASC";

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
				 
					JText::_("PRODUCT_NAME")																=> "",
					
					JText::_("CATEGORY")																	=> "",
					
					JText::_("PREVIEW") 																	=> "",
										
					JText::_("PUBLISHED")																	=> "");

// run the results query

$ps_vma->_db->setQuery($query);

$results	= $ps_vma->_db->loadObjectList();
	
?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminProductAdsIcon">
    
        <h1 class="adminPanelTitle"><?php echo JText::_("PRODUCT_ADS"); ?></h1>
        
    </div>
    
    <?php if (!empty($keyword) || $resultsNo > 0) { ?>
    
    <div class="affiliateSearchBox">
    
	<?php    
    
        // write the search field
    
        $listObj->writeSearchHeader(NULL, NULL, "vma", "product_ads");
        
    ?>
        
        <input type="hidden" name="type" value="productads" />
        
    </div>
    
    <?php } ?>
    
    <?php
	
		// include the publish/unpublish buttons
		
		echo $ps_vma->getPublishUnpublishButtons();
	
	?>
    
    <br />
    
    <div style="clear: both;"></div>
    
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
			
            $listObj->addCell($pageNav->rowNumber($i),										"style=\"width: 20px;\"");
            
			// add checkbox
			
            $listObj->addCell(vmCommonHTML::idBox($i, $result->productID, false, "id"),		"style=\"width: 20px;\"");
            
			// add product name
			
            $listObj->addCell("<a href=\"" . $link . "page=product.product_form&amp;product_id=" . $result->productID . "\">" . $result->productName . "</a>");
            
			// add category name
			
            $listObj->addCell($result->categoryName);
			
			// add preview link
			
            $listObj->addCell("<a class=\"modal\" href=\"" 	. $ps_vma->_website . "index.php?option=com_affiliate&amp;view=prev&amp;tmpl=component&amp;type=productads&amp;id=" . 
			
							  $result->productID . "&amp;frontend=0&amp;format=raw\">" . 
							  
							  JText::_("PREVIEW") . 
															  
							  "</a>");
			
			// add publish button
			
            $listObj->addCell($ps_vma->itemToggleButton($result->productID, "productads", ($result->productPublished == '0' ? 0 : 1), $keyword, $limitstart));
            
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
		
		echo "<div style=\"text-align: center;\">" . JText::_("NO_RESULTS") . "</div>";
		
	}
	
	?>
        
</div>