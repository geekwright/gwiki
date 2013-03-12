<?php
/*
 * blocks
 *
 * @copyright	Geekwright, LLC http://geekwright.com
 * @license	GNU General Public License (GPL)
 * @since	1.0
 * @author	Richard Griffith richard@geekwright.com
 * @package	qr
 * @version	$Id$
 */

if (!defined('XOOPS_ROOT_PATH')){ exit(); }

function b_gwiki_wikiblock_show($options) {
global $xoopsConfig,$xoTheme;

	$block=false;

	$dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
	// Access module configs from block:
	$module_handler = xoops_gethandler('module');
	$module         = $module_handler->getByDirname($dir);
	$config_handler = xoops_gethandler('config');
	$moduleConfig   = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

	include_once XOOPS_ROOT_PATH.'/modules/'.$dir.'/classes/gwikiPage.php';

	$wikiPage = new gwikiPage;
	$wikiPage->setRecentCount($moduleConfig['number_recent']);

	$remotegwiki = !empty($options[2]);
	if(!$remotegwiki) {
		$block=$wikiPage->getPage($options[0]);
	}
	if (!$block) {
		$block['keyword']=$options[0];
		$block['display_keyword']=$options[0];
	}


//	if(!defined('_MI_GWIKI_NAME')) {
//		$langfile=XOOPS_ROOT_PATH.'/modules/'.$dir.'/language/'.$xoopsConfig['language'].'/modinfo.php';
//		if (!file_exists($langfile)) {
//			$langfile=XOOPS_ROOT_PATH.'/modules/'.$dir.'/language/english/modinfo.php';
//		}
//		include_once $langfile;
//	}
	$xoTheme->addStylesheet(XOOPS_URL.'/modules/'.$dir.'/module.css');

//		$wikiPage->setWikiLinkURL("javascript:ajaxGwikiLoad('%s','{$options[1]}');");
//		$block['rendered']='<h1 class="wikititle">'.$block['title'].'</h1>' . $wikiPage->renderPage();
		
	$block['bid']=$options[1]; // we use our block id to make a (quasi) unique div id
		
	$block['moddir']  = $dir;
	$block['modpath'] = XOOPS_ROOT_PATH .'/modules/' . $dir;
	$block['modurl']  = XOOPS_URL .'/modules/' . $dir;
	if($remotegwiki) {
		$block['ajaxurl']=$options[2];
		$block['mayEdit'] = false;
		$block['remotewiki']=true;
	}
	else {
		$block['ajaxurl']=$block['modurl'];
		$block['mayEdit'] = $wikiPage->checkEdit();
		$block['remotewiki']=false;
	}
		
	return $block;
}

function b_gwiki_wikiblock_edit($options) {

	$form = _MB_GWIKI_WIKIPAGE . ' <input type="text" value="'.$options[0].'"id="options[0]" name="options[0]" /><br />';
	// capture the block id from the url and save through a hidden option.
	if($_GET['op']=='clone') $form.=_MI_GWIKI_BL_CLONE_WARN.'<br />';
	$form .= '<input type="hidden" value="'.intval($_GET['bid']).'"id="options[1]" name="options[1]" />';
	$form .= _MB_GWIKI_REMOTE_AJAX_URL . ' <input type="text" size="35" value="'.$options[2].'"id="options[2]" name="options[2]" />  <i>'._MB_GWIKI_REMOTE_AJAX_URL_DESC.'</i><br />';

	return $form;
}

function b_gwiki_newpage_show($options) {
global $xoopsUser,$xoopsDB;

	$block=false;

	$dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
	
	include_once XOOPS_ROOT_PATH.'/modules/'.$dir.'/classes/gwikiPage.php';
	$wikiPage = new gwikiPage;

	$module_handler = xoops_gethandler('module');
	$module         = $module_handler->getByDirname($dir);
	$module_id = $module->getVar('mid');
	// $config_handler =& xoops_gethandler('config');
	// $moduleConfig   =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));

	if (is_object($xoopsUser)) {
		$groups = $xoopsUser->getGroups();
	} else {
		$groups = XOOPS_GROUP_ANONYMOUS;
	}

	$gperm_handler =& xoops_gethandler('groupperm');

	$edit_any = $gperm_handler->checkRight('wiki_authority', _MD_GWIKI_PAGE_PERM_EDIT_ANY_NUM, $groups, $module_id);
	$edit_pfx = $gperm_handler->checkRight('wiki_authority', _MD_GWIKI_PAGE_PERM_EDIT_PFX_NUM, $groups, $module_id);
	$create_any = $gperm_handler->checkRight('wiki_authority', _MD_GWIKI_PAGE_PERM_CREATE_ANY_NUM, $groups, $module_id);
	$create_pfx = $gperm_handler->checkRight('wiki_authority', _MD_GWIKI_PAGE_PERM_CREATE_PFX_NUM, $groups, $module_id);

	if(is_array($groups)) $groupwhere=' IN ('.implode(', ',$groups).') ';
	else $groupwhere=" = '".$groups."'";

//	$sql = 'SELECT distinct prefix FROM '.$xoopsDB->prefix('gwiki_group_prefix').' WHERE group_id '.$groupwhere;
	$sql = 'SELECT distinct p.prefix_id, prefix FROM ';
	$sql.= $xoopsDB->prefix('gwiki_prefix').' p, ';
	$sql.= $xoopsDB->prefix('gwiki_group_prefix').' g ';
	$sql.= ' WHERE group_id '.$groupwhere;
	$sql.= ' AND p.prefix_id = g.prefix_id';
	$prefixes=array();
	$result = $xoopsDB->query($sql);
	$first=true;
	while($myrow = $xoopsDB->fetchArray($result)) {
		if($first && $create_any) $prefixes[]=array('prefix_id'=>-1, 'prefix'=>'');
		$first=false;
		$prefixes[] = $myrow;
	}

	 // make sure we have som edit/create permission. We need full keyword to be certain, so let edit sort it out.
	$mayEdit = ($edit_any || $create_any || $edit_pfx || $create_pfx);
	if($mayEdit) {
		$block['moddir']  = $dir;
		$block['modpath'] = XOOPS_ROOT_PATH .'/modules/' . $dir;
		$block['modurl']  = XOOPS_URL .'/modules/' . $dir;
		$block['prefixes']  = $prefixes;
	}
	else $block=false;

	return $block;
}

function b_gwiki_newpage_edit($options) {
	return '';
}

function b_gwiki_teaserblock_show($options) {
global $xoopsDB, $xoopsConfig, $xoTheme;

	$block=false;

	$dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
	// Access module configs from block:
	$module_handler = xoops_gethandler('module');
	$module         = $module_handler->getByDirname($dir);
	$config_handler = xoops_gethandler('config');
	$moduleConfig   = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

	include_once XOOPS_ROOT_PATH.'/modules/'.$dir.'/classes/gwikiPage.php';

	$wikiPage = new gwikiPage;
	$wikiPage->setRecentCount($moduleConfig['number_recent']);

	$block=$wikiPage->getPage($options[1]);
	if ($block) {
		$block['title']=htmlspecialchars($block['title']);
		if(!defined('_MI_GWIKI_NAME')) {
			$langfile=XOOPS_ROOT_PATH.'/modules/'.$dir.'/language/'.$xoopsConfig['language'].'/modinfo.php';
			if (!file_exists($langfile)) {
				$langfile=XOOPS_ROOT_PATH.'/modules/'.$dir.'/language/english/modinfo.php';
			}
			include_once $langfile;
		}
		$xoTheme->addStylesheet(XOOPS_URL.'/modules/'.$dir.'/module.css');

		if($options[0]) $block['body']=$wikiPage->renderPage();
		else $block['body']=$wikiPage->renderTeaser();
		
		$block['moddir']  = $dir;
		$block['modpath'] = XOOPS_ROOT_PATH .'/modules/' . $dir;
		$block['modurl']  = XOOPS_URL .'/modules/' . $dir;
		$block['mayEdit'] = $wikiPage->checkEdit();
		$block['template']= 'db:'.$wikiPage->getTemplateName();

		if($options[2]) {
			$sql  = 'SELECT image_file, image_alt_text FROM ' . $xoopsDB->prefix('gwiki_page_images') ;
			$sql .= ' WHERE keyword = "'.$options[1].'" AND use_to_represent = 1 ';
			$result = $xoopsDB->query($sql,$options[0],0);
			if($myrow = $xoopsDB->fetchArray($result)) {
				$block['image_file'] = XOOPS_URL .'/uploads/' . $dir . '/' . $myrow['image_file'];
				$block['image_alt_text'] = $myrow['image_alt_text'];
			}
		}
		$block['pageurl'] = sprintf($wikiPage->getWikiLinkURL(),$block['keyword']);

	}

	return $block;
}

function b_gwiki_teaserblock_edit($options) {
	$form  = '';
	$form .= _MB_GWIKI_SHOW_FULL_PAGE . ": <input type='radio' name='options[0]' value='1' ";
	if($options[0]) $form .="checked='checked'"; 
	$form .=" />&nbsp;"._YES."&nbsp;<input type='radio' name='options[0]' value='0' ";
	if(!$options[0]) $form .="checked='checked'"; 
	$form .= " />&nbsp;"._NO."<br /><br />";
	$form .= _MB_GWIKI_WIKIPAGE . ' <input type="text" value="'.$options[1].'"id="options[1]" name="options[1]" /><br /><br />';
	$form .= _MB_GWIKI_SHOW_DEFAULT_IMAGE. ": <input type='radio' name='options[2]' value='1' ";
	if($options[2]) $form .="checked='checked'"; 
	$form .=" />&nbsp;"._YES."&nbsp;<input type='radio' name='options[2]' value='0' ";
	if(!$options[2]) $form .="checked='checked'"; 
	$form .= " />&nbsp;"._NO."<br />";
	return $form;
}


function b_gwiki_recentblock_show($options) {
global $xoopsDB,$xoTheme;

	$block=false;

	$dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
	include_once XOOPS_ROOT_PATH.'/modules/'.$dir.'/classes/gwikiPage.php';

	$wikiPage = new gwikiPage;

	$prefix='';
	$sql  = 'SELECT prefix FROM ' . $xoopsDB->prefix('gwiki_prefix') .' WHERE prefix_id = "'.$options[1].'"';
	$result = $xoopsDB->query($sql);
	$myrow = $xoopsDB->fetchArray($result);
	if($myrow) $prefix = $myrow['prefix'];
	$prefix.='%';
	
	$maxage=0;
	if(!empty($options[2])) $maxage=strtotime($options[2]);
	
	$keywords=array();
		
	$sql  = 'SELECT p.keyword, image_file, image_alt_text FROM ' . $xoopsDB->prefix('gwiki_pages') . ' p ';
	$sql .= ' left join ' . $xoopsDB->prefix('gwiki_page_images') . ' i on p.keyword=i.keyword and use_to_represent = 1 ';
	$sql .= ' WHERE active=1 AND show_in_index=1 AND p.keyword like "'.$prefix.'" ';
	$sql .= ' AND lastmodified > "'.$maxage.'" ORDER BY lastmodified desc';
	$result = $xoopsDB->query($sql,$options[0],0);
	while($myrow = $xoopsDB->fetchArray($result)) {
		$keywords[] = $myrow;
	}

	if(empty($keywords)) return false; // nothing to show

	if(!defined('_MI_GWIKI_NAME')) {
		$langfile=XOOPS_ROOT_PATH.'/modules/'.$dir.'/language/'.$xoopsConfig['language'].'/modinfo.php';
		if (!file_exists($langfile)) {
			$langfile=XOOPS_ROOT_PATH.'/modules/'.$dir.'/language/english/modinfo.php';
		}
		include_once $langfile;
	}
	$xoTheme->addStylesheet(XOOPS_URL.'/modules/'.$dir.'/module.css');
	
	foreach($keywords as $keyimg) {
		$gwiki=$wikiPage->getPage($keyimg['keyword']);
		if ($gwiki) {
			$gwiki['title']=htmlspecialchars($gwiki['title']);
			$gwiki['body']       = $wikiPage->renderTeaser();
			$gwiki['moddir']     = $dir;
			$gwiki['modpath']    = XOOPS_ROOT_PATH .'/modules/' . $dir;
			$gwiki['modurl']     = XOOPS_URL .'/modules/' . $dir;
			$gwiki['mayEdit']    = $wikiPage->checkEdit();
			$gwiki['template']   = 'db:'.$wikiPage->getTemplateName();
			if(!empty($keyimg['image_file'])) {
				$gwiki['image_file'] = XOOPS_URL .'/uploads/' . $dir . '/' . $keyimg['image_file'];
				$gwiki['image_alt_text'] = $keyimg['image_alt_text'];
			}
			$gwiki['pageurl'] = sprintf($wikiPage->getWikiLinkURL(),$gwiki['keyword']);
			$gwiki['title'] = sprintf('<a href="%s" title="%s">%s</a>', $gwiki['pageurl'], htmlspecialchars($gwiki['title'],ENT_COMPAT), $gwiki['title']);
		
			$block['pages'][]=$gwiki;
		}
	}


//	$block['body']='WHAT?'; // print_r($page,true);
	return $block;
}

function b_gwiki_recentblock_edit($options) {
global $xoopsDB;

	$form  = '';
	$form .= _MB_GWIKI_RECENT_COUNT . ' <input type="text" value="'.$options[0].'"id="options[0]" name="options[0]" /><br />';
	$form .= _MB_GWIKI_PICK_NAMESPACE . ' <select id="options[1]" name="options[1]">';
	$form .= '<option value="0"'.(intval($options[1])==0?' selected':'').'></option>';
	$sql = 'SELECT prefix_id, prefix FROM ' . $xoopsDB->prefix('gwiki_prefix') . ' ORDER BY prefix';
	$result = $xoopsDB->query($sql);
	while($myrow = $xoopsDB->fetchArray($result)) {
		$pid = intval($myrow['prefix_id']);
		$form.='<option value="'.$pid.'"'.(intval($options[1])==$pid?' selected':'').'>'.$myrow['prefix'].'</option>';
	}
	$form .= '</select><br />';
	$form .= _MB_GWIKI_MAX_AGE . ' <input type="text" value="'.$options[2].'"id="options[2]" name="options[2]" /><br />';
	return $form;
}

?>