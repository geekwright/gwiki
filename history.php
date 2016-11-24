<?php
/**
 * history.php - display/manage page revisions
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 */

include __DIR__ . '/header.php';
global $xoTheme, $xoopsTpl;
global $wikiPage;

// $_GET variables we use
$page      = $wikiPage->normalizeKeyword(isset($_GET['page']) ? cleaner($_GET['page']) : $wikiPage->wikiHomePage);
$highlight = isset($_GET['query']) ? cleaner($_GET['query']) : null;

global $wikiPage, $xoopsDB, $xoopsModuleConfig;
$pageX   = $wikiPage->getPage($page);
$mayEdit = $wikiPage->checkEdit();
if ($wikiPage->admin_lock) {
    if ($mayEdit) {
        $message = _MD_GWIKI_PAGE_IS_LOCKED;
    }
    $mayEdit = false;
}

if ($mayEdit) {
    // $_POST variable for restore operation
    if (isset($_POST['page']) && isset($_POST['id']) && isset($_POST['op']) && $_POST['op'] === 'restore') {
        $page = cleaner($_POST['page']);
        $id   = (int)$_POST['id'];
        if ($id) {
            $wikiPage->setRevision($page, $id);
        }
        $message = _MD_GWIKI_RESTORED;
        redirect_header("history.php?page=$page", 2, $message);
    }
}

if ($pageX) {
    $pageX['author']       = $wikiPage->getUserName($wikiPage->uid);
    $pageX['revisiontime'] = date($wikiPage->dateFormat, $pageX['lastmodified']);
    $pageX['mayEdit']      = $mayEdit;
    $pageX['pageFound']    = true;
    $_GET['page_id']       = $wikiPage->page_id;
    $_GET['nsid']          = $wikiPage->currentprefixid;
} else {
    if (!$mayEdit) {
        redirect_header("index.php?page=$page", 2, _MD_GWIKI_PAGENOTFOUND);
    }
    $pageX                 = array();
    $pageX['author']       = '';
    $pageX['revisiontime'] = '';
    $pageX['mayEdit']      = $mayEdit;
    $pageX['pageFound']    = false;
}

$dir              = basename(__DIR__);
$pageX['moddir']  = $dir;
$pageX['modpath'] = XOOPS_ROOT_PATH . '/modules/' . $dir;
$pageX['modurl']  = XOOPS_URL . '/modules/' . $dir;

//    $dir = basename(__DIR__) ;
loadLanguage('admin', $dir); // borrow the admin strings

//    allowRestoration($page);

$sql    = 'SELECT * FROM ' . $xoopsDB->prefix('gwiki_pages') . " WHERE keyword='{$page}' ORDER BY lastmodified DESC";
$result = $xoopsDB->query($sql);

$history = false;
if ($result) {
    $history = array();
    while ($row = $xoopsDB->fetchArray($result)) {
        if (empty($row['title'])) {
            $row['title'] = _MD_GWIKI_EMPTY_TITLE;
        }
        prepOut($row);
        $row['revisiontime'] = date($wikiPage->dateFormat, $row['lastmodified']);
        $row['username']     = $wikiPage->getUserName($row['uid']);
        $history[]           = $row;
        //          echo '<td><a href="pages.php?page='.$page.'&op=display&id='.$id.'">'._AD_GWIKI_VIEW.'</a> | <a href="javascript:restoreRevision(\''.$id.'\');">'._AD_GWIKI_RESTORE.'</a> | <a href="pages.php?page='.$page.'&op=fix&id='.$id.'">'._AD_GWIKI_FIX.'</a></td></tr>';
    }
}

$pageX['moddir']  = $dir;
$pageX['modpath'] = XOOPS_ROOT_PATH . '/modules/' . $dir;
$pageX['modurl']  = XOOPS_URL . '/modules/' . $dir;

$GLOBALS['xoopsOption']['template_main'] = 'gwiki_history.tpl';
include XOOPS_ROOT_PATH . '/header.php';

$xoopsTpl->assign('gwiki', $pageX);
$xoopsTpl->assign('history', $history);

$xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $dir . '/assets/css/module.css');
$title = _MD_GWIKI_HISTORY_TITLE;
$xoopsTpl->assign('title', $title . ' : ' . $page);
if (empty($title)) {
    $title = htmlspecialchars($xoopsModule->name());
}
$xoopsTpl->assign('xoops_pagetitle', $title);
if (!empty($message)) {
    $xoopsTpl->assign('message', $message);
}
if (!empty($err_message)) {
    $xoopsTpl->assign('err_message', $err_message);
}

include XOOPS_ROOT_PATH . '/footer.php';
