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

// get initial variables

$paid			= &JRequest::getVar("paid",			1);

$unique			= &JRequest::getVar("unique",		0);

$affiliateID	= &JRequest::getVar("affiliate_id",	0);

$database		= &JFactory::getDBO();

$affiliateName	= NULL;

if ($affiliateID) {
	
	$query			= "SELECT CONCAT(`fname`, ' ', `lname`) AS name FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
	
	$database->setQuery($query);
	
	$affiliateName	= $database->loadResult();
	
}

// display the title

JToolBarHelper::title($affiliateName ? JText::_("TRAFFIC_FOR") . " " . $affiliateName : JText::_("TRAFFIC"), 'head adminTrafficIcon');

// initiate other variables

$link = $vmaHelper->getAdminLink();

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">

    <div id="header">

        <div id="filterbox" >
    
            <table>
    
            <tr>
    
                <td align="left" width="100%">

                    <?php echo $this->displayDefaultViewSearch(); ?>
    
                </td>
    
            </tr>
    
            </table>
    
        </div>
    
        <div id="resultscounter">
        
            <?php echo $this->pagination->getResultsCounter(); ?>
            
        </div>

    </div>
	
    <div class="affiliateTopMenu affiliateLogsMenu" style="background-color: #F0F0F0; width: 100%;">
            
        <div class="affiliateTopMenuLink">
        
            <span class="affiliateFilterMenu">
            
            	<span>
				
					<?php
                
						if (!$paid) {
						
					?>
                    
                			<a href="<?php echo $link . "_traffic&amp;paid=1&amp;unique=" . $unique . "&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                    
                			<a href="<?php echo $link . "_traffic&amp;paid=0&amp;unique=" . $unique . "&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                    
                			<a href="<?php echo $link . "_traffic&amp;unique=0&amp;paid=" . $paid . "&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                    
                			<a href="<?php echo $link . "_traffic&amp;unique=1&amp;paid=" . $paid . "&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                        
                            <a href="<?php echo $link . "_traffic&amp;unique=" . $unique . "&amp;paid=" . $paid; ?>"><?php echo ucwords(JText::_("JALL")); ?></a>
                            
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
    
    <table class="adminlist" cellspacing="0" cellpadding="0">

        <thead>
    
            <tr>
    
                <th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->traffic); ?>')" /></th>
        
                <th style="text-align: left;"><?php echo JText::_("IP_ADDRESS"); ?></th>
        
                <th style="text-align: left; width: <?php echo $affiliateID ? "500" : "400"; ?>px;"><?php echo JText::_("REFERRING_URL"); ?></th>
        
        		<?php if (!$affiliateID) { ?>
                
                	<th style="text-align: left;"><?php echo JText::_("AFFILIATE_ID"); ?></th>
        
        		<?php } ?>
                
                <th style="text-align: left;"><?php echo JText::_("TIME"); ?></th>
    			
                <th style="text-align: left;"><?php echo JText::_("DATE"); ?></th>
                
            </tr>
    
        </thead>
    
        <tbody>
        
        <?php

			if (count($this->traffic) > 0) {
				
				$i = 0;

    			$k = 0;
				
				foreach ($this->traffic as $key => $traffic) {

					// process data
			
					$checked			= JHTML::_('grid.id', 	$i, 		$traffic->ClickID);
			
					$date 				= getdate($traffic->UnixTime);
			
					$traffic->RefURL 	= str_replace("&amp;",	"&",		$traffic->RefURL);
			
					$traffic->RefURL 	= str_replace("&",		"&amp;",	$traffic->RefURL);
			
					?>

		    		<tr class="row<?php echo $k ; ?>">

						<!-- add checkbox -->
                        
						<td style="text-align: center;"><?php echo $checked; ?></td>
                        
                        <!-- add ip address  -->
                        
                        <td><?php echo $traffic->RemoteAddress; ?></td>
						
                        <!-- add referring url -->
                        
                        <td style="width: <?php echo $affiliateID ? "500" : "400"; ?>px;"><?php echo $traffic->RefURL ? $traffic->RefURL : JText::_("JNONE"); ?></td>
                        
                        <!-- add affiliate id -->
                        
                        <?php if (!$affiliateID) { ?>
                        
                        <td><?php echo $traffic->AffiliateID . 
				
									   ($traffic->name ? 
										
									   " (<a href=\"" . $link . "_traffic&amp;affiliate_id=" . $traffic->AffiliateID . "&amp;paid=" . $paid . "&amp;unique=" . $unique . "\">" . 
										
									   $traffic->name 	. "</a>)" : 
										
									   NULL); ?></td>
                        
                        <?php } ?>
                              
                        <!-- add time -->
                        
                        <td><?php echo $date["hours"]	. ":" . $date["minutes"]	. ":" . $date["seconds"]; ?></td>
                        
                        <!-- add date -->
                        
                        <td><?php echo $date["year"]	. "-" . $date["mon"]		. "-" . $date["mday"]; ?></td>
                        
                    </tr>
                    
                    <?php
					
					$k = 1 - $k;

					$i++;
				
				}
				
			}
			
		?>
        
        </tbody>
        
        <tfoot>

	    	<tr>

				<td colspan="11">

					<?php echo $this->pagination->getListFooter(); ?>

				</td>

	    	</tr>

		</tfoot>
        
	</table>

	<input type="hidden" name="task" 			value="" 							/>

	<input type="hidden" name="option" 			value="com_virtuemart"				/>

	<input type="hidden" name="view" 			value="vma_traffic" 				/>

	<input type="hidden" name="boxchecked" 		value="0"							/>
    
    <input type="hidden" name="paid" 			value="<?php echo $paid; ?>" 		/>
    
    <input type="hidden" name="unique" 			value="<?php echo $unique; ?>" 		/>
    
    <input type="hidden" name="affiliate_id" 	value="<?php echo $affiliateID; ?>" />

	<?php echo JHTML::_('form.token'); ?>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>