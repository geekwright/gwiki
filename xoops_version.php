<?php
$modversion['name']        = _MI_GWIKI_NAME;
$modversion['version']     = '1.00';
$modversion['description'] = _MI_GWIKI_DESC;
$modversion['author']      = 'Richard Griffith';
$modversion['credits']     = "Adapted from Simon \"zeniko\" B&uuml;nzli's wikimod";
$modversion['license']     = 'GNU General Public License';
$modversion['official']    = 0;
$modversion['image'] = 'images/icon.png';
if (defined('ICMS_ROOT_PATH')) $modversion['image'] = 'images/icon_big.png';

$modversion['dirname']     = basename( dirname( __FILE__ ) ) ;

// things for ModuleAdmin() class
$modversion['license_url'] = XOOPS_URL.'/modules/gwiki/docs/license.txt';
$modversion['license_url'] = substr($modversion['license_url'],strpos($modversion['license_url'],'//')+2);
$modversion['release_date']     = '2013/01/08';
$modversion['module_website_url'] = 'geekwright.com';
$modversion['module_website_name'] = 'geekwright, LLC';
$modversion['module_status'] = "Beta";
$modversion['min_php']='5.2';
$modversion['min_xoops']='2.5';
$modversion['system_menu'] = 1;
$modversion['help'] = "page=help";

// Tables created by the SQL file (without prefix!)
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][0] = 'gwiki_pages';
$modversion['tables'][] = 'gwiki_pageids';
$modversion['tables'][] = 'gwiki_group_prefix';
$modversion['tables'][] = 'gwiki_prefix';
$modversion['tables'][] = 'gwiki_template';
$modversion['tables'][] = 'gwiki_page_images';
$modversion['tables'][] = 'gwiki_page_files';
//$modversion['tables'][] = '';

// Administration tools
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Module Administration
$modversion['onInstall'] = 'include/install.php';
$modversion['onUpdate'] = 'include/update.php';
$modversion['onUninstall'] = 'include/uninstall.php';


// Main menu
$modversion['hasMain'] = 1;

// comments
$modversion['hasComments'] = 1;
$modversion['comments'] = array(
	'itemName' => 'page_id',
	'pageName' => 'index.php');

// notification
$modversion['hasNotification'] = 1;

$modversion['notification']['lookup_file'] = 'include/notification.inc.php';
$modversion['notification']['lookup_func'] = 'gwiki_notify_iteminfo';

$modversion['notification']['category'][1]=array(
	'name' => 'global',
	'title' => _MI_GWIKI_NOTIFY_GLOBAL,
	'description' => _MI_GWIKI_NOTIFY_GLOBAL_DESC,
	'subscribe_from' => array('index.php')
);

$modversion['notification']['category'][]=array(
	'name' => 'page',
	'title' =>  _MI_GWIKI_NOTIFY_PAGE_CAT,
	'description' =>  _MI_GWIKI_NOTIFY_PAGE_CAT_DESC,
	'subscribe_from' => array('index.php','history.php'),
	'item_name' => 'page_id',
	'allow_bookmark' => 1
);

$modversion['notification']['category'][]=array(
	'name' => 'namespace',
	'title' =>  _MI_GWIKI_NOTIFY_NS_CAT,
	'description' =>  _MI_GWIKI_NOTIFY_NS_CAT_DESC,
	'subscribe_from' => array('index.php'),
	'item_name' => 'nsid',
	'allow_bookmark' => 0
);

$modversion['notification']['event'][1]=array(
	'name' => 'new_page',
	'category' => 'global',
	'title' => _MI_GWIKI_NOTIFY_GLOBAL_NEW_PAGE,
	'caption' => _MI_GWIKI_NOTIFY_GLOBAL_NEW_PAGE_CAPTION,
	'description' => _MI_GWIKI_NOTIFY_GLOBAL_NEW_PAGE_DESC,
	'mail_template' => 'notify_global_new_page',
	'mail_subject' => _MI_GWIKI_NOTIFY_GLOBAL_NEW_PAGE_SUBJECT
	// 'admin_only' => 1
);

$modversion['notification']['event'][]=array(
	'name' => 'upd_page',
	'category' => 'global',
	'title' => _MI_GWIKI_NOTIFY_GLOBAL_UPD_PAGE,
	'caption' => _MI_GWIKI_NOTIFY_GLOBAL_UPD_PAGE_CAPTION,
	'description' => _MI_GWIKI_NOTIFY_GLOBAL_UPD_PAGE_DESC,
	'mail_template' => 'notify_global_upd_page',
	'mail_subject' => _MI_GWIKI_NOTIFY_GLOBAL_UPD_PAGE_SUBJECT
	// 'admin_only' => 1
);

$modversion['notification']['event'][]=array(
	'name' => 'page_watch',
	'category' => 'page',
	'title' => _MI_GWIKI_NOTIFY_PAGE_UPD_PAGE,
	'caption' => _MI_GWIKI_NOTIFY_PAGE_UPD_PAGE_CAPTION,
	'description' => _MI_GWIKI_NOTIFY_PAGE_UPD_PAGE_DESC,
	'mail_template' => 'notify_page_upd_page',
	'mail_subject' => _MI_GWIKI_NOTIFY_PAGE_UPD_PAGE_SUBJECT
	// 'admin_only' => 1
);

$modversion['notification']['event'][]=array(
	'name' => 'new_ns_page',
	'category' => 'namespace',
	'title' => _MI_GWIKI_NOTIFY_NS_NEW_PAGE,
	'caption' => _MI_GWIKI_NOTIFY_NS_NEW_PAGE_CAPTION,
	'description' => _MI_GWIKI_NOTIFY_NS_NEW_PAGE_DESC,
	'mail_template' => 'notify_namespace_new_page',
	'mail_subject' => _MI_GWIKI_NOTIFY_NS_NEW_PAGE_SUBJECT
	// 'admin_only' => 1
);

$modversion['notification']['event'][]=array(
	'name' => 'upd_ns_page',
	'category' => 'namespace',
	'title' => _MI_GWIKI_NOTIFY_NS_UPD_PAGE,
	'caption' => _MI_GWIKI_NOTIFY_NS_UPD_PAGE_CAPTION,
	'description' => _MI_GWIKI_NOTIFY_NS_UPD_PAGE_DESC,
	'mail_template' => 'notify_namespace_upd_page',
	'mail_subject' => _MI_GWIKI_NOTIFY_NS_UPD_PAGE_SUBJECT
	// 'admin_only' => 1
);

// Templates
$modversion['templates'][1] = array(
	'file' => 'gwiki_view.html',
	'description' => 'gwiki - View Wiki Page');

$modversion['templates'][] = array(
	'file' => 'gwiki_edit.html',
	'description' => 'gwiki - Edit/Preview Wiki Page');

$modversion['templates'][] = array(
	'file' => 'gwiki_history.html',
	'description' => 'gwiki - Page History');

$modversion['templates'][] = array(
	'file' => 'gwiki_page_info.html',
	'description' => 'gwiki - Page Info and Tool Bar');

// Search
$modversion['hasSearch'] = 1;
$modversion['search'] = array(
	'file' => 'include/search.inc.php',
	'func' => 'gwiki_search' );

// Configuration settings
$modversion['config'][1] = array(
	'name' => 'wiki_home_page',
	'title' => '_MI_GWIKI_WIKI_HOME',
	'description' => '_MI_GWIKI_WIKI_HOME_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => _MI_GWIKI_WIKIHOME,
	'options' => array() );

$modversion['config'][]= array(
	'name' => 'date_format',
	'title' => '_MI_GWIKI_DATEFORMAT',
	'description' => '_MI_GWIKI_DATEFORMAT_DESC',
	'formtype' => 'select',
	'valuetype' => 'text',
	'default' => 'Y-m-d',
	'options' => array('dd.mm.yy' => 'd.m.y', 'mm/dd/yy' => 'm/d/y', 'yyyy-mm-dd' => 'Y-m-d', 'RFC2822' => 'r', 'ISO 8601' => 'c') );

$modversion['config'][]= array(
	'name' => 'number_recent',
	'title' => '_MI_GWIKI_NUMBERRECENT',
	'description' => '_MI_GWIKI_NUMBERRECENT_DESC',
	'formtype' => 'select',
	'valuetype' => 'int',
	'default' => 10,
	'options' => array('5' => 5, '10' => 10, '20' => 20, '50' => 50) );
	
$modversion['config'][]= array(
	'name' => 'wikilink_template',
	'title' => '_MI_GWIKI_LINK_TEMPLATE',
	'description' => '_MI_GWIKI_LINK_TEMPLATE_DESC',
	'formtype' => 'textbox' ,
	'valuetype' => 'text',
	'default' => XOOPS_URL.'/modules/'.$modversion['dirname'].'/index.php?page=%s',
	'options' => array() );


$modversion['config'][]= array(
	'name' => 'searchlink_template',
	'title' => '_MI_GWIKI_SEARCH_LINK_TEMPLATE',
	'description' => '_MI_GWIKI_SEARCH_LINK_TEMPLATE_DESC',
	'formtype' => 'textbox' ,
	'valuetype' => 'text',
	'default' => XOOPS_URL.'/modules/'.$modversion['dirname'].'/index.php?page=%s&query=%s',
	'options' => array() );

$modversion['config'][]= array(
	'name' => 'imagelib_pages',
	'title' => '_MI_GWIKI_IMAGE_LIBRARY_PAGES',
	'description' => '_MI_GWIKI_IMAGE_LIBRARY_PAGES_DESC',
	'formtype' => 'textbox' ,
	'valuetype' => 'text',
	'default' => _MI_GWIKI_WIKIHOME,
	'options' => array() );

$modversion['config'][]= array(
	'name' => 'retain_days',
	'title' => '_MI_GWIKI_RETAIN_DAYS',
	'description' => '_MI_GWIKI_RETAIN_DAYS_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 60,
	'options' => array() );

$modversion['config'][]= array(
	'name' => 'attach_ext_whitelist',
	'title' => '_MI_GWIKI_ATTACH_EXT_WHITELIST',
	'description' => '_MI_GWIKI_ATTACH_EXT_WHITELIST_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => 'txt,pdf,doc,docx,xls,ppt,jpg,jpeg,png',
	'options' => array() );
	
$modversion['config'][]= array(
	'name' => 'allow_origin',
	'title' => '_MI_GWIKI_AJAX_ALLOW_ORIGIN',
	'description' => '_MI_GWIKI_AJAX_ALLOW_ORIGIN_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'text',
	'default' => '',
	'options' => array() );

$modversion['config'][]=array(
  'name' => 'allow_camelcase',
  'title' => '_MI_GWIKI_ALLOW_CAMELCASE',
  'description' => '_MI_GWIKI_ALLOW_CAMELCASE_DESC',
  'formtype' => 'yesno',
  'valuetype' => 'int',
  'default' => '1');

$modversion['config'][]= array(
	'name' => 'default_thumb_size',
	'title' => '_MI_GWIKI_DEFAULT_THUMB_SIZE',
	'description' => '_MI_GWIKI_DEFAULT_THUMB_SIZE_DESC',
	'formtype' => 'textbox',
	'valuetype' => 'int',
	'default' => 150 );
	

// Blocks
$modversion['blocks'][1] = array(
	'file' => 'blocks.php',
	'name' => _MI_GWIKI_BL_WIKIBLOCK,
	'description' =>  _MI_GWIKI_BL_WIKIBLOCK_DESC,
	'show_func' => 'b_gwiki_wikiblock_show',
	'edit_func' => 'b_gwiki_wikiblock_edit',
	'options' => _MI_GWIKI_WIKIHOME.'|0|',
	'template' => 'gwiki_ajaxblock.html');

$modversion['blocks'][] = array(
	'file' => 'blocks.php',
	'name' => _MI_GWIKI_BL_NEWPAGE,
	'description' =>  _MI_GWIKI_BL_NEWPAGE_DESC,
	'show_func' => 'b_gwiki_newpage_show',
	'edit_func' => 'b_gwiki_newpage_edit',
	'options' => array(),
	'template' => 'gwiki_newpage.html');

$modversion['blocks'][] = array(
	'file' => 'blocks.php',
	'name' => _MI_GWIKI_BL_TEASERBLOCK,
	'description' =>  _MI_GWIKI_BL_TEASERBLOCK_DESC,
	'show_func' => 'b_gwiki_teaserblock_show',
	'edit_func' => 'b_gwiki_teaserblock_edit',
	'options' => '0||0|1',
	'template' => 'gwiki_block.html');

$modversion['blocks'][] = array(
	'file' => 'blocks.php',
	'name' => _MI_GWIKI_BL_RECENTBLOCK,
	'description' =>  _MI_GWIKI_BL_RECENTBLOCK_DESC,
	'show_func' => 'b_gwiki_recentblock_show',
	'edit_func' => 'b_gwiki_recentblock_edit',
	'options' => '4|0|-3 months',
	'template' => 'gwiki_recentblock.html');

$modversion['blocks'][] = array(
	'file' => 'blocks.php',
	'name' => _MI_GWIKI_BL_PAGESET_TOC,
	'description' =>  _MI_GWIKI_BL_PAGESET_TOC_DESC,
	'show_func' => 'b_gwiki_pagesettoc_show',
	'edit_func' => 'b_gwiki_pagesettoc_edit',
	'options' => '1|',
	'template' => 'gwiki_pagesettoc.html');

$modversion['blocks'][] = array(
	'file' => 'blocks.php',
	'name' => _MI_GWIKI_BL_RELATED,
	'description' =>  _MI_GWIKI_BL_RELATED_DESC,
	'show_func' => 'b_gwiki_related_show',
	'edit_func' => 'b_gwiki_related_edit',
	'options' => '1||0',
	'template' => 'gwiki_relatedblock.html');

?>
