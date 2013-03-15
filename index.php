<?php
include "header.php";
global $xoTheme, $xoopsTpl;
global $wikiPage;

// this exists to make xoops comments work
// index.php?page_id=15&com_mode=thread&com_order=0&com_id=2&com_rootid=1
if(isset($_GET['page_id']) && !isset($_GET['page'])) {
	$page_id=$_GET['page_id'];
	$page=$wikiPage->getKeywordById($page_id);
	if(!empty($page)) {
		$extra='';
		if(isset($_GET['com_mode'])) $extra.='&com_mode='.$_GET['com_mode'];
		if(isset($_GET['com_order'])) $extra.='&com_order='.$_GET['com_order'];
		if(isset($_GET['com_id'])) $extra.='&com_id='.$_GET['com_id'];
		if(isset($_GET['com_rootid'])) $extra.='&com_rootid='.$_GET['com_rootid'];
		$header=sprintf($xoopsModuleConfig['wikilink_template'],$page).$extra;
		header("Location: {$header}");
		exit;
	}
}
// $_GET variables we use
$page = $wikiPage->normalizeKeyword((isset($_GET['page']))?cleaner($_GET['page']):$wikiPage->wikiHomePage);
$highlight = isset($_GET['query'])?cleaner($_GET['query']):null;

// if we get a naked or external prefix, try and do something useful
$pfx=$wikiPage->getPrefix($page);
if ($pfx) {
	$page=$pfx['actual_page'];
	if($pfx['prefix_is_external']) {
		header("Location: {$pfx['actual_page']}");
		exit;
	}
}

	global $wikiPage;
	$pageX = $wikiPage->getPage($page);
	$attachments=$wikiPage->getAttachments($page);
	$wikiPage->registerHit($page);
	$mayEdit = $wikiPage->checkEdit();

	if($pageX) {
		$pageX['body']=$wikiPage->renderPage($wikiPage->body);
		$pageX['mayEdit'] = $mayEdit;
		$pageX['pageFound'] = true;
		if(!empty($highlight)) $pageX['body'] = $wikiPage->highlightWords($highlight);
	}
	else {
		$dir=$wikiPage->getWikiDir();
		if ($mayEdit) redirect_header(XOOPS_URL."/modules/{$dir}/edit.php?page={$page}", 2, _MD_GWIKI_PAGENOTFOUND);
		$pageX=array();
		$pageX['keyword']=$page;
		$pageX['title']=_MD_GWIKI_NOEDIT_NOTFOUND_TITLE;
		$pageX['body']=_MD_GWIKI_NOEDIT_NOTFOUND_BODY;
		$pageX['author']='';
		$pageX['revisiontime']='';
		$pageX['createdtime']='';
		$pageX['mayEdit'] = $mayEdit;
		$pageX['pageFound'] = false;
	}

	$dir = basename( dirname( __FILE__ ) ) ;
	$pageX['moddir']  = $dir;
	$pageX['modpath'] = XOOPS_ROOT_PATH .'/modules/' . $dir;
	$pageX['modurl']  = XOOPS_URL .'/modules/' . $dir;
	if(!empty($attachments)) $pageX['attachments']  = prepOut($attachments);


	$xoopsOption['template_main'] = $wikiPage->getTemplateName(); // 'gwiki_view.html';
	include XOOPS_ROOT_PATH."/header.php";

	$pageX['title']=prepOut($pageX['title']);
	$xoopsTpl->assign('gwiki', $pageX);
	
	//echo '<pre>';print_r($pageX);echo '</pre>';

	$xoTheme->addStylesheet(XOOPS_URL.'/modules/gwiki/module.css');
	if($pageX['pageFound']) {
		$xoTheme->addMeta('meta','keywords',htmlspecialchars($pageX['meta_keywords'], ENT_QUOTES,null,false));
		$xoTheme->addMeta('meta','description',htmlspecialchars($pageX['meta_description'], ENT_QUOTES,null,false));
	}
	$title=$pageX['title'];
	if(empty($title)) $title=$myts->htmlSpecialChars($xoopsModule->name());
	$xoopsTpl->assign('xoops_pagetitle', $title);
	$xoopsTpl->assign('icms_pagetitle', $title);
	if(!empty($message)) $xoopsTpl->assign('message', $message);
	if(!empty($err_message)) $xoopsTpl->assign('err_message', $err_message);
	
	$_GET['page_id']=$wikiPage->page_id;

	include XOOPS_ROOT_PATH.'/include/comment_view.php';

include XOOPS_ROOT_PATH."/footer.php";
?>
