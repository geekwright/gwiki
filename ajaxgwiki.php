<?php
/**
 * ajaxwiki.php - serve wiki page via ajax
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 * @version    $Id$
 */
include dirname(dirname(__DIR__)) . '/mainfile.php';
$xoopsLogger->activated = false;
// provide error logging for our sanity in debugging ajax use (won't see xoops logger)
//restore_error_handler();
//error_reporting(-1);

/**
 * @param $string
 *
 * @return string
 */
function cleaner($string)
{
    $string = stripcslashes($string);
    $string = html_entity_decode($string);
    $string = strip_tags($string); // DANGER -- kills wiki text
    $string = trim($string);
    $string = stripslashes($string);

    return $string;
}

// $_GET variables we use
unset($page, $bid, $id);
$page = isset($_GET['page']) ? cleaner($_GET['page']) : null;

// strip rid of any anchor references
//$x=strpos($page,'#');
//if($x!==false) $page=substr($page,0,$x);
//trigger_error($page);

if (isset($_GET['bid'])) {
    $bid = (int)($_GET['bid']);
} // from a block
if (isset($_GET['id'])) {
    $id = (int)($_GET['id']);
}    // from utility (i.e. history)

$dir = basename(__DIR__);
// Access module configs from block:
$module_handler = xoops_gethandler('module');
$module         = $module_handler->getByDirname($dir);
$config_handler = xoops_gethandler('config');
$moduleConfig   = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

$alloworigin = $moduleConfig['allow_origin'];
if (!empty($alloworigin)) {
    header("Access-Control-Allow-Origin: " . $alloworigin);
}

include_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/gwikiPage.php';
$imgdir = XOOPS_URL . '/modules/' . $dir . '/images';

$wikiPage = new gwikiPage;
$wikiPage->setRecentCount($moduleConfig['number_recent']);

if (empty($page)) {
    $page = $wikiPage->wikiHomePage;
}
$page = $wikiPage->normalizeKeyword($page);

if (isset($id)) {
    $wikiPage->setWikiLinkURL("javascript:alert('%s');");
    $wikiPage->setTocFormat('toc' . $id . '-', '#%s');
}
if (isset($bid)) {
    $wikiPage->setWikiLinkURL("javascript:ajaxGwikiLoad('%s','{$bid}');");
    $wikiPage->setTocFormat('toc' . $bid . '-', '#%s');
}
if (isset($id)) {
    $thispage = $wikiPage->getPage($page, $id);
} else {
    $thispage = $wikiPage->getPage($page);
}
if ($thispage) {
    if (!defined('_MI_GWIKI_NAME')) {
        $langfile = XOOPS_ROOT_PATH . '/modules/' . $dir . '/language/' . $xoopsConfig['language'] . '/modinfo.php';
        if (!file_exists($langfile)) {
            $langfile = XOOPS_ROOT_PATH . '/modules/' . $dir . '/language/english/modinfo.php';
        }
        include_once $langfile;
    }

    $rendered = '<h1 class="wikititle">' . htmlspecialchars($wikiPage->title) . '</h1>';
    $rendered .= $wikiPage->renderPage();
    if (!empty($thispage['pageset']['first']['link'])) {
        $rendered .= '<div class="wikipagesetnav">';
        $rendered .= '<a href="' . $thispage['pageset']['first']['link'] . '"><img src="' . $imgdir . '/psfirst.png" alt="' . $thispage['pageset']['first']['desc'] . '" title="' . $thispage['pageset']['first']['text'] . '" /></a>';
        $rendered .= '<a href="' . $thispage['pageset']['prev']['link'] . '"><img src="' . $imgdir . '/psprev.png" alt="' . $thispage['pageset']['prev']['desc'] . '" title="' . $thispage['pageset']['prev']['text'] . '" /></a>';
        $rendered .= '<a href="' . $thispage['pageset']['home']['link'] . '"><img src="' . $imgdir . '/pshome.png" alt="' . $thispage['pageset']['home']['desc'] . '" title="' . $thispage['pageset']['home']['text'] . '" /></a>';
        $rendered .= '<a href="' . $thispage['pageset']['next']['link'] . '"><img src="' . $imgdir . '/psnext.png" alt="' . $thispage['pageset']['next']['desc'] . '" title="' . $thispage['pageset']['next']['text'] . '" /></a>';
        $rendered .= '<a href="' . $thispage['pageset']['last']['link'] . '"><img src="' . $imgdir . '/pslast.png" alt="' . $thispage['pageset']['last']['desc'] . '" title="' . $thispage['pageset']['last']['text'] . '" /></a>';
        $rendered .= '</div>';
    }

    if (!isset($id)) {
        $wikiPage->registerHit($page);
    } // don't count hits from utilities
} else {
    //if ($mayEdit) redirect_header("edit.php?page=$page", 2, _MD_GWIKI_PAGENOTFOUND);
    $rendered = '<h1 class="wikititle">' . _MD_GWIKI_NOEDIT_NOTFOUND_TITLE . '</h1>';
    $rendered .= _MD_GWIKI_NOEDIT_NOTFOUND_BODY;
}
echo $rendered;
exit;
