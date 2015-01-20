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

global $ps_vma, $VM_LANG;

$keyword	= &JRequest::getVar("keyword", 	"");

$modal		= &JRequest::getVar("modal", 	"");

$type		= &JRequest::getVar("type", 	"");

$link		= $ps_vma->getAdminLink();

require_once (CLASSPATH 				. 	"pageNavigation.class.php");

require_once (CLASSPATH 				. 	"htmlTools.class.php");

// prepare the queries

$condition	= $type == "banners" ? "`width` > 0 AND `height` > 0" : "1 = 1";

$query		= "SELECT * FROM #__vm_affiliate_size_groups WHERE " 			. $condition;

$navQuery	= "SELECT COUNT(*) FROM #__vm_affiliate_size_groups WHERE " 	. $condition;

// add the search query if a keyword exists

if (!empty($keyword)) {
	
	// prepare the search query
	
	$searchIn 	= array("width", "height", "name");
	
	$filter		= " AND " . $ps_vma->prepareSearch($searchIn, $keyword);
	
	// add the search query to the actual queries
	
	$query	   .= $filter;
	
	$navQuery  .= $filter;
			  
}

// run the navigation query

$ps_vma->_db->setQuery($navQuery);

$resultsNo	= $ps_vma->_db->loadResult();

// add ordering parameters to the results query

$query	   .= " ORDER BY `width` ASC";

// add navigation parameters to the results query

$limitstart = $resultsNo <= $limitstart ? 0 : $limitstart;

$query	   .= " LIMIT " . $limitstart . ", " . $limit;

// create the page navigation

$pageNav 	= new vmPageNav($resultsNo, $limitstart, $limit);

// create the list object with page navigation

$listObj 	= new listFactory($pageNav);

// prepare the table columns

$columns 	= array("#" 																					=> 	"style=\"width: 20px;\"");

if (!$modal) {
	
	$columns["<input type=\"checkbox\" name=\"toggle\" onclick=\"checkAll(" . $resultsNo . ")\" />"]		= 	"style=\"width: 20px;\"";

}

$columns[JText::_("NAME")]																					= 	"";
					
$columns[JText::_("WIDTH")]																					= 	"";
					
$columns[JText::_("HEIGHT")]																				= 	"";
					
$columns[JText::_("REMOVE")]																				= 	"";

// run the results query

$ps_vma->_db->setQuery($query);

$results	= $ps_vma->_db->loadObjectList();

?>

<div class="affiliateAdminPage" <?php 

	if ($modal) {
		
		echo "style=\"border: 0 none;\"";
		
	}
	
	?>>

    <div class="adminPanelTitleIcon" id="adminSizeGroupsIcon">
    
        <div style="float: left;">
        
        	<h1 class="adminPanelTitle" <?php 
				
					echo $modal ? "style=\"height: 64px; overflow: hidden; width: 420px;\"" : NULL; 
					
					?>>
			
				<?php echo JText::_("SIZE_GROUPS"); ?>
                
			</h1>
            
		</div>
        
        <?php 
		
			if ($modal) {
				
				?>
                
                <div style="float: right; text-align: center; margin-top: 10px; margin-right: 10px;">
                
					<a class="toolbar" href="javascript:void(0);" id="affiliateNewSizeGroup">
                    
                    	<div class="vmicon-32-new" type="Standard"></div><?php 
						
							echo $VM_LANG->_("CMN_NEW"); 
							
						?>
                        
					</a>
                    
                </div>
        
        		<?php
				
			}
			
		?>
        
    </div>
    
    <?php if ((!empty($keyword) || $resultsNo > 0) && !$modal) { ?>
    
    <div class="affiliateSearchBox">
    
	<?php    
    
        // write the search field
    
        $listObj->writeSearchHeader(NULL, NULL, "vma", "size_groups_list");
        
    ?>
        
    </div>
    
    <br />
    
    <div style="clear: both;"></div>
    
    <?php } ?>
    
    <?php
	
	if ($modal) {
		
		?>
        
        <div style="width: 100%; height: 1px; margin-top: 19px; display: block;"></div>
        
        <?php
		
	}
	
	?>
    
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
			
            $listObj->addCell($pageNav->rowNumber($i),									"style=\"width: 20px;\"");
            
			// add checkbox
			
			if (!$modal) {
				
	            $listObj->addCell(vmCommonHTML::idBox($i, $i, false, "banner_id"),		"style=\"width: 20px;\"");
    		
			}
			
			// add size group name
			
            $listObj->addCell("<a href=\"" . JRoute::_($link . "page=vma.size_groups_form&amp;size_group_id=" . $result->size_group_id . ($modal ? "&amp;modal=true" : NULL)) . "&type=" . $type . "\">" . ($result->name ? $result->name : "N/A") . "</a>");
			
			// add size group width
			
            $listObj->addCell($result->width 	? $result->width 	: "&#8734;");
			
			// add size group height
			
            $listObj->addCell($result->height 	? $result->height 	: "&#8734;");
            
			// add size group delete button
			
            $listObj->addCell($ps_vma->itemDeleteButton($result->size_group_id, "sizegroup", $keyword, $limitstart) . 
			
							  "<div style=\"display: none;\">" 		. $result->size_group_id . "</div>", "style=\"text-align: center;\"");
            
            $i++;
            
        }
        
        // write the data table
        
        $listObj->writeTable();
        
		// finish the table
		
        $listObj->endTable();
        
		// write the navigation footer
		
		//if (!$modal) {
			
			$listObj->writeFooter("");
        
		//}
		
        ?>

	</div>
    
    <?php 
	
	}
	
	else {
		
		echo "<div style=\"text-align: center;\">" . JText::_("NO_RESULTS") . "</div>";
		
	}
	
	?>
    
    <?php
	
	if ($modal) {
		
		?>
        
        <div style="display: none;" id="affiliatePaginationLimitContainer" class="affiliatePaginationValue-<?php echo $limit; ?>"></div>
        
        <div style="display: none;" id="affiliatePaginationLimitStartContainer" class="affiliatePaginationValue-<?php echo $limitstart; ?>"></div>
        
        <?php
		
	}
	
	?>

    <div style="clear: both;"></div>
    
</div>