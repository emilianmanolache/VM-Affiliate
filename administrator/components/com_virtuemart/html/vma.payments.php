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

$confirmed		= &JRequest::getVar("confirmed",	0);

$affiliateID	= &JRequest::getVar("affiliate_id",	0);

$affiliateName	= NULL;

$condition		= "1 = 1";

$link			= $ps_vma->getAdminLink();

require_once (CLASSPATH 					. "pageNavigation.class.php");

require_once (CLASSPATH 					. "htmlTools.class.php");


// get affiliate's name (if this is a specific affiliate's sales page)

if ($affiliateID) {
	
	$query				= "SELECT CONCAT(`fname`, ' ', `lname`) AS name FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
	
	$ps_vma->_db->setQuery($query);
	
	$affiliateName		= $ps_vma->_db->loadResult();
	
}

// prepare the queries

$condition	   .= $affiliateID 	? " AND payments.`affiliate_id` 	= '" . $affiliateID . "' "				: NULL;

$condition	   .= $confirmed	? " AND payments.`status`			= 'C' "									: NULL;

$join			= "LEFT JOIN #__vm_affiliate affiliates ON payments.`affiliate_id` 	= affiliates.`affiliate_id`";

$query			= "SELECT payments.*, CONCAT(affiliates.`fname`, ' ', affiliates.`lname`) AS name " . 

				  "FROM #__vm_affiliate_payments payments " 										. $join . " WHERE " . $condition;

$navQuery		= "SELECT COUNT(*) FROM #__vm_affiliate_payments payments " 						. $join . " WHERE " . $condition;

// add the search query if a keyword exists

if (!empty($keyword)) {
	
	// prepare the search query
	
	$searchIn 	= array("affiliate_id", "method", "amount", "date", "status");
	
	$filter		= " AND " . $ps_vma->prepareSearch($searchIn, $keyword, "payments");
	
	// add the search query to the actual queries
	
	$query	   .= $filter;
	
	$navQuery  .= $filter;
			  
}

// run the navigation query

$ps_vma->_db->setQuery($navQuery);

$resultsNo	= $ps_vma->_db->loadResult();

// add navigation parameters to the results query

$limitstart = $resultsNo <= $limitstart ? 0 : $limitstart;

$query	   .= " ORDER BY payments.`payment_id` DESC LIMIT " . $limitstart . ", " . $limit;

// create the page navigation

$pageNav 	= new vmPageNav($resultsNo, $limitstart, $limit);

// create the list object with page navigation

$listObj 	= new listFactory($pageNav);

// prepare the table columns

$columns 	= array("#" 																					=> 	"style=\"width: 20px;\"", 

					"<input type=\"checkbox\" name=\"toggle\" onclick=\"checkAll(" . $resultsNo . ")\" />"	=> 	"style=\"width: 20px;\"",
				 
					JText::_("AMOUNT")																		=> 	"",
					
					JText::_("PAYMENT_METHOD")																=>	"");

if (!$affiliateID) {
	
	$columns[		JText::_("AFFILIATE_ID")]																= 	"";
	
}

if (!$confirmed) {
	
	$columns[		JText::_("CONFIRMED")]																	=	"";

}

$columns[			JText::_("DATE")]			 															=	"";

// run the results query

$ps_vma->_db->setQuery($query);

$results	= $ps_vma->_db->loadObjectList();

?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminPaymentsIcon">
    
        <h1 class="adminPanelTitle"><?php echo $affiliateName ? JText::_("PAYMENTS") . ": " . $affiliateName : JText::_("PAYMENTS"); ?></h1>
        
    </div>
    
    <br />
    
    <?php if (!empty($keyword) || $resultsNo > 0) { ?>
    
    <div class="affiliateSearchBox">
    
	<?php    
    
        // write the search field
    
        $listObj->writeSearchHeader(NULL, NULL, "vma", "payments");
        
    ?>
        
    </div>
    
    <?php } ?>
    
    <input type="hidden" name="confirmed" 		value="<?php echo $confirmed; ?>" 	/>
    
    <input type="hidden" name="affiliate_id" 	value="<?php echo $affiliateID; ?>" />
    
    <div class="affiliateTopMenu affiliateLogsMenu">
            
        <div class="affiliateTopMenuLink">
            
            <span class="affiliateFilterMenu">
            	
                <span>
                
                	<?php
                
						if ($confirmed) {
						
					?>
                    
                			<a href="<?php echo $link . "page=vma.payments&amp;confirmed=0&amp;affiliate_id=" . $affiliateID; ?>">
						
                    <?php
					
						}
						
					?>
                    
					<?php 
					
						echo ucwords(JText::_("ALL")); 
						
					?>
                    
                    <?php
                
						if ($confirmed) {
						
					?>
                    
                    		</a>
                            
                    <?php
					
						}
						
					?>
                
                </span>
                
                <span>/</span>
                
                <span>
                
                	<?php
                
						if (!$confirmed) {
						
					?>
                    
                			<a href="<?php echo $link . "page=vma.payments&amp;confirmed=1&amp;affiliate_id=" . $affiliateID; ?>">
						
                    <?php
					
						}
						
					?>
                    
					<?php 
					
						echo JText::_("CONFIRMED"); 
						
					?>
                    
                    <?php
                
						if (!$confirmed) {
						
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
                        
                            <a href="<?php echo $link . "page=vma.payments&amp;confirmed=" . $confirmed; ?>"><?php echo ucwords(JText::_("ALL")); ?></a>
                            
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
			
			// parse data
			
			if (!$confirmed) {
				
				$confirmedStatus = $result->status == "C" ?	"yes" 			: ($result->status == "P"	? "pending"	: "no");
																							
				$confirmedText	 = $result->status == "C" ? JText::_("JYES")	: JText::_("JNO");
																							
				$confirmedIcon	 = "<img src=\"" . $ps_vma->_website 			. "components/com_affiliate/views/panel/tmpl/images/status_" . $confirmedStatus . ".png" . "\" 
				
										 alt=\"" . $confirmedText 	. "\" />";

			}
			
            // start a new row
            
            $listObj->newRow();
            
			// add row number
			
            $listObj->addCell($pageNav->rowNumber($i),								"style=\"width: 20px;\"");
            
			// add checkbox
			
            $listObj->addCell(vmCommonHTML::idBox($i, $i, false, "payment_id"),		"style=\"width: 20px;\"");
            
			// add amount
			
			$listObj->addCell($ps_vma->formatAmount($result->amount));
			
			// add payment method
			
			$listObj->addCell($result->method ? $result->method : "N/A");

			// add affiliate id (and name)
			
			if (!$affiliateID) {

				$listObj->addCell($result->affiliate_id . 
				
								 ($result->name ? 
								  
								 " (<a href=\"" . $link . "page=vma.payments&amp;affiliate_id=" . $result->affiliate_id . "&amp;confirmed=" . $confirmed . "\">" . 
								  
								 $result->name 	. "</a>)" : 
								  
								 NULL)
								  
								 );
			
			}
			
			// add confirmed status
			
			if (!$confirmed) {
				
				$listObj->addCell($confirmedIcon);
			
			}
			
			// add date
			
			$listObj->addCell($result->date);

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