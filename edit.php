<?php
/**
* edit.php - edit a wiki page
*
* @copyright  Copyright © 2013 geekwright, LLC. All rights reserved. 
* @license    gwiki/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/
include "header.php";
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';

global $xoTheme, $xoopsTpl;

// $_GET and $_POST variables we use
if (isset($_GET['page']))  $page = cleaner($_GET['page']);
if (isset($_POST['page'])) $page = cleaner($_POST['page']);

if(isset($_GET['op'])) $op = strtolower(cleaner($_GET['op']));
if(isset($_POST['op'])) $op = strtolower(cleaner($_POST['op']));
if(empty($op) || ($op!='preview' && $op!='edit' && $op!='insert')) $op = "edit"; // get a valid op

// namespace id (prefix_id) is set by newpage block, turn it into a full page name
if (isset($_GET['nsid'])) {
	$nsid=intval($_GET['nsid']);
	if($nsid>=0) {
		$pfx=getPrefixFromId($nsid);
		if(empty($page)) {
			if($pfx['prefix_auto_name']) $page=date('Y-m-d-His'); // TODO should this be a config item?
			else $page=$pfx['prefix_home'];
		}
		$page=$pfx['prefix'].':'.$page;
	}
}
if(empty($page)) $page=$wikiPage->wikiHomePage;

$normpage=$wikiPage->normalizeKeyword($page);
if ($normpage==_MI_GWIKI_WIKI404 && strcasecmp($page,_MI_GWIKI_WIKI404)!=0) redirect_header("index.php?page=$page", 2, _MI_GWIKI_WIKI404);
else $page=$normpage;

$id=0; $uid=0; $title=''; $body=''; $display_keyword='';
$parent_page=''; $page_set_home=''; $page_set_order=0; 
$meta_description=''; $meta_keywords=''; $show_in_index=1; $leave_inactive=0;

if(isset($_GET['id'])) $id = intval($_GET['id']); // post value will override
// $_POST variables we use
if(isset($_POST['id'])) $id = intval($_POST['id']);
if(isset($_POST['uid'])) $uid = intval($_POST['uid']);
if(isset($_POST['title'])) $title = cleaner($_POST['title']);
if(isset($_POST['body'])) $body = cleaner($_POST['body'],false);
if(isset($_POST['display_keyword'])) $display_keyword = cleaner($_POST['display_keyword']);
if(isset($_POST['parent_page'])) $parent_page = cleaner($_POST['parent_page']);
if(isset($_POST['page_set_home'])) $page_set_home = cleaner($_POST['page_set_home']);
if(isset($_POST['page_set_order'])) $page_set_order = intval($_POST['page_set_order']);
if(isset($_POST['meta_description'])) $meta_description = cleaner($_POST['meta_description']);
if(isset($_POST['meta_keywords'])) $meta_keywords = cleaner($_POST['meta_keywords']);
if(isset($_POST['show_in_index'])) $show_in_index = intval($_POST['show_in_index']);
if(isset($_POST['leave_inactive'])) $leave_inactive = intval($_POST['leave_inactive']);


	global $wikiPage;
	$pageX = $wikiPage->getPage($page,($id==0?null:$id));
	$mayEdit = $wikiPage->checkEdit();
	
	if($pageX) {
		$pageX['author'] = $wikiPage->getUserName($wikiPage->uid);
		$pageX['revisiontime']=date($wikiPage->dateFormat,$pageX['lastmodified']);
		$pageX['mayEdit'] = $mayEdit;
		$pageX['pageFound'] = true;
		if(!empty($highlight)) $pageX['body'] = $wikiPage->highlightWords($highlight);
	}
	else {
		$pageX=array();
		$uid = ($xoopsUser)?$xoopsUser->getVar('uid'):0;
		$pageX['uid']=$uid;
		$pageX['author']=$wikiPage->getUserName($uid);
		$pageX['revisiontime']=date($wikiPage->dateFormat);
		$pageX['mayEdit'] = $mayEdit;
		$pageX['keyword'] = $page;
		$pageX['pageFound'] = false;
	}
	$dir = basename( dirname( __FILE__ ) ) ;
	$pageX['moddir']  = $dir;
	$pageX['modpath'] = XOOPS_ROOT_PATH .'/modules/' . $dir;
	$pageX['modurl']  = XOOPS_URL .'/modules/' . $dir;
	$pageX['ineditor']  = true;
	$pageX['imglib'] = $wikiPage->getImageLib($page);

	if (!$mayEdit) {
		$err_message=_MD_GWIKI_NO_PAGE_PERMISSION;
		redirect_header("index.php?page=$page", 2, $err_message);
		exit();
	}

	if ($wikiPage->admin_lock) {
		redirect_header("index.php?page=$page", 2, _MD_GWIKI_PAGE_IS_LOCKED);
		exit();
	}

	if (($op == "insert")) {
		// check if this page was updated elsewhere while we were editing
		// if so, we save it, but don't make it the active revision
		if (intval($id) == $wikiPage->getCurrentId($page)) {
			$forced_inactive=false;
		} else {
			$leave_inactive=true; $forced_inactive=true;
		}
		$wikiPage->keyword=$page;
		$wikiPage->title=$title;
		$wikiPage->display_keyword=$display_keyword;
		$wikiPage->body=$body;
		$wikiPage->uid=$uid;

		$wikiPage->parent_page=$parent_page;
		$wikiPage->page_set_home=$page_set_home;
		$wikiPage->page_set_order=$page_set_order;
		$wikiPage->meta_description=$meta_description;
		$wikiPage->meta_keywords=$meta_keywords;
		$wikiPage->show_in_index=$show_in_index;

		$success = $wikiPage->addRevision($leave_inactive);

		if($success) {
			if($forced_inactive) {
				$err_message= _MD_GWIKI_EDITCONFLICT;
				$op='edit';
				$id=$success;
			}
			else {
				if($leave_inactive) $message= _MD_GWIKI_SAVED_INACTIVE;
				else $message= _MD_GWIKI_DBUPDATED;
				$op='';
				redirect_header("index.php?page=$page", 2,$message);
				exit();
			}
		} else {
			$err_message= _MD_GWIKI_ERRORINSERT;
			$op='edit';
		}
	}


$pagestatmessage='';
$pagechanged='';
$result=false;
if (($op == "preview") && isset($id)) {
    $result=intval($id);
    $pagestatmessage=_MD_GWIKI_PAGENOTSAVED;
	$pagechanged='yes';
} else {
	//print_r($pageX);
	if($pageX['pageFound']) {
		$result=true;
	} else {
		$result=false;
		$pagestatmessage=_MD_GWIKI_PAGENOTFOUND;
		$op = "edit";
		$pageX['keyword'] = $page;
//		$pageX['pageFound'] = true; // not really, but used in template only from here on
	}

	$gwiki_id=$wikiPage->gwiki_id;
	$keyword=$wikiPage->keyword;
	$display_keyword=$wikiPage->display_keyword;
	$title=$wikiPage->title;
	$body=$wikiPage->body;
	$parent_page=$wikiPage->parent_page;
	$page_set_home=$wikiPage->page_set_home;
	$page_set_order=$wikiPage->page_set_order;
	$meta_description=$wikiPage->meta_description;
	$meta_keywords=$wikiPage->meta_keywords;
	$show_in_index=$wikiPage->show_in_index;
	$lastmodified=$wikiPage->lastmodified;
	$uid=$wikiPage->uid;
	$admin_lock=$wikiPage->admin_lock;
	$active=$wikiPage->active;
}

switch ($op) {
case "edit":
case "preview":
//case "images":
	$xoopsOption['template_main'] = 'gwiki_edit.html';
	include XOOPS_ROOT_PATH."/header.php";
    
	$title = prepOut($title); // we need title ready to display in several places
	if ($op == "preview") {
		$pageX['keyword']=$page;
		$pageX['title']=$title;
		$pageX['body']=$wikiPage->renderPage($body);
		$pageX['preview'] = true;
	}
	else {
		unset($pageX['title']);
		unset($pageX['body']);
		$pageX['preview'] = false;
	}

	$uid = ($xoopsUser)?$xoopsUser->getVar('uid'):0;
    
	$form = new XoopsThemeForm(_MD_GWIKI_EDITPAGE.": $page", "gwikiform", "edit.php?page=$page");
    
	if(empty($display_keyword)) $display_keyword=$page;
    
	$form->addElement(new XoopsFormHidden('op', 'insert'));
	$form->addElement(new XoopsFormHidden('page', $page));
	$form->addElement(new XoopsFormHidden('id', $wikiPage->getCurrentId($page)));
	$form->addElement(new XoopsFormHidden('uid', $uid));
	$form->addElement(new XoopsFormHidden('pagechanged', $pagechanged));
       
	$form->addElement(new XoopsFormText(_MD_GWIKI_TITLE, "title", 40, 250, $title));
	$form->addElement(new XoopsFormLabel('', '', 'gwikieditbuttons')); // edit buttons added in template
	
	$form_edit_body=new XoopsFormTextArea(_MD_GWIKI_BODY, 'body', htmlspecialchars($body), 20, 80);
	$form_edit_body->setExtra("onclick='setWikiChanged();'");
	$form->addElement($form_edit_body);

	$btn_tray = new XoopsFormElementTray('', ' ','gwikiformpage1');
	$submit_btn = new XoopsFormButton("", "submit", _MD_GWIKI_SUBMIT, "submit");
	$submit_btn->setExtra("onclick='prepForSubmit();'");
	$btn_tray->addElement($submit_btn);

	$metadata_btn = new XoopsFormButton("", "metaedit", _MD_GWIKI_EDIT_SHOW_META, "button");
	$metadata_btn->setExtra("onclick=".
	"'var ele = document.getElementById(\"gwikiformmetaedit\"); ele.style.display = \"inherit\";".
	" var ele2 = document.getElementById(\"gwikiformbodyedit\"); ele2.style.display = \"none\";'");
	$btn_tray->addElement($metadata_btn);
  
	$preview_btn = new XoopsFormButton("", "preview", _PREVIEW, "button");
	$preview_btn->setExtra("onclick='prepForPreview();'");
	$btn_tray->addElement($preview_btn);
    
	$cancel_btn = new XoopsFormButton("", "cancel", _CANCEL, "button");
	$cancel_btn->setExtra("onclick='".(($op == "edit")?"history.back();":"document.location.href=\"index.php".(($result)?"?page=$page":"")."\";")."'");
	$btn_tray->addElement($cancel_btn);

	$btn_tray->addElement(new XoopsFormLabel("", " - <strong>{$pagestatmessage}</strong>"));
        
	$form->addElement($btn_tray);

	$form->addElement(new XoopsFormText(_MD_GWIKI_DISPLAY_KEYWORD, "display_keyword", 40, 250, htmlspecialchars($display_keyword)));
	$form->addElement(new XoopsFormText(_MD_GWIKI_PARENT_PAGE, "parent_page", 40, 250, htmlspecialchars($parent_page)));
	$form->addElement(new XoopsFormText(_MD_GWIKI_PAGE_SET_HOME, "page_set_home", 40, 250, htmlspecialchars($page_set_home)));
	$form->addElement(new XoopsFormText(_MD_GWIKI_PAGE_SET_ORDER, "page_set_order", 4, 10, htmlspecialchars($page_set_order)));
	$form->addElement(new XoopsFormText(_MD_GWIKI_META_KEYWORDS, "meta_keywords", 80, 500, htmlspecialchars($meta_keywords)));
	$form->addElement(new XoopsFormTextArea(_MD_GWIKI_META_DESCRIPTION, 'meta_description', htmlspecialchars($meta_description), 6, 80));
	$form->addElement(new XoopsFormRadioYN(_MD_GWIKI_SHOW_IN_INDEX, "show_in_index", intval($show_in_index)));
	$form->addElement(new XoopsFormRadioYN(_MD_GWIKI_LEAVE_INACTIVE, "leave_inactive", intval($leave_inactive)));
	$btn_tray2 = new XoopsFormElementTray('', ' ','gwikiformpage2');
	$btn_tray2->addElement(new XoopsFormButton("", "submit2", _MD_GWIKI_SUBMIT, "submit"));

	$bodydata_btn = new XoopsFormButton("", "bodyedit", _MD_GWIKI_EDIT_SHOW_BODY, "button");
	$bodydata_btn->setExtra("onclick=".
	"'var ele = document.getElementById(\"gwikiformmetaedit\"); ele.style.display = \"none\"; ".
	" var ele2 = document.getElementById(\"gwikiformbodyedit\"); ele2.style.display = \"inherit\";'");
	$btn_tray2->addElement($bodydata_btn);
    
	$preview_btn2 = new XoopsFormButton("", "preview2", _PREVIEW, "button");
	$preview_btn2->setExtra("onclick='document.forms.gwikiform.op.value=\"preview\"; document.forms.gwikiform.action=document.forms.gwikiform.action+\"#wikipage\"; document.forms.gwikiform.submit.click();'");
	$btn_tray2->addElement($preview_btn2);
    
	$cancel_btn2 = new XoopsFormButton("", "cancel2", _CANCEL, "button");
	$cancel_btn2->setExtra("onclick='".(($op == "edit")?"history.back();":"document.location.href=\"index.php".(($result)?"?page=$page":"")."\";")."'");
	$btn_tray2->addElement($cancel_btn2);

	$btn_tray2->addElement(new XoopsFormLabel("", " - <strong>{$pagestatmessage}</strong>"));
        
	$form->addElement($btn_tray2);

    $form->assign($xoopsTpl);
    $xoopsTpl->assign('gwiki', $pageX);
    break;

}


$xoTheme->addStylesheet(XOOPS_URL.'/modules/gwiki/module.css');
if(empty($title)) $title=$xoopsModule->name();
$xoopsTpl->assign('xoops_pagetitle', $title);
$xoopsTpl->assign('icms_pagetitle', $title);
if(!empty($message)) $xoopsTpl->assign('message', htmlspecialchars($message));
if(!empty($err_message)) $xoopsTpl->assign('err_message', htmlspecialchars($err_message));

include XOOPS_ROOT_PATH."/footer.php";
?>
