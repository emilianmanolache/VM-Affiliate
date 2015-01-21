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
 
// get vma settings

global $vmaSettings, $vmaHelper;

?>

<br />
    
<div id="affiliateHomePanel">

    <div class="affiliateHomePanelIcon">
        
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=summary")); ?>">
        
            <img src="<?php echo JURI::base() . "components/com_affiliate/views/panel/tmpl/images/summary.png"; ?>" alt="<?php echo JText::_("SUMMARY"); ?>" /></a>
        
        <br />
        
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=summary")); ?>">
		
			<?php echo JText::_("SUMMARY"); ?>
            
		</a>
        
    </div>
    
    <div class="affiliateHomePanelIcon">
    
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=banners")); ?>">
            
            <img src="<?php echo JURI::base() . "components/com_affiliate/views/panel/tmpl/images/banners.png"; ?>" alt="<?php echo JText::_("BANNERS_AND_ADS"); ?>" /></a>
        
        <br />
        
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=banners")); ?>">
        
        	<?php echo JText::_("BANNERS_AND_ADS"); ?>
        
        </a>
        
    </div>
    
    <div class="affiliateHomePanelIcon">
    
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=emails")); ?>">
            
            <img src="<?php echo JURI::base() . "components/com_affiliate/views/panel/tmpl/images/emails.png"; ?>" alt="<?php echo JText::_("EMAIL_CAMPAIGN"); ?>" /></a>
        
        <br />
        
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=emails")); ?>">
        
        	<?php echo JText::_("EMAIL_CAMPAIGN"); ?>
        
        </a>
        
    </div>
    
    <div style="clear: both"></div>
    
    <div class="affiliateHomePanelIcon">
    
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=traffic")); ?>">
            
            <img src="<?php echo JURI::base() . "components/com_affiliate/views/panel/tmpl/images/traffic.png"; ?>" alt="<?php echo JText::_("TRAFFIC"); ?>" /></a>
        
        <br />
        
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=traffic")); ?>">
        
        	<?php echo JText::_("TRAFFIC"); ?>
        
        </a>
        
    </div>
    
    <div class="affiliateHomePanelIcon">
    
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=sales")); ?>">
            
            <img src="<?php echo JURI::base() . "components/com_affiliate/views/panel/tmpl/images/sales.png"; ?>" alt="<?php echo JText::_("SALES"); ?>" /></a>
        
        <br />
        
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=sales")); ?>">
        
        	<?php echo JText::_("SALES"); ?>
        
        </a>
        
    </div>
    
    <div class="affiliateHomePanelIcon">
    
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=payments")); ?>">
            
            <img src="<?php echo JURI::base() . "components/com_affiliate/views/panel/tmpl/images/payments.png"; ?>" alt="<?php echo JText::_("PAYMENTS"); ?>" /></a>
        
        <br />
        
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=payments")); ?>">
        
        	<?php echo JText::_("PAYMENTS"); ?>
        
        </a>
        
    </div>
    
    <div style="clear: both"></div>
    
    <div class="affiliateHomePanelIcon">
    
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=statistics")); ?>">
            
            <img src="<?php echo JURI::base() . "components/com_affiliate/views/panel/tmpl/images/statistics.png"; ?>" alt="<?php echo JText::_("STATISTICS"); ?>" /></a>
        
        <br />
        
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=statistics")); ?>">
        
        	<?php echo JText::_("STATISTICS"); ?>
        
        </a>
        
    </div>
    
    <div class="affiliateHomePanelIcon">
    
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=details")); ?>">
            
            <img src="<?php echo JURI::base() . "components/com_affiliate/views/panel/tmpl/images/details.png"; ?>" alt="<?php echo JText::_("EDIT_DETAILS"); ?>" /></a>
        
        <br />
        
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=details")); ?>">
        
        	<?php echo JText::_("EDIT_DETAILS"); ?>
        
        </a>
        
    </div>
    
    <div class="affiliateHomePanelIcon">
    
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=preferences")); ?>">
            
            <img src="<?php echo JURI::base() . "components/com_affiliate/views/panel/tmpl/images/preferences.png"; ?>" alt="<?php echo JText::_("PREFERENCES"); ?>" /></a>
        
        <br />
        
        <a href="<?php echo JRoute::_($vmaHelper->vmaRoute("index.php?option=com_affiliate&view=panel&subview=preferences")); ?>">
        
        	<?php echo JText::_("PREFERENCES"); ?>
        
        </a>
        
    </div>

</div>

<div style="clear: both;"></div>