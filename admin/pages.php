<?php
/**
* admin/pages.php - manage wiki page revision
*
* @copyright  Copyright © 2013 geekwright, LLC. All rights reserved. 
* @license    gwiki/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/
include 'header.php';

include_once '../include/functions.php';
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';

if(!$xoop25plus) adminmenu(3);
else echo $moduleAdmin->addNavigation('pages.php');

function post_clean_request($url, $params)
{
    foreach ($params as $key => &$val) {
      if (is_array($val)) $val = implode(',', $val);
        $post_params[] = $key.'='.urlencode($val);
    }
    $post_string = implode('&', $post_params);

    $parts=parse_url($url);

    $fp = fsockopen($parts['host'],
        isset($parts['port'])?$parts['port']:80,
        $errno, $errstr, 30);

    $out = 'POST '.$parts['path']." HTTP/1.1\r\n";
    $out.= 'Host: '.$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out.= 'Content-Length: '.strlen($post_string)."\r\n";
    $out.= "Connection: Close\r\n\r\n";
    if (isset($post_string)) $out.= $post_string;

    fwrite($fp, $out);
    fclose($fp);
}

function showPages($message=null)
{
global $xoopsDB;   
echo <<<EOT
<style>
div.pagination.default {display:inline;}
form {display:inline;}
</style>
EOT;
	$total=0;
	$limit=10;
	$start=0;
	$like='';
	if(!empty($_GET['start'])) $start=intval($_GET['start']);
	if(!empty($_GET['like'])) $like=cleaner($_GET['like']);

	$sql='SELECT COUNT(*) FROM '.$xoopsDB->prefix('gwiki_pageids');
	if(!empty($like)) $sql.=" WHERE keyword LIKE '{$like}%' ";
	$result = $xoopsDB->query($sql);
	if ($result) {
		$myrow=$xoopsDB->fetchRow($result);
		$total=$myrow[0];
	}

	echo '<form method="get"><b>'. _AD_GWIKI_KEYWORD_FILTER . '</b><input type="text" name="like"><input type="submit"></form><br />';
	adminTableStart(_AD_GWIKI_ADMINTITLE,4);
	if(!empty($message)) {
		echo '<tr><td colspan="4" align="center"><br /><b>'.$message.'</b><br /><br /></td></tr>';
	}
	echo '<tr><th width="15%">'._AD_GWIKI_KEYWORD.'</th><th>'._MD_GWIKI_TITLE.'</th><th width="5%">'._AD_GWIKI_REVISIONS.'</th><th width="30%">'._AD_GWIKI_ACTION.'</th></tr>';
 	$sqlwhere=''; if(!empty($like)) $sqlwhere=" WHERE t1.keyword LIKE '{$like}%' ";   
	$sql = 'SELECT t1.keyword, COUNT(*), t2.title, t2.admin_lock, t2.active FROM '.$xoopsDB->prefix('gwiki_pages').' t1 '.
		' LEFT JOIN '.$xoopsDB->prefix('gwiki_pages').' t2 on t1.keyword = t2.keyword and t2.active = 1 '.
		$sqlwhere.' GROUP BY keyword ';
	$result = $xoopsDB->query($sql, $limit, $start);
    
	for ($i = 0; $i < $xoopsDB->getRowsNum($result); $i++) {
		list($page, $revs, $title, $lock, $active) = $xoopsDB->fetchRow($result);
		if(empty($active)) $title=_AD_GWIKI_NO_ACTIVE_PAGE;
		//if(empty($title)) $title=_AD_GWIKI_NO_ACTIVE_PAGE;
		if($lock) $lockaction=' | <a href="pages.php?page='.$page.'&op=unlock">'._AD_GWIKI_UNLOCK.'</a>';
		else $lockaction=' | <a href="pages.php?page='.$page.'&op=lock">'._AD_GWIKI_LOCK.'</a>';
		echo '<tr class="'.(($i % 2)?"even":"odd").'"><td><a href="pages.php?page='.$page.'&op=history">'.$page.'</a></td>' .
			'<td>'.htmlspecialchars($title, ENT_QUOTES).'</td>'.
			'<td>'.$revs.'</td>'.
			'<td><a href="pages.php?page='.$page.'&op=display">'._AD_GWIKI_VIEW.'</a> | <a href="pages.php?page='.$page.'&op=history">'._AD_GWIKI_HISTORY.'</a>'.$lockaction.' | <a href="pages.php?page='.$page.'&op=delete">'._DELETE.'</a></td></tr>';
	}
	if ($i == 0) {
		echo '<tr class="odd"><td colspan="3">'._AD_GWIKI_EMPTYWIKI.'</td></tr>';
	}

	$endarray[_AD_GWIKI_CLEANUPDB]='pages.php?op=clean';
	$endarray[_AD_GWIKI_PARTITION]='pages.php?op=partition';
	$endarray[_AD_GWIKI_ADD_HELP]='pages.php?op=addhelp';
	// set up pagenav
	$pager='';
	if ($total > $limit) {
		include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
		$likenav=''; if(!empty($like)) $likenav="like={$like}"; 
		$nav = new xoopsPageNav($total,$limit,$start,'start',$likenav);
		if(intval($total/$limit) < 5) $pager=$nav->renderNav();
		else $pager= _AD_GWIKI_PAGENAV . $nav->renderSelect(false);
	}
	if(!empty($pager)) $endarray['!PREFORMATTED!']=$pager;
	
	adminTableEnd($endarray);

}

function showHistory($page)
{
    global $xoopsDB, $xoopsModuleConfig, $wikiPage;
    
    allowRestoration($page);
    
    adminTableStart(_AD_GWIKI_ADMINTITLE.' : '.$page,4);
    echo '<tr><th>'._MD_GWIKI_TITLE.'</th><th width="20%">'._AD_GWIKI_MODIFIED.'</th><th width="10%">'._AD_GWIKI_AUTHOR.'</th><th width="30%">'._AD_GWIKI_ACTION.'</th></tr>';
    
    $sql = "SELECT gwiki_id, title, body, lastmodified, uid, active, FROM_UNIXTIME(lastmodified) FROM ".$xoopsDB->prefix('gwiki_pages')." WHERE keyword='{$page}' ORDER BY active DESC, lastmodified DESC";
    $result = $xoopsDB->query($sql);
    
    for ($i = 0; $i < $xoopsDB->getRowsNum($result); $i++) {
        list($id, $title, $body, $lastmodified, $uid, $active, $modified) = $xoopsDB->fetchRow($result);
        
        echo '<tr class="'.(($i % 2)?"even":"odd").'"><td><a href="pages.php?page='.$page.'&op=display&id='.$id.'">'.htmlspecialchars($title,ENT_QUOTES).'</a></td>';
        echo '<td>'.$modified.($active?'*':'').'</td>';
        echo '<td>'.$wikiPage->getUserName($uid).'</td>';
        echo '<td><a href="pages.php?page='.$page.'&op=display&id='.$id.'">'._AD_GWIKI_VIEW.'</a> | <a href="javascript:restoreRevision(\''.$id.'\');">'._AD_GWIKI_RESTORE.'</a> ';
        echo ' | <a href="pages.php?page='.$page.'&op=fix&id='.$id.'">'._AD_GWIKI_FIX.'</a> | <a href="pages.php?page='.$page.'&op=tool&id='.$id.'">'._AD_GWIKI_PAGETOOLS.'</a>';
        echo ' | <a href="../edit.php?page='.$page.'&id='.$id.'">'._EDIT.'</a> </td></tr>';
    }
    if ($i == 0) {
        echo '<tr class="odd"><td colspan="4">'._MD_GWIKI_PAGENOTFOUND.'</td></tr>';
    }
    
    adminTableEnd(array(_BACK => 'pages.php?op=manage'));

}

function showPage($page, $id)
{
    global $xoopsDB, $xoopsModuleConfig, $wikiPage, $xoTheme;
    
    $dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
    if(is_object($xoTheme)) $xoTheme->addStylesheet(XOOPS_URL.'/modules/'.$dir.'/module.css'); 
    
//    xoops_cp_header();
    allowRestoration($page);

    $wikiPage->setWikiLinkURL('pages.php?page=%s&op=history');
    $wikiPage->getPage($page,$id);
    if(empty($id)) $id=$wikiPage->gwiki_id;

    adminTableStart(_AD_GWIKI_SHOWPAGE,1);
    echo '<tr><td width="100%" >';
    echo '<div style="width: 94%; margin: 2em;">';
    echo '<p style="padding-bottom: 2px; border-bottom: 1px solid #000000;">'._MD_GWIKI_PAGE.": <strong>{$page}</strong> - "._MD_GWIKI_LASTMODIFIED." <i>".date($xoopsModuleConfig['date_format'], $wikiPage->lastmodified)."</i> "._MD_GWIKI_BY." <i>".$wikiPage->getUserName($wikiPage->uid)."</i></p>";
    
    echo '<div id="wikipage"><h1 class="wikititle" id="toc0">' . htmlspecialchars($wikiPage->title) . '</h1>';
    echo $wikiPage->renderPage();
    echo '</div>';

    echo '</div>';
    echo '</td></tr>';    
    adminTableEnd(array( _BACK => "pages.php?page={$page}&op=history", 
          _AD_GWIKI_RESTORE => "javascript:restoreRevision('{$id}');", 
          _AD_GWIKI_PAGETOOLS => "pages.php?page={$page}&op=tool&id={$id}",
          _AD_GWIKI_FIX => "pages.php?page={$page}&op=fix&id={$id}" ));
}

function showPageTool($page, $id)
{
    global $xoopsDB, $xoopsModuleConfig, $wikiPage, $xoTheme;
    
    $dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
    if(is_object($xoTheme)) $xoTheme->addStylesheet(XOOPS_URL.'/modules/'.$dir.'/module.css'); 
    
//    xoops_cp_header();
    allowRestoration($page);

    $wikiPage->setWikiLinkURL("javascript:alert('%s');");
    $wikiPage->getPage($page,$id);

$form = new XoopsThemeForm(_AD_GWIKI_PAGETOOLS.": {$page}", "gwikiform", "pages.php?page={$page}");
$form->addElement(new XoopsFormSelectUser( 'user', 'uid', true, $wikiPage->uid ));
$form->addElement(new XoopsFormDateTime( _MD_GWIKI_LASTMODIFIED, 'lastmodified', $size = 15, $wikiPage->lastmodified ));
$form->addElement(new XoopsFormHidden('op', 'toolupdate'));
$form->addElement(new XoopsFormHidden('page', $page));
$form->addElement(new XoopsFormHidden('id', $id));
$form->addElement(new XoopsFormButton("", "submit", _SUBMIT, "submit"));
//$form->addElement(new XoopsFormText(_MD_GWIKI_TITLE, "title", 40, 250, $title));
//$form->addElement(new XoopsFormTextArea(_MD_GWIKI_BODY, 'body', $body, 20, 80));
//$var_name = strtotime($var_name['date']) + $var_name['time'];

    adminTableStart(_AD_GWIKI_PAGETOOLS,1);
    echo '<tr><td width="100%" >';
    echo '<div style="width: 94%; margin: 2em;">';
    echo '<p style="padding-bottom: 2px; border-bottom: 1px solid #000000;">'._MD_GWIKI_PAGE.": <strong>{$page}</strong> - "._MD_GWIKI_LASTMODIFIED." <i>".date($xoopsModuleConfig['date_format'], $wikiPage->lastmodified)."</i> "._MD_GWIKI_BY." <i>".$wikiPage->getUserName($wikiPage->uid)."</i></p>";
    echo $form->render();    
    echo '<br /><div id="wikipage" style="height: 120px; overflow: auto;" ><h1 class="wikititle" id="toc0">' . htmlspecialchars($wikiPage->title) . '</h1>';
    echo $wikiPage->renderPage();
    echo '</div>';

    echo '</div>';
    echo '</td></tr>';    
    adminTableEnd(array( _BACK => "pages.php?page={$page}&op=history", 
          _AD_GWIKI_RESTORE => "javascript:restoreRevision('{$id}');", 
          _AD_GWIKI_FIX => "pages.php?page={$page}&op=fix&id={$id}" ));
}

function pageToolUpdate($page, $id)
{
    global $xoopsDB;

    if(isset($_POST['uid'])) $uid = intval($_POST['uid']);
    if(isset($_POST['lastmodified'])) $modified = $_POST['lastmodified'];
    if(empty($uid) || empty($modified)) return false;
    $lastmodified = strtotime($modified['date']) + $modified['time'];
//print_r($modified);
    $sql = "UPDATE ".$xoopsDB->prefix('gwiki_pages')." SET uid = {$uid}, lastmodified = {$lastmodified}  WHERE keyword='{$page}' AND gwiki_id='{$id}'";
    $result=$xoopsDB->query($sql);

    return $result;
}

function confirmAction($action, $keyword = '', $id = -1)
{

	adminTableStart(_AD_GWIKI_CONFIRM,1);
	echo '<tr><td width="100%" >';
	echo '<div class="confirmMsg">';
	echo '<form method="post" action="pages.php">';
    
	switch ($action) {
		case 'clean':
			echo '<input type="hidden" name="op" value="cleanit" />';
			$confMsg=_AD_GWIKI_CONFIRM_CLEAN;
			break;
		case 'delete':
			echo '<input type="hidden" name="page" value="'.$keyword.'" />';
			echo '<input type="hidden" id="op" name="op" value="deleteit" />';
			$confMsg=sprintf(_AD_GWIKI_CONFIRM_DEL,$keyword);
			break;
		case 'fix':
			echo '<input type="hidden" name="page" value="'.$keyword.'" />';
			echo '<input type="hidden" id="id" name="id" value="'.$id.'" />
				<input type="hidden" id="op" name="op" value="fixit" />';
			$confMsg=sprintf(_AD_GWIKI_CONFIRM_FIX,$keyword);
			break;
		case 'lock':
			echo '<input type="hidden" name="page" value="'.$keyword.'" />';
			echo '<input type="hidden" id="op" name="op" value="lockit" />';
			$confMsg=sprintf(_AD_GWIKI_CONFIRM_LOCK,$keyword);
			break;
		case 'unlock':
			echo '<input type="hidden" name="page" value="'.$keyword.'" />';
			echo '<input type="hidden" id="op" name="op" value="unlockit" />';
			$confMsg=sprintf(_AD_GWIKI_CONFIRM_UNLOCK,$keyword);
			break;
		case 'partition':
//			echo '<input type="hidden" name="page" value="'.$keyword.'" />';
			echo '<input type="hidden" id="op" name="op" value="partitionit" />';
			$confMsg=_AD_GWIKI_CONFIRM_PARTITION;
			break;
		case 'addhelp':
//			echo '<input type="hidden" name="page" value="'.$keyword.'" />';
			echo '<input type="hidden" id="op" name="op" value="addhelpit" />';
			$confMsg=_AD_GWIKI_CONFIRM_ADD_HELP;
			break;
	}

	echo '<p align="center">'.$confMsg.'<br /><br />
		<input type="submit" value="'._YES.'">
		<input type="button" onclick="history.back();" value="'._NO.'"></p></form></div>';
	echo '</td></tr>';
    adminTableEnd(array(_BACK => 'pages.php?op=manage'));
}

function getRevision($page, $id)
{
    global $xoopsDB;
    
    $sql = "SELECT title, body, lastmodified, uid FROM ".$xoopsDB->prefix('gwiki_pages')." WHERE gwiki_id='{$id}' AND keyword='{$page}'";
    $result = $xoopsDB->query($sql);
    
    return $xoopsDB->fetchRow($result);
}


function fixRevision($page, $id)
{
    global $xoopsDB, $wikiPage;
    
    $result=$wikiPage->setRevision($page, $id);
    if($result) {
      $sql = "DELETE FROM ".$xoopsDB->prefix('gwiki_pages')." WHERE keyword='{$page}' AND active=0 ";
      $result=$xoopsDB->query($sql);
    }
    
    return $result;
}


function checkForPartitions()
{
	global $xoopsDB;
    
	$sql = 'SELECT PARTITION_NAME FROM INFORMATION_SCHEMA.PARTITIONS WHERE TABLE_SCHEMA = \''.XOOPS_DB_NAME.'\' AND TABLE_NAME  = \''.$xoopsDB->prefix('gwiki_pages').'\'';
	$result=$xoopsDB->query($sql);
	$partitions=$xoopsDB->getRowsNum($result);
	if($partitions>1) return true;
	return false;
}

function createPartitions()
{
	global $xoopsDB;
	
	if(checkForPartitions()) {
		$message=_AD_GWIKI_PARTITION_ALREADY;
	}
	else {
		$tablename = $xoopsDB->prefix('gwiki_pages');
		$sql = 'ALTER TABLE '.$tablename. ' PARTITION BY LIST (active) ';
		$sql.= '(PARTITION '.$tablename.'_inactive VALUES IN (0), ';
		$sql.= ' PARTITION '.$tablename.'_active VALUES IN (1) )';
		$result=$xoopsDB->query($sql);
		if($result) $message=_AD_GWIKI_PARTITION_OK;
		else $message=_AD_GWIKI_PARTITION_FAILED;
	}
	return $message;
}

function createHelpPages()
{
	global $xoopsDB;
	
	$result=$xoopsDB->queryFromFile(dirname(__FILE__).'/helppages.sql');
	if($result) $message=_AD_GWIKI_ADD_HELP_OK;
	else $message=_AD_GWIKI_ADD_HELP_FAILED;

	return $message;
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
    <form id="restore" name="restore" action="pages.php" method="post">
    <input type="hidden" id="op" name="op" value="restore" />
    <input type="hidden" id="page" name="page" value="'.$page.'" />
    <input type="hidden" id="id" name="id" value="" />
    </form>';
}


// page, op, id
$page = (isset($_GET['page']))?cleaner($_GET['page']):"";
//$page = makeKeyWord((isset($_GET['page']))?cleaner($_GET['page']):"");
$op = (isset($_GET['op']))?cleaner($_GET['op']):"";

// $_POST variables we use
if(isset($_POST['op'])) $op = cleaner($_POST['op']);
if(isset($_POST['page'])) $page = cleaner($_POST['page']);
if(isset($_POST['id'])) $id = intval($_POST['id']);
//if(isset($_POST[''])) $ = intval($_POST['']);

switch ($op) {
case 'history':
    showHistory($page);
    break;

case 'display':
	if(!empty($_GET['id'])) $id=intval($_GET['id']);
	else $id=null;
    showPage($page, $id);
    break;

case 'restore':
    $success = $wikiPage->setRevision($page, $id);
    redirect_header('pages.php?page='.$page.'&op=history', 2, ($success)?_MD_GWIKI_DBUPDATED:_MD_GWIKI_ERRORINSERT);
    break;

case 'fix':
    confirmAction('fix', $page, intval($_GET['id']));
    break;

case 'fixit':
    $success = fixRevision($page, $id);
    redirect_header('pages.php?page='.$page.'&op=history', 2, ($success)?_MD_GWIKI_DBUPDATED:_MD_GWIKI_ERRORINSERT);
    break;

case 'tool':
    showPageTool($page, intval($_GET['id']));
    break;

case 'toolupdate':
    $success = pageToolUpdate($page, $id);
    $message = $success ? _MD_GWIKI_DBUPDATED : _MD_GWIKI_ERRORINSERT ;
    $op='';
    showPages($message);
    break;

case 'delete':
    confirmAction('delete',$page);
    break;

case 'deleteit':
//  mark all versions inactive -- these will disappear as they age and the database is cleaned
    $sql = "UPDATE ".$xoopsDB->prefix('gwiki_pages')." SET active = 0 WHERE keyword='{$page}' ";

    $success = $xoopsDB->query($sql);
    redirect_header('pages.php?op=manage', 2, ($success)?_MD_GWIKI_DBUPDATED:_MD_GWIKI_ERRORINSERT);
    break;

case 'clean':
    confirmAction('clean');
    break;

case 'cleanit':
	// delete inactive pages older than config option retain_days
	$retaindays=intval($xoopsModuleConfig['retain_days']);
	if($retaindays>0) {
		$dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
		$url = XOOPS_URL.'/modules/'.$dir.'/cleanit.php';
		$params=array('check'=>$retaindays);
		post_clean_request($url, $params);
		$message=_MD_GWIKI_CLEAN_STARTED;
	}
	else {
		$message=_MD_GWIKI_CLEAN_DISABLED;
	}
	$op='';
	showPages($message);
	break;

case 'lock':
    confirmAction('lock',$page);
    break;

case 'lockit':
    $sql = "UPDATE ".$xoopsDB->prefix('gwiki_pages')." SET admin_lock = 1 WHERE keyword='{$page}' ";

    $success = $xoopsDB->query($sql);
    redirect_header('pages.php?op=manage', 2, ($success)?_MD_GWIKI_DBUPDATED:_MD_GWIKI_ERRORINSERT);
    break;

case 'unlock':
    confirmAction('unlock',$page);
    break;

case 'unlockit':
    $sql = "UPDATE ".$xoopsDB->prefix('gwiki_pages')." SET admin_lock = 0 WHERE keyword='{$page}' ";

    $success = $xoopsDB->query($sql);
    redirect_header('pages.php?op=manage', 2, ($success)?_MD_GWIKI_DBUPDATED:_MD_GWIKI_ERRORINSERT);
    break;

case 'partition':
    if(checkForPartitions()) showPages(_AD_GWIKI_PARTITION_ALREADY);
    else confirmAction('partition','');
    break;

case 'partitionit':
    $message=createPartitions();
    showPages($message);
    break;

case 'addhelp':
    confirmAction('addhelp','');
    break;

case 'addhelpit':
    $message=createHelpPages();
    showPages($message);
    break;

case 'manage':
default:
    showPages();
    break;

}

include 'footer.php';
?>
