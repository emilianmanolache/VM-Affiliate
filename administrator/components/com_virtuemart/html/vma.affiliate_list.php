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

global $ps_vma, $vmaSettings;

$keyword	= &JRequest::getVar("keyword", "");

$link		= $ps_vma->getAdminLink();

require_once (CLASSPATH 							. "pageNavigation.class.php");

require_once (CLASSPATH 							. "htmlTools.class.php");

// prepare the queries

$query		= "SELECT * FROM #__vm_affiliate";

$navQuery	= "SELECT COUNT(*) FROM #__vm_affiliate";

// add the search query if a keyword exists

if (!empty($keyword)) {
	
	// prepare the search query
	
	$searchIn 	= array("fname", "lname", "username", "mail", "website", "street", "city", "state", "country", "zipcode", "phoneno", "taxssn");
	
	$filter		= " WHERE " . $ps_vma->prepareSearch($searchIn, $keyword);
	
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

// check if there are any affiliates that need to be paid

$payQuery	= "SELECT COUNT(*) FROM #__vm_affiliate WHERE `commissions` >= '" . $vmaSettings->pay_balance . "' AND `method` != '' AND `method` != 'N/A' AND `blocked` != '1'";

$ps_vma->_db->setQuery($payQuery);

$toBePaid	= $ps_vma->_db->loadResult();

// get payment method names

if ($toBePaid > 0) {

	$methodsNames = $ps_vma->getPaymentMethodsNames();
	
}

// create the page navigation

$pageNav 	= new vmPageNav($resultsNo, $limitstart, $limit);

// create the list object with page navigation

$listObj 	= new listFactory($pageNav);

// prepare the table columns

$columns 	= array("#" 																					=> "style=\"width: 20px;\"", 

					"<input type=\"checkbox\" name=\"toggle\" onclick=\"checkAll(" . $resultsNo . ")\" />"	=> "style=\"width: 20px;\"",
				 
					JText::_("AFFILIATE_ID")																=> "",
				 
					JText::_("NAME")																		=> "",
					
					JText::_("CLICKS")																		=> "",
					
					JText::_("UNIQUE_CLICKS")																=> "",
					
					JText::_("SALES") 																		=> "",
					
					JText::_("BALANCE")				 														=> "",
					
					JText::_("ENABLED")			 															=> "",
						
					JText::_("RATES")																		=> "",
					
					JText::_("EDIT") 																		=> "",
					
					JText::_("REMOVE")																		=> "");

// run the results query

$ps_vma->_db->setQuery($query);

$results	= $ps_vma->_db->loadObjectList();

?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminAffiliatesIcon">
    
        <h1 class="adminPanelTitle"><?php echo JText::_("MANAGE_AFFILIATES"); ?></h1>
        
    </div>
	
    <?php if ($toBePaid > 0) { ?>
    
    <br />
    
    <div style="text-align: center;">
    
    	<a href="<?php echo $link . "page=vma.pay_affiliates"; ?>" class="affiliatePayoutNotice">
            
			<?php 
			
			echo JText::sprintf("PAYOUT_NOTICE", $toBePaid, $ps_vma->formatAmount($vmaSettings->pay_balance), 
		
								$vmaSettings->pay_day, (date("j") > $vmaSettings->pay_day ? JText::_("NEXT") : JText::_("THIS"))); 
								
			?>
            
		</a>
        
    </div>
    
    <?php } ?>
    
    <?php if (!empty($keyword) || $resultsNo > 0) { ?>

    <div class="affiliateSearchBox">
    
	<?php    
    
        // write the search field
    
        $listObj->writeSearchHeader(NULL, NULL, "vma", "affiliate_list");
        
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
            
            // process data
            
            $confirmedSales = $ps_vma->getSales($result->affiliate_id, true, true);
            
            $totalSales		= $confirmedSales + $ps_vma->getSales($result->affiliate_id, true, false);
            
			$clicks			= $ps_vma->getClicks($result->affiliate_id, true, false);
			
			$uniqueClicks	= $ps_vma->getClicks($result->affiliate_id, true, true);
			
			$mustBePaid		= $result->commissions >= $vmaSettings->pay_balance && $result->method && $result->method != "N/A" ? true : false;
			
            // start a new row
            
            $listObj->newRow();
            
			// add row number
			
            $listObj->addCell($pageNav->rowNumber($i), 								"style=\"width: 20px;\"");
            
			// add checkbox
			
            $listObj->addCell(vmCommonHTML::idBox($i, $i, false, "affiliate_id"),	"style=\"width: 20px;\"");
            
			// add affiliate id
			
            $listObj->addCell($result->affiliate_id);
            
			// add name (attaching link which leads to affiliate's info page)
			
            $listObj->addCell("<a href=\"" 													. $link 			. "page=vma.affiliate_details&amp;affiliate_id=" 	. 
			
							  $result->affiliate_id . "\">" 								. $result->fname 	. " " . $result->lname . 
							  
							  "</a>");
            
			// add clicks (attaching link which leads to affiliate's traffic page)
			
            $listObj->addCell(($clicks > 0 ? "<a href=\"" 									. $link 			. "page=vma.traffic&amp;affiliate_id=" 				. 
			
							  $result->affiliate_id . "&amp;paid=0&amp;unique=0\">" : NULL) . $clicks 			. ($clicks > 0 ? "</a>" : NULL));
            
			// add unique clicks (attaching link which leads to affiliate's unique traffic page)
			
            $listObj->addCell(($uniqueClicks > 0 ? "<a href=\"" 							. $link 			. "page=vma.traffic&amp;affiliate_id=" 				. 
			
							  $result->affiliate_id . "&amp;paid=0&amp;unique=1\">" : NULL) . $uniqueClicks 	. ($uniqueClicks > 0 ? "</a>" : NULL));
            
			// add sales (attaching link which leads to affiliate's sales page)
			
            $listObj->addCell(($confirmedSales > 0 || $totalSales > 0 ? "<a href=\"" 		. $link 			. "page=vma.sales&amp;affiliate_id=" 				. 
			
							  $result->affiliate_id . "&amp;paid=0\">" : NULL) 				. $confirmedSales 	. "/" . $totalSales . 
							  
							  ($confirmedSales > 0 || $totalSales > 0 ? "</a>" : NULL));
            
			// add balance
			
            $listObj->addCell(($mustBePaid ? "<a style=\"float: right;\" href=\"" 	. ($result->method == "1" ? $ps_vma->getPayPalLink($result) : $link 						. 
							  
							  "page=vma.pay_affiliate&amp;affiliate_id=" 	. $result->affiliate_id) 	. "\" title=\"" 	. JText::_("PAY_AFFILIATE") . " (" 					. 
							  
							  $methodsNames[$result->method]["name"] . ")" 	. "\">" 	. "<img src=\""		. $ps_vma->_website . "components/com_affiliate/assets/images/pay_"	. 
							  
							  $methodsNames[$result->method]["image"] . ".png\" " 		. "alt=\"Pay\" /></a>" : NULL) . "<span style=\"vertical-align: middle; float: right; " . 
							  
							  ($mustBePaid ? "margin-top: 2px; margin-right: 6px;" : NULL) . "\">" . $ps_vma->formatAmount($result->commissions) . "</span>", 
							  
							  "style=\"text-align: right;" . ($mustBePaid ? " font-weight: bold;" : NULL) . "\"");
            
			// add affiliate enable/disable button
			
            $listObj->addCell($ps_vma->itemToggleButton($result->affiliate_id, "affiliate", $result->blocked, $keyword, $limitstart), 	"style=\"text-align: center;\"");
            
			// add affiliate commission rates button
			
            $listObj->addCell($ps_vma->affiliateRatesButton($result->affiliate_id), 													"style=\"text-align: center;\"");
            
			// add affiliate edit button
			
            $listObj->addCell($ps_vma->affiliateEditButton($result->affiliate_id), 														"style=\"text-align: center;\"");
            
			// add affiliate delete button
			
            $listObj->addCell($ps_vma->itemDeleteButton($result->affiliate_id, "affiliate", $keyword, $limitstart), 					"style=\"text-align: center;\"");
            
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
		
		echo $ps_vma->displayNoResultsFound("vma.affiliate_list");
		
	}
	
	?>
    
</div>