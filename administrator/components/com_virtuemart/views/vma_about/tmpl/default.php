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

// get helper and settings

global $vmaHelper, $vmaSettings;

// start the virtuemart administration area

AdminUIHelper::startAdminArea($this); 

// initiate other variables

$link 				= $vmaHelper->getAdminLink();

// display the title

JToolBarHelper::title(JText::_("ABOUT") . "VM Affiliate 4.5", 'head adminAboutIcon');

?>

<div class="affiliateAdminPage">
    
    <div id="aboutContainer">
        
        <p>
        
        	VM Affiliate is the first and only truly integrated affiliate program software for the VirtueMart e-commerce component and its host CMS, Joomla!&trade;.
            
		</p>
        
        <img src="<?php echo $vmaHelper->_website . "components/com_affiliate/assets/images/virtuemartaffiliate.png"; ?>" alt="VM Affiliate" 
        
       		 style="float: right; margin: 10px; width: 164px;" />
        
        <p>
            
            Seamless to install, and easy to use and configure, VM Affiliate is a complex extension providing all the power you need to instantly deploy
            
            and manage a powerful affiliate program, in a comprehensive and hassle-free manner.
            
		</p>
        
        <p>
            
            VM Affiliate brings tremendous value over other stand-alone or general Joomla!&trade; affiliate program software, in that it has been specifically and
            
            exclusively designed for VirtueMart and its features.
            
		<p>
            
            This integration brings many advantages:
		
        </p>
            
        <ul>
        
            <li>seamless operation within VirtueMart's look and feel, VM Affiliate's administration being a part of VirtueMart's;</li>
            
            <li>no maintenance required by the administrator, visits and orders being automatically tracked and commissioned;</li>
        
            <li>automatic generation of advertising material from the shop's products and categories, that affiliates can instantly use;</li>
            
        </ul>
            
		<p>
            
            VM Affiliate is designed to empower your business with an affiliate program, with minimal to no interaction and maintenance from your side.
            
            Basically, all you have to do is install VM Affiliate, configure it (less than 5 minutes), and then just pay your affiliates on a monthly basis, as they earn 
            
            commissions and bring you sales.
            
		</p>
        
        <p>
            
            Installation is instant, via Joomla!&trade;'s standard installer, using a single package that automatically deploys and enables all the 
            
            required components of the software: the frontend component (Affiliate Panel), the tracking plugin, the language file and the backend 
            
            administration/management.
            
		</p>
        
        <p>
            
            VM Affiliate is a Joomla!&trade; native component, following the MVC design pattern to the letter, thus being easy to customize, either visually or featurewise. 
            
            It achieves all the above with absolutely no Joomla!&trade;/VirtueMart core modifications or hacks of any kind. It uses smart techniques for all the integration and 
            
            automation requirements.
            
        </p>
        
        <p>
        
        	Since its release, in 2006, VM Affiliate has grown and matured thanks to the wonderful community and engaging users, working closely with us to share their 
            
            needs and requirements, which we heard and implemented.
            
        </p>
        
        <p>
        
        	VM Affiliate is a commercial product released under the <a href="http://www.gnu.org/copyleft/gpl.html">GNU/GPL</a> open-source license.
            
        </p>
        
    </div>
    
    <div style="clear: both;"></div>

</div>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>