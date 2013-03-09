<?php
include "header.php";
global $xoTheme, $xoopsTpl;
global $wikiPage;

// $_GET variables we use
$page = $wikiPage->normalizeKeyword((isset($_GET['page']))?cleaner($_GET['page']):$wikiPage->wikiHomePage);
$highlight = isset($_GET['query'])?cleaner($_GET['query']):null;

global $wikiPage, $xoopsDB, $xoopsModuleConfig;
$pageX = $wikiPage->getPage($page);
$mayEdit = $wikiPage->checkEdit();
if ($wikiPage->admin_lock) {
	if($mayEdit) $message=_MD_GWIKI_PAGE_IS_LOCKED;
	$mayEdit=false;
}

if($mayEdit) {
	// $_POST variable for restore operation
	if (isset($_POST['page']) && isset($_POST['id']) && isset($_POST['op']) && $_POST['op']=='restore') {
		$page=cleaner($_POST['page']);
		$id=intval($_POST['id']);
		if($id) setRevision($page,$id);
		$message=_MD_GWIKI_RESTORED;
        	redirect_header("history.php?page=$page", 2, $message);
	}
}

function setRevision($page, $id)
{
    global $xoopsDB;
    
    $sql = "UPDATE ".$xoopsDB->prefix('gwiki_pages')." SET active = 0 WHERE keyword='{$page}' and active = 1 ";
    $result=$xoopsDB->query($sql);
    if($result) {
      $sql = "UPDATE ".$xoopsDB->prefix('gwiki_pages')." SET active = 1 WHERE keyword='{$page}' AND gwiki_id='{$id}'";
      $result=$xoopsDB->query($sql);
    }

    return $result;
}

	if($pageX) {
		$pageX['author'] = $wikiPage->getUserName($wikiPage->uid);
		$pageX['revisiontime']=date($wikiPage->dateFormat,$pageX['lastmodified']);
		$pageX['mayEdit'] = $mayEdit;
		$pageX['pageFound'] = true;
	}
	else {
		if (!$mayEdit) redirect_header("index.php?page=$page", 2, _MD_GWIKI_PAGENOTFOUND);
		$pageX=array();
		$pageX['author']='';
		$pageX['revisiontime']='';
		$pageX['mayEdit'] = $mayEdit;
		$pageX['pageFound'] = false;
	}

	$dir = basename( dirname( __FILE__ ) ) ;
	$pageX['moddir']  = $dir;
	$pageX['modpath'] = XOOPS_ROOT_PATH .'/modules/' . $dir;
	$pageX['modurl']  = XOOPS_URL .'/modules/' . $dir;

	$dir = basename( dirname ( __FILE__ ) ) ;
	loadLanguage('admin',$dir); // borrow the admin strings
 
//    allowRestoration($page);
    
	$sql = 'SELECT * FROM '.$xoopsDB->prefix('gwiki_pages')." WHERE keyword='{$page}' ORDER BY lastmodified DESC";
	$result = $xoopsDB->query($sql);
    
	$history=false;
	if($result) {
		$history=array();
		while ($row = $xoopsDB->fetchArray($result)) {
			if(empty($row['title'])) $row['title']= _MD_GWIKI_EMPTY_TITLE;
			prepOut($row);
			$row['revisiontime']=date($wikiPage->dateFormat,$row['lastmodified']);
			$row['username']=$wikiPage->getUserName($row['uid']);
			$history[]=$row;
//			echo '<td><a href="pages.php?page='.$page.'&op=display&id='.$id.'">'._AD_GWIKI_VIEW.'</a> | <a href="javascript:restoreRevision(\''.$id.'\');">'._AD_GWIKI_RESTORE.'</a> | <a href="pages.php?page='.$page.'&op=fix&id='.$id.'">'._AD_GWIKI_FIX.'</a></td></tr>';
		}
	}

	$pageX['moddir']  = $dir;
	$pageX['modpath'] = XOOPS_ROOT_PATH .'/modules/' . $dir;
	$pageX['modurl']  = XOOPS_URL .'/modules/' . $dir;


	$xoopsOption['template_main'] = 'gwiki_history.html';
	include XOOPS_ROOT_PATH."/header.php";

	$xoopsTpl->assign('gwiki', $pageX);
	$xoopsTpl->assign('history', $history);

	$xoTheme->addStylesheet(XOOPS_URL.'/modules/'.$dir.'/module.css');
	$title=_MD_GWIKI_HISTORY_TITLE;
	$xoopsTpl->assign('title', $title.' : '.$page);
	if(empty($title)) $title=$myts->htmlSpecialChars($xoopsModule->name());
	$xoopsTpl->assign('xoops_pagetitle', $title);
	$xoopsTpl->assign('icms_pagetitle', $title);
	if(!empty($message)) $xoopsTpl->assign('message', $message);
	if(!empty($err_message)) $xoopsTpl->assign('err_message', $err_message);

include XOOPS_ROOT_PATH."/footer.php";
?>
