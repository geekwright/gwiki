<?php
/**
 * showdiff.php - show diff between two revisions of a page
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 * @version    $Id$
 */
include "header.php";
require_once 'include/Diff.php';
global $xoTheme, $xoopsTpl;
global $wikiPage;

// $_GET variables we use
$page = $wikiPage->normalizeKeyword((isset($_GET['page'])) ? cleaner($_GET['page']) : $wikiPage->wikiHomePage);
$id   = isset($_GET['id']) ? cleaner($_GET['id']) : null; // old revision id
$nid  = isset($_GET['nid']) ? cleaner($_GET['nid']) : null; // new revision id

$pageX   = $wikiPage->getPage($page, $nid);
$mayEdit = $wikiPage->checkEdit();
$dir     = $wikiPage->getWikiDir();

if ($pageX) {
    //      $pageX['body']=$wikiPage->renderPage($wikiPage->body);
    $pageX['mayEdit']   = $mayEdit;
    $pageX['pageFound'] = true;
    $oldpage            = $wikiPage->getPage($page, $id);
    if (!$oldpage) {
        redirect_header(XOOPS_URL . "/modules/{$dir}/index.php?page={$page}", 2, _MD_GWIKI_PAGENOTFOUND);
    }
    $diff      = new Diff;
    $diffout   = $diff->getDiff(trim($oldpage['body']) . "\n", trim($pageX['body']) . "\n");
    $difflines = explode("\n", $diffout);
    $body      = '<div class="wikidiff"><pre>';
    foreach ($difflines as $line) {
        if (strpos($line, "\n") === false) {
            $line .= "\n";
        }
        switch ($line[0]) {
            case '+':
                $body .= '<span class="wikidiffadd">' . htmlspecialchars($line, ENT_QUOTES) . '</span>';
                break;
            case '-':
                $body .= '<span class="wikidiffdel"">' . htmlspecialchars($line, ENT_QUOTES) . '</span>';
                break;
            default:
                $body .= '<span class="wikidiffsame"">' . htmlspecialchars($line, ENT_QUOTES) . '</span>';
                break;
        }
    }
    $body .= '</pre></div>';
    $pageX['body'] = $body;
} else {
    redirect_header(XOOPS_URL . "/modules/{$dir}/index.php?page={$page}", 2, _MD_GWIKI_PAGENOTFOUND);
}

$dir              = basename(__DIR__);
$pageX['moddir']  = $dir;
$pageX['modpath'] = XOOPS_ROOT_PATH . '/modules/' . $dir;
$pageX['modurl']  = XOOPS_URL . '/modules/' . $dir;

$xoopsOption['template_main'] = 'gwiki_view.tpl';
include XOOPS_ROOT_PATH . "/header.php";

$pageX['title'] = sprintf(_MD_GWIKI_DIFF_TITLE, prepOut($pageX['title']));
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

include XOOPS_ROOT_PATH . "/footer.php";
