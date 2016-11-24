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
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * @param XoopsModule $module
 *
 * @return bool
 */
function xoops_module_install_gwiki(XoopsModule $module)
{
    // global $xoopsDB,$xoopsConfig;

    // TODO - create uploads dirs?
    // TODO - Install a home page, docs?
    return true;
}
