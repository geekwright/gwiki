<?php
/**
* admin/about.php
*
* @copyright  Copyright © 2012 geekwright, LLC. All rights reserved. 
* @license    fbcomment/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    fbcomment
* @version    $Id$
*/

include 'header.php';
if($xoop25plus) {
	echo $moduleAdmin->addNavigation('about.php');
	echo $moduleAdmin->renderabout('',false);
}
else { // !$xoop25plus
$module_handler =& xoops_gethandler('module');
$module_info =& $module_handler->get($xoopsModule->getVar("mid"));

adminmenu(2);

adminTableStart(_AD_GW_ABOUT_ABOUT,1);
echo '<tr><td width="100%" ><center>';
echo '<br /><b>'. $module_info->getInfo('name') . ''.$module_info->getInfo('version').' '.$module_info->getInfo('module_status').'</b>';
echo '<br />'.$module_info->getInfo('description');
echo '<br /><br /><b>'. _AD_GW_ABOUT_AUTHOR . '</b>';
echo '<br />'. $module_info->getInfo('author');
echo '<br /><br /><b>'. _AD_GW_ABOUT_CREDITS . '</b>';
echo '<br />'. $module_info->getInfo('credits');
echo '<br /><br /><b>'. _AD_GW_ABOUT_LICENSE .'  </b><a href="http://'.$module_info->getInfo('license_url').'">'.$module_info->getInfo('license').'</a>';
echo '<br /><br /><center>Brought to you by <a href="http://'.$module_info->getInfo('module_website_url').'" target="_blank">'.$module_info->getInfo('module_website_name').'</a>';
echo '<br /><br /></center></td></tr>';
adminTableEnd(NULL);

}

include 'footer.php';
?>