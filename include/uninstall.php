<?php
/**
* uninstall.php - cleanup on module uninstall
*
* @copyright  Copyright © 2012 geekwright, LLC. All rights reserved. 
* @license    qr/docs/license.txt  GNU General Public License (GPL)
* @since      1.2
* @author     Richard Griffith <richard@geekwright.com>
* @package    qr
* @version    $Id$
*/

if (!defined("XOOPS_ROOT_PATH"))  die("Root path not defined");

function xoops_module_uninstall_gwiki(&$module) {
// global $xoopsDB,$xoopsConfig;

	// nothing to do yet
	//$module->setErrors("Uninstall Process Completed");
	return true;
}

?>