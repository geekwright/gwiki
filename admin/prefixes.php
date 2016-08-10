<?php
/**
 * admin/prefixes.php - manage wiki namespaces
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 * @version    $Id$
 */
include __DIR__ . '/header.php';
if (!$xoop25plus) {
    adminmenu(5);
} else {
    echo $moduleAdmin->addNavigation('prefixes.php');
}

// return groups and current permissions for a prefix as an array of options for a form select
/**
 * @param $pid
 *
 * @return array
 */
function getPrefixGroups($pid)
{
    global $xoopsDB;

    $sql = 'SELECT groupid, name, prefix_id FROM ' . $xoopsDB->prefix('groups');
    $sql .= ' LEFT JOIN ' . $xoopsDB->prefix('gwiki_group_prefix') . ' on groupid = group_id ';
    $sql .= " AND prefix_id = '{$pid}' ";

    $result = $xoopsDB->query($sql);

    $options = array();
    for ($i = 0; $i < $xoopsDB->getRowsNum($result); ++$i) {
        $row       = $xoopsDB->fetchArray($result);
        $selected  = ($row['prefix_id'] ? 'selected ' : '');
        $options[] = "<option {$selected}value=\"{$row['groupid']}\">{$row['name']}</option>";
    }

    return $options;
}

/**
 * @param $pid
 * @param $groups
 */
function setPrefixGroups($pid, $groups)
{
    global $xoopsDB;

    $sql = 'DELETE FROM ' . $xoopsDB->prefix('gwiki_group_prefix');
    $sql .= " WHERE prefix_id = '{$pid}' ";

    $result = $xoopsDB->query($sql);

    if (count($groups) > 0) {
        $sql = 'INSERT INTO ' . $xoopsDB->prefix('gwiki_group_prefix') . ' (group_id, prefix_id) VALUES ';
        $val = '';
        foreach ($groups as $group) {
            if (!empty($val)) {
                $val .= ', ';
            }
            $val .= "('$group', '$pid')";
        }
        $sql .= $val;
        $result = $xoopsDB->query($sql);
    }
}

function showPrefixes()
{
    global $xoopsDB;
    /*
    gwiki_prefix
      prefix_id int(10) NOT NULL auto_increment,
      prefix varchar(255) NOT NULL default '',
      prefix_home varchar(255) NOT NULL default '',
      prefix_template_id int(10) NOT NULL default '0',
      prefix_is_external tinyint(1) NOT NULL default '0',
      prefix_external_url
    */

    echo <<<EOT
<style>
div.pagination.default {display:inline;}
form {display:inline;}
</style>
EOT;
    $total = 0;
    $limit = 10;
    $start = 0;
    if (!empty($_GET['start'])) {
        $start = (int)($_GET['start']);
    }

    $sql    = "SELECT count(*) FROM " . $xoopsDB->prefix('gwiki_prefix');
    $result = $xoopsDB->query($sql);
    if ($result) {
        $myrow = $xoopsDB->fetchRow($result);
        $total = $myrow[0];
    }

    adminTableStart(_AD_GWIKI_NAMESPACE_LIST, 6);
    echo '<tr class="head">' . '<th>' . _AD_GWIKI_NAMESPACE_PREFIX . '</th>' . '<th>' . _AD_GWIKI_NAMESPACE_HOME . '</th>' . '<th>' . _AD_GWIKI_NAMESPACE_AUTONAME_SHORT . '</th>' . '<th>' . _AD_GWIKI_NAMESPACE_TEMPLATE . '</th>' . '<th>' . _AD_GWIKI_NAMESPACE_EXTERN_SHORT . '</th>' . '<th>' . _AD_GWIKI_NAMESPACE_EXTERN_URL . '</th>' . '</tr>';

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('gwiki_prefix');
    $sql .= ' LEFT JOIN ' . $xoopsDB->prefix('gwiki_template') . ' on prefix_template_id = template_id ';
    $sql .= ' ORDER BY prefix ';

    $result = $xoopsDB->query($sql, $limit, $start);

    for ($i = 0; $i < $xoopsDB->getRowsNum($result); ++$i) {
        $row = $xoopsDB->fetchArray($result);

        if (empty($row['template'])) {
            $template = '<a href="prefixes.php?pid=' . $row['prefix_id'] . '&op=newtemplate">' . _AD_GWIKI_TEMPLATE_ADD . '</a>';
        } else {
            $template = '<a href="prefixes.php?pid=' . $row['prefix_id'] . '&op=edittemplate" title="' . _AD_GWIKI_TEMPLATE_EDIT . '">' . htmlspecialchars($row['template'], ENT_QUOTES) . '</a>';
        }

        echo '<tr class="' . (($i % 2) ? "even" : "odd") . '"><td><a href="prefixes.php?pid=' . $row['prefix_id'] . '&op=edit">' . htmlspecialchars($row['prefix'], ENT_QUOTES) . '</a></td>' . '<td>' . htmlspecialchars($row['prefix_home'], ENT_QUOTES) . '</td>' . '<td>' . ($row['prefix_auto_name'] ? _YES : _NO) . '</td>' . '<td>' . $template . '</td>' . '<td>' . ($row['prefix_is_external'] ? _YES : _NO) . '</td>' . '<td>' . htmlspecialchars($row['prefix_external_url'], ENT_QUOTES) . '</td>' . '</tr>';
    }
    if ($i === 0) {
        echo '<tr class="odd"><td colspan="5">' . _AD_GWIKI_NAMESPACE_EMPTY . '</td></tr>';
    }

    $endarray[_AD_GWIKI_NAMESPACE_NEW] = 'prefixes.php?op=new';

    // set up pagenav
    $pager = '';
    if ($total > $limit) {
        include_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $nav = new xoopsPageNav($total, $limit, $start, 'start', '');
        if ((int)($total / $limit) < 5) {
            $pager = $nav->renderNav();
        } else {
            $pager = _AD_GWIKI_PAGENAV . $nav->renderSelect(false);
        }
    }
    if (!empty($pager)) {
        $endarray['!PREFORMATTED!'] = $pager;
    }

    adminTableEnd($endarray);
}

// Prefixes
/**
 * @param $row
 * @param $action
 *
 * @return string
 */
function prefixForm($row, $action)
{
    if (empty($row)) {
        return false;
    }
    $groups = getPrefixGroups($row['prefix_id']);

    $form = '<form action="prefixes.php"  method="POST">';
    $form .= '<input type="hidden" name="pid" value="' . $row['prefix_id'] . '">';
    $form .= '<input type="hidden" name="op" value="update">';

    if (empty($row['template'])) {
        $template = '<a href="prefixes.php?pid=' . $row['prefix_id'] . '&op=newtemplate">' . _AD_GWIKI_TEMPLATE_ADD . '</a>';
    } else {
        $template = '<a href="prefixes.php?pid=' . $row['prefix_id'] . '&op=edittemplate" title="' . _AD_GWIKI_TEMPLATE_EDIT . '">' . htmlspecialchars($row['template'], ENT_QUOTES) . '</a>';
    }

    if ($action !== 'new') {
        $form .= '<tr><td class="head">' . _AD_GWIKI_NAMESPACE_PREFIX . '</td><td class="odd">' . $row['prefix'] . '</td></tr>';
    } else {
        $form .= '<tr><td class="head">' . _AD_GWIKI_NAMESPACE_PREFIX . '</td><td class="odd"><input name="prefix" type="text" size="25" value="' . htmlspecialchars($row['prefix'], ENT_QUOTES) . '" ></td></tr>';
    }
    $form .= '<tr><td class="head">' . _AD_GWIKI_NAMESPACE_HOME . '</td><td class="odd"><input name="prefix_home" type="text" size="25" value="' . htmlspecialchars($row['prefix_home'], ENT_QUOTES) . '" ></td></tr>';
    $form .= '<tr><td class="head">' . _AD_GWIKI_NAMESPACE_AUTONAME . '</td><td class="odd"><input type="checkbox" name="prefix_auto_name"' . ($row['prefix_auto_name'] ? ' checked ' : '') . 'value="auto"></td></tr>';
    if ($action !== 'new') {
        $form .= '<tr><td class="head">' . _AD_GWIKI_NAMESPACE_TEMPLATE . '</td><td class="odd">' . $template . '</td></tr>';
    }
    $form .= '<tr><td class="head">' . _AD_GWIKI_NAMESPACE_EXTERN . '</td><td class="odd"><input type="checkbox" name="prefix_is_external"' . ($row['prefix_is_external'] ? ' checked ' : '') . 'value="external"></td></tr>';
    $form .= '<tr><td class="head">' . _AD_GWIKI_NAMESPACE_EXTERN_URL . '</td><td class="odd"><input name="prefix_external_url" type="text" size="60" value="' . htmlspecialchars($row['prefix_external_url'], ENT_QUOTES) . '" ></td></tr>';

    $form .= '<tr><td class="head">' . _AD_GWIKI_NAMESPACE_GROUPS . '</td><td class="odd"><select name="groups[]" multiple size="8">' . implode($groups, "\n") . '</select></td></tr>';
    $form .= '<tr><td class="head"> </td><td class="odd"><input type="submit" value="' . _AD_GWIKI_NAMESPACE_SUBMIT . '"></td></tr>';
    $form .= '</form>';

    return $form;
}

/**
 * @param $pid
 *
 * @return mixed
 */
function getPrefix($pid)
{
    global $xoopsDB;

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('gwiki_prefix');
    $sql .= ' LEFT JOIN ' . $xoopsDB->prefix('gwiki_template') . ' on prefix_template_id = template_id ';
    //    $sql .= ' WHERE prefix_id = "'.$pid.'" ';
    $sql .= " WHERE prefix_id = '{$pid}' ";

    $result = $xoopsDB->query($sql);

    $rows = $xoopsDB->getRowsNum($result);
    if ($rows) {
        $row = $xoopsDB->fetchArray($result);
    } else {
        $row['prefix_id']           = 0;
        $row['prefix']              = '';
        $row['prefix_home']         = '';
        $row['prefix_auto_name']    = 0;
        $row['prefix_template_id']  = 0;
        $row['prefix_is_external']  = 0;
        $row['prefix_external_url'] = '';

        $row['template_id']    = 0;
        $row['template']       = '';
        $row['template_body']  = '';
        $row['template_notes'] = '';
    }

    return $row;
}

function newPrefix()
{
    $row = getPrefix(0);

    adminTableStart(_AD_GWIKI_NAMESPACE_NEW, 2);
    echo prefixForm($row, 'new');
    adminTableEnd(array(_BACK => 'prefixes.php'));
}

/**
 * @param $pid
 */
function editPrefix($pid)
{
    global $xoopsDB;

    $row = getPrefix($pid);

    if ($row['prefix_id']) {
        adminTableStart(_AD_GWIKI_NAMESPACE_EDIT, 2);
        echo prefixForm($row, 'edit');
        adminTableEnd(array(_AD_GWIKI_DELETE => "prefixes.php?pid={$pid}&op=delete", _BACK => 'prefixes.php'));
    } else {
        echo _AD_GWIKI_NAMESPACE_NOT_FOUND;
    }
}

/**
 * @param $pid
 */
function deletePrefix($pid)
{
    global $xoopsDB;

    $row = getPrefix($pid);

    if ($row['template_id']) {
        installTemplate($pid, true);

        $sql = 'DELETE FROM ' . $xoopsDB->prefix('gwiki_template');
        $sql .= ' WHERE template_id = "' . $row['template_id'] . '" ';
        $result = $xoopsDB->queryF($sql);
    }

    $sql = 'DELETE FROM ' . $xoopsDB->prefix('gwiki_prefix');
    //    $sql .= ' WHERE prefix_id = "'.$pid.'" ';
    $sql .= " WHERE prefix_id = '{$pid}' ";
    $result = $xoopsDB->queryF($sql);

    redirect_header("prefixes.php", 2, _MD_GWIKI_DBUPDATED);
}

/**
 * @param $pid
 */
function updatePrefix($pid)
{
    global $xoopsDB, $wikiPage;

    $row = getPrefix($pid);

    if (isset($_POST['prefix'])) {
        $row['prefix'] = $_POST['prefix'];
    }
    if (isset($_POST['prefix_home'])) {
        $row['prefix_home'] = $_POST['prefix_home'];
    }

    $row['prefix_auto_name'] = 0;
    if (isset($_POST['prefix_auto_name']) && $_POST['prefix_auto_name'] === 'auto') {
        $row['prefix_auto_name'] = 1;
    }

    $row['prefix_is_external'] = 0;
    if (isset($_POST['prefix_is_external']) && $_POST['prefix_is_external'] === 'external') {
        $row['prefix_is_external'] = 1;
    }

    if (isset($_POST['prefix_external_url'])) {
        $row['prefix_external_url'] = $_POST['prefix_external_url'];
    }

    if ($row['prefix_id']) {
        $sql = 'UPDATE ' . $xoopsDB->prefix('gwiki_prefix');
        $sql .= ' SET prefix_home = \'' . $wikiPage->escapeForDB($row['prefix_home']) . '\'';
        $sql .= ' , prefix_auto_name = \'' . $wikiPage->escapeForDB($row['prefix_auto_name']) . '\'';
        $sql .= ' , prefix_is_external = \'' . $wikiPage->escapeForDB($row['prefix_is_external']) . '\'';
        $sql .= ' , prefix_external_url = \'' . $wikiPage->escapeForDB($row['prefix_external_url']) . '\'';
        //        $sql .= ' WHERE prefix_id = "'.$pid.'" ';
        $sql .= " WHERE prefix_id = '{$pid}' ";
        $result = $xoopsDB->queryF($sql);
    } else {
        $sql = 'INSERT INTO ' . $xoopsDB->prefix('gwiki_prefix');
        $sql .= ' (prefix, prefix_home, prefix_auto_name, prefix_template_id, prefix_is_external, prefix_external_url)';
        $sql .= ' VALUES (\'' . $wikiPage->escapeForDB($row['prefix']) . '\'';
        $sql .= ' , \'' . $wikiPage->escapeForDB($row['prefix_home']) . '\'';
        $sql .= ' , \'' . $wikiPage->escapeForDB($row['prefix_auto_name']) . '\'';
        $sql .= ' , \'0\'';
        $sql .= ' , \'' . $wikiPage->escapeForDB($row['prefix_is_external']) . '\'';
        $sql .= ' , \'' . $wikiPage->escapeForDB($row['prefix_external_url']) . '\'';
        $sql .= ' ) ';
        $result = $xoopsDB->queryF($sql);
        if ($result) {
            $pid = $xoopsDB->getInsertId();
        }
    }

    //echo '<pre>'; print_r($_POST); echo '</pre>';
    //echo '<pre>'; print_r($row); echo '</pre>';
    //echo $sql;

    if ($result) {
        setPrefixGroups($pid, $row['prefix_is_external'] ? array() : $_POST['groups']); // permissions don't apply to externals
        $message = _MD_GWIKI_DBUPDATED;
    } else {
        $message = _MD_GWIKI_ERRORINSERT;
    }
    redirect_header("prefixes.php", 2, $message);
}

// Templates
/**
 * @param      $pid
 * @param bool $delete
 *
 * @return null
 */
function installTemplate($pid, $delete = false)
{
    global $xoopsModule;

    $template = getPrefix($pid);
    if (!$template['template_id']) {
        return false;
    }

    $tplfile_handler = xoops_gethandler('tplfile');

    $dir  = basename(dirname(__DIR__));
    $mid  = $xoopsModule->getVar('mid');
    $file = $dir . '_prefix_' . $pid . '.tpl';

    $tplfiles = $tplfile_handler->find('default', 'module', $mid, $dir, $file, false);

    // if delete requested, delete it if we found it, and leave.
    if ($delete && count($tplfiles)) {
        $tplfile = $tplfiles[0];
        $tplfile_handler->delete($tplfile);
    }
    if ($delete) {
        return null;
    }

    if (count($tplfiles)) {
        $tplfile = $tplfiles[0];
        $isnew   = false;
    } else {
        $tplfile = $tplfile_handler->create();
        $isnew   = true;
    }

    $tplfile->setVar('tpl_source', $template['template_body'], true);
    $tplfile->setVar('tpl_refid', $mid);
    $tplfile->setVar('tpl_tplset', 'default');
    $tplfile->setVar('tpl_file', $file);
    $tplfile->setVar('tpl_desc', $template['template'], true);
    $tplfile->setVar('tpl_module', $dir);
    $tplfile->setVar('tpl_lastmodified', time());
    $tplfile->setVar('tpl_lastimported', 0);
    $tplfile->setVar('tpl_type', 'module');
    if ($isnew) {
        if (!$tplfile_handler->insert($tplfile)) {
            echo '<span style="color:#ff0000;">ERROR: Could not insert template <b>' . htmlspecialchars($file) . '</b> to the database.</span><br />';
        } else {
            $tplid = $tplfile->getVar('tpl_id');
            echo 'Template <b>' . htmlspecialchars($file) . '</b> added to the database. (ID: <b>' . $tplid . '</b>)<br />';
        }
    }
    if (!$tplfile_handler->forceUpdate($tplfile)) {
        echo '<span style="color:#ff0000;">ERROR: Could not update template <b>' . htmlspecialchars($file) . '</b> to the database.</span><br />';
    } else {
        $tplid = $tplfile->getVar('tpl_id');
        echo 'Template <b>' . htmlspecialchars($file) . '</b> updated to the database. (ID: <b>' . $tplid . '</b>)<br />';
    }

    return null;
}

/**
 * @param $row
 * @param $action
 *
 * @return string
 */
function templateForm($row, $action)
{
    if (empty($row)) {
        return false;
    }

    $form = '<form action="prefixes.php"  method="POST">';
    $form .= '<input type="hidden" name="pid" value="' . $row['prefix_id'] . '">';
    $form .= '<input type="hidden" name="op" value="updatetemplate">';
    $form .= '<tr><td class="head" width="10%">' . _AD_GWIKI_TEMPLATE_NAME . '</td><td class="odd"><input name="template" type="text" size="25" value="' . htmlspecialchars($row['template'], ENT_QUOTES) . '" ></td></tr>';
    $form .= '<tr><td class="head">' . _AD_GWIKI_TEMPLATE_BODY . '</td><td class="odd"><textarea name="template_body" rows="20" cols="80">' . htmlspecialchars($row['template_body'], ENT_QUOTES) . '</textarea></td></tr>';
    $form .= '<tr><td class="head">' . _AD_GWIKI_TEMPLATE_NOTES . '</td><td class="odd"><textarea name="template_notes" rows="2" cols="80">' . htmlspecialchars($row['template_notes'], ENT_QUOTES) . '</textarea></td></tr>';
    $form .= '<tr><td class="head"> </td><td class="odd"><input type="submit" value="' . _AD_GWIKI_NAMESPACE_SUBMIT . '"></td></tr>';
    $form .= '</form>';

    return $form;
}

/**
 * @param $pid
 */
function newTemplate($pid)
{
    $row = getPrefix($pid);

    adminTableStart(_AD_GWIKI_TEMPLATE_NEW, 2);

    $row['template']      = $row['prefix'] . ' ' . _AD_GWIKI_NAMESPACE_PREFIX;
    $row['template_body'] = file_get_contents('../templates/gwiki_view.tpl');

    echo templateForm($row, 'new');
    adminTableEnd(array(_BACK => 'prefixes.php?pid=' . $pid . '&op=edit'));
}

/**
 * @param $pid
 */
function editTemplate($pid)
{
    $row = getPrefix($pid);

    adminTableStart(_AD_GWIKI_TEMPLATE_EDIT, 2);
    echo templateForm($row, 'edit');
    adminTableEnd(array(_AD_GWIKI_DELETE => "prefixes.php?pid={$pid}&op=deletetemplate", _BACK => 'prefixes.php?pid=' . $pid . '&op=edit'));
}

/**
 * @param $pid
 */
function deleteTemplate($pid)
{
    global $xoopsDB;

    $row = getPrefix($pid);

    if ($row['template_id']) {
        installTemplate($pid, true);

        $sql = 'UPDATE ' . $xoopsDB->prefix('gwiki_prefix');
        $sql .= ' SET prefix_template_id = \'0\'';
        //        $sql .= ' WHERE prefix_id = "'.$pid.'" ';
        $sql .= " WHERE prefix_id = '{$pid}' ";
        $result = $xoopsDB->queryF($sql);

        $sql = 'DELETE FROM ' . $xoopsDB->prefix('gwiki_template');
        $sql .= ' WHERE template_id = "' . $row['template_id'] . '" ';
        $result = $xoopsDB->queryF($sql);
    }
    redirect_header("prefixes.php", 2, _MD_GWIKI_DBUPDATED);
}

/**
 * @param $string
 *
 * @return string
 */
function gpcStrip($string)
{
    if (get_magic_quotes_gpc()) {
        $string = stripslashes($string);
    }

    return $string;
}

/**
 * @param $pid
 */
function updateTemplate($pid)
{
    global $xoopsDB, $wikiPage;

    $row = getPrefix($pid);

    if (isset($_POST['template'])) {
        $row['template'] = gpcStrip($_POST['template']);
    }
    if (isset($_POST['template_body'])) {
        $row['template_body'] = gpcStrip($_POST['template_body']);
    }
    if (isset($_POST['template_notes'])) {
        $row['template_notes'] = gpcStrip($_POST['template_notes']);
    }

    if ($row['template_id']) {
        $sql = 'UPDATE ' . $xoopsDB->prefix('gwiki_template');
        $sql .= ' SET template = \'' . $wikiPage->escapeForDB($row['template']) . '\'';
        $sql .= ' , template_body = \'' . $wikiPage->escapeForDB($row['template_body']) . '\'';
        $sql .= ' , template_notes = \'' . $wikiPage->escapeForDB($row['template_notes']) . '\'';
        $sql .= ' WHERE template_id = "' . $row['template_id'] . '" ';
        $result = $xoopsDB->queryF($sql);
    } else {
        $sql = 'INSERT INTO ' . $xoopsDB->prefix('gwiki_template');
        $sql .= ' (template, template_body, template_notes)';
        $sql .= ' VALUES (\'' . $wikiPage->escapeForDB($row['template']) . '\'';
        $sql .= ' , \'' . $wikiPage->escapeForDB($row['template_body']) . '\'';
        $sql .= ' , \'' . $wikiPage->escapeForDB($row['template_notes']) . '\'';
        $sql .= ' ) ';
        $result = $xoopsDB->queryF($sql);
        if ($result) {
            $row['template_id'] = $xoopsDB->getInsertId();
        }

        $sql = 'UPDATE ' . $xoopsDB->prefix('gwiki_prefix');
        $sql .= ' SET prefix_template_id = \'' . $row['template_id'] . '\'';
        //        $sql .= ' WHERE prefix_id = "'.$pid.'" ';
        $sql .= " WHERE prefix_id = '{$pid}' ";
        $result = $xoopsDB->queryF($sql);
    }

    if ($result) {
        installTemplate($pid);
        $message = _MD_GWIKI_DBUPDATED;
    } else {
        $message = _MD_GWIKI_ERRORINSERT;
    }
    redirect_header("prefixes.php", 2, $message);
}

// utility
/**
 * @param     $action
 * @param int $pid
 */
function confirmAction($action, $pid = 0)
{
    if ($pid) {
        $row = getPrefix($pid);
    }
    adminTableStart(_AD_GWIKI_CONFIRM, 1);
    echo '<tr><td width="100%" >';
    echo '<div class="confirmMsg">';
    echo '<form method="post" action="prefixes.php">';

    switch ($action) {
        case 'delete':
            echo '<input type="hidden" name="pid" value="' . $pid . '" />';
            echo '<input type="hidden" id="op" name="op" value="deleteit" />';
            $confMsg = sprintf(_AD_GWIKI_NAMESPACE_CONFIRM_DEL, $row['prefix']);
            break;
        case 'deletetemplate':
            echo '<input type="hidden" name="pid" value="' . $pid . '" />';
            echo '<input type="hidden" id="op" name="op" value="deleteittemplate" />';
            $confMsg = sprintf(_AD_GWIKI_TEMPLATE_CONFIRM_DEL, $row['template']);
            break;
    }

    echo '<p align="center">' . $confMsg . '<br /><br />
        <input type="submit" value="' . _YES . '">
        <input type="button" onclick="history.back();" value="' . _NO . '"></p></form></div>';
    echo '</td></tr>';
    adminTableEnd(array(_BACK => 'prefixes.php'));
}

/**
 * @param      $string
 * @param bool $trim
 *
 * @return string
 */
function cleaner($string, $trim = true)
{
    //  $string=stripcslashes($string);
    $string = html_entity_decode($string);
    $string = strip_tags($string);
    if ($trim) {
        $string = trim($string);
    }
    $string = stripslashes($string);

    return $string;
}

/**
 * @param $op
 * @param $pid
 */
function tobedone($op, $pid)
{
    echo "Not yet implemented: " . $op . ' pid=' . $pid . '<br />';
}

$pid = 0;
$op  = '';
// get variables
if (!empty($_GET['pid'])) {
    $pid = (int)($_GET['pid']);
}
if (!empty($_GET['op'])) {
    $op = cleaner($_GET['op']);
}
// override get with post
if (!empty($_POST['pid'])) {
    $pid = (int)($_POST['pid']);
}
if (!empty($_POST['op'])) {
    $op = cleaner($_POST['op']);
}

switch ($op) {
    case 'edit':
        editPrefix($pid);
        break;
    case 'new':
        newPrefix();
        break;
    case 'delete':
        confirmAction($op, $pid);
        break;
    case 'deleteit':
        deletePrefix($pid);
        break;
    case 'update':
        updatePrefix($pid);
        break;
    case 'newtemplate':
        newTemplate($pid);
        break;
    case 'edittemplate':
        editTemplate($pid);
        break;
    case 'deletetemplate':
        confirmAction($op, $pid);
        break;
    case 'deleteittemplate':
        deleteTemplate($pid);
        break;
    case 'updatetemplate':
        updateTemplate($pid);
        break;
    default:
        showPrefixes();
        break;
}

include __DIR__ . '/footer.php';
