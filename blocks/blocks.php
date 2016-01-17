<?php
/**
 * blocks/blocks.php
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 * @version    $Id$
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

/**
 * @param $options
 *
 * @return bool
 */
function b_gwiki_wikiblock_show($options)
{
    global $xoopsConfig, $xoTheme;

    $block = false;

    $dir = basename(dirname(__DIR__));
    // Access module configs from block:
    $module_handler = &xoops_gethandler('module');
    $module         = $module_handler->getByDirname($dir);
    $config_handler = &xoops_gethandler('config');
    $moduleConfig   = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

    include_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/gwikiPage.php';

    $wikiPage = new gwikiPage;
    $wikiPage->setRecentCount($moduleConfig['number_recent']);

    $remotegwiki = !empty($options[2]);
    if (!$remotegwiki) {
        $block = $wikiPage->getPage($options[0]);
    }
    if (!$block) {
        $block['keyword']         = $options[0];
        $block['display_keyword'] = $options[0];
    }

    $xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $dir . '/assets/css/module.css');

    $block['bid'] = $options[1]; // we use our block id to make a (quasi) unique div id

    $block['moddir']  = $dir;
    $block['modpath'] = XOOPS_ROOT_PATH . '/modules/' . $dir;
    $block['modurl']  = XOOPS_URL . '/modules/' . $dir;
    if ($remotegwiki) {
        $block['ajaxurl']    = $options[2];
        $block['mayEdit']    = false;
        $block['remotewiki'] = true;
    } else {
        $block['ajaxurl']    = $block['modurl'];
        $block['mayEdit']    = $wikiPage->checkEdit();
        $block['remotewiki'] = false;
    }

    return $block;
}

/**
 * @param $options
 *
 * @return string
 */
function b_gwiki_wikiblock_edit($options)
{
    $form = _MB_GWIKI_WIKIPAGE . ' <input type="text" value="' . $options[0] . '"id="options[0]" name="options[0]" /><br />';
    // capture the block id from the url and save through a hidden option.
    if ($_GET['op'] === 'clone') {
        $form .= _MI_GWIKI_BL_CLONE_WARN . '<br />';
    }
    $form .= '<input type="hidden" value="' . (int)($_GET['bid']) . '"id="options[1]" name="options[1]" />';
    $form .= _MB_GWIKI_REMOTE_AJAX_URL . ' <input type="text" size="35" value="' . $options[2] . '"id="options[2]" name="options[2]" />  <i>' . _MB_GWIKI_REMOTE_AJAX_URL_DESC . '</i><br />';

    return $form;
}

/**
 * @param $options
 *
 * @return bool
 */
function b_gwiki_newpage_show($options)
{
    global $xoopsUser, $xoopsDB;

    if (!isset($options[0])) {
        $options[0] = 0;
    }
    $block = false;

    $dir = basename(dirname(__DIR__));
    include_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/gwikiPage.php';

    $wikiPage = new gwikiPage;
    $prefixes = $wikiPage->getUserNamespaces();
    if ($prefixes) {
        $block['moddir']   = $dir;
        $block['modpath']  = XOOPS_ROOT_PATH . '/modules/' . $dir;
        $block['modurl']   = XOOPS_URL . '/modules/' . $dir;
        $block['prefixes'] = $prefixes;
        if ($options[0]) {
            $block['action'] = 'wizard.php';
        } else {
            $block['action'] = 'edit.php';
        }
    } else {
        $block = false;
    }

    return $block;
}

/**
 * @param $options
 *
 * @return string
 */
function b_gwiki_newpage_edit($options)
{
    if (!isset($options[0])) {
        $options[0] = 0;
    }
    $form = '';
    $form .= _MB_GWIKI_NEWPAGE_USE_WIZARD . ' <input type="radio" name="options[0]" value="1" ';
    if ($options[0]) {
        $form .= 'checked="checked"';
    }
    $form .= ' />&nbsp;' . _YES . '&nbsp;<input type="radio" name="options[0]" value="0" ';
    if (!$options[0]) {
        $form .= 'checked="checked"';
    }
    $form .= ' />&nbsp;' . _NO . '<br /><br />';

    return $form;
}

/**
 * @param $options
 *
 * @return bool
 */
function b_gwiki_teaserblock_show($options)
{
    global $xoopsDB, $xoopsConfig, $xoTheme;

    $block = false;

    $dir = basename(dirname(__DIR__));
    // Access module configs from block:
    $module_handler = &xoops_gethandler('module');
    $module         = $module_handler->getByDirname($dir);
    $config_handler = &xoops_gethandler('config');
    $moduleConfig   = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

    include_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/gwikiPage.php';

    $wikiPage = new gwikiPage;
    $wikiPage->setRecentCount($moduleConfig['number_recent']);

    $page = $options[1];
    if ($options[2]) {
        $pagelike = $page . "%";
        $sql      = 'SELECT keyword FROM ' . $xoopsDB->prefix('gwiki_pageids');
        $sql .= " WHERE keyword like '{$pagelike}' ORDER BY RAND() LIMIT 1 ";
        $result = $xoopsDB->query($sql);
        if ($result) {
            $myrow = $xoopsDB->fetchRow($result);
            $page  = $myrow[0];
        }
    }

    $block = $wikiPage->getPage($page);
    if ($block) {
        $block['title'] = htmlspecialchars($block['title']);
        if (!defined('_MI_GWIKI_NAME')) {
            $langfile = XOOPS_ROOT_PATH . '/modules/' . $dir . '/language/' . $xoopsConfig['language'] . '/modinfo.php';
            if (!file_exists($langfile)) {
                $langfile = XOOPS_ROOT_PATH . '/modules/' . $dir . '/language/english/modinfo.php';
            }
            include_once $langfile;
        }
        $xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $dir . '/assets/css/module.css');

        if ($options[0]) {
            $block['body'] = $wikiPage->renderPage();
        } else {
            $block['body'] = $wikiPage->renderTeaser();
        }

        $block['moddir']   = $dir;
        $block['modpath']  = XOOPS_ROOT_PATH . '/modules/' . $dir;
        $block['modurl']   = XOOPS_URL . '/modules/' . $dir;
        $block['mayEdit']  = $wikiPage->checkEdit();
        $block['template'] = 'db:' . $wikiPage->getTemplateName();

        if ($options[3]) {
            $sql = 'SELECT * FROM ' . $xoopsDB->prefix('gwiki_page_images');
            //            $sql .= ' WHERE keyword = "'.$page.'" AND use_to_represent = 1 ';
            $sql .= " WHERE keyword = '{$page}' AND use_to_represent = 1 ";
            $result = $xoopsDB->query($sql);
            if ($myrow = $xoopsDB->fetchArray($result)) {
                // $block['image_file'] = XOOPS_URL .'/uploads/' . $dir . '/' . $myrow['image_file'];
                $block['image_file']     = XOOPS_URL . '/modules/' . $dir . '/getthumb.php?page=' . $page . '&name=' . $myrow['image_name'];
                $block['image_alt_text'] = $myrow['image_alt_text'];
            }
        }
        $block['pageurl'] = sprintf($wikiPage->getWikiLinkURL(), $block['keyword']);
    }

    return $block;
}

/**
 * @param $options
 *
 * @return string
 */
function b_gwiki_teaserblock_edit($options)
{
    $form = '';
    $form .= _MB_GWIKI_SHOW_FULL_PAGE . ' <input type="radio" name="options[0]" value="1" ';
    if ($options[0]) {
        $form .= 'checked="checked"';
    }
    $form .= ' />&nbsp;' . _YES . '&nbsp;<input type="radio" name="options[0]" value="0" ';
    if (!$options[0]) {
        $form .= 'checked="checked"';
    }
    $form .= ' />&nbsp;' . _NO . '<br /><br />';
    $form .= _MB_GWIKI_WIKIPAGE . ' <input type="text" value="' . $options[1] . '"id="options[1]" name="options[1]" /><br /><br />';
    $form .= _MB_GWIKI_RANDOM_PAGE . ' <input type="radio" name="options[2]" value="1" ';
    if ($options[2]) {
        $form .= 'checked="checked"';
    }
    $form .= ' />&nbsp;' . _YES . '&nbsp;<input type="radio" name="options[2]" value="0" ';
    if (!$options[2]) {
        $form .= 'checked="checked"';
    }
    $form .= ' />&nbsp;' . _NO . '<br />' . _MB_GWIKI_RANDOM_PAGE_DESC . '<br /><br />';
    $form .= _MB_GWIKI_SHOW_DEFAULT_IMAGE . ' <input type="radio" name="options[3]" value="1" ';
    if ($options[3]) {
        $form .= 'checked="checked" ';
    }
    $form .= ' />&nbsp;' . _YES . '&nbsp;<input type="radio" name="options[3]" value="0" ';
    if (!$options[3]) {
        $form .= 'checked="checked"';
    }
    $form .= ' />&nbsp;' . _NO . '<br /><br />';

    return $form;
}

/**
 * @param $options
 *
 * @return bool
 */
function b_gwiki_recentblock_show($options)
{
    global $xoopsDB, $xoTheme;

    $block = false;

    $dir = basename(dirname(__DIR__));
    include_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/gwikiPage.php';

    $wikiPage = new gwikiPage;

    $prefix = '';
    $sql    = 'SELECT prefix FROM ' . $xoopsDB->prefix('gwiki_prefix') . ' WHERE prefix_id = "' . $options[1] . '"';
    $result = $xoopsDB->query($sql);
    $myrow  = $xoopsDB->fetchArray($result);
    if ($myrow) {
        $prefix = $myrow['prefix'];
    }
    $prefix .= '%';

    $maxage = 0;
    if (!empty($options[2])) {
        $maxage = strtotime($options[2]);
    }

    $keywords = array();

    $sql = 'SELECT p.keyword, image_file, image_alt_text, image_name FROM ' . $xoopsDB->prefix('gwiki_pages') . ' p ';
    $sql .= ' left join ' . $xoopsDB->prefix('gwiki_page_images') . ' i on p.keyword=i.keyword and use_to_represent = 1 ';
    //    $sql .= ' WHERE active=1 AND show_in_index=1 AND p.keyword like "'.$prefix.'" ';
    $sql .= " WHERE active=1 AND show_in_index=1 AND p.keyword like '{$prefix}'";
    $sql .= ' AND lastmodified > "' . $maxage . '" ORDER BY lastmodified desc';
    $result = $xoopsDB->query($sql, $options[0], 0);
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $keywords[] = $myrow;
    }

    if (empty($keywords)) {
        return false;
    } // nothing to show

    if (!defined('_MI_GWIKI_NAME')) {
        $langfile = XOOPS_ROOT_PATH . '/modules/' . $dir . '/language/' . $xoopsConfig['language'] . '/modinfo.php';
        if (!file_exists($langfile)) {
            $langfile = XOOPS_ROOT_PATH . '/modules/' . $dir . '/language/english/modinfo.php';
        }
        include_once $langfile;
    }
    $xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $dir . '/assets/css/module.css');

    foreach ($keywords as $keyimg) {
        $gwiki = $wikiPage->getPage($keyimg['keyword']);
        if ($gwiki) {
            $gwiki['title']    = htmlspecialchars($gwiki['title']);
            $gwiki['body']     = $wikiPage->renderTeaser();
            $gwiki['moddir']   = $dir;
            $gwiki['modpath']  = XOOPS_ROOT_PATH . '/modules/' . $dir;
            $gwiki['modurl']   = XOOPS_URL . '/modules/' . $dir;
            $gwiki['mayEdit']  = $wikiPage->checkEdit();
            $gwiki['template'] = 'db:' . $wikiPage->getTemplateName();
            if (!empty($keyimg['image_file'])) {
                // $gwiki['image_file'] = XOOPS_URL .'/uploads/' . $dir . '/' . $keyimg['image_file'];
                $gwiki['image_file']     = XOOPS_URL . '/modules/' . $dir . '/getthumb.php?page=' . $keyimg['keyword'] . '&name=' . $keyimg['image_name'];
                $gwiki['image_alt_text'] = $keyimg['image_alt_text'];
            }
            $gwiki['pageurl'] = sprintf($wikiPage->getWikiLinkURL(), $gwiki['keyword']);
            $gwiki['title']   = sprintf('<a href="%s" title="%s">%s</a>', $gwiki['pageurl'], htmlspecialchars($gwiki['title'], ENT_COMPAT), $gwiki['title']);

            $block['pages'][] = $gwiki;
        }
    }

    return $block;
}

/**
 * @param $options
 *
 * @return string
 */
function b_gwiki_recentblock_edit($options)
{
    global $xoopsDB;

    $form = '';
    $form .= _MB_GWIKI_RECENT_COUNT . ' <input type="text" value="' . $options[0] . '"id="options[0]" name="options[0]" /><br />';
    $form .= _MB_GWIKI_PICK_NAMESPACE . ' <select id="options[1]" name="options[1]">';
    $form .= '<option value="0"' . ((int)($options[1]) === 0 ? ' selected' : '') . '></option>';
    $sql    = 'SELECT prefix_id, prefix FROM ' . $xoopsDB->prefix('gwiki_prefix') . ' ORDER BY prefix';
    $result = $xoopsDB->query($sql);
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $pid = (int)($myrow['prefix_id']);
        $form .= '<option value="' . $pid . '"' . ((int)($options[1]) === $pid ? ' selected' : '') . '>' . $myrow['prefix'] . '</option>';
    }
    $form .= '</select><br />';
    $form .= _MB_GWIKI_MAX_AGE . ' <input type="text" value="' . $options[2] . '"id="options[2]" name="options[2]" /><br />';

    return $form;
}

/**
 * @param $options
 *
 * @return bool
 */
function b_gwiki_pagesettoc_show($options)
{
    global $xoTheme;

    $block = false;

    $dir = basename(dirname(__DIR__));
    include_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/gwikiPage.php';
    $wikiPage = new gwikiPage;

    if (empty($options[1])) {
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if (get_magic_quotes_gpc()) {
                $page = stripslashes($page);
            }
            $page = html_entity_decode($page);
            $page = trim($page);
        }
    } else {
        $page = $options[1];
    }

    if (empty($page)) {
        return false;
    }
    $page = $wikiPage->getOOBFromKeyword($page);

    $level = (int)($options[0]);
    if ($level < 1) {
        $level = 1;
    }

    $toc = $wikiPage->renderPageSetToc($page, $level, 'wikitocblock');
    if ($toc) {
        $block['toc'] = $toc;

        $xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $dir . '/assets/css/module.css');

        $block['keyword'] = $page;
        $block['moddir']  = $dir;
        $block['modpath'] = XOOPS_ROOT_PATH . '/modules/' . $dir;
        $block['modurl']  = XOOPS_URL . '/modules/' . $dir;
    }

    return $block;
}

/**
 * @param $options
 *
 * @return string
 */
function b_gwiki_pagesettoc_edit($options)
{
    $form = _MB_GWIKI_WIKIPAGESET_LEVELS . ' <input type="text" value="' . $options[0] . '"id="options[0]" name="options[0]" /><br />';
    $form .= _MB_GWIKI_WIKIPAGESET . ' <input type="text" value="' . $options[1] . '"id="options[1]" name="options[1]" /> ' . _MB_GWIKI_WIKIPAGESET_DESC . '<br />';

    return $form;
}

/**
 * @param $options
 *
 * @return bool
 */
function b_gwiki_related_show($options)
{
    global $xoTheme, $xoopsDB;

    $block = false;

    $dir = basename(dirname(__DIR__));
    include_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/gwikiPage.php';
    $wikiPage = new gwikiPage;

    $q_exclude_page = '';

    if (empty($options[1])) {
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if (get_magic_quotes_gpc()) {
                $page = stripslashes($page);
            }
            $page = html_entity_decode($page);
            $page = trim($page);
            $page = $wikiPage->getOOBFromKeyword($page);

            $q_page         = $wikiPage->escapeForDB($page);
            $q_exclude_page = $wikiPage->escapeForDB($page);

            $sql = 'SELECT parent_page ';
            $sql .= ' FROM ' . $xoopsDB->prefix('gwiki_pages');
            $sql .= " WHERE active=1 and keyword='{$q_page}' ";

            $result = $xoopsDB->query($sql);

            $rows = $xoopsDB->getRowsNum($result);
            if ($rows) {
                $row = $xoopsDB->fetchArray($result);
                if (!empty($row['parent_page'])) {
                    $page = $row['parent_page'];
                }
            }
            $xoopsDB->freeRecordSet($result);
        }
    } else {
        $page = $options[1];
    }

    if (empty($page)) {
        return false;
    }

    $limit = (int)($options[0]);
    if ($limit < 1) {
        $limit = 1;
    }

    $sort = (int)($options[2]);
    if ($sort < 0) {
        $sort = 0;
    }
    if ($sort > 1) {
        $sort = 1;
    }

    $relatedsort = " lastmodified DESC, hit_count DESC, ";
    if ($sort === 1) {
        $relatedsort = " hit_count DESC, lastmodified DESC, ";
    }

    $q_page = $wikiPage->escapeForDB($page);

    $sql = 'SELECT keyword, display_keyword, title, lastmodified, uid, page_id, created, hit_count ';
    $sql .= ' FROM ' . $xoopsDB->prefix('gwiki_pages');
    $sql .= ' natural left join ' . $xoopsDB->prefix('gwiki_pageids');
    $sql .= " WHERE active=1 and parent_page = '{$q_page}' and keyword!='{$q_exclude_page}' ";
    $sql .= " ORDER BY {$relatedsort} keyword ";

    $related = false;
    $result  = $xoopsDB->query($sql, $limit, 0);
    while ($row = $xoopsDB->fetchArray($result)) {
        $row['pageurl']  = sprintf($wikiPage->getWikiLinkURL(), $row['keyword']);
        $row['pagelink'] = sprintf('<a href="%s" title="%s">%s</a>', $row['pageurl'], htmlspecialchars($row['title'], ENT_COMPAT), $row['title']);
        $related[]       = $row;
    }
    $xoopsDB->freeRecordSet($result);

    if ($related) {
        $block['related'] = $related;

        $xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $dir . '/assets/css/module.css');

        $block['keyword'] = $page;
        $block['moddir']  = $dir;
        $block['modpath'] = XOOPS_ROOT_PATH . '/modules/' . $dir;
        $block['modurl']  = XOOPS_URL . '/modules/' . $dir;
    }

    return $block;
}

/**
 * @param $options
 *
 * @return string
 */
function b_gwiki_related_edit($options)
{
    $form = _MB_GWIKI_RELATED_COUNT . ' <input type="text" value="' . $options[0] . '"id="options[0]" name="options[0]" /><br />';
    $form .= _MB_GWIKI_RELATED . ' <input type="text" value="' . $options[1] . '"id="options[1]" name="options[1]" /> ' . _MB_GWIKI_RELATED_DESC . '<br />';
    $form .= _MB_GWIKI_RELATED_SORT . ' <select id="options[2]" name="options[2]">';
    $form .= '<option value="0"' . ((int)($options[2]) === 0 ? ' selected' : '') . '>' . _MB_GWIKI_RELATED_SORT_DATE . '</option>';
    $form .= '<option value="1"' . ((int)($options[2]) === 1 ? ' selected' : '') . '>' . _MB_GWIKI_RELATED_SORT_HITS . '</option>';
    $form .= '</select><br />';

    return $form;
}

/**
 * @param $options
 *
 * @return bool
 */
function b_gwiki_linkshere_show($options)
{
    global $xoTheme, $xoopsDB;

    $block = false;

    $dir = basename(dirname(__DIR__));
    include_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/gwikiPage.php';
    $wikiPage = new gwikiPage;

    if (isset($_GET['page'])) {
        $page = $_GET['page'];
        if (get_magic_quotes_gpc()) {
            $page = stripslashes($page);
        }
        $page   = html_entity_decode($page);
        $page   = trim($page);
        $page   = $wikiPage->getOOBFromKeyword($page);
        $q_page = $wikiPage->escapeForDB($page);
    }

    if (empty($page)) {
        return false;
    }

    $limit = (int)($options[0]);
    if ($limit < 0) {
        $limit = 0;
    }

    $sort = (int)($options[1]);
    if ($sort < 0) {
        $sort = 0;
    }
    if ($sort > 2) {
        $sort = 2;
    }

    $relatedsort = ' display_keyword, ';
    if ($sort === 1) {
        $relatedsort = ' lastmodified DESC, hit_count DESC, ';
    }
    if ($sort === 2) {
        $relatedsort = ' hit_count DESC, lastmodified DESC, ';
    }

    $q_page = $wikiPage->escapeForDB($page);

    $sql = 'SELECT keyword, display_keyword, title, lastmodified, uid, page_id, created, hit_count ';
    $sql .= ' FROM ' . $xoopsDB->prefix('gwiki_pages');
    $sql .= ' natural left join ' . $xoopsDB->prefix('gwiki_pageids');
    $sql .= ' left join ' . $xoopsDB->prefix('gwiki_pagelinks') . ' on from_keyword = keyword ';
    $sql .= " WHERE active=1 and to_keyword = '{$q_page}' ";
    $sql .= " ORDER BY {$relatedsort} keyword ";

    $linkshere = false;
    if ($limit) {
        $result = $xoopsDB->query($sql, $limit, 0);
    } else {
        $result = $xoopsDB->query($sql);
    }
    while ($row = $xoopsDB->fetchArray($result)) {
        $row['pageurl']  = sprintf($wikiPage->getWikiLinkURL(), $row['keyword']);
        $row['pagelink'] = sprintf('<a href="%s" title="%s">%s</a>', $row['pageurl'], htmlspecialchars($row['title'], ENT_COMPAT), $row['title']);
        $linkshere[]     = $row;
    }
    $xoopsDB->freeRecordSet($result);

    if ($linkshere) {
        $block['linkshere'] = $linkshere;

        $xoTheme->addStylesheet(XOOPS_URL . '/modules/' . $dir . '/assets/css/module.css');

        $block['keyword'] = $page;
        $block['moddir']  = $dir;
        $block['modpath'] = XOOPS_ROOT_PATH . '/modules/' . $dir;
        $block['modurl']  = XOOPS_URL . '/modules/' . $dir;
    }

    return $block;
}

/**
 * @param $options
 *
 * @return string
 */
function b_gwiki_linkshere_edit($options)
{
    $form = _MB_GWIKI_RELATED_COUNT . ' <input type="text" value="' . $options[0] . '"id="options[0]" name="options[0]" /><br />';
    $form .= _MB_GWIKI_RELATED_SORT . ' <select id="options[1]" name="options[1]">';
    $form .= '<option value="0"' . ((int)($options[1]) === 0 ? ' selected' : '') . '>' . _MB_GWIKI_RELATED_SORT_ALPHA . '</option>';
    $form .= '<option value="1"' . ((int)($options[1]) === 1 ? ' selected' : '') . '>' . _MB_GWIKI_RELATED_SORT_DATE . '</option>';
    $form .= '<option value="2"' . ((int)($options[1]) === 2 ? ' selected' : '') . '>' . _MB_GWIKI_RELATED_SORT_HITS . '</option>';
    $form .= '</select><br />';

    return $form;
}
