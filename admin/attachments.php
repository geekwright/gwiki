<?php
include 'header.php';
//include_once '../include/functions.php';

if(!$xoop25plus) adminmenu(6);

function cleaner($string,$trim=true) {
//	$string=stripcslashes($string);
	$string=html_entity_decode($string);
	$string=strip_tags($string);
	if($trim) $string=trim($string);
	$string=stripslashes($string);
	return $string;
}
  
function showAttachments()
{
global $xoopsDB, $wikiPage;
$dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
/*
gwiki_page_files
  file_id int(10) NOT NULL AUTO_INCREMENT,
  keyword varchar(128) NOT NULL DEFAULT '',
  file_name varchar(128) NOT NULL DEFAULT '',
  file_path varchar(255) NOT NULL DEFAULT '',
  file_type varchar(128) NOT NULL DEFAULT '',
  file_icon varchar(64) NOT NULL DEFAULT '',
  file_size int(10) NOT NULL DEFAULT '0',
  file_upload_date int(10) NOT NULL DEFAULT '0',
  file_description text,
  file_uid int(10) NOT NULL DEFAULT '0',
*/
$kw='';$fn='';$ty='';$ds='';
if(!empty($_GET['kw'])) $kw=cleaner($_GET['kw']);
if(!empty($_GET['fn'])) $fn=cleaner($_GET['fn']);
if(!empty($_GET['ty'])) $ty=cleaner($_GET['ty']);
if(!empty($_GET['ds'])) $ds=cleaner($_GET['ds']);

$q_kw='%'.$wikiPage->escapeForDB($kw).'%';
$q_fn='%'.$wikiPage->escapeForDB($fn).'%';
$q_ty='%'.$wikiPage->escapeForDB($ty).'%';
$q_ds='%'.$wikiPage->escapeForDB($ds).'%';

$likeclause='';
if(!empty($kw)) $likeclause .= (empty($likeclause)?'':' and ') . " keyword like '{$q_kw}' ";
if(!empty($fn)) $likeclause .= (empty($likeclause)?'':' and ') . " file_name like '{$q_fn}' ";
if(!empty($ty)) $likeclause .= (empty($likeclause)?'':' and ') . " file_type like '{$q_ty}' ";
if(!empty($ds)) $likeclause .= (empty($likeclause)?'':' and ') . " file_description like '{$q_ds}' ";
$whereclause=(empty($likeclause)?'':' where '.$likeclause);

echo <<<EOT
<style>
div.pagination.default {display:inline;}
form {display:inline;}
</style>
EOT;
	$total=0;
	$limit=10;
	$start=0;
	if(!empty($_GET['start'])) $start=intval($_GET['start']);

	$sql="SELECT count(*) FROM ".$xoopsDB->prefix('gwiki_page_files') . $whereclause;
	$result = $xoopsDB->query($sql);
	if ($result) {
		$myrow=$xoopsDB->fetchRow($result);
		$total=$myrow[0];
	}

	adminTableStart(_AD_GWIKI_FILES_LIST,9);
	echo '<tr><form method="get">'.
		'<td><input type="text" name="kw" size="10" value="'.$kw.'"></td>'.
		'<td><input type="text" name="fn" size="10" value="'.$fn.'"></td>'.
		'<td>&nbsp;</td>'.
		'<td><input type="text" name="ty" size="10" value="'.$ty.'"></td>'.
		'<td>&nbsp;</td>'.
		'<td>&nbsp;</td>'.
		'<td>&nbsp;</td>'.
		'<td><input type="text" name="ds" size="10" value="'.$ds.'"></td>'.
		'<td><input type="submit" value="'._AD_GWIKI_FILES_FILTER.'"></td>'.
		'</form></tr>';
	echo '<tr class="head">'.
		'<th>'._AD_GWIKI_FILES_KEYWORD.'</th>'.
		'<th>'._AD_GWIKI_FILES_NAME.'</th>'.
		'<th>'._AD_GWIKI_FILES_PATH.'</th>'.
		'<th>'._AD_GWIKI_FILES_TYPE.'</th>'.
		'<th>'._AD_GWIKI_FILES_ICON.'</th>'.
		'<th>'._AD_GWIKI_FILES_SIZE.'</th>'.
		'<th>'._AD_GWIKI_FILES_DATE.'</th>'.
		'<th>'._AD_GWIKI_FILES_DESC.'</th>'.
		'<th>'._AD_GWIKI_FILES_UID.'</th>'.
		'</tr>';
		
	$sql  = 'SELECT * FROM '.$xoopsDB->prefix('gwiki_page_files');
	$sql .= $whereclause;
	$sql .= ' ORDER BY file_upload_date DESC ';
	
	$result = $xoopsDB->query($sql, $limit, $start);
    
	for ($i = 0; $i < $xoopsDB->getRowsNum($result); $i++) {
		$row = $xoopsDB->fetchArray($result);
/*
gwiki_page_files
  file_id int(10) NOT NULL AUTO_INCREMENT,
  keyword varchar(128) NOT NULL DEFAULT '',
  file_name varchar(128) NOT NULL DEFAULT '',
  file_path varchar(255) NOT NULL DEFAULT '',
  file_type varchar(128) NOT NULL DEFAULT '',
  file_icon varchar(64) NOT NULL DEFAULT '',
  file_size int(10) NOT NULL DEFAULT '0',
  file_upload_date int(10) NOT NULL DEFAULT '0',
  file_description text,
  file_uid int(10) NOT NULL DEFAULT '0',
*/
		echo '<tr class="'.(($i % 2)?"even":"odd").'"><td><a href="../edit.php?page='.$row['keyword'].'">'.htmlspecialchars($row['keyword'], ENT_QUOTES).'</a></td>' .
			'<td>'.htmlspecialchars($row['file_name'], ENT_QUOTES).'</td>'.
			'<td><a href="'.XOOPS_URL.'/uploads/'.$dir.'/'.$row['file_path'].'">'.htmlspecialchars($row['file_path'], ENT_QUOTES).'</a></td>'.
			'<td>'.htmlspecialchars($row['file_type'], ENT_QUOTES).'</td>'.
			'<td><img src="'.XOOPS_URL.'/modules/'.$dir.'/icons/16px/'.$row['file_icon'].'.png" alt="'.$row['file_icon'].'" title="'.$row['file_icon'].'" /></td>'.
			'<td>'.htmlspecialchars($row['file_size'], ENT_QUOTES).'</td>'.
			'<td>'.date('Y-m-d',$row['file_upload_date']).'</td>'.
			'<td>'.htmlspecialchars($row['file_description'], ENT_QUOTES).'</td>'.
			'<td>'.$wikiPage->getUserName($row['file_uid']).'</td>'.
		'</tr>';
	}
	if ($i == 0) {
		echo '<tr class="odd"><td colspan="9">'._AD_GWIKI_FILES_EMPTY.'</td></tr>';
	}

	// set up pagenav
	$endarray=array();
	$pager='';
	if ($total > $limit) {
		include_once XOOPS_ROOT_PATH.'/class/pagenav.php';
		$likenav='';
		if(!empty($kw)) $likenav .= (empty($likenav)?'':'&') . "kw={$kw}";
		if(!empty($fn)) $likenav .= (empty($likenav)?'':'&') . "fn={$fn}";
		if(!empty($ty)) $likenav .= (empty($likenav)?'':'&') . "ty={$ty}";
		if(!empty($ds)) $likenav .= (empty($likenav)?'':'&') . "ds={$ds}";
		$nav = new xoopsPageNav($total,$limit,$start,'start',$likenav);
		if(intval($total/$limit) < 5) $pager=$nav->renderNav();
		else $pager= _AD_GWIKI_PAGENAV . $nav->renderSelect(false);
	}
	if(!empty($pager)) $endarray['!PREFORMATTED!']=$pager;
	
	adminTableEnd($endarray);

}

showAttachments();
	
include 'footer.php';
?>
