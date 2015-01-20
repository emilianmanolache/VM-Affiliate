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

$keyword		= &JRequest::getVar("keyword",		"");

$paid			= &JRequest::getVar("paid",			1);

$unique			= &JRequest::getVar("unique",		0);

$affiliateID	= &JRequest::getVar("affiliate_id",	0);

$affiliateName	= NULL;

$condition		= "1 = 1";

$link			= $ps_vma->getAdminLink();

require_once (CLASSPATH 					. 		"pageNavigation.class.php");

require_once (CLASSPATH 					. 		"htmlTools.class.php");

// get affiliate's name (if this is a specific affiliate's traffic page)

if ($affiliateID) {
	
	$query			= "SELECT CONCAT(`fname`, ' ', `lname`) AS name FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
	
	$ps_vma->_db->setQuery($query);
	
	$affiliateName	= $ps_vma->_db->loadResult();
	
}

// prepare the queries

$condition	   .= !$paid 		? " AND clicks.`paid` 			= '0' " 					: NULL;

$condition	   .= $affiliateID 	? " AND clicks.`AffiliateID` 	= '" . $affiliateID . "' "	: NULL;

$join			= "LEFT JOIN #__vm_affiliate affiliates ON clicks.`AffiliateID` = affiliates.`affiliate_id`";

$query			= "SELECT " . 		($unique ? "DISTINCT " : NULL) . "clicks.*, CONCAT(affiliates.`fname`, ' ', affiliates.`lname`) " 	. 

				  "AS name FROM #__vm_affiliate_clicks clicks " 																		. $join . " WHERE " . $condition;

$navQuery		= "SELECT COUNT(" . ($unique ? "DISTINCT " : NULL) . "clicks.`RemoteAddress`) FROM #__vm_affiliate_clicks clicks " 		. $join . " WHERE " . $condition;

// add the search query if a keyword exists

if (!empty($keyword)) {
	
	// prepare the search query
	
	$searchIn 	= array("AffiliateID", "RefURL", "Browser", "date");
	
	$filter		= " AND " . $ps_vma->prepareSearch($searchIn, $keyword, "clicks");
	
	// add the search query to the actual queries
	
	$query	   .= $filter;
	
	$navQuery  .= $filter;
			  
}

// run the navigation query

$ps_vma->_db->setQuery($navQuery);

$resultsNo	= $ps_vma->_db->loadResult();

// add navigation parameters to the results query

$limitstart = $resultsNo <= $limitstart ? 0 : $limitstart;

$query	   .= ($unique ? " GROUP BY clicks.`RemoteAddress`" : NULL) . " ORDER BY clicks.`ClickID` DESC LIMIT " . $limitstart . ", " . $limit;

// create the page navigation

$pageNav 	= new vmPageNav($resultsNo, $limitstart, $limit);

// create the list object with page navigation

$listObj 	= new listFactory($pageNav);

// prepare the table columns

$columns 	= array("#" 																					=> 	"style=\"width: 20px;\"", 

					"<input type=\"checkbox\" name=\"toggle\" onclick=\"checkAll(" . $resultsNo . ")\" />"	=> 	"style=\"width: 20px;\"",
				 
					JText::_("IP_ADDRESS")																	=> 	"",
					
					JText::_("REFERRING_URL")																=>	"style=\"width: " . ($affiliateID ? "500" : "400") . "px;\"");

if (!$affiliateID) {
	
	$columns[		JText::_("AFFILIATE_ID")]																= 	"";
	
}

$columns[			JText::_("TIME")]																		=	"";
					
$columns[			JText::_("DATE")]			 															=	"";

// run the results query

$ps_vma->_db->setQuery($query);

$results	= $ps_vma->_db->loadObjectList();

?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminTrafficIcon">
    
        <h1 class="adminPanelTitle"><?php echo $affiliateName ? JText::_("TRAFFIC_FOR") . " " . $affiliateName : JText::_("TRAFFIC"); ?></h1>
        
    </div>
    
    <br />
    
    <?php if (!empty($keyword) || $resultsNo > 0) { ?>
    
    <div class="affiliateSearchBox">
    
	<?php    
    
        // write the search field
    
        $listObj->writeSearchHeader(NULL, NULL, "vma", "traffic");
        
    ?>
        
    </div>
    
    <?php } ?>
    
    <input type="hidden" name="paid" 			value="<?php echo $paid; ?>" 		/>
    
    <input type="hidden" name="unique" 			value="<?php echo $unique; ?>" 		/>
    
    <input type="hidden" name="affiliate_id" 	value="<?php echo $affiliateID; ?>" />
    
    <div class="affiliateTopMenu affiliateLogsMenu">
            
        <div class="affiliateTopMenuLink">
        
            <span class="affiliateFilterMenu">
            
            	<span>
				
					<?php
                
						if (!$paid) {
						
					?>
                    
                			<a href="<?php echo $link . "page=vma.traffic&amp;paid=1&amp;unique=" . $unique . "&amp;affiliate_id=" . $affiliateID; ?>">
						
                    <?php
					
						}
						
					?>
                    
					<?php 
					
						echo JText::_("OVERALL"); 
						
					?>
                    
                    <?php
                
						if (!$paid) {
						
					?>
                    
                    		</a>
                            
                    <?php
					
						}
						
					?>
                
                </span>
                
                <span>/</span>
                
                <span>
                
                	<?php
                
						if ($paid) {
						
					?>
                    
                			<a href="<?php echo $link . "page=vma.traffic&amp;paid=0&amp;unique=" . $unique . "&amp;affiliate_id=" . $affiliateID; ?>">
						
                    <?php
					
						}
						
					?>
                    
					<?php 
					
						echo JText::_("CURRENT"); 
						
					?>
                    
                    <?php
                
						if ($paid) {
						
					?>
                    
                    		</a>
                            
                    <?php
					
						}
						
					?>
                
                </span>
                
            </span>
            
            <span class="affiliateFilterMenu">|</span>
            
            <span class="affiliateFilterMenu">
            	
                <span>
                
                	<?php
                
						if ($unique) {
						
					?>
                    
                			<a href="<?php echo $link . "page=vma.traffic&amp;unique=0&amp;paid=" . $paid . "&amp;affiliate_id=" . $affiliateID; ?>">
						
                    <?php
					
						}
						
					?>
                    
					<?php 
					
						echo JText::_("CLICKS"); 
						
					?>
                    
                    <?php
                
						if ($unique) {
						
					?>
                    
                    		</a>
                            
                    <?php
					
						}
						
					?>
                
                </span>
                
                <span>/</span>
                
                <span>
                
                	<?php
                
						if (!$unique) {
						
					?>
                    
                			<a href="<?php echo $link . "page=vma.traffic&amp;unique=1&amp;paid=" . $paid . "&amp;affiliate_id=" . $affiliateID; ?>">
						
                    <?php
					
						}
						
					?>
                    
					<?php 
					
						echo JText::_("UNIQUE_CLICKS"); 
						
					?>
                    
                    <?php
                
						if (!$unique) {
						
					?>
                    
                    		</a>
                            
                    <?php
					
						}
						
					?>
                
                </span>
                
            </span>
            
            <?php
			
				if ($affiliateID) {
					
			?>
            
                    <span class="affiliateFilterMenu">|</span>
                    
                    <span class="affiliateFilterMenu">
                    
                        <span>
                        
                            <a href="<?php echo $link . "page=vma.traffic&amp;unique=" . $unique . "&amp;paid=" . $paid; ?>"><?php echo ucwords(JText::_("ALL")); ?></a>
                            
                        </span>
                        
                        <span>/</span>
                        
                        <span>
						
							<?php
                        
								echo $affiliateName;
							
							?>
                            
						</span>
                        
                    </span>
                    
			<?php
			
				}
				
			?>
            
        </div>
        
    </div>
        
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
			
			// process data
			
			$date = getdate($result->UnixTime);
			
			$result->RefURL = str_replace("&amp;",	"&",		$result->RefURL);
			
			$result->RefURL = str_replace("&",		"&amp;",	$result->RefURL);
			
            // start a new row
            
            $listObj->newRow();
            
			// add row number
			
            $listObj->addCell($pageNav->rowNumber($i),								"style=\"width: 20px;\"");
            
			// add checkbox
			
            $listObj->addCell(vmCommonHTML::idBox($i, $i, false, "ClickID"),		"style=\"width: 20px;\"");
            
			// add ip address
			
			$listObj->addCell($result->RemoteAddress);
			
			// add referring url
			
			$listObj->addCell($result->RefURL ? $result->RefURL : JText::_("NONE"),	"style=\"width: " . ($affiliateID ? "500" : "400") . "px;\"");
			
			// add affiliate id (and name)
						
			if (!$affiliateID) {

				$listObj->addCell($result->AffiliateID . 
				
								 ($result->name ? 
								  
								 " (<a href=\"" . $link . "page=vma.traffic&amp;affiliate_id=" . $result->AffiliateID . "&amp;paid=" . $paid . "&amp;unique=" . $unique . "\">" . 
								  
								 $result->name 	. "</a>)" : 
								  
								 NULL)
								  
								 );
			
			}
			
			// add time
			
			$listObj->addCell($date["hours"]	. ":" . $date["minutes"]	. ":" . $date["seconds"]);
			
			// add date
			
			$listObj->addCell($date["year"]		. "-" . $date["mon"]		. "-" . $date["mday"]);

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