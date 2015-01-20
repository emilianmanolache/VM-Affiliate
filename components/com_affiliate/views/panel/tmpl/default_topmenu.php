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
 
// get vma settings

global $vmaSettings, $ps_vma;

$user			= &JFactory::getUser();

$displayLogout	= $user->get('guest') || $user->username != $this->affiliate->linkedto ? true : false;

?>

<?php if ((!$this->affiliate->method || $this->affiliate->method == "N/A") && $this->subview != "preferences") { ?>

    <div class="affiliateTopMenu">
    
        <div class="affiliateTopMenuLink">
        
            <span>
            
                <a href="<?php echo JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=preferences")); ?>"><?php echo JText::_("SELECT_PAYOUT_METHOD"); ?></a>
                
            </span>
            
        </div>
        
    </div>
	
    <div style="clear: both;"></div>

	<div class="affiliateHR"></div>

<?php } ?>

<div class="affiliateTopMenu">
    
    <div class="affiliateTopMenuLink">
    
        <span>
    
            <?php echo JText::_("WELCOME"); ?>, <strong><?php echo $this->affiliate->fname . " " . $this->affiliate->lname; ?></strong>!
        
        </span>
        
    </div>
    
    <?php if ($this->subview != "home") { ?>
    
        <div class="affiliateTopMenuLink">
        
            <span style="background: url(<?php echo JURI::base(); ?>components/com_affiliate/views/panel/tmpl/images/home_small.png) no-repeat left top;" class="affiliateTopMenuLinkItem">
            
                <a href="<?php echo JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=home")); ?>"><?php echo JText::_("AFFILIATE_PANEL"); ?></a>
                
            </span>
            
        </div>
    
    <?php } ?>
    
    <?php if ($this->subview != "changepass") { ?>
    
    <div class="affiliateTopMenuLink">
    
        <span style="background: url(<?php echo JURI::base(); ?>components/com_affiliate/views/panel/tmpl/images/password_small.png) no-repeat left top;" class="affiliateTopMenuLinkItem">
        
            <a href="<?php echo JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=panel&subview=changepass")); ?>"><?php echo JText::_("CHANGE_PASSWORD"); ?></a>
            
        </span>
        
    </div>
    
    <?php } ?>
    
    <?php if ($displayLogout) { ?>
    
    <div class="affiliateTopMenuLink">
        
        <span style="background: url(<?php echo JURI::base(); ?>components/com_affiliate/views/panel/tmpl/images/logout_small.png) no-repeat left top;" class="affiliateTopMenuLinkItem">
        
            <a href="<?php echo JRoute::_($ps_vma->vmaRoute("index.php?option=com_affiliate&view=login&task=logout")); ?>"><?php echo JText::_("LOGOUT"); ?></a>
            
        </span>
    
    </div>
    
    <?php } ?>
    
</div>

<div style="clear: both;"></div>

<div class="affiliateHR"></div>