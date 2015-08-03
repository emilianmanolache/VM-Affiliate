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
 
// require the base controller
 
require_once( JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controller.php' );
 
// require specific controller if requested

if ($controller = JRequest::getWord('controller')) {
	
    $path 		= JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller . '.php';
	
    if (file_exists($path)) {
		
        require_once $path;
		
    } 
	
	else {
		
        $controller = '';
		
    }
	
}
 
// create the controller

$classname    = 'AffiliateController' . $controller;

$controller   = new $classname( );
 
// perform the request task

$controller->execute( JRequest::getWord( 'task' ) );
 
// redirect if set by the controller

$controller->redirect();