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

JHTML::_("behavior.modal", "a.affiliateModal");

// prepare the queries

$join		= "LEFT JOIN #__vm_affiliate_size_groups sizegroups ON banners.`banner_width` = sizegroups.`width` AND banners.`banner_height` = sizegroups.`height`";

$query		= "SELECT banners.*, sizegroups.`name` AS sizegroup FROM #__vm_affiliate_banners banners " 	. $join;

$navQuery	= "SELECT COUNT(*) FROM #__vm_affiliate_banners banners " 									. $join;

// add the search query if a keyword exists

if (!empty($keyword)) {
	
	// prepare the search query
	
	$searchIn 	= array("banner_name", "banner_type", "banner_width", "banner_height");
	
	$filter		= " WHERE " . $ps_vma->prepareSearch($searchIn, $keyword, "banners");
	
	// add the join and search query to the actual queries
	
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
					
					JText::_("TYPE")																		=> "",
					
					JText::_("SIZE")																		=> "",
					
					JText::_("HITS")																		=> "",
					
					JText::_("LINK")																		=> "",
					
					JText::_("PREVIEW")			 															=> "",
					
					JText::_("PUBLISHED")			 														=> "",
					
					JText::_("REMOVE")																		=> "");

// run the results query

$ps_vma->_db->setQuery($query);

$results	= $ps_vma->_db->loadObjectList();

?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminBannersIcon">
    
        <h1 class="adminPanelTitle"><?php echo JText::_("BANNERS"); ?></h1>
        
    </div>
    
    <?php if (!empty($keyword) || $resultsNo > 0) { ?>
    
    <div class="affiliateSearchBox">
    
	<?php    
    
        // write the search field
    
        $listObj->writeSearchHeader(NULL, NULL, "vma", "banners_list");
        
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
			
            $listObj->addCell(vmCommonHTML::idBox($i, $i, false, "banner_id"),	"style=\"width: 20px;\"");
            
			// add banner name
			
            $listObj->addCell("<a href=\"" . JRoute::_($link . "page=vma.banners_form&amp;banner_id=" . $result->banner_id) . "\">" . $result->banner_name . "</a>");
			
			// add banner type
			
            $listObj->addCell(strtoupper($result->banner_type));
			
			// add banner size
			
            $listObj->addCell($result->banner_width . "x" . $result->banner_height . ($result->sizegroup ? " " . "(" . $result->sizegroup . ")" : NULL));
			
			// add banner hits
			
			$hits = $ps_vma->getHits("banner", $result->banner_id);
			
			$hits = $hits ? $hits : "0";
			
			$listObj->addCell($hits);
			
			// add banner link
			
			$listObj->addCell($ps_vma->processLink($result->banner_link));
			
			// add preview link
			
            $listObj->addCell("<a class=\"affiliateModal\" href=\"" . $ps_vma->_website . "index.php?option=com_affiliate&amp;view=prev&amp;tmpl=component&amp;type=banners&amp;id=" . 
			
							  $result->banner_id . "&amp;frontend=0&amp;format=raw\" rel=\"{size: {x: " . $result->banner_width . ", y: " . $result->banner_height . 
							  
							  "}, classWindow: 'affiliatePreviewWindow'}\">" . JText::_("PREVIEW") . 
															  
							  "</a>");
            
			// add banner enable/disable button
			
            $listObj->addCell($ps_vma->itemToggleButton($result->banner_id, "banner", $result->published, $keyword, $limitstart), 	"style=\"text-align: center;\"");
            
			// add banner delete button
			
            $listObj->addCell($ps_vma->itemDeleteButton($result->banner_id, "banner", $keyword, $limitstart), 						"style=\"text-align: center;\"");
            
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
		
		echo $ps_vma->displayNoResultsFound("vma.banners_list");
		
	}
	
	?>
    
</div>