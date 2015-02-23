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

$confirmed		= &JRequest::getVar("confirmed",	0);

$affiliateID	= &JRequest::getVar("affiliate_id",	0);

$database		= &JFactory::getDBO();

$affiliateName	= NULL;

if ($affiliateID) {
	
	$query			= "SELECT CONCAT(`fname`, ' ', `lname`) AS name FROM #__vm_affiliate WHERE `affiliate_id` = '" . $affiliateID . "'";
	
	$database->setQuery($query);
	
	$affiliateName	= $database->loadResult();
	
}

// display the title

JToolBarHelper::title($affiliateName ? JText::_("PAYMENTS") . ": " . $affiliateName : JText::_("PAYMENTS"), 'head adminPaymentsIcon');

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
                
						if ($confirmed) {
						
					?>
                    
                			<a href="<?php echo $link . "_payments&amp;confirmed=0&amp;affiliate_id=" . $affiliateID; ?>">
						
                    <?php
					
						}
						
					?>
                    
					<?php 
					
						echo ucwords(JText::_("JALL")); 
						
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
                    
                			<a href="<?php echo $link . "_payments&amp;confirmed=1&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                        
                            <a href="<?php echo $link . "_payments&amp;confirmed=" . $confirmed; ?>"><?php echo ucwords(JText::_("JALL")); ?></a>
                            
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
    
                <th><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->payments); ?>')" /></th>
        
                <th style="text-align: left;"><?php echo JText::_("AMOUNT"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("PAYMENT_METHOD"); ?></th>
                
        		<?php if (!$affiliateID) { ?>
                
                	<th style="text-align: left;"><?php echo JText::_("AFFILIATE_ID"); ?></th>
        
        		<?php } ?>
    			
                <?php if (!$confirmed) { ?>
                
                	<th style="text-align: left;"><?php echo JText::_("CONFIRMED"); ?></th>
        
        		<?php } ?>
                
                <th style="text-align: left;"><?php echo JText::_("DATE"); ?></th>
                
            </tr>
    
        </thead>
    
        <tbody>
        
        <?php

			if (count($this->payments) > 0) {
				
				$i = 0;

    			$k = 0;
				
				foreach ($this->payments as $key => $payment) {

					// process data
			
					$checked			= JHTML::_('grid.id', 	$i, 		$payment->payment_id);
			
					if (!$confirmed) {
				
						$confirmedStatus = $payment->status == "C" ?	"yes" 			: ($payment->status == "P"	? "pending"	: "no");
																									
						$confirmedText	 = $payment->status == "C" ? JText::_("JYES")	: JText::_("JNO");
																									
						$confirmedIcon	 = "<img src=\"" . $vmaHelper->_website . "components/com_affiliate/views/panel/tmpl/images/status_" . $confirmedStatus . ".png" . "\" 
						
												 alt=\"" . $confirmedText 	. "\" />";
		
					}
			
					?>

		    		<tr class="row<?php echo $k ; ?>">

						<!-- add checkbox -->
                        
						<td style="text-align: center;"><?php echo $checked; ?></td>
                        
                        <!-- add amount  -->
                        
                        <td><?php echo $vmaHelper->formatAmount($payment->amount); ?></td>
						
                        <!-- add payment method -->
                        
                        <td><?php echo $payment->method ? $payment->method : "N/A"; ?></td>
                        
                        <!-- add affiliate id -->
                        
                        <?php if (!$affiliateID) { ?>
                        
                        <td><?php echo $payment->affiliate_id . 
				
									   ($payment->name ? 
										
									   " (<a href=\"" . $link . "_payments&amp;affiliate_id=" . $payment->affiliate_id . "&amp;confirmed=" . $confirmed . "\">" . 
										
									   $payment->name 	. "</a>)" : 
										
									   NULL); ?></td>
                        
                        <?php } ?>
                              
                        <!-- add confirmed status -->
			
            			<?php if (!$confirmed) { ?>
                        
                        <td><?php echo $confirmedIcon; ?></td>
                        
                        <?php } ?>
                        
                        <!-- add date -->
                        
                        <td><?php echo $payment->date; ?></td>
                        
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

	<input type="hidden" name="view" 			value="vma_payments"				/>

	<input type="hidden" name="boxchecked" 		value="0"							/>
    
    <input type="hidden" name="confirmed" 		value="<?php echo $confirmed; ?>" 	/>
    
    <input type="hidden" name="affiliate_id" 	value="<?php echo $affiliateID; ?>" />

	<?php echo JHTML::_('form.token'); ?>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>