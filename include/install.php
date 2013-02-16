<?php
/**
* install.php - initializations on module installation
*
* @copyright  Copyright © 2012 geekwright, LLC. All rights reserved. 
* @license    qr/docs/license.txt  GNU General Public License (GPL)
* @since      1.2
* @author     Richard Griffith <richard@geekwright.com>
* @package    qr
* @version    $Id$
*/

if (!defined("XOOPS_ROOT_PATH"))  die("Root path not defined");

function xoops_module_install_gwiki(&$module) {
// global $xoopsDB,$xoopsConfig;

	// TODO - create uploads dirs?
	// TODO - Install a home page, docs?
	
	// $module->setErrors("Install Post-Process Completed");
	return true;
}

?>