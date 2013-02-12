<?php
include "admin_header.php";

function showPages()
{
    global $xoopsDB, $myts;
    
    xoops_cp_header();
    echo '<h4>'._AM_WIKIMOD_ADMINTITLE.'</h4>';
    
    echo '<table border="0" cellpadding="0" cellspacing="1" width="100%" class="outer">';
    echo '<tr class="head"><td width="15%"><b>'._AM_WIKIMOD_KEYWORD.'</b></td><td><b>'._MD_WIKIMOD_TITLE.'</b></td><td width="5%"><b>'._AM_WIKIMOD_REVISIONS.'</b></td><td width="30%"><b>'._AM_WIKIMOD_ACTION.'</b></td></tr>';
    
    $sql = "SELECT w1.keyword, w1.title FROM ".$xoopsDB->prefix(_TAB_WIKIMOD)." AS w1 LEFT JOIN ".$xoopsDB->prefix(_TAB_WIKIMOD)." AS w2 ON w1.keyword=w2.keyword AND w1.id<w2.id WHERE w2.id IS NULL ORDER BY w1.keyword ASC";
    $sql2 = "SELECT keyword, COUNT(*) FROM ".$xoopsDB->prefix(_TAB_WIKIMOD)." GROUP BY keyword ORDER BY keyword ASC";
    $result = $xoopsDB->query($sql);
    $result2 = $xoopsDB->query($sql2);
    
    for ($i = 0; $i < $xoopsDB->getRowsNum($result); $i++) {
        list($page, $title) = $xoopsDB->fetchRow($result);
        list($page, $revs) = $xoopsDB->fetchRow($result2);
        
        echo '<tr class="'.(($i % 2)?"even":"odd").'"><td><a href="index.php?page='.$page.'&op=history">'.$page.'</a></td>
        <td>'.$myts->htmlSpecialChars($title).'</td>
        <td>'.$revs.'</td>
        <td><a href="../index.php?page='.$page.'">'._AM_WIKIMOD_VIEW.'</a> | <a href="index.php?page='.$page.'&op=history">'._AM_WIKIMOD_HISTORY.'</a> | <a href="index.php?page='.$page.'&op=delete">'._DELETE.'</a></td></tr>';
    }
    if ($i == 0) {
        echo '<tr class="odd"><td colspan="3">'._AM_WIKIMOD_EMPTYWIKI.'</td></tr>';
    }
    
    echo '</table></br>
    <p>[<a href="index.php?op=clean">'._AM_WIKIMOD_CLEANUPDB.'</a>]</p>';
    xoops_cp_footer();
}

function showHistory($page)
{
    global $xoopsDB, $myts;
    
    xoops_cp_header();
    echo "<h4>"._AM_WIKIMOD_ADMINTITLE.": $page</h4>";
    allowRestoration($page);
    
    echo '<table border="0" cellpadding="0" cellspacing="1" width="100%" class="outer">';
    echo '<tr class="head"><td><b>'._MD_WIKIMOD_TITLE.'</b></td><td width="20%"><b>'._AM_WIKIMOD_MODIFIED.'</b></td><td width="10%"><b>'._AM_WIKIMOD_AUTHOR.'</b></td><td width="30%"><b>'._AM_WIKIMOD_ACTION.'</b></td></tr>';
    
    $sql = "SELECT id, title, body, lastmodified, u_id FROM ".$xoopsDB->prefix(_TAB_WIKIMOD)." WHERE keyword='$page' ORDER BY id DESC";
    $result = $xoopsDB->query($sql);
    
    for ($i = 0; $i < $xoopsDB->getRowsNum($result); $i++) {
        list($id, $title, $body, $lastmodified, $uid) = $xoopsDB->fetchRow($result);
        
        echo '<tr class="'.(($i % 2)?"even":"odd").'"><td><a href="index.php?page='.$page.'&op=display&id='.$id.'">'.$myts->htmlSpecialChars($title).'</a></td>';
        echo '<td>'.$lastmodified.'</td>';
        echo '<td>'.getUserName($uid).'</td>';
        echo '<td><a href="index.php?page='.$page.'&op=display&id='.$id.'">'._AM_WIKIMOD_VIEW.'</a> | <a href="javascript:restoreRevision(\''.$id.'\');">'._AM_WIKIMOD_RESTORE.'</a> | <a href="index.php?page='.$page.'&op=fix&id='.$id.'">'._AM_WIKIMOD_FIX.'</a></td></tr>';
    }
    if ($i == 0) {
        echo '<tr class="odd"><td colspan="4">'._MD_WIKIMOD_PAGENOTFOUND.'</td></tr>';
    }
    
    echo '</table>';
    echo '<p style="text-align: right;"><a href="index.php?op=manage">--&gt; '._BACK.'</a></p>';
    xoops_cp_footer();
}

function showPage($page, $id)
{
    global $xoopsDB, $xoopsModuleConfig, $myts;
    
    xoops_cp_header();
    allowRestoration($page);
    
    list($title, $body, $lastmodified, $uid) = getRevision($page, $id);
    
    echo '<p style="padding-bottom: 2px; border-bottom: 1px solid #000000;">'._MD_WIKIMOD_PAGE.": <strong>$page</strong> - "._MD_WIKIMOD_LASTMODIFIED." <i>".date($xoopsModuleConfig['date_format'], @strtotime($lastmodified))."</i> "._MD_WIKIMOD_BY." <i>".getUserName($uid)."</i></p>";
    
    echo '<h1>'.$myts->htmlSpecialChars($title).'</h1>';
    echo wikiDisplay($body);
    
    echo '<p style="text-align: right; padding-top: 2px; border-top: 1px solid #000000;"><a href="index.php?page='.$page.'&op=history">--&gt; '._BACK.'</a> | <a href="javascript:restoreRevision(\''.$id.'\');">'._AM_WIKIMOD_RESTORE.'</a> | <a href="index.php?page='.$page.'&op=fix&id='.$id.'">'._AM_WIKIMOD_FIX.'</a></p>';
    
    xoops_cp_footer();
}

function confirmDelete($keyword = '', $id = -1)
{
    xoops_cp_header();
    OpenTable();
    
    echo '<form method="post" action="index.php">';
    if ($keyword) {
        echo '<input type="hidden" name="page" value="'.$keyword.'" />';
        if ($id > -1) {
            echo '<input type="hidden" id="id" name="id" value="'.$id.'" />
            <input type="hidden" id="op" name="op" value="fixit" />
            <p align="center">'._AM_WIKIMOD_CONFIRMFIX;
        } elseif ($keyword != '') {
            echo '<input type="hidden" id="op" name="op" value="deleteit" />
            <p align="center">'._AM_WIKIMOD_CONFIRMDEL;
        }
    } else {
        echo '<input type="hidden" name="op" value="cleanit" />
        <p align="center">'._AM_WIKIMOD_CONFIRMCLEAN;
    }
    echo (($keyword)?': <strong>'.$keyword.'</strong>':'').'?<br /><br />
    <input type="submit" value="'._YES.'">
    <input type="button" onclick="history.back();" value="'._NO.'"></p></form>';
    
    CloseTable();
    xoops_cp_footer();
}

function getRevision($page, $id)
{
    global $xoopsDB;
    
    $sql = "SELECT title, body, lastmodified, u_id FROM ".$xoopsDB->prefix(_TAB_WIKIMOD)." WHERE id='$id' AND keyword='$page'";
    $result = $xoopsDB->query($sql);
    
    return $xoopsDB->fetchRow($result);
}

function fixRevision($page, $id)
{
    global $xoopsDB;
    
    $sql = "DELETE FROM ".$xoopsDB->prefix(_TAB_WIKIMOD)." WHERE keyword='$page' AND id<'$id'";
    
    return $xoopsDB->query($sql);
}

function allowRestoration($page)
{
    echo '<script type="text/javascript">
    <!--
        function restoreRevision(id)
        {
            document.restore.id.value = id;
            document.restore.submit();
        }
    // -->
    </script>
    <form id="restore" name="restore" action="index.php" method="post">
    <input type="hidden" id="op" name="op" value="restore" />
    <input type="hidden" id="page" name="page" value="'.$page.'" />
    <input type="hidden" id="id" name="id" value="" />
    </form>';
}


$page = makeKeyWord((isset($HTTP_GET_VARS['page']))?$HTTP_GET_VARS['page']:"");
$op = (isset($HTTP_GET_VARS['op']))?$HTTP_GET_VARS['op']:"";
if (!empty($HTTP_POST_VARS)) {
    extract($HTTP_POST_VARS);
}

switch ($op) {
case "history":
    showHistory($page);
    break;

case "display":
    showPage($page, $HTTP_GET_VARS["id"]);
    break;

case "restore":
    list($title, $body) = getRevision($page, $id);
    $success = addRevision($page, $title, $body, $xoopsUser->getVar('uid'));
    redirect_header("index.php?page=$page&op=history", 2, ($success)?_MD_WIKIMOD_DBUPDATED:_MD_WIKIMOD_ERRORINSERT);
    break;

case "fix":
    confirmDelete($page, $HTTP_GET_VARS["id"]);
    break;

case "fixit":
    $success = fixRevision($page, $id);
    redirect_header("index.php?page=$page&op=history", 2, ($success)?_MD_WIKIMOD_DBUPDATED:_MD_WIKIMOD_ERRORINSERT);
    break;

case "delete":
    confirmDelete($page);
    break;

case "deleteit":
    $sql = "DELETE FROM ".$xoopsDB->prefix(_TAB_WIKIMOD)." WHERE keyword='$page'";
    $success = $xoopsDB->query($sql);
    redirect_header("index.php?op=manage", 2, ($success)?_MD_WIKIMOD_DBUPDATED:_MD_WIKIMOD_ERRORINSERT);
    break;

case "clean":
    confirmDelete();
    break;

case "cleanit":
    $success = true;
    $sql = "SELECT keyword, MAX(id) AS id FROM ".$xoopsDB->prefix(_TAB_WIKIMOD)." WHERE lastmodified<'".date("Y-m-d H:i:s", time() - 61 * 24 * 3600)."' GROUP BY keyword";
    $result = $xoopsDB->query($sql);
    while ($content = $xoopsDB->fetcharray($result)) {
        $success &= fixRevision($content['keyword'], $content['id']);
    }
    redirect_header("index.php?op=manage", 2, ($success)?_MD_WIKIMOD_DBUPDATED:_MD_WIKIMOD_ERRORINSERT);
    break;

case "manage":
default:
    showPages();
    break;

}
?>