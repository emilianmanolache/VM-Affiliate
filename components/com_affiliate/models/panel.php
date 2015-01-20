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

// import the joomla component model application

jimport( 'joomla.application.component.model' );
 
/**
 * Model file for VM Affiliate's Affiliate Panel component
 */
 
class AffiliateModelPanel extends JModel {
	
	/**
	 * Items total
	 */
	 
	var $_total 			= NULL;
 
	/**
	 * Pagination object
	 */
	 
	var $_pagination 		= NULL;
	
	/**
	 * Current item
	 */
	 
	var $_index 			= 1;
	
	/**
	 * Current subview
	 */
	 
	var $_subview 			= NULL;
	
	/**
	 * Current section (if applicable)
	 */
	 
	var $_section 			= NULL;
	
	/**
	 * Active ads menus
	 */
	 
	var $_activeAdsMenus	= NULL;
	
	/**
	 * Contructor method for the Panel Model
	 */
	 
	function __construct() {
		
		global $mainframe, $option;
		
		parent::__construct();
 
        // get pagination request variables
		
        $limit 				= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		
        $limitstart 		= JRequest::getVar('limitstart', 0, '', 'int');
 		
		// get subview and section
		
		$this->_subview 	= JRequest::getVar("subview", "");
		
		$this->_section 	= JRequest::getVar("section", "banners");
		
        // in case limit has been changed, adjust it
		
        $limitstart 		= ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
        $this->setState('limit', 		$this->_section == "productads" && ($limit > 40 || !$limit) ? 20 : $limit);
		
        $this->setState('limitstart',	$limitstart);
		
	}
	
	/**
	 * Method to build the select query for a specific subview or section of the panel
	 */
	 
	function _buildQuery($menu = NULL) {
		
		global $ps_vma;
		
		// initiate variables
		
		$query		= "";
		
		$database	= &JFactory::getDBO();
		
		$session	= &JFactory::getSession();
		
		$affiliate	= $session->get("affiliate");
		
		$withTax	= $ps_vma->getVATSetting();
		
		// build the corresponding query
		
		switch ($this->_subview) {
			
			case 'banners': 
				
				$queryFor		= $menu ? $menu : $this->_section;
				
				switch ($queryFor) {
					
					case 'banners':
					
						$query 	= !$menu ? 
						
								  "SELECT banners.*, sizegroups.`name` AS sizegroup FROM #__vm_affiliate_banners banners LEFT JOIN #__vm_affiliate_size_groups sizegroups ON " . 
						
								  "banners.`banner_width` = sizegroups.`width` AND banners.`banner_height` = sizegroups.`height` WHERE banners.`published` = '1'" : 
								  
								  "SELECT COUNT(`banner_id`) FROM #__vm_affiliate_banners WHERE `published` = '1'";
						
						break;
						
					case 'textads':
						
						$query 	= !$menu ? 
						
								  "SELECT textads.*, sizegroups.`name` AS sizegroup FROM #__vm_affiliate_textads textads LEFT JOIN #__vm_affiliate_size_groups sizegroups ON " . 
						
								  "textads.`width` = sizegroups.`width` AND textads.`height` = sizegroups.`height` WHERE textads.`published` = '1'" : 
								  
								  "SELECT COUNT(`textad_id`) FROM #__vm_affiliate_textads WHERE `published` = '1'";
						
						break;
						
					case 'productads':
						
						// build the product ads query
						
						$query	= !$menu ? 
						
								  "SELECT product.`product_id`, product.`product_name`, product.`product_thumb_image`, price.`product_price` AS product_price, " . 
								  
								  "tax.`tax_rate` AS product_tax, discount.`amount` AS discount_amount, MIN(price.`price_quantity_start`), " . 
						
								  "discount.`is_percent` AS discount_percentage, discount.`start_date` AS discount_start, discount.`end_date` AS discount_end " . 
								  
								  "FROM #__vm_product product LEFT JOIN #__vm_product_price price ON product.`product_id` = price.`product_id` " . 
								  
								  "LEFT JOIN #__vm_tax_rate tax ON product.`product_tax_id` = tax.`tax_rate_id` LEFT JOIN #__vm_product_discount discount " . 
								  
								  "ON product.`product_discount_id` = discount.`discount_id` LEFT JOIN #__vm_product_category_xref cat ON " . 
								  
								  "product.`product_id` = cat.`product_id` WHERE product.`product_parent_id` = '0' AND " . 
								  
								  "product.`product_publish` = 'Y' AND product.`product_thumb_image` != '' AND price.`shopper_group_id` = '5' " : 
								  
								  "SELECT COUNT(DISTINCT product.`product_id`) FROM #__vm_product product " . 
								  
								  "LEFT JOIN #__vm_product_category_xref cat ON product.`product_id` = cat.`product_id` " . 
								  
								  "WHERE product.`product_parent_id` = '0' AND product.`product_publish` = 'Y' AND product.`product_thumb_image` != '' ";
						
						// don't include unpublished products
						
						$unpublishedProducts = $ps_vma->getUnpublishedProducts();
						
						if (is_array($unpublishedProducts)) {
							
							foreach ($unpublishedProducts as $unpublishedProduct) {
								
								$query .= "AND product.`product_id` != '" . $unpublishedProduct["product_id"] . "' ";
								
							}
							
						}
						
						// check if only products from a specific category should be displayed
						
						$categoryID = JRequest::getVar("category_id", "");
						
						$query	   .= $categoryID ? "AND cat.`category_id` = '" . $categoryID . "' " : NULL;
						
						// group by product id, and order by product name
						
						$query	   .= !$menu ? "GROUP BY product.`product_id` ORDER BY product.`product_name` ASC" : NULL;
						
						break;
						
					case 'categoryads':
						
						// build the category ads query
						
						$query	= !$menu ? 
						
								  "SELECT DISTINCT * FROM #__vm_category WHERE `category_publish` = 'Y' AND `category_thumb_image` != '' " : 
								  
								  "SELECT COUNT(DISTINCT `category_id`) FROM #__vm_category WHERE `category_publish` = 'Y' AND `category_thumb_image` != '' ";
						
						// don't include unpublished categories
						
						$unpublishedCategories = $ps_vma->getUnpublishedCategories();
						
						if (is_array($unpublishedCategories)) {
							
							foreach ($unpublishedCategories as $unpublishedCategory) {
								
								$query .= "AND `category_id` != '" . $unpublishedCategory["category_id"] . "' ";
								
							}
							
						}
						
						break;
						
					case 'productadscategories':
						
						$query 	= !$menu ? 
						
								  "SELECT DISTINCT c.`category_id`, c.`category_name`, COUNT(DISTINCT p.`product_id`) AS products FROM #__vm_category c, #__vm_product p, " . 
						
								  "#__vm_product_category_xref cp WHERE cp.`category_id` = c.`category_id` AND cp.`product_id` = p.`product_id` AND " . 
						
								  "p.`product_publish` = 'Y' AND p.`product_thumb_image` != '' " : 
								  
								  "SELECT COUNT(DISTINCT c.`category_id`) FROM #__vm_category c, #__vm_product p, #__vm_product_category_xref cp " . 
								  
								  "WHERE cp.`category_id` = c.`category_id` AND cp.`product_id` = p.`product_id` AND p.`product_publish` = 'Y' AND p.`product_thumb_image` != '' ";
						
						$unpublishedProducts 	= $ps_vma->getUnpublishedProducts();
						
						if (is_array($unpublishedProducts)) {
						
							foreach ($unpublishedProducts as $unpublishedProduct) {
								
								$query .= "AND p.`product_id` != '" . $unpublishedProduct["product_id"] . "' ";
								
							}
							
						}
						
						$unpublishedCategories 	= $ps_vma->getUnpublishedCategories();
						
						if (is_array($unpublishedCategories)) {
						
							foreach ($unpublishedCategories as $unpublishedCategory) {
								
								$query .= "AND c.`category_id` != '" . $unpublishedCategory["category_id"] . "' ";
								
							}
							
						}
						
						$query .= !$menu ? "GROUP BY c.`category_id` " : NULL;
						
						break;
						
				}
				
				break;
				
				case 'traffic':
					
					$paid		= &JRequest::getVar("paid",			1);

					$unique		= &JRequest::getVar("unique",		0);
		
					$query 		= !$menu ? 
					
								  "SELECT " . ($unique ? "DISTINCT " : NULL) . "clicks.* FROM #__vm_affiliate_clicks clicks WHERE `AffiliateID` = '" . $affiliate->affiliate_id . 
					
								  "' " . (!$paid ? "AND clicks.`paid` = '0' " : NULL) . ($unique ? "GROUP BY clicks.`RemoteAddress` " : NULL) . "ORDER BY clicks.`UnixTime` DESC" : 
								  
								  "SELECT COUNT(" . ($unique ? "DISTINCT " : NULL) . "ClickID) FROM #__vm_affiliate_clicks WHERE `AffiliateID` = '" . $affiliate->affiliate_id . 
					
								  "' " . (!$paid ? "AND `paid` = '0' " : NULL) . ($unique ? "GROUP BY `RemoteAddress` " : NULL);
					
					break;
					
				case 'sales':
					
					$paid		= &JRequest::getVar("paid",			1);

					$confirmed	= &JRequest::getVar("confirmed",	0);

					$query		= !$menu ? 
					
								  "SELECT ao.*, o.`cdate` AS order_date, o.`order_subtotal` - o.`coupon_discount` " 		. 
								  
								  "AS subtotal FROM #__vm_affiliate_orders ao "	. 
					
								  "LEFT JOIN #__vm_orders o ON ao.`order_id` = o.`order_id` WHERE ao.`affiliate_id` = '"	. $affiliate->affiliate_id . "' " . 
								  
								  (!$paid 		? "AND ao.`paid` = '0' " : NULL) . 
								  
								  ($confirmed 	? "AND (" . $ps_vma->buildStatusesCondition("confirmed", "OR", "=", "order_status", NULL, "ao") . ") " : NULL) .

								  "ORDER BY order_date DESC" : 
								  
								  "SELECT COUNT(DISTINCT ao.`order_id`) FROM #__vm_affiliate_orders ao WHERE ao.`affiliate_id` = '" . $affiliate->affiliate_id . "' " . 
								  
								  (!$paid		? "AND ao.`paid` = '0' " : NULL) . 
								  
								  ($confirmed	? "AND (" . $ps_vma->buildStatusesCondition("confirmed", "OR", "=", "order_status", NULL, "ao") . ") " : NULL);
					
					break;
					
				case 'payments':
				
					$query		= !$menu ? 
					
								  "SELECT * FROM #__vm_affiliate_payments WHERE `affiliate_id` = '" . $affiliate->affiliate_id . "' ORDER BY date DESC" : 
								  
								  "SELECT COUNT(DISTINCT `payment_id`) FROM #__vm_affiliate_payments WHERE `affiliate_id` = '" . $affiliate->affiliate_id . "'";
								  
					break;
					
		}
		
		// return the query

		return $query;
		
	}
	
	/**
	 * Method to load required data
	 */
	 
	function getData() {
		
        // if data hasn't already been obtained, load it
		
        if (empty($this->_data)) {
			
            $query 			= $this->_buildQuery();
			
			if (!$query) {
				
				return false;
				
			}
			
            $this->_data 	= $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 
			
        }
		
        return $this->_data;
		
	}
	
	/**
	 * Method to get the total number of rows
	 */
	 
	function getTotal() {
		
        // load the content if it doesn't already exist
		
        if (empty($this->_total)) {
			
			$database		= &JFactory::getDBO();
				
            $query 			= $this->_buildQuery($this->_section);
			
			$database->setQuery($query);
			
			$database->query();
			
            $this->_total 	= $database->loadResult();

        }
		
        return $this->_total;
		
	}

	/**
	 * Method to retrieve the pagination object
	 */
	 
	function getPagination() {
		
		// define subviews which require pagination
		
		$lists		= array("banners", "traffic", "sales", "payments");
			
		// load the content if required
				
		if (in_array($this->_subview, $lists) && empty($this->_pagination)) {
			
            jimport('joomla.html.pagination');
			
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
			
		}
		
		return $this->_pagination;
		
	}
		
	/**
	 * Method to process info rows into the required variables
	 */
	 
	function processRows($type = NULL, $data = NULL) {
		
		$rawRows 		= $data ? $data 	: $this->getData();
		
		$section		= $type ? $type 	: JRequest::getVar("section", "banners");
		
		$type			= $type ? "banners" : JRequest::getVar("subview", "");
		
		$categoryID		= &JRequest::getVar("category_id",	"");
		
		$frontend		= &JRequest::getVar("frontend",		true);

		$rows			= array();
		
		foreach ($rawRows as $rawRow) {
			
			switch ($type) {
				
				case 'banners':
					
					switch ($section) {
						
						case 'banners':
						
							$rows[] 	= $this->processBanner($rawRow, $frontend);
							
							break;
						
						case 'textads':
						
							$rows[] 	= $this->processTextAd($rawRow, $frontend);
							
							break;
							
						case 'productads':
							
							$rows[] 	= $this->processProductAd($rawRow, $frontend);
							
							break;
							
						case 'productadscategories':
							
							if ($rawRow->products > 0) {
								
								$rows[] = $this->processProductCategoryRow($rawRow);
							
							}
							
							break;
							
						case 'categoryads':
							
							$rows[] 	= $this->processCategoryAd($rawRow, $frontend);
							
							break;
					
					}
					
					break;
					
				case 'traffic':
					
					$rows[] 	= $this->processTrafficRow($rawRow);
					
					break;
					
				case 'sales':
					
					$rows[] 	= $this->processSalesRow($rawRow);
					
					break;
					
				case 'payments':
					
					$rows[] 	= $this->processPaymentsRow($rawRow);
					
					break;
				
			}
			
		}
		
		// insert the flash preview function
		
		if ($type == "banners" && $section == "banners" && count($rawRows > 0)) {
			
			$document = &JFactory::getDocument();
			
			$document->addScriptDeclaration($this->affiliatePreviewFunction());
			
		}
		
		// return the rows
		
		return $rows;
		
	}
	
	/**
	 * Method to process a banner info row into the required variables for the banner page
	 */
	
	function processBanner($rawBanner, $frontend = true) {
		
		global $vmaSettings, $ps_vma;
		
		$session 				= &JFactory::getSession();
		
		$affiliate				= $frontend == true ? $session->get("affiliate") : NULL;
		
		$banner					= array();
		
		$bannerFilename			= $rawBanner->banner_image . "." . $rawBanner->banner_type;
		
		$bannerPath				= JPATH_ROOT . DS . "components" . DS . "com_affiliate" . DS . "banners" . DS . $bannerFilename;
		
		$ieBrowser				= isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) ? true : false;
	
		// make sure the banner file exists
		
		if (!file_exists($bannerPath)) {
			
			return false;
			
		}
		
		// get the banner file dimensions
		
		list($previewWidth, $previewHeight) = getimagesize($bannerPath);
		
		// link
		
		$bannerURL				= JRoute::_(JURI::root() . str_replace("&amp;", "&", $rawBanner->banner_link) . (stristr($rawBanner->banner_link, "?") ? "&" : "?") . 
			
								  "banner_id=" . $rawBanner->banner_id . ($frontend ? "&" . $vmaSettings->link_feed . "=" . $affiliate->affiliate_id : NULL), false);

		if ($rawBanner->banner_type == "swf") {
			
			$bannerSWFPath		= JURI::base() . "components/com_affiliate/banners/flash.swf?url=" . urlencode($bannerURL) . 
								  
								  "&file=" . urlencode(JURI::base() . "components/com_affiliate/banners/" . $rawBanner->banner_image);

			$bannerSWFPath		= htmlspecialchars($bannerSWFPath, ENT_QUOTES);
								  
			$banner["content"]	= "<object data=\"" . $bannerSWFPath . "\" width=\"" . $rawBanner->banner_width . "\" height=\"" . $rawBanner->banner_height . "\" type=\"" . 
			
								  "application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $bannerSWFPath . "\" /><param name=\"quality\" value=\"high\" />" . 
								  
								  "<param name=\"allowScriptAccess\" value=\"always\" /></object>";
			
		}
		
		else {
			
			$bannerURL			= htmlspecialchars($bannerURL, ENT_QUOTES);
			
			$bannerLink			= "<a href=\"" 	. $bannerURL 	. "\">";
								
			$bannerImage		= "<img src=\"" . JURI::base() 	. "components/com_affiliate/banners/" . $bannerFilename . "\" alt=\"Ad\" />";
			
			$banner["content"]	= $bannerLink 	. $bannerImage 	. "</a>";
		
		}
		
		$inputContents			= htmlspecialchars($banner["content"], ENT_QUOTES);
		
		$banner["html"] 		= "<input type=\"text\" value=\"" . $inputContents . "\" onclick=\"this.focus(); this.select();\" />";
		
		// thumbnail
		
		$thumbnailPath			= "components" . DS . "com_affiliate" . DS . "thumbs" . DS . "thumb_" . ($rawBanner->banner_type == "swf" ? "swf.png" : $bannerFilename);
		
		$thumbnailImage			= "components/com_affiliate/thumbs/thumb_" . ($rawBanner->banner_type == "swf" ? "swf.png" : $bannerFilename);
		
		if (!file_exists(JPATH_ROOT . DS . $thumbnailPath)) {
			
			$ps_vma->resizeImage($bannerPath, $rawBanner->banner_image, "thumb");
			
		}
		
		$thumbnailImage			= "<img src=\"" . JURI::base() . $thumbnailImage . "\" alt=\"" . JText::_("THUMB") . "\" />";
		
		$banner["thumb"]		= $rawBanner->banner_type == "swf" ? "<a href=\"javascript:affiliatePreviewFunction(" 		. $previewWidth 			. ", " 			. 
		
								  $previewHeight . ", '" 				. $bannerFilename . "');\">" . $thumbnailImage 		. "</a>" : "<a href=\"" 	. JURI::base() 	. 
								  
								  "components/com_affiliate/banners/" 	. $bannerFilename . "\" class=\"affiliateModal\">" 	. $thumbnailImage 			. "</a>";
		
		$banner["thumb"]		= $rawBanner->banner_type == "swf" && $ieBrowser ? $thumbnailImage : $banner["thumb"];
		
		// index
		
		$banner["index"]		= $this->getState('limitstart') + $this->_index;
		
		$this->_index++;
		
		// name
		
		$banner["name"] 		= $rawBanner->banner_name;
		
		// type
		
		$banner["type"]			= strtoupper($rawBanner->banner_type);
		
		// size
		
		$banner["size"]			= $rawBanner->banner_width . "x" . $rawBanner->banner_height . 
		
								  (isset($rawBanner->sizegroup) ? (($rawBanner->sizegroup ? " " . "(" . $rawBanner->sizegroup . ")" : NULL)) : NULL);
		
		// hits
		
		$banner["hits"] 		= $ps_vma->getHits("banner", $rawBanner->banner_id, $frontend, ($frontend ? $affiliate->affiliate_id : NULL));
		
		// preview link
		
		$previewLink			= JRoute::_("index.php?option=com_affiliate&view=prev&tmpl=component&format=raw&type=banners&id=" . $rawBanner->banner_id);
		
		$banner["prev"]			= "<a href=\"" . $previewLink . "\" class=\"affiliateModal\" rel=\"{size: {x: " . $rawBanner->banner_width . ", y: " .
		
								  $rawBanner->banner_height . "}, classWindow: 'affiliatePreviewWindow'}\">" . JText::_("PREVIEW") . "</a>";
		
		// return banner
		
		return $banner;
		
	}
	
	/**
	 * Method to process a text ad info row into the required variables for the text ads page
	 */
	
	function processTextAd($rawTextAd, $frontend = true) {
		
		global $vmaSettings, $ps_vma;
		
		$session 				= &JFactory::getSession();
		
		$view 					= JRequest::getVar('view', '');
		
		$affiliate				= $frontend == true ? $session->get("affiliate") : NULL;
		
		$textad					= array();
		
		// link
		
		$textadURL				= JRoute::_(JURI::root() . str_replace("&amp;", "&", $rawTextAd->link) . (stristr($rawTextAd->link, "?") ? "&" : "?") . 
			
								  "textad_id=" . $rawTextAd->textad_id . ($frontend ? "&" . $vmaSettings->link_feed . "=" . $affiliate->affiliate_id : NULL), false);
			
		$textadURL				= htmlspecialchars($textadURL, ENT_QUOTES);
		
		$textadLink				= "<a href=\"" . $textadURL . "\" style=\"text-decoration: none;\"><strong>" . $rawTextAd->title . "</strong><br />" . $rawTextAd->content . "</a>";
		
		$padding				= 10;
		
		$textadStyle			= "padding" 	. ":" . " " . $padding 								. "px" . ";" . " ";
		
		$textadStyle		   .= "border" 		. ":" . " " . "1" 									. "px" . " " . "solid" . " " . "#CCC" . ";" . " ";
		
		if ($rawTextAd->width) {
			
			$textadStyle	   .= "width" 		. ":" . " " . ($rawTextAd->width - $padding * 2)	. "px" . ";" . " ";
			
		}
		
		if ($rawTextAd->height) {
			
			$textadStyle	   .= "height" 		. ":" . " " . ($rawTextAd->height - $padding * 2) 	. "px" . ";";
		
		}
		
		if ($view == "prev") {
				
			$textadStyle	   .= "margin-left: auto; margin-right: auto;";
			
		}
		
		$textad["content"]		= "<div style=\"" . $textadStyle . "\">" . $textadLink . "</div>";
		
		$inputContents			= htmlspecialchars($textad["content"], ENT_QUOTES);
		
		$textad["html"] 		= "<input type=\"text\" value=\"" . $inputContents . "\" onclick=\"this.focus(); this.select();\" />";
		
		// index
		
		$textad["index"]		= $this->getState('limitstart') + $this->_index;
		
		$this->_index++;
		
		// name
		
		$textad["title"] 		= $rawTextAd->title;
		
		// size
		
		$textad["size"]			= ($rawTextAd->width > 0 ? $rawTextAd->width : "&#8734;") . "x" . ($rawTextAd->height > 0 ? $rawTextAd->height : "&#8734;");
		
		$textad["size"]			= $rawTextAd->width <= 0 && $rawTextAd->height <= 0 ? "&#8734;" : $textad["size"];
		
		$textad["size"]			.= isset($rawTextAd->sizegroup) ? ($rawTextAd->sizegroup ? " " . "(" . $rawTextAd->sizegroup . ")" : NULL) : NULL;

		// hits
		
		$textad["hits"] 		= $ps_vma->getHits("textad", $rawTextAd->textad_id, $frontend, ($frontend ? $affiliate->affiliate_id : NULL));
		
		// preview link
		
		$previewLink			= JRoute::_("index.php?option=com_affiliate&view=prev&tmpl=component&format=raw&type=textads&id=" . $rawTextAd->textad_id);
		
		$textad["prev"]			= "<a href=\"" . $previewLink . "\" class=\"affiliateModal\">" . JText::_("PREVIEW") . "</a>";
		
		// return text ad
		
		return $textad;
		
	}
	
	/**
	 * Method to process a product ad info row into the required variables for the product ads page
	 */
	
	function processProductAd($rawProductAd, $frontend = true) {
		
		global $vmaSettings, $ps_vma;
		
		$session 				= &JFactory::getSession();
		
		$view 					= JRequest::getVar('view', '');
		
		$affiliate				= $frontend == true ? $session->get("affiliate") : NULL;
		
		$textad					= array();
		
		$image					= $this->getImagePaths($rawProductAd->product_thumb_image, "product");
		
		// price
		
		$withTax				= $ps_vma->getVATSetting();
		
		$productPrice			= $rawProductAd->product_price;
		
		$productPrice		   += $withTax && $rawProductAd->product_tax > 0 ? $rawProductAd->product_price * $rawProductAd->product_tax : 0;
		
		// discount
		
		$discounted				= $rawProductAd->discount_amount && $rawProductAd->discount_start <= time() && 
		
								  (!$rawProductAd->discount_end || $rawProductAd->discount_end >= time()) ? true : false;
		
		$discountedPrice		= $discounted ? 
		
								  ($rawProductAd->discount_percentage ? ($rawProductAd->discount_amount / 100 * $productPrice) : $rawProductAd->discount_amount) : 0;
		
		$discountedPrice		= $productPrice - $discountedPrice;
		
		// format prices
		
		$productPrice			= $ps_vma->formatAmount($productPrice);
		
		$discountedPrice		= $ps_vma->formatAmount($discountedPrice);
		
		$priceStyle				= $discounted ? " " . "style=\"font-weight: normal; color: #FF0000; text-decoration: line-through;\"" : NULL;
		
		// link
		
		$productURL				= JRoute::_(JURI::root() . "index.php?option=com_virtuemart&page=shop.product_details&product_id=" . $rawProductAd->product_id . 
			
								  ($frontend ? "&" . $vmaSettings->link_feed . "=" . $affiliate->affiliate_id : NULL), false);
			
		$productURL				= htmlspecialchars($productURL, ENT_QUOTES);
		
		$productAdLink			= "<a href=\"" . $productURL . "\" style=\"text-decoration: none;\"><strong>" . $rawProductAd->product_name . "</strong><br /><br />" . 
		
								  "<img src=\"" . $image->url . "\" alt=\"Ad\" /><br /><br /><strong " . ($discounted ? $priceStyle : NULL) . ">" . $productPrice . "</strong>" . 
								  
								  ($discounted ? "<br /><strong>" . $discountedPrice . "</strong>" : NULL) . "</a>";
		
		$padding				= 10;
		
		$productAdStyle			= "padding" 	. ":" . " " . $padding 				. "px" . ";" . " ";
		
		$productAdStyle		   .= "border" 		. ":" . " " . "1" 					. "px" . " " . "solid" . " " . "#CCC" . ";" . " ";
		
		if ($view == "prev") {
				
			$productAdStyle	   .= "margin-left: auto; margin-right: auto;";
			
		}
		
		$productad["content"]	= "<div style=\"" . $productAdStyle . "\">" . $productAdLink . "</div>";
		
		$inputContents			= htmlspecialchars($productad["content"], ENT_QUOTES);
		
		$productad["html"] 		= "<input type=\"text\" value=\"" . $inputContents . "\" onclick=\"this.focus(); this.select();\" />";
		
		// thumbnail
		
		$productad["thumb"]		= "<a href=\"" . $image->url . "\" class=\"affiliateModal\"><img src=\"" . $image->thumb . "\" alt=\"" . JText::_("THUMB") . "\" /></a>";
								  
		// index
		
		$productad["index"]		= $this->getState('limitstart') + $this->_index;
		
		$this->_index++;
		
		// name
		
		$productad["name"] 		= $rawProductAd->product_name;
		
		// preview link
		
		$previewLink			= JRoute::_("index.php?option=com_affiliate&view=prev&tmpl=component&format=raw&type=productads&id=" . $rawProductAd->product_id);
		
		$productad["prev"]		= "<a href=\"" . $previewLink . "\" class=\"affiliateModal\">" . JText::_("PREVIEW") . "</a>";
		
		// return product ad
		
		return $productad;
		
	}
	
	/**
	 * Method to process a product category ad info row into the required variables for the product categories ads page
	 */
	
	function processProductCategoryRow($rawCategory) {
		
		global $ps_vma;
		
		$category				= array();
		
		// index
		
		$category["index"]		= $this->getState('limitstart') + $this->_index;
		
		$this->_index++;
		
		// category name
		
		$categoryLink			= JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=banners&section=productads&category_id=" . 
		
								  $rawCategory->category_id));
		
		$category["name"]		= "<a href=\"" . $categoryLink . "\">" . htmlspecialchars($rawCategory->category_name, ENT_QUOTES) . "</a>";
		
		// products
		
		$category["products"]	= $rawCategory->products;
		
		// return category
		
		return $category;
		
	}
	
	/**
	 * Method to process a category ad info row into the required variables for the category ads page
	 */
	
	function processCategoryAd($rawCategoryAd, $frontend = true) {
		
		global $vmaSettings, $ps_vma;
		
		$session 				= &JFactory::getSession();
		
		$view 					= JRequest::getVar('view', '');
		
		$affiliate				= $frontend == true ? $session->get("affiliate") : NULL;
		
		$categoryad				= array();
		
		$image					= $this->getImagePaths($rawCategoryAd->category_thumb_image, "category");
		
		// link
		
		$categoryURL			= JRoute::_(JURI::root() . "index.php?option=com_virtuemart&page=shop.browse&category_id=" . $rawCategoryAd->category_id . 
			
								  ($frontend ? "&" . $vmaSettings->link_feed . "=" . $affiliate->affiliate_id : NULL), false);
			
		$categoryURL			= htmlspecialchars($categoryURL, ENT_QUOTES);
		
		$categoryAdLink			= "<a href=\"" . $categoryURL . "\" style=\"text-decoration: none;\"><strong>" . $rawCategoryAd->category_name . "</strong><br /><br />" . 
		
								  "<img src=\"" . $image->url . "\" alt=\"Ad\" /></a>";
		
		$padding				= 10;
		
		$categoryAdStyle		= "padding" 	. ":" . " " . $padding 				. "px" . ";" . " ";
		
		$categoryAdStyle	   .= "border" 		. ":" . " " . "1" 					. "px" . " " . "solid" . " " . "#CCC" . ";" . " ";
		
		if ($view == "prev") {
				
			$categoryAdStyle   .= "margin-left: auto; margin-right: auto;";
			
		}
		
		$categoryad["content"]	= "<div style=\"" . $categoryAdStyle . "\">" . $categoryAdLink . "</div>";
		
		$inputContents			= htmlspecialchars($categoryad["content"], ENT_QUOTES);
		
		$categoryad["html"] 	= "<input type=\"text\" value=\"" . $inputContents . "\" onclick=\"this.focus(); this.select();\" />";
		
		// thumbnail
		
		
		$categoryad["thumb"]	= "<a href=\"" . $image->url . "\" class=\"affiliateModal\"><img src=\"" . $image->thumb . "\" alt=\"" . JText::_("THUMB") . "\" /></a>";
								  
		// index
		
		$categoryad["index"]	= $this->getState('limitstart') + $this->_index;
		
		$this->_index++;
		
		// name
		
		$categoryad["name"] 	= $rawCategoryAd->category_name;
		
		// preview link
		
		$previewLink			= JRoute::_("index.php?option=com_affiliate&view=prev&tmpl=component&format=raw&type=categoryads&id=" . $rawCategoryAd->category_id);
		
		$categoryad["prev"]		= "<a href=\"" . $previewLink . "\" class=\"affiliateModal\">" . JText::_("PREVIEW") . "</a>";
		
		// return category ad
		
		return $categoryad;
		
	}
	
	/**
	 * Method to process a traffic info row into the required variables for the traffic page
	 */
	
	function processTrafficRow($rawTrafficRow, $frontend = true) {
		
		global $vmaSettings;
		
		$traffic				= array();
		
		// index
		
		$traffic["index"]		= $this->getState('limitstart') + $this->_index;
		
		$this->_index++;
		
		// ip address
		
		$traffic["ip"]			= $rawTrafficRow->RemoteAddress;
		
		// referrer
		
		$rawTrafficRow->RefURL	= str_replace("&amp;",	"&",		$rawTrafficRow->RefURL);
			
		$rawTrafficRow->RefURL	= str_replace("&",		"&amp;",	$rawTrafficRow->RefURL);
			
		$traffic["referrer"]	= $rawTrafficRow->RefURL ? $rawTrafficRow->RefURL : JText::_("NONE");
		
		$traffic["referrer"]	= $rawTrafficRow->RefURL && strlen($rawTrafficRow->RefURL) > 50 ? substr($rawTrafficRow->RefURL, 0, 50) : $traffic["referrer"];
		
		$traffic["referrer"]	= strlen($traffic["referrer"]) == 50 ? "<a href=\"javascript:expandAffiliateReferrerLink('affiliateReferrerLink" . $traffic["index"] . "');\" " . 
		
								  "id=\"affiliateReferrerLink" . $traffic["index"] . "\" title=\"" . $rawTrafficRow->RefURL . "\">" . $traffic["referrer"] . "...</a>"
								  
								  : $traffic["referrer"];
				
		// date
		
		$traffic["date"]		= date("Y-m-d", $rawTrafficRow->UnixTime);
		
		// time
		
		$traffic["time"]		= date("H:i:s", $rawTrafficRow->UnixTime);
		
		// return traffic
		
		return $traffic;
		
	}
	
	/**
	 * Method to process a sales info row into the required variables for the sales page
	 */
	
	function processSalesRow($rawSalesRow, $frontend = true) {
		
		global $vmaSettings, $ps_vma;
		
		$sale					= array();
		
		// index
		
		$sale["index"]			= $this->getState('limitstart') + $this->_index;
		
		$this->_index++;
		
		// id
		
		$sale["id"]				= $rawSalesRow->order_id;
		
		// subtotal
		
		$sale["subtotal"]		= $ps_vma->formatAmount($rawSalesRow->subtotal);
		
		// status
		
		if (in_array($rawSalesRow->order_status, $ps_vma->_confirmedStatuses)) {
			
			$statusIcon			= "yes";
				
			$statusAlt			= JText::_("JYES");
				
		}
		
		if (in_array($rawSalesRow->order_status, $ps_vma->_pendingStatuses)) {
			
			$statusIcon			= "pending";
				
			$statusAlt			= JText::_("JNO");
				
		}
		
		if (in_array($rawSalesRow->order_status, $ps_vma->_cancelledStatuses)) {
			
			$statusIcon			= "no";
				
			$statusAlt			= JText::_("JNO");
				
		}
		
		$sale["status"]			= "<img src=\"" . JURI::base() 		. "components/com_affiliate/views/panel/tmpl/images/status_" 	. 
		
								  $statusIcon 	. ".png\" alt=\"" 	. $statusAlt 	. "\" />";
		
		// paid
		
		$paidIcon				= $rawSalesRow->paid ? "yes" 						: "pending";
		
		$paidAlt				= $rawSalesRow->paid ? JText::_("JYES") 				: JText::_("JNO");
		
		$sale["paid"]			= "<img src=\"" . JURI::base() 		. "components/com_affiliate/views/panel/tmpl/images/status_" 	. 
		
								  $paidIcon 	. ".png\" alt=\"" 	. $paidAlt 		. "\" />";
				
		// date
		
		$sale["date"]			= date("Y-m-d", $rawSalesRow->order_date);
		
		// time
		
		$sale["time"]			= date("H:i:s", $rawSalesRow->order_date);
		
		// return sale
		
		return $sale;
		
	}
	
	/**
	 * Method to process a payment info row into the required variables for the payments page
	 */
	
	function processPaymentsRow($rawPaymentsRow, $frontend = true) {
		
		global $vmaSettings, $ps_vma;
		
		$payment				= array();
		
		// index
		
		$payment["index"]		= $this->getState('limitstart') + $this->_index;
		
		$this->_index++;
		
		// method
		
		$payment["method"]		= $rawPaymentsRow->method;
		
		// amount
		
		$payment["amount"]		= $ps_vma->formatAmount($rawPaymentsRow->amount);
		
		// status
		
		$confirmedIcon			= $rawPaymentsRow->status == "C" 	? "yes" 			: ($rawPaymentsRow->status == "P" ? "pending" : "no");
		
		$confirmedAlt			= $rawPaymentsRow->status == "C" 	? JText::_("JYES") 	: JText::_("JNO");
		
		$payment["status"]		= "<img src=\"" 					. JURI::base() 		. "components/com_affiliate/views/panel/tmpl/images/status_" 	. 
		
								  $confirmedIcon 					. ".png\" alt=\"" 	. $confirmedAlt . "\" />";
		
		// date
		
		$payment["date"]		= $rawPaymentsRow->date;
		
		// return payment
		
		return $payment;
		
	}
	
	/**
     * Method to validate an affiliate details form and return a filtered affiliate details array
     */
	
	function validateDetails() {
		
		global $ps_vma;
		
		// initialize variables
		
		$mainframe 						= &JFactory::getApplication();
		
		$redirectionLink				= JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=details"), false);
		
		$session						= &JFactory::getSession();
		
		$affiliate						= $session->get("affiliate");
		
		$database						= &JFactory::getDBO();
		
		// get registration form fields
		
		$newAffiliate 					= array();
		
		$newAffiliate["mail"] 			= JRequest::getString("mail", 		"", 	"post");
		
		$newAffiliate["website"] 		= JRequest::getString("website", 	"", 	"post");
		
		$newAffiliate["fname"] 			= JRequest::getString("fname", 		"", 	"post");
		
		$newAffiliate["lname"] 			= JRequest::getString("lname", 		"", 	"post");
		
		$newAffiliate["street"] 		= JRequest::getString("street", 	"", 	"post");
		
		$newAffiliate["city"] 			= JRequest::getString("city", 		"", 	"post");
		
		$newAffiliate["state"] 			= JRequest::getString("state", 		"", 	"post");
		
		$newAffiliate["country"] 		= JRequest::getString("country", 	"", 	"post");
		
		$newAffiliate["zipcode"] 		= JRequest::getString("zipcode", 	"", 	"post");
		
		$newAffiliate["phoneno"] 		= JRequest::getString("phoneno", 	"", 	"post");
		
		$newAffiliate["taxssn"] 		= JRequest::getString("taxssn", 	"", 	"post");
		
		// validate form input
		
		if (!$newAffiliate["mail"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_EMAIL_ADDRESS"), 	"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["fname"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_FIRST_NAME"), 		"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["lname"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_LAST_NAME"), 		"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["street"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_ADDRESS"), 		"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["city"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_CITY"), 			"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["country"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_COUNTRY"), 		"error");
			
			return false;
			
		}
		
		if (!$newAffiliate["zipcode"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_ZIPCODE"), 		"error");
			
			return false;

			
		}
		
		// check if e-mail is already in use by another affiliate
		
		$query 		= "SELECT `affiliate_id`, `mail` FROM #__vm_affiliate WHERE `mail` = '" . $newAffiliate['mail'] . "'";
		
		$database->setQuery( $query );
		
		$info		= $database->loadAssoc();
		
		if ($info["mail"] == $newAffiliate['mail'] && $info["affiliate_id"] != $affiliate->affiliate_id) {
			
			$mainframe->redirect($redirectionLink, JText::_("WARNREG_EMAIL_INUSE"), 	"error");
			
			return false;
			
		}
		
		// return the filtered registration array
		
		return $newAffiliate;
		
    }
	
	/**
     * Method to validate a password change form and return a filtered password
     */
	
	function validatePasswordChange() {
		
		global $ps_vma;
		
		// initialize variables
		
		$mainframe 						= &JFactory::getApplication();
		
		$database						= &JFactory::getDBO();
		
		$session						= &JFactory::getSession();
		
		$affiliate						= $session->get("affiliate");
		
		$redirectionLink				= JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=changepass"), false);
		
		// get password form fields
		
		$password						= array();
		
		$password["old"] 				= JRequest::getString("oldpassword", 		"", 	"post");
		
		$password["new"] 				= JRequest::getString("newpassword", 		"", 	"post");
		
		$password["verify"] 			= JRequest::getString("verifypassword", 	"", 	"post");
		
		// validate form input
		
		if (!$password["old"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_PASSWORD"), 	"error");
			
			return false;
			
		}
		
		if (!$password["new"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PROVIDE_PASSWORD"), 	"error");
			
			return false;
			
		}
		
		if (!$password["verify"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("RETYPE_PASSWORD"), 	"error");
			
			return false;
			
		}
		
		if ($password["new"] != $password["verify"]) {
			
			$mainframe->redirect($redirectionLink, JText::_("PASSWORDS_DIFFER"), 	"error");
			
			return false;
			
		}
		
		// verify the old password
		
		$query 			= "SELECT `password` FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliate->affiliate_id . "' AND `password` = '" . md5($password["old"]) . "'";
		
		$database->setQuery($query);
		
		$verifyPassword = $database->loadResult();
		
		if (!$verifyPassword) {
			
			$mainframe->redirect($redirectionLink, JText::_("INCORRECT_PASSWORD"), 	"error");
			
			return false;
			
		}
		
		// return the filtered password array
		
		return $password;
		
    }
	
	/**
	 * Method to retrieve active ads pages menus
	 */
	 
	function getActiveAdsMenus() {
		
		if (!$this->_activeAdsMenus) {
			
			$database							= &JFactory::getDBO();
			
			$adsMenus							= array("banners", "textads", "productads", "categoryads");
				
			$activeAdsMenus						= array();
			
			$categoryID							= JRequest::getVar("category_id", "");

			foreach ($adsMenus as $adsMenu) {
				
				$query 							= $this->_buildQuery($adsMenu);
				
				$database->setQuery($query);

				$results 						= $database->loadResult();
				
				$activeAdsMenus[$adsMenu] 		= $results > 0 ? true : false;
				
				// only display category ads if there are more than 1 categories
				
				$activeAdsMenus[$adsMenu]		= $adsMenu == "categoryads" ? ($results > 1 ? true : false) : $activeAdsMenus[$adsMenu];
				
				// remember the number of products
				
				$activeAdsMenus[$adsMenu]		= $adsMenu == "productads"	? $results : $activeAdsMenus[$adsMenu];
			
			}

			// get correct product ads display page
			
			$activeAdsMenus["productads"]		= $activeAdsMenus["categoryads"] && $activeAdsMenus["productads"] > 100 ? "productadscategories" : 
			
												  ($activeAdsMenus["productads"] ? "productads" : $activeAdsMenus["productads"]);
		}
		
		else {
			
			$activeAdsMenus = $this->_activeAdsMenus;
			
		}
		
		return $activeAdsMenus;
			
	}
	
	/**
	 * Method to return a JavaScript function that is used to customize the behaviour and size of the SqueezeBox modal windows used for the banner thumbs and previews
	 */
	 
	function affiliatePreviewFunction() {
		
		// initiate required variables
		
		$document = &JFactory::getDocument();
		
		// include a simple css fix for unneeded scrollbars
		
		$document->addStyleDeclaration("div.affiliatePreviewWindow div#sbox-content {
			
											overflow: hidden;
											
										}");
		
		// prepare the javascript function
		
		$affiliatePreviewFunction = "function affiliatePreviewFunction(width, height, filename) {
										
										// if this is a flash banner, preview it
										
										if (typeof(filename) != 'undefined') {
											
											var flashObject = 	   new Element('object', {
									  
																						  'data': 	'" . JURI::base() . "components/com_affiliate/banners/' + filename,
																				  
																						  'width':	width,
																				  
																						  'height':	height,
																				  
																						  'type':	'application/x-shockwave-flash'
																				  
																			  })
																			  
																  .adopt(new Element('param', {
																	  
																						  'name':	'movie',
																						  
																						  'value':	'" . JURI::base() . "components/com_affiliate/banners/' + filename
																						  
																					  })
																					  
																  )
																					  
																  .adopt(new Element('param', {
																	  
																						  'name':	'quality',
																						  
																						  'value':	'high'
																						  
																					  })
																					  
																  );
																  
											SqueezeBox.fromElement(new Element('div').adopt(flashObject), { 
											
																		handler: 	'adopt', 
																		
																		size: { 
																		
																			x: 		width, 
																			
																			y: 		height 
																			
																		},
																		
																		classWindow: 'affiliatePreviewWindow'
																		
																  }
																  
											);
											
										}
										
									}";
									
		// include the javascript function
		
		$document->addScriptDeclaration($affiliatePreviewFunction);
		
	}
	
	/**
	 * Method that parses a given image file and prepares the corresponding paths
	 */

	function getImagePaths($file, $type) {
		
		global $ps_vma;
		
		// initiate required variables
		
		$image		= new stdClass();
		
		$fileType	= "file";
		
		// check if this is not a simple file
		
		if (stristr($file, "/") || stristr($file, DS)) {
			
			// check if this is a url
			
			if (stristr($file, "http")) {
				
				$fileType	= "url";
				
			}
			
			// check if this is a subfolder
			
			else if (stristr($file, "resized")) {
				
				$fileType	= "subfolder";
				
			}
			
			// this must be a path then
			
			else {
				
				$fileType	= "path";
				
			}
			
		}
		
		// determine image parameters
		
		switch ($fileType) {
			
			case "url":
			
				$image->url			= $file;
				
				$image->path		= JPATH_ROOT . DS . 
				
									  str_replace("/", DS, str_ireplace(stristr($file, "https") ? 
									  
									  str_ireplace("http", "https", str_ireplace("https", "http", JURI::base())) : JURI::base(), "", $file));
									  
				$breakURL			= explode("/", $file);
				
				$image->name		= $breakURL[(count($breakURL) - 1)];
				
				break;
				
			case "path":
			
				$image->url			= JURI::base() . str_replace(DS, "/", str_replace(JPATH_ROOT . DS, "", $file));
				
				$image->path		= $file;
				
				$breakPath			= explode(DS, $file);
				
				$image->name		= $breakPath[(count($breakPath) - 1)];
				
				break;
				
			case "file":
			
			case "subfolder":
			
			default:
			
				$image->url			= JURI::base() . "components/com_virtuemart/shop_image/" . $type . "/" . $file;
				
				$image->path		= JPATH_ROOT . DS . "components" . DS . "com_virtuemart" . DS . "shop_image" . DS . $type . DS . $file;
				
				$image->name		= $file;
				
				break;
				
		}
		
		// determine new thumbnail parameters
		
		$thumbsPath			= JPATH_ROOT . DS . "components" . DS . "com_affiliate" . DS . "thumbs" . DS . "thumb_";
		
		$imageNameParts		= explode("/", $image->name);
		
		$imageFileName		= $imageNameParts[(count($imageNameParts) - 1)];
		
		$fileNameParts		= explode(".", $imageNameParts[(count($imageNameParts) - 1)]);
		
		$imageName			= str_replace("." . $fileNameParts[(count($fileNameParts) - 1)], "", $imageFileName);
		
		// generate new thumbnail, if not exists
		
		if (!file_exists($thumbsPath . $imageFileName)) {

			$ps_vma->resizeImage($image->path, $imageName, "thumb");
			
		}
		
		// get new thumbnail's url
		
		$image->thumb		= JURI::base() . "components/com_affiliate/thumbs/thumb_" . $imageFileName;
		
		// return the image parameters
		
		return $image;
		
	}
	
}