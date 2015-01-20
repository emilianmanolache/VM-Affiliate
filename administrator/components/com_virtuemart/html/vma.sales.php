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

$confirmed		= &JRequest::getVar("confirmed",	0);

$affiliateID	= &JRequest::getVar("affiliate_id",	0);

$withTax		= $ps_vma->getVATSetting();

$affiliateName	= NULL;

$condition		= "1 = 1";

$link			= $ps_vma->getAdminLink();

require_once (CLASSPATH 					. 		"pageNavigation.class.php");

require_once (CLASSPATH 					. 		"htmlTools.class.php");

// define statuses

$pendingStatuses		= $ps_vma->_pendingStatuses;

$unconfirmedStatuses 	= $ps_vma->_cancelledStatuses;
				
$confirmedStatuses		= $ps_vma->_confirmedStatuses;

// get affiliate's name (if this is a specific affiliate's sales page)

if ($affiliateID) {
	
	$query				= "SELECT CONCAT(`fname`, ' ', `lname`) AS name FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
	
	$ps_vma->_db->setQuery($query);
	
	$affiliateName		= $ps_vma->_db->loadResult();
	
}

// prepare the queries

$condition	   .= !$paid 		? " AND orders.`paid` 			= '0' " 								: NULL;

$condition	   .= $affiliateID 	? " AND orders.`affiliate_id` 	= '" . $affiliateID . "' "				: NULL;

$condition	   .= $confirmed	? " AND (" . $ps_vma->buildStatusesCondition("confirmed", "OR", "=", "order_status", NULL, "orders") . ") " : NULL;

$join			= "LEFT JOIN #__vm_affiliate affiliates 		ON orders.`affiliate_id` 	= affiliates.`affiliate_id` " . 

				  "LEFT JOIN #__vm_orders vmorders 				ON vmorders.`order_id` 		= orders.`order_id`";

$query			= "SELECT orders.*, CONCAT(affiliates.`fname`, ' ', affiliates.`lname`) AS name, vmorders.`order_subtotal` - vmorders.`coupon_discount` " . 

				  "AS order_subtotal, vmorders.`order_total` FROM #__vm_affiliate_orders orders " 							. $join . " WHERE " . $condition;

$navQuery		= "SELECT COUNT(*) FROM #__vm_affiliate_orders orders " 													. $join . " WHERE " . $condition;

// add the search query if a keyword exists

if (!empty($keyword)) {
	
	// prepare the search query
	
	$searchIn 	= array("affiliate_id", "order_id", "order_status", "date");
	
	$filter		= " AND " . $ps_vma->prepareSearch($searchIn, $keyword, "orders");
	
	// add the search query to the actual queries
	
	$query	   .= $filter;
	
	$navQuery  .= $filter;
			  
}

// run the navigation query

$ps_vma->_db->setQuery($navQuery);

$resultsNo	= $ps_vma->_db->loadResult();

// add navigation parameters to the results query

$limitstart = $resultsNo <= $limitstart ? 0 : $limitstart;

$query	   .= " ORDER BY orders.`order_id` DESC LIMIT " . $limitstart . ", " . $limit;

// create the page navigation

$pageNav 	= new vmPageNav($resultsNo, $limitstart, $limit);

// create the list object with page navigation

$listObj 	= new listFactory($pageNav);

// prepare the table columns

$columns 	= array("#" 																					=> 	"style=\"width: 20px;\"", 

					"<input type=\"checkbox\" name=\"toggle\" onclick=\"checkAll(" . $resultsNo . ")\" />"	=> 	"style=\"width: 20px;\"",
				 
					JText::_("ORDER_ID")																	=> 	"",
					
					JText::_("ORDER_SUBTOTAL")																=>	"",
					
					JText::_("ORDER_TOTAL")																	=>	"");

if (!$affiliateID) {
	
	$columns[		JText::_("AFFILIATE_ID")]																= 	"";
	
}

if (!$confirmed) {
	
	$columns[		JText::_("CONFIRMED")]																	=	"";

}

if ($paid) {
	
	$columns[		JText::_("PAID")]																		=	"";

}

$columns[			JText::_("DATE")]			 															=	"";

// run the results query

$ps_vma->_db->setQuery($query);

$results	= $ps_vma->_db->loadObjectList();

?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminSalesIcon">
    
        <h1 class="adminPanelTitle"><?php echo $affiliateName ? JText::_("SALES_FOR") . " " . $affiliateName : JText::_("SALES"); ?></h1>
        
    </div>
    
    <br />
    
    <?php if (!empty($keyword) || $resultsNo > 0) { ?>
    
    <div class="affiliateSearchBox">
    
	<?php    
    
        // write the search field
    
        $listObj->writeSearchHeader(NULL, NULL, "vma", "sales");
        
    ?>
        
    </div>
    
    <?php } ?>
    
    <input type="hidden" name="paid" 			value="<?php echo $paid; ?>" 		/>
    
    <input type="hidden" name="confirmed" 		value="<?php echo $confirmed; ?>" 	/>
    
    <input type="hidden" name="affiliate_id" 	value="<?php echo $affiliateID; ?>" />
    
    <div class="affiliateTopMenu affiliateLogsMenu">
            
        <div class="affiliateTopMenuLink">
        
            <span class="affiliateFilterMenu">
            
            	<span>
				
					<?php
                
						if (!$paid) {
						
					?>
                    
                			<a href="<?php echo $link . "page=vma.sales&amp;paid=1&amp;confirmed=" . $confirmed . "&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                    
                			<a href="<?php echo $link . "page=vma.sales&amp;paid=0&amp;confirmed=" . $confirmed . "&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                
						if ($confirmed) {
						
					?>
                    
                			<a href="<?php echo $link . "page=vma.sales&amp;confirmed=0&amp;paid=" . $paid . "&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                    
                			<a href="<?php echo $link . "page=vma.sales&amp;confirmed=1&amp;paid=" . $paid . "&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                        
                            <a href="<?php echo $link . "page=vma.sales&amp;confirmed=" . $confirmed . "&amp;paid=" . $paid; ?>"><?php echo ucwords(JText::_("ALL")); ?></a>
                            
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
				
				$confirmedStatus = in_array($result->order_status, $confirmedStatuses) 	? 	"yes" 			: (
				
								   in_array($result->order_status, $pendingStatuses)	? 	"pending"		: 
								 
								 												 			"no");
																							
				$confirmedText	 = in_array($result->order_status, $confirmedStatuses) 	? 	JText::_("JYES")	: JText::_("JNO");
																							
				$confirmedIcon	 = "<img src=\"" . $ps_vma->_website 			. "components/com_affiliate/views/panel/tmpl/images/status_" . $confirmedStatus . ".png" . "\" 
				
										 alt=\"" . $confirmedText 	. "\" />";

			}
			
			if ($paid) {
				
				$paidStatus		 = $result->paid ? "yes" : "pending";
																							
				$paidText		 = $result->paid ? JText::_("JYES")	: JText::_("JNO");
																							
				$paidIcon		 = "<img src=\"" . $ps_vma->_website 			. "components/com_affiliate/views/panel/tmpl/images/status_" . $paidStatus . ".png" . "\" 
				
										 alt=\"" . $paidText	 	. "\" />";
										 
			}
			
            // start a new row
            
            $listObj->newRow();
            
			// add row number
			
            $listObj->addCell($pageNav->rowNumber($i),								"style=\"width: 20px;\"");
            
			// add checkbox
			
            $listObj->addCell(vmCommonHTML::idBox($i, $i, false, "aff_order_id"),	"style=\"width: 20px;\"");
            
			// add order id
			
			$listObj->addCell("<a href=\"" . $link . "page=order.order_print&amp;limitstart=0&amp;keyword=&amp;option=com_virtuemart&amp;order_id=" . $result->order_id . "\">" . 
			
							  $result->order_id . 
							  
							  "</a>");
			
			// add order subtotal
			
			$listObj->addCell($ps_vma->formatAmount($result->order_subtotal));
			
			// add order total
			
			$listObj->addCell($ps_vma->formatAmount($result->order_total));
			
			// add affiliate id (and name)
			
			if (!$affiliateID) {

				$listObj->addCell($result->affiliate_id . 
				
								 ($result->name ? 
								  
								 " (<a href=\"" . $link . "page=vma.sales&amp;affiliate_id=" . $result->affiliate_id . "&amp;paid=" . 
								 
								 $paid . "&amp;confirmed=" . $confirmed . "\">" . 
								  
								 $result->name 	. "</a>)" : 
								  
								 NULL)
								  
								 );
			
			}
			
			// add confirmed status
			
			if (!$confirmed) {
				
				$listObj->addCell($confirmedIcon);
			
			}
			
			// add paid status
			
			if ($paid) {
				
				$listObj->addCell($paidIcon);
			
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