<?php
	trigger_error("Clean Invoked");
include '../../mainfile.php';
	if(empty($_POST['check'])) { // this is set by the admin page option, not by a regular call
		$xoopsOption['template_main'] = 'gwiki_view.html';
		include XOOPS_ROOT_PATH."/header.php";
		do_clean();
		include XOOPS_ROOT_PATH."/footer.php";
	}
	else {
		$xoopsLogger->activated = false;
		do_clean();
		exit;
	}

function do_clean() {
global $xoopsDB;

	$dir = basename( dirname( __FILE__ ) ) ;
	// Access module configs from block:
	$module_handler = xoops_gethandler('module');
	$module         = $module_handler->getByDirname($dir);
	$config_handler = xoops_gethandler('config');
	$moduleConfig   = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

	$retaindays=intval($moduleConfig['retain_days']);
	if($retaindays<=0) return;

	$lastmodifiedbefore=time()-($retaindays * 24 * 3600);
	$sql = 'DELETE FROM '.$xoopsDB->prefix('gwiki_pages')." WHERE active = 0 AND lastmodified< $lastmodifiedbefore";
	$result = $xoopsDB->queryF($sql);
	$cnt=$xoopsDB->getAffectedRows();
	if($cnt>0) {
		$sql = 'DELETE FROM '.$xoopsDB->prefix('gwiki_pageids').' WHERE keyword NOT IN (SELECT keyword from '.$xoopsDB->prefix('gwiki_pages').')';
		$result = $xoopsDB->queryF($sql);
		$sql = 'OPTIMIZE TABLE '.$xoopsDB->prefix('gwiki_pages');
		$result = $xoopsDB->queryF($sql);
	}
}
?>