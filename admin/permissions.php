<?php
/**
* admin/permissions.php - group permissions for wiki
*
* @copyright  Copyright © 2013 geekwright, LLC. All rights reserved. 
* @license    gwiki/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/
include 'header.php';
if(!$xoop25plus) adminmenu(4);
else echo $moduleAdmin->addNavigation('permissions.php');


global $xoopsModule, $xoopsConfig;

include_once XOOPS_ROOT_PATH.'/class/xoopsform/grouppermform.php';

if(!defined(_MD_GWIKI_PAGE_PERM_EDIT_ANY)) {
if (file_exists("../language/".$xoopsConfig['language']."/main.php")) {
    include_once "../language/".$xoopsConfig['language']."/main.php";
} else {
    include_once "../language/english/main.php";
}
}

$module_id = $xoopsModule->getVar('mid');

$item_list = array(
  _MD_GWIKI_PAGE_PERM_EDIT_ANY_NUM => _MD_GWIKI_PAGE_PERM_EDIT_ANY,
  _MD_GWIKI_PAGE_PERM_EDIT_PFX_NUM => _MD_GWIKI_PAGE_PERM_EDIT_PFX,

  _MD_GWIKI_PAGE_PERM_CREATE_ANY_NUM => _MD_GWIKI_PAGE_PERM_CREATE_ANY,
  _MD_GWIKI_PAGE_PERM_CREATE_PFX_NUM => _MD_GWIKI_PAGE_PERM_CREATE_PFX
);

$title_of_form = _MI_GWIKI_AD_PERM_TITLE;
$perm_name = 'wiki_authority';
$perm_desc = _MI_GWIKI_AD_PERM_DESC;

$form = new XoopsGroupPermForm($title_of_form, $module_id, $perm_name, $perm_desc);
foreach ($item_list as $item_id => $item_name) {
	$form->addItem($item_id, $item_name);
}

adminTableStart(_MI_GWIKI_AD_PERM_TITLE,1);
echo '<tr><td width="100%" >';
echo $form->render(); 
echo '</td></tr>';
adminTableEnd(NULL);

include 'footer.php';
?>
