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

// define statuses

$pendingStatuses		= $vmaHelper->_pendingStatuses;

$unconfirmedStatuses 	= $vmaHelper->_cancelledStatuses;
		
$confirmedStatuses		= $vmaHelper->_confirmedStatuses;
		
// get initial variables

$paid			= &JRequest::getVar("paid",			1);

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

JToolBarHelper::title($affiliateName ? JText::_("SALES_FOR") . " " . $affiliateName : JText::_("SALES"), 'head adminSalesIcon');

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
                    
                			<a href="<?php echo $link . "_sales&amp;paid=1&amp;confirmed=" . $confirmed . "&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                    
                			<a href="<?php echo $link . "_sales&amp;paid=0&amp;confirmed=" . $confirmed . "&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                    
                			<a href="<?php echo $link . "_sales&amp;confirmed=0&amp;paid=" . $paid . "&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                    
                			<a href="<?php echo $link . "_sales&amp;confirmed=1&amp;paid=" . $paid . "&amp;affiliate_id=" . $affiliateID; ?>">
						
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
                        
                            <a href="<?php echo $link . "_sales&amp;confirmed=" . $confirmed . "&amp;paid=" . $paid; ?>"><?php echo ucwords(JText::_("JALL")); ?></a>
                            
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

    <div id="editcell">

    <table class="adminlist jgrid table table-striped" cellspacing="0" cellpadding="0">

        <thead>
    
            <tr>
    
                <th class="admin-checkbox"><input type="checkbox" name="toggle" value="" onclick="checkAll('<?php echo count($this->sales); ?>')" /></th>
        
                <th style="text-align: left;"><?php echo JText::_("ORDER_ID"); ?></th>
        
                <th style="text-align: left;"><?php echo JText::_("ORDER_SUBTOTAL"); ?></th>
        		
                <th style="text-align: left;"><?php echo JText::_("ORDER_TOTAL"); ?></th>
                
        		<?php if (!$affiliateID) { ?>
                
                	<th style="text-align: left;"><?php echo JText::_("AFFILIATE_ID"); ?></th>
        
        		<?php } ?>
    			
                <?php if (!$confirmed) { ?>
                
                	<th style="text-align: left;"><?php echo JText::_("CONFIRMED"); ?></th>
        
        		<?php } ?>
                
                <?php if ($paid) { ?>
                
                	<th style="text-align: left;"><?php echo JText::_("PAID"); ?></th>
        
        		<?php } ?>
                
                <th style="text-align: left;"><?php echo JText::_("DATE"); ?></th>
                
            </tr>
    
        </thead>
    
        <tbody>
        
        <?php

			if (count($this->sales) > 0) {
				
				$i = 0;

    			$k = 0;
				
				foreach ($this->sales as $key => $sale) {

					// process data
			
					$checked			= JHTML::_('grid.id', 	$i, 		$sale->aff_order_id);
			
					if (!$confirmed) {
				
						$confirmedStatus = in_array($sale->order_status, $confirmedStatuses) 	? 	"yes" 			: (
						
										   in_array($sale->order_status, $pendingStatuses)		? 	"pending"		: 
										 
																									"no");
																									
						$confirmedText	 = in_array($sale->order_status, $confirmedStatuses) 	? 	JText::_("JYES")	: JText::_("JNO");
																									
						$confirmedIcon	 = "<img src=\"" . $vmaHelper->_website 	. "components/com_affiliate/views/panel/tmpl/images/status_" . $confirmedStatus . ".png" . "\" 
						
												 alt=\"" . $confirmedText 	. "\" />";
		
					}
					
					if ($paid) {
						
						$paidStatus		 = $sale->paid ? "yes" : "pending";
																									
						$paidText		 = $sale->paid ? JText::_("JYES")	: JText::_("JNO");
																									
						$paidIcon		 = "<img src=\"" . $vmaHelper->_website 	. "components/com_affiliate/views/panel/tmpl/images/status_" . $paidStatus . ".png" . "\" 
						
												 alt=\"" . $paidText	 	. "\" />";
												 
					}
			
					?>

		    		<tr class="row<?php echo $k ; ?>">

						<!-- add checkbox -->
                        
						<td class="admin-checkbox"><?php echo $checked; ?></td>
                        
                        <!-- add order id  -->
                        
                        <td><?php echo "<a href=\"index.php?option=com_virtuemart&amp;view=orders&amp;task=edit&amp;virtuemart_order_id=" . $sale->order_id . "\">" . 
			
										$sale->order_id . 
										
										"</a>"; ?></td>
						
                        <!-- add order subtotal -->
                        
                        <td><?php echo $vmaHelper->formatAmount($sale->order_subtotal); ?></td>
                        
                        <!-- add order total -->
                        
                        <td><?php echo $vmaHelper->formatAmount($sale->order_total); ?></td>
                        
                        <!-- add affiliate id -->
                        
                        <?php if (!$affiliateID) { ?>
                        
                        <td><?php echo $sale->affiliate_id . 
				
									   ($sale->name ? 
										
									   " (<a href=\"" . $link . "_sales&amp;affiliate_id=" . $sale->affiliate_id . "&amp;paid=" . 
									   
									   $paid . "&amp;confirmed=" . $confirmed . "\">" . 
										
									   $sale->name 	. "</a>)" : 
										
									   NULL); ?></td>
                        
                        <?php } ?>
                              
                        <!-- add confirmed status -->
			
            			<?php if (!$confirmed) { ?>
                        
                        <td><?php echo $confirmedIcon; ?></td>
                        
                        <?php } ?>
                        
                        <!-- add paid status -->
                        
                        <?php if ($paid) { ?>
                        
                        <td><?php echo $paidIcon; ?></td>
                        
                        <?php } ?>
                        
                        <!-- add date -->
                        
                        <td><?php echo $sale->date; ?></td>
                        
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

    </div>

	<input type="hidden" name="task" 			value="" 							/>

	<input type="hidden" name="option" 			value="com_virtuemart"				/>

	<input type="hidden" name="view" 			value="vma_sales" 					/>

	<input type="hidden" name="boxchecked" 		value="0"							/>
    
    <input type="hidden" name="paid" 			value="<?php echo $paid; ?>" 		/>
    
    <input type="hidden" name="confirmed" 		value="<?php echo $confirmed; ?>" 	/>
    
    <input type="hidden" name="affiliate_id" 	value="<?php echo $affiliateID; ?>" />

	<?php echo JHTML::_('form.token'); ?>

</form>

<?php

// end the virtuemart administration area

AdminUIHelper::endAdminArea();

?>