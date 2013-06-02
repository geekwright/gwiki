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
* @version    $Id$
*/
if (!defined("XOOPS_ROOT_PATH"))  die("Root path not defined");
$dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
include_once XOOPS_ROOT_PATH.'/modules/'.$dir.'/classes/gwikiPage.php';
global $wikiPage;
$wikiPage = new gwikiPage;
$wikiPage->setRecentCount($xoopsModuleConfig['number_recent']);


function cleaner($string,$trim=true) {
	if (get_magic_quotes_gpc()) $string=stripslashes($string);
//	$string=stripcslashes($string);
	$string=html_entity_decode($string);
//	$string=strip_tags($string); // DANGER -- kills wiki text
	if($trim) $string=trim($string);
//	$string=stripslashes($string);
	return $string;
}

function loadLanguage($name, $domain = '',$language = null)
{
global $xoopsConfig;
	if ( !@include_once XOOPS_ROOT_PATH . "/modules/{$domain}/language/" . $xoopsConfig['language'] . "/{$name}.php") {
		include_once XOOPS_ROOT_PATH . "/modules/{$domain}/language/english/{$name}.php" ;
	}
}

function prepOut(&$var)
{
	if(is_array($var)) {
		foreach($var as $i => $v) $var[$i]=prepOut($v);
	} else {
		if(is_string($var)) $var=htmlspecialchars($var);
	}
	return $var;
}
?>
