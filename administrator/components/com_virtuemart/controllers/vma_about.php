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

// import the component controller library

if (!class_exists('VmController')) require(VMPATH_ADMIN . DS . 'helpers' . DS . 'vmcontroller.php');

/**
 * VM Affiliate backend controller
 */

class VirtuemartControllerVma_about extends VmController {

    /**
     * Method to display the view
     */

    public function __construct() {

        parent::__construct();

    }
	
}