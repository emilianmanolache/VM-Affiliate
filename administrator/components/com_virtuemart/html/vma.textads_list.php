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

$keyword	= &JRequest::getVar("keyword", 	"");

$link		= $ps_vma->getAdminLink();

require_once (CLASSPATH 				. 	"pageNavigation.class.php");

require_once (CLASSPATH 				. 	"htmlTools.class.php");

// load the modal box

JHTML::_("behavior.modal");

// prepare the queries

$join		= "LEFT JOIN #__vm_affiliate_size_groups sizegroups ON textads.`width` = sizegroups.`width` AND textads.`height` = sizegroups.`height`";

$query		= "SELECT textads.*, sizegroups.`name` AS sizegroup FROM #__vm_affiliate_textads textads " 	. $join;

$navQuery	= "SELECT COUNT(*) FROM #__vm_affiliate_textads textads " 									. $join;

// add the search query if a keyword exists

if (!empty($keyword)) {
	
	// prepare the search query
	
	$searchIn 	= array("title", "content", "width", "height");
	
	$filter		= " WHERE " . $ps_vma->prepareSearch($searchIn, $keyword, "textads");
	
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
				 
					JText::_("TITLE")																		=> "",
					
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

    <div class="adminPanelTitleIcon" id="adminTextAdsIcon">
    
        <h1 class="adminPanelTitle"><?php echo JText::_("TEXT_ADS"); ?></h1>
        
    </div>
    
    <?php if (!empty($keyword) || $resultsNo > 0) { ?>
    
    <div class="affiliateSearchBox">
    
	<?php    
    
        // write the search field
    
        $listObj->writeSearchHeader(NULL, NULL, "vma", "textads_list");
        
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
			
            $listObj->addCell(vmCommonHTML::idBox($i, $i, false, "textad_id"),	"style=\"width: 20px;\"");
            
			// add textad title
			
            $listObj->addCell("<a href=\"" . JRoute::_($link . "page=vma.textads_form&amp;textad_id=" . $result->textad_id) . "\">" . $result->title . "</a>");
			
			// add textad size
			
            $listObj->addCell(($result->width || $result->height ? 
			
							  (($result->width ? $result->width : "&#8734;") . "x" . ($result->height ? $result->height : "&#8734;")) : 
							  
							  "&#8734;") . ($result->sizegroup ? " " . "(" . $result->sizegroup . ")" : NULL));
			
			// add textad hits
			
			$hits = $ps_vma->getHits("textad", $result->textad_id);
			
			$hits = $hits ? $hits : "0";
			
			$listObj->addCell($hits);
			
			// add textad link
			
			$listObj->addCell($ps_vma->processLink($result->link));
			
			// add preview link
			
            $listObj->addCell("<a class=\"modal\" href=\"" 	. $ps_vma->_website . "index.php?option=com_affiliate&amp;view=prev&amp;tmpl=component&amp;type=textads&amp;id=" . 
			
							  $result->textad_id . "&amp;frontend=0&amp;format=raw\">" . 
							  
							  JText::_("PREVIEW") . 
															  
							  "</a>");
            
			// add textad enable/disable button
			
            $listObj->addCell($ps_vma->itemToggleButton($result->textad_id, "textad", $result->published, $keyword, $limitstart), 	"style=\"text-align: center;\"");
            
			// add textad delete button
			
            $listObj->addCell($ps_vma->itemDeleteButton($result->textad_id, "textad", $keyword, $limitstart), 						"style=\"text-align: center;\"");
            
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
		
		echo $ps_vma->displayNoResultsFound("vma.textads_list");
		
	}
	
	?>
    
</div>