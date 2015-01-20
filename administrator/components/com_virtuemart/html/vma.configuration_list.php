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

global $mainframe, $ps_vma;

// redirect to the proper page

$mainframe->redirect($ps_vma->getAdminLink(false, true) . "page=vma.summary");

?>