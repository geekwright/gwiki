<?php
/**
 * wiki page anywhere - call it anything, put it anywhere
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 * @version    $Id$
 */

// ******************************************************************
// adjust these next few lines to reflect your installation
include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
$dir     = 'gwiki';  // wiki module directory
$pagevar = 'page'; // what is our page variable name?

// $_GET variables we use
$page      = isset($_GET[$pagevar]) ? cleaner($_GET[$pagevar]) : null;
$highlight = isset($_GET['query']) ? cleaner($_GET['query']) : null;

// build a URL template to point wiki links to this script
$script         = (!empty($_SERVER['HTTPS'])) ? "https://" . $_SERVER['SERVER_NAME'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : "http://" . $_SERVER['SERVER_NAME'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$ourWikiLinkURL = $script . '?' . $pagevar . '=%s';

// normally, adjustments to the remaining code are not required
// ******************************************************************

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

/**
 * @param $var
 *
 * @return array|string
 */
function prepOut(&$var)
{
    if (is_array($var)) {
        foreach ($var as $i => $v) {
            $var[$i] = prepOut($v);
        }
    } else {
        if (is_string($var)) {
            $var = htmlspecialchars($var);
        }
    }

    return $var;
}

/**
 * @param        $name
 * @param string $domain
 * @param null   $language
 */
function loadLanguage($name, $domain = '', $language = null)
{
    global $xoopsConfig;
    if (!@include_once XOOPS_ROOT_PATH . "/modules/{$domain}/language/" . $xoopsConfig['language'] . "/{$name}.php") {
        include_once XOOPS_ROOT_PATH . "/modules/{$domain}/language/english/{$name}.php";
    }
}

// Access module configs from outside module:
$module_handler = &xoops_gethandler('module');
$module         = $module_handler->getByDirname($dir);
$config_handler = &xoops_gethandler('config');
$moduleConfig   = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

loadLanguage('main', $dir);
loadLanguage('modinfo', $dir);
include_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/gwikiPage.php';

$wikiPage = new gwikiPage;
$wikiPage->setRecentCount($moduleConfig['number_recent']);
$wikiPage->setWikiLinkURL($ourWikiLinkURL);

if (empty($page)) {
    $page = $wikiPage->wikiHomePage;
}

// if we get a naked or external prefix, try and do something useful
$pfx = $wikiPage->getPrefix($page);
if ($pfx && $pfx['defined']) {
    $page = $pfx['actual_page'];
    if ($pfx['prefix_is_external']) {
        header("Location: {$pfx['actual_page']}");
        exit;
    }
}

$pageX       = $wikiPage->getPage($page);
$attachments = $wikiPage->getAttachments($page);
$mayEdit     = $wikiPage->checkEdit();

if ($pageX) {
    $pageX['body']         = $wikiPage->renderPage($wikiPage->body);
    $pageX['author']       = $wikiPage->getUserName($wikiPage->uid);
    $pageX['revisiontime'] = date($wikiPage->dateFormat, $pageX['lastmodified']);
    $pageX['mayEdit']      = $mayEdit;
    $pageX['pageFound']    = true;
    if (!empty($highlight)) {
        $pageX['body'] = $wikiPage->highlightWords($highlight);
    }
} else {
    $pageX                 = array();
    $pageX['keyword']      = $page;
    $pageX['title']        = _MD_GWIKI_NOEDIT_NOTFOUND_TITLE;
    $pageX['body']         = _MD_GWIKI_NOEDIT_NOTFOUND_BODY;
    $pageX['author']       = '';
    $pageX['revisiontime'] = '';
    $pageX['mayEdit']      = $mayEdit;
    $pageX['pageFound']    = false;
}

$pageX['moddir']  = $dir;
$pageX['modpath'] = XOOPS_ROOT_PATH . '/modules/' . $dir;
$pageX['modurl']  = XOOPS_URL . '/modules/' . $dir;
if (!empty($attachments)) {
    $pageX['attachments'] = prepOut($attachments);
}

$xoopsOption['template_main'] = $wikiPage->getTemplateName(); // 'gwiki_view.tpl';
include XOOPS_ROOT_PATH . "/header.php";

$pageX['title'] = prepOut($pageX['title']);
$xoopsTpl->assign('gwiki', $pageX);

$xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $dir . '/assets/css/module.css');
if ($pageX['pageFound']) {
    $xoTheme->addMeta('meta', 'keywords', htmlspecialchars($pageX['meta_keywords'], ENT_QUOTES, null, false));
    $xoTheme->addMeta('meta', 'description', htmlspecialchars($pageX['meta_description'], ENT_QUOTES, null, false));
}
$title = $pageX['title'];
$xoopsTpl->assign('xoops_pagetitle', $title);
$xoopsTpl->assign('icms_pagetitle', $title);

include XOOPS_ROOT_PATH . '/footer.php';
