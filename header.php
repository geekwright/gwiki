<?php
/**
 * header.php - common startup for most scripts
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 * @version    $Id$
 */

include dirname(dirname(__DIR__)) . '/mainfile.php';
include_once "include/functions.php";

if (file_exists('language/' . $xoopsConfig['language'] . '/modinfo.php')) {
    include_once 'language/' . $xoopsConfig['language'] . '/modinfo.php';
} else {
    include_once 'language/english/modinfo.php';
}
