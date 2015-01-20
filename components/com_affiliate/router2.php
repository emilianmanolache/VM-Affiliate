<?php

/**
 * @package   VM Affiliate
 * @version   4.5.2.0 January 2012
 * @author    Globacide Solutions http://www.globacide.com
 * @copyright Copyright (C) 2006 - 2012 Globacide Solutions
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access

defined( '_JEXEC' ) or die( 'Direct access to this location is not allowed.' );

/**
 * Method to build the SEF route
 */

function affiliateBuildRoute(&$query) {
	
	$segments		= array();
	
	$segments[]		= "affiliates";

	if (isset($query["view"])) {
		   
		$segments[] = $query["view"];
		
		unset($query["view"]);
		
	}
	
	else {
		
		$segments[] = "login";
		
	}

	if (isset($query["task"])) {
		
		$segments[] = $query["task"];
		
		unset($query["task"]);
		
	}
	
	if (isset($query["subview"])) {
		
		$segments[] = $query["subview"];
		
		unset($query["subview"]);
		
	}
	
	if (isset($query["section"])) {
		
		$segments[] = $query["section"];
		
		unset($query["section"]);
		
	}
	
	if (isset($query["tmpl"])) {
		
		$segments[] = $query["tmpl"];
		
		unset($query["tmpl"]);
		
	}
	 
	if (isset($query["format"])) {
		
		$segments[] = $query["format"];
		
		unset($query["format"]);
		
	}
	
	if (isset($query["type"])) {
		
		$segments[] = $query["type"];
		
		unset($query["type"]);
		
	}
	
	if (isset($query["id"])) {
		
		$segments[] = $query["id"];
		
		unset($query["id"]);
		
	}
	
	if (isset($query["frontend"])) {
		
		$segments[] = $query["frontend"];
		
		unset($query["frontend"]);
		
	}
	
	if (isset($query["vmatoken"])) {
		
		$segments[] = $query["vmatoken"];
		
		unset($query["vmatoken"]);
		
	}
	
	if (isset($query["vmatokenid"])) {
		
		$segments[] = $query["vmatokenid"];
		
		unset($query["vmatokenid"]);
		
	}
	
	if (isset($query["category_id"])) {
		
		$segments[] = $query["category_id"];
		
		unset($query["category_id"]);
		
	}
	
	if (isset($query["period"])) {
		
		$segments[] = $query["period"];
		
		unset($query["period"]);
		
	}
	
	if (isset($query["confirmed"])) {
		
		$segments[] = $query["confirmed"];
		
		unset($query["confirmed"]);
		
	}
	
	if (isset($query["unique"])) {
		
		$segments[] = $query["unique"];
		
		unset($query["unique"]);
		
	}
	
	if (isset($query["paid"])) {
		
		$segments[] = $query["paid"];
		
		unset($query["paid"]);
		
	}
	
	return $segments;
	   
}

/**
 * Method to parse the SEF route
 */

function affiliateParseRoute($segments) {
	
	// initiate required variables

	$vars	= array();
	
	$app	= &JFactory::getApplication();
	
	$menu	= &$app->getMenu();
	
	$item	= &$menu->getActive();
	
	// count the segments
	
	$count	= count($segments);

	switch($segments[1]) {
		
		case "login":
		
			$vars["view"]				= "login";
			
			if ($count == 3) {
				
				$vars["task"]			= $segments[2];
				
			}
			
			break;
		
		case "register":
			
			$vars["view"]				= "register";

			break;
			
		case "lostpassword":
		
			$vars["view"]				= "lostpassword";
			
			break;
			
		case "terms":
			
			$vars["view"]				= "terms";
			
			if (count == 3) {
				
				$vars["tmpl"]			= $segments[2];
			
			}
			
			break;
			
		case "prev":
		
			$vars["view"]				= "prev";
			
			if ($count > 5) {
				
				$vars["tmpl"]			= $segments[2];
				
				$vars["format"]			= $segments[3];
				
				$vars["type"]			= $segments[4];
				
				$vars["id"]				= $segments[5];
			
			}
			
			if ($count == 7) {
					
				$vars["frontend"]		= $segments[6];
				
			}
			
			break;	
			
		case "panel":
		
			$vars["view"]				= "panel";
			
			if ($count > 2) {
				
				if (in_array($segments[2], array("changePassword", "linkTo", "paymentMethod"))) {
					
					$vars["task"]		= $segments[2];
					
				}
				
				else {
					
					$vars["subview"]	= $segments[2];
					
				}
				
			}
			
			if (isset($vars["subview"])) {
				
				if ($count == 4 && $vars["subview"] != "banners") {
					
					$vars["task"]					= $segments[3];
					
				}
				
				switch ($vars["subview"]) {
					
					case "banners":
					
						$vars["section"]			= $segments[3];
						
						if ($vars["section"] == "productads" && $count > 4) {
							
							$vars["category_id"]	= $segments[34];
							
						}
						
						break;
					
					case "statistics":
					
						if ($count > 3) {
							
							$vars["type"]			= $segments[4];
							
							$vars["period"]			= $segments[5];
							
						}
						
						break;
						
					case "sales":
					
						if ($count > 3) {
							
							$vars["confirmed"]		= $segments[4];
							
							$vars["paid"]			= $segments[5];
						
						}
						
						break;
					
					case "traffic":
					
						if ($count > 3) {
							
							$vars["unique"]			= $segments[4];
							
							$vars["paid"]			= $segments[5];
						
						}
						
						break;
						
				}
				
			}
			
			break;
		
		default:
		
			$vars["task"]					= $segments[2];
			
			if ($count > 5) {
				
				switch ($vars["task"]) {
				
					case "processUploadedBanner":
						
						$vars["format"]		= $segments[3];
						
						$vars["vmatoken"]	= $segments[4];
						
						$vars["vmatokenid"]	= $segments[5];
						
						break;
						
					case "exportSizeGroups":
					
						$vars["type"]		= $segments[3];
						
						$vars["format"]		= $segments[4];
						
						$vars["vmatoken"]	= $segments[5];
						
						$vars["vmatokenid"]	= $segments[6];
						
						break;
						
				}
				
			}
			
			break;
			
	}
	
	JRequest::setVar("view", $vars["view"], "get");
	
	return $vars;
	
}

?>