<?php
include '../../mainfile.php';
$xoopsLogger->activated = false;

function cleaner($string) {
	$string=stripcslashes($string);
	$string=html_entity_decode($string);
	$string=strip_tags($string); // DANGER -- kills wiki text
	$string=trim($string);
	$string=stripslashes($string);
	return $string;
}

// $_GET variables we use
unset($page,$bid,$id);
$page = isset($_GET['page'])?cleaner($_GET['page']):null;
if (isset($_GET['bid'])) $bid=intval($_GET['bid']); // from a block
if (isset($_GET['id'])) $id=intval($_GET['id']);    // from utility (i.e. history)

	$dir = basename( dirname( __FILE__ ) ) ;
	// Access module configs from block:
	$module_handler =& xoops_gethandler('module');
	$module         =& $module_handler->getByDirname($dir);
	$config_handler =& xoops_gethandler('config');
	$moduleConfig   =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));

	include_once XOOPS_ROOT_PATH.'/modules/'.$dir.'/classes/gwikiPage.php';

	$wikiPage = new gwikiPage;
	$wikiPage->setRecentCount($moduleConfig['number_recent']);

	if(empty($page)) $page = $wikiPage->wikiHomePage;

	if(isset($id)) $thispage=$wikiPage->getPage($page,$id);
	else $thispage=$wikiPage->getPage($page);
	if ($thispage) {
		if(!defined('_MI_GWIKI_NAME')) {
			$langfile=XOOPS_ROOT_PATH.'/modules/'.$dir.'/language/'.$xoopsConfig['language'].'/modinfo.php';
			if (!file_exists($langfile)) {
				$langfile=XOOPS_ROOT_PATH.'/modules/'.$dir.'/language/english/modinfo.php';
			}
			include_once $langfile;
		}
		if(isset($id)) {
			$wikiPage->setWikiLinkURL("javascript:alert('%s');");
			$wikiPage->setTocFormat('toc'.$id.'-','#%s');
		}
		if(isset($bid)) {
			$wikiPage->setWikiLinkURL("javascript:ajaxGwikiLoad('%s','{$bid}');");
			$wikiPage->setTocFormat('toc'.$bid.'-','#%s');
		}
		$rendered = '<h1 class="wikititle">'.htmlspecialchars($wikiPage->title).'</h1>';
		$rendered.=$wikiPage->renderPage();
	} else {
		//if ($mayEdit) redirect_header("edit.php?page=$page", 2, _MD_GWIKI_PAGENOTFOUND);
		$rendered = '<h1 class="wikititle">'._MD_GWIKI_NOEDIT_NOTFOUND_TITLE.'</h1>';
		$rendered.= _MD_GWIKI_NOEDIT_NOTFOUND_BODY;
	}
	echo $rendered;
	exit;
?>