<?php
/**
* install.php - initializations on module installation
*
* This file is part of gwiki - geekwright wiki
*
* @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
* @license    gwiki/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");

/**
 * @param $module
 *
 * @return bool
 */
function xoops_module_install_gwiki(&$module) {
// global $xoopsDB,$xoopsConfig;

    // TODO - create uploads dirs?
    // TODO - Install a home page, docs?
        return true;
}
