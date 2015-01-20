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

require_once (CLASSPATH 				. "pageNavigation.class.php");

require_once (CLASSPATH 				. "htmlTools.class.php");

// prepare the queries

$condition	= "`commissions` >= '" . $vmaSettings->pay_balance . "' AND `method` != '' AND `method` != 'N/A' AND `blocked` != '1'";

$query		= "SELECT * FROM #__vm_affiliate WHERE " 			. $condition;

$navQuery	= "SELECT COUNT(*) FROM #__vm_affiliate WHERE " 	. $condition;

// add the search query if a keyword exists

if (!empty($keyword)) {
	
	// prepare the search query
	
	$searchIn 	= array("fname", "lname", "username", "mail", "website", "street", "city", "state", "country", "zipcode", "phoneno", "taxssn");
	
	$filter		= " AND " . $ps_vma->prepareSearch($searchIn, $keyword);
	
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

// get payment method names

$methodsNames = $ps_vma->getPaymentMethodsNames();

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
					
					JText::_("PAYMENT_METHOD")																=> "",
					
					JText::_("BALANCE")		 																=> "",
					
					JText::_("PAY")				 															=> "");

// run the results query

$ps_vma->_db->setQuery($query);

$results	= $ps_vma->_db->loadObjectList();

?>

<div class="affiliateAdminPage">

    <div class="adminPanelTitleIcon" id="adminPayIcon">
    
        <h1 class="adminPanelTitle"><?php echo JText::_("PAY_AFFILIATES"); ?></h1>
        
    </div>
    
    <?php if (!empty($keyword) || $resultsNo > 0) { ?>
    
    <div class="affiliateSearchBox">
    
	<?php    
    
        // write the search field
    
        $listObj->writeSearchHeader(NULL, NULL, "vma", "pay_affiliates");
        
    ?>
        
    </div>
    
    <?php } ?>

    <div class="affiliateTopMenu">
        
        <div class="affiliateTopMenuLink">
        
            <span style="background: url(<?php echo $ps_vma->_website; ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" class="affiliateTopMenuLinkItem">
            
                <a href="<?php echo $link . "page=vma.affiliate_list"; ?>"><?php echo JText::_("MANAGE_AFFILIATES"); ?></a>
                
            </span>
            
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
            
            $confirmedSales = $ps_vma->getSales($result->affiliate_id, true, true);
            
            $totalSales		= $confirmedSales + $ps_vma->getSales($result->affiliate_id, true, false);
            
			$clicks			= $ps_vma->getClicks($result->affiliate_id, true, false);
			
			$uniqueClicks	= $ps_vma->getClicks($result->affiliate_id, true, true);
			
            // start a new row
            
            $listObj->newRow();
            
			// add row number
			
            $listObj->addCell($pageNav->rowNumber($i),								"style=\"width: 20px;\"");
            
			// add checkbox
			
            $listObj->addCell(vmCommonHTML::idBox($i, $i, false, "affiliate_id"),	"style=\"width: 20px;\"");
            
			// add affiliate id
			
            $listObj->addCell($result->affiliate_id);
            
			// add name
			
            $listObj->addCell($result->fname 	. " " . $result->lname);
            
			// add clicks (attaching link which leads to affiliate's traffic page)
			
            $listObj->addCell($clicks);
            
			// add unique clicks (attaching link which leads to affiliate's unique traffic page)
			
            $listObj->addCell($uniqueClicks);
            
			// add sales (attaching link which leads to affiliate's sales page)
			
            $listObj->addCell($confirmedSales 	. "/" . $totalSales);
            
			// add payment method
			
			$listObj->addCell($methodsNames[$result->method]["name"]);
			
			// add balance
			
            $listObj->addCell($ps_vma->formatAmount($result->commissions), "style=\"text-align: right;\"");
			
			// add pay button
			
            $listObj->addCell("<a href=\"" . ($result->method == "1" ? $ps_vma->getPayPalLink($result) : $link . "page=vma.pay_affiliate&amp;affiliate_id=" . 
			
							  $result->affiliate_id) . "\" title=\"" . JText::_("PAY_AFFILIATE") . " (" . $methodsNames[$result->method]["name"] . ")" 		. 
							  
							  "\">" . "<img src=\"" . $ps_vma->_website . "components/com_affiliate/assets/images/pay_" . $methodsNames[$result->method]["image"]		. 
							  
							  ".png\" /></a>", "style=\"text-align: center;\"");
            
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