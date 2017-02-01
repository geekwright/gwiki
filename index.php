<?php

use Xmf\Request;

/**
 * index.php - display wiki page
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

// this exists to make xoops comments work
// index.php?page_id=15&com_mode=thread&com_order=0&com_id=2&com_rootid=1
if (Request::hasVar('page_id', 'GET')) {
    $page_id = Request::getInt('page_id', 0, 'GET');
    $page    = $wikiPage->getKeywordById($page_id);
    if (!empty($page)) {
        $extra = '';
        if (isset($_GET['com_mode'])) {
            $extra .= '&com_mode=' . $_GET['com_mode'];
        }
        if (isset($_GET['com_order'])) {
            $extra .= '&com_order=' . $_GET['com_order'];
        }
        if (isset($_GET['com_id'])) {
            $extra .= '&com_id=' . $_GET['com_id'];
        }
        if (isset($_GET['com_rootid'])) {
            $extra .= '&com_rootid=' . $_GET['com_rootid'];
        }
        $header = sprintf($xoopsModuleConfig['wikilink_template'], $page) . $extra;
        header("Location: {$header}");
        exit;
    }
}
// $_GET variables we use
$page      = $wikiPage->normalizeKeyword(Request::getString('page', $wikiPage->wikiHomePage, 'GET'));
$highlight = Request::getString('query', null, 'GET');

// if we get a naked or external prefix, try and do something useful
$pfx = $wikiPage->getPrefix($page);
if ($pfx && $pfx['defined']) {
    $page = $pfx['actual_page'];
    if ($pfx['prefix_is_external']) {
        header("Location: {$pfx['actual_page']}");
        exit;
    }
}

global $wikiPage;
$pageX       = $wikiPage->getPage($page);
$attachments = $wikiPage->getAttachments($page);
$wikiPage->registerHit($page);
$mayEdit = $wikiPage->checkEdit();

if ($pageX) {
    $pageX['body']      = $wikiPage->renderPage($wikiPage->body);
    $pageX['mayEdit']   = $mayEdit;
    $pageX['pageFound'] = true;
    if (!empty($highlight)) {
        $pageX['body'] = $wikiPage->highlightWords($highlight);
    }
} else {
    $dir = $wikiPage->getWikiDir();
    if ($mayEdit) {
        redirect_header(XOOPS_URL . "/modules/{$dir}/edit.php?page={$page}", 2, _MD_GWIKI_PAGENOTFOUND);
    }
    $pageX                 = array();
    $pageX['keyword']      = $page;
    $pageX['title']        = _MD_GWIKI_NOEDIT_NOTFOUND_TITLE;
    $pageX['body']         = _MD_GWIKI_NOEDIT_NOTFOUND_BODY;
    $pageX['author']       = '';
    $pageX['revisiontime'] = '';
    $pageX['createdtime']  = '';
    $pageX['mayEdit']      = $mayEdit;
    $pageX['pageFound']    = false;
}

$dir              = basename(__DIR__);
$pageX['moddir']  = $dir;
$pageX['modpath'] = XOOPS_ROOT_PATH . '/modules/' . $dir;
$pageX['modurl']  = XOOPS_URL . '/modules/' . $dir;
if (!empty($attachments)) {
    $pageX['attachments'] = prepOut($attachments);
}

$_GET['page_id'] = $wikiPage->page_id;
$_GET['nsid']    = $wikiPage->currentprefixid;

$GLOBALS['xoopsOption']['template_main'] = $wikiPage->getTemplateName(); // 'gwiki_view.tpl';
include XOOPS_ROOT_PATH . '/header.php';

$pageX['title'] = prepOut($pageX['title']);
$xoopsTpl->assign('gwiki', $pageX);

//echo '<pre>';print_r($pageX);echo '</pre>';

$xoTheme->addStylesheet(XOOPS_URL . '/modules/gwiki/assets/css/module.css');
if ($pageX['pageFound']) {
    $xoTheme->addMeta('meta', 'keywords', htmlspecialchars($pageX['meta_keywords'], ENT_QUOTES, null, false));
    $xoTheme->addMeta('meta', 'description', htmlspecialchars($pageX['meta_description'], ENT_QUOTES, null, false));
}
$title = $pageX['title'];
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

include XOOPS_ROOT_PATH . '/include/comment_view.php';

include XOOPS_ROOT_PATH . '/footer.php';
