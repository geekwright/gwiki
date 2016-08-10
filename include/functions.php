<?php
/**
 * include/functions.php - gwiki untility functions
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
$dir = basename(dirname(__DIR__));
include_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/gwikiPage.php';
global $wikiPage;
$wikiPage = new GwikiPage;
$wikiPage->setRecentCount($xoopsModuleConfig['number_recent']);

/**
 * @param      $string
 * @param bool $trim
 *
 * @return string
 */
function cleaner($string, $trim = true)
{
    //  $string=stripcslashes($string);
    $string = html_entity_decode($string);
    //  $string=strip_tags($string); // DANGER -- kills wiki text
    if ($trim) {
        $string = trim($string);
    }

    //  $string=stripslashes($string);
    return $string;
}

/**
 * @param        $name
 * @param string $domain
 * @param null   $language
 */
function loadLanguage($name, $domain = '', $language = null)
{
    global $xoopsConfig;
    if (!@include_once XOOPS_ROOT_PATH . "/modules/{$domain}/language/" . $xoopsConfig['language'] . "/{$name}.php") {
        include_once XOOPS_ROOT_PATH . "/modules/{$domain}/language/english/{$name}.php";
    }
}

/**
 * @param $var
 *
 * @return array|string
 */
function prepOut(&$var)
{
    if (is_array($var)) {
        foreach ($var as $i => $v) {
            $var[$i] = prepOut($v);
        }
    } else {
        if (is_string($var)) {
            $var = htmlspecialchars($var);
        }
    }

    return $var;
}
