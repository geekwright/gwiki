<?php
/**
 * admin/header.php - preamble for all admin pages
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 */

include dirname(dirname(dirname(__DIR__))) . '/include/cp_header.php';

if (is_object($GLOBALS['xoops'])) {
    if (file_exists($GLOBALS['xoops']->path('Frameworks/moduleclasses/moduleadmin/moduleadmin.php'))) {
        include_once $GLOBALS['xoops']->path('Frameworks/moduleclasses/moduleadmin/moduleadmin.php');
    }
}

//if ( !@include_once XOOPS_ROOT_PATH . "/modules/" . $xoopsModule->getVar("dirname") . "/language/" . $xoopsConfig['language'] . "/main.php") {
//    include_once XOOPS_ROOT_PATH . "/modules/" . $xoopsModule->getVar("dirname") . "/language/english/main.php" ;
//}

if (!defined('_MI_GWIKI_NAME')) { // if modinfo isn't loaded, do it
    if (!@include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/modinfo.php') {
        include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/english/modinfo.php';
    }
}

include __DIR__ . '/functions.php';

xoops_cp_header();

$moduleAdmin = new ModuleAdmin();
