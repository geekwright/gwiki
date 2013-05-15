<?php
/**
* admin/menu.php - menu array for building admin menus
*
* @copyright  Copyright © 2013 geekwright, LLC. All rights reserved. 
* @license    gwiki/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/
$adminmenu[1] = array(
	'title'	=> _MI_GWIKI_ADMAIN ,
	'link'	=> 'admin/index.php' ,
	'icon'	=> 'images/admin/home.png'
) ;

$adminmenu[] = array(
	'title'	=> _MI_GWIKI_ABOUT ,
	'link'	=> 'admin/about.php' ,
	'icon'	=> 'images/admin/about.png'
) ;

$adminmenu[] = array(
	'title'	=> _MI_GWIKI_PAGES ,
	'link'	=> 'admin/pages.php' ,
	'icon'	=> 'images/admin/manage.png'
) ;

$adminmenu[] = array(
	'title'	=> _MI_GWIKI_ADPERM ,
	'link'	=> 'admin/permissions.php' ,
	'icon'	=> 'images/admin/group.png'
) ;

$adminmenu[] = array(
	'title'	=> _MI_GWIKI_ADPREFIX ,
	'link'	=> 'admin/prefixes.php' ,
	'icon'	=> 'images/admin/namespaces.png'
) ;

$adminmenu[] = array(
	'title'	=> _MI_GWIKI_ADFILES ,
	'link'	=> 'admin/attachments.php' ,
	'icon'	=> 'images/admin/attachments.png'
) ;

$adminmenu[] = array(
	'title'	=> _MI_GWIKI_ADRECENT ,
	'link'	=> 'admin/recent.php' ,
	'icon'	=> 'images/admin/recent.png'
) ;
?>
