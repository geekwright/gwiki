<?php
if (!defined("XOOPS_ROOT_PATH"))  die("Root path not defined");
$dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
include_once XOOPS_ROOT_PATH.'/modules/'.$dir.'/classes/gwikiPage.php';
global $wikiPage;
$wikiPage = new gwikiPage;
$wikiPage->setRecentCount($xoopsModuleConfig['number_recent']);

//function makeKeyWord($keyword)
//{
//	global $wikiPage;
//	$x=$wikiPage->makeKeyword($keyword);
//	return $x;
//}

//function getCurrentId($page)
//{
//	global $wikiPage;
//	return $wikiPage->getCurrentId($page);
//}

//function addRevision($page, $title, $body, $uid)
//{
//	global $wikiPage;
//	$wikiPage->keyword=$page;
//	$wikiPage->title=$title;
//	$wikiPage->body=$body;
//	$wikiPage->uid=$uid;
//	return $wikiPage->addRevision();
//}

//function getPage($page)
//{
//	global $wikiPage;
//	return $wikiPage->getPage($page);
//}

//function wikiDisplay($body)
//{
//	global $wikiPage;
//	return $wikiPage->renderPage($body);
//}

//function normalizePageName($page)
//{
//	global $wikiPage;
//	return $wikiPage->normalizeKeyword($page);
//}

//function getUserName($uid)
//{
//	global $wikiPage;
//	return $wikiPage->getUserName($uid);
//}

function cleaner($string,$trim=true) {
	if (get_magic_quotes_gpc()) $string=stripslashes($string);
//	$string=stripcslashes($string);
	$string=html_entity_decode($string);
//	$string=strip_tags($string); // DANGER -- kills wiki text
	if($trim) $string=trim($string);
//	$string=stripslashes($string);
	return $string;
}

/**
* Check edit permissions for the current page
* @param mixed $keyword - wiki page name
* @since 1.0
*/
//function checkEdit()
//{
//	global $wikiPage;
//	return $wikiPage->checkEdit();
//}

function getPrefixFromId($pid)
{
global $xoopsDB;

	$sql = 'SELECT * FROM '.$xoopsDB->prefix('gwiki_prefix').' WHERE prefix_id ='.$pid;
	$result = $xoopsDB->query($sql);
	while($myrow = $xoopsDB->fetchArray($result)) {
		return $myrow;
	}
	return '';
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
