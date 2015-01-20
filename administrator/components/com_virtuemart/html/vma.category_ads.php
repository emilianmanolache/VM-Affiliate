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

// load the modal box

JHTML::_("behavior.modal");

// prepare the queries

$join		= "LEFT JOIN #__vm_affiliate_links_categories categoryAds ON category.`category_id` = categoryAds.`category_id`, " 								. 

			  "#__vm_product product, #__vm_product_category_xref xref";

$condition	= "category.`category_publish` = 'Y' AND product.`product_id` = xref.`product_id` AND category.`category_id` = xref.`category_id` " 			. 

			  "AND category.`category_thumb_image` != '' AND product.`product_publish` = 'Y'";

$query		= "SELECT category.`category_id` AS categoryID, category.`category_name` AS categoryName, categoryAds.`published` AS categoryPublished, "		. 

			  "COUNT(DISTINCT xref.`product_id`) AS productsNo FROM #__vm_category category "																. $join;

$navQuery	= "SELECT COUNT(DISTINCT category.`category_id`) FROM #__vm_category category "																	. $join;

$query	   .= " WHERE " . $condition;

$navQuery  .= " WHERE " . $condition;

// add the search query if a keyword exists

if (!empty($keyword)) {
	
	// prepare the search query
	
	$searchIn 	= array("category_name");
	
	$filter		= " AND " . $ps_vma->prepareSearch($searchIn, $keyword, "category");
	
	// add the search query to the actual queries
	
	$query	   .= $filter;
	
	$navQuery  .= $filter;
			  
}

// run the navigation query

$ps_vma->_db->setQuery($navQuery);

$resultsNo	= $ps_vma->_db->loadResult();

// add ordering parameters to the results query

$query	   .= " GROUP BY category.`category_id` ORDER BY category.`category_name` ASC";

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
					
					JText::_("PRODUCTS")																	=> "",
					
					JText::_("PREVIEW") 																	=> "",
										
					JText::_("PUBLISHED")																	=> "");

// run the results query

$ps_vma->_db->setQuery($query);

$results	= $ps_vma->_db->loadObjectList();
	
?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminCategoryAdsIcon">
    
        <h1 class="adminPanelTitle"><?php echo JText::_("CATEGORY_ADS"); ?></h1>
        
    </div>
    
    <?php if (!empty($keyword) || $resultsNo > 0) { ?>
    
    <div class="affiliateSearchBox">
    
	<?php    
    
        // write the search field
    
        $listObj->writeSearchHeader(NULL, NULL, "vma", "category_ads");
        
    ?>
        
        <input type="hidden" name="type" value="categoryads" />
        
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
			
            $listObj->addCell($pageNav->rowNumber($i), 										"style=\"width: 20px;\"");
            
			// add checkbox
			
            $listObj->addCell(vmCommonHTML::idBox($i, $result->categoryID, false, "id"),	"style=\"width: 20px;\"");
            
			// add category name
			
            $listObj->addCell("<a href=\"" . $link . "page=product.product_category_form&amp;category_id=" . $result->categoryID . "\">" . $result->categoryName . "</a>");
            
			// add products number
			
            $listObj->addCell($result->productsNo);
			
			// add preview link
			
            $listObj->addCell("<a class=\"modal\" href=\"" 	. $ps_vma->_website . "index.php?option=com_affiliate&amp;view=prev&amp;tmpl=component&amp;type=categoryads&amp;id=" . 
			
							  $result->categoryID . "&amp;frontend=0&amp;format=raw\">" . 
							  
							  JText::_("PREVIEW") . 
															  
							  "</a>");
			
			// add publish button
			
            $listObj->addCell($ps_vma->itemToggleButton($result->categoryID, "categoryads", ($result->categoryPublished == '0' ? 0 : 1), $keyword, $limitstart));
            
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