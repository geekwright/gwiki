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
if (file_exists(XOOPS_ROOT_PATH.'/Frameworks/moduleclasses/icons/32/about.png')) {
$pathIcon32='../../Frameworks/moduleclasses/icons/32';

$adminmenu[1] = array(
    'title'	=> _MI_GWIKI_ADMAIN ,
    'link'	=> 'admin/index.php' ,
    'icon'	=> $pathIcon32.'/home.png'
) ;

$adminmenu[] = array(
    'title'	=> _MI_GWIKI_PAGES ,
    'link'	=> 'admin/pages.php' ,
    'icon'	=> $pathIcon32.'/content.png'
) ;

$adminmenu[] = array(
    'title'	=> _MI_GWIKI_ADPERM ,
    'link'	=> 'admin/permissions.php' ,
    'icon'	=> $pathIcon32.'/permissions.png'
) ;

$adminmenu[] = array(
    'title'	=> _MI_GWIKI_ADPREFIX ,
    'link'	=> 'admin/prefixes.php' ,
    'icon'	=> $pathIcon32.'/category.png'
) ;

$adminmenu[] = array(
    'title'	=> _MI_GWIKI_ADFILES ,
    'link'	=> 'admin/attachments.php' ,
    'icon'	=> $pathIcon32.'/fileshare.png'
) ;

$adminmenu[] = array(
    'title'	=> _MI_GWIKI_ADRECENT ,
    'link'	=> 'admin/recent.php' ,
    'icon'	=> $pathIcon32.'/stats.png'
) ;

$adminmenu[] = array(
    'title'	=> _MI_GWIKI_ABOUT ,
    'link'	=> 'admin/about.php' ,
    'icon'	=> $pathIcon32.'/about.png'
) ;

} else {
$adminmenu[1] = array(
    'title'	=> _MI_GWIKI_ADMAIN ,
    'link'	=> 'admin/index.php' ,
    'icon'	=> 'assets/images/admin/home.png'
) ;

$adminmenu[] = array(
    'title'	=> _MI_GWIKI_PAGES ,
    'link'	=> 'admin/pages.php' ,
    'icon'	=> 'assets/images/admin/manage.png'
) ;

$adminmenu[] = array(
    'title'	=> _MI_GWIKI_ADPERM ,
    'link'	=> 'admin/permissions.php' ,
    'icon'	=> 'assets/images/admin/group.png'
) ;

$adminmenu[] = array(
    'title'	=> _MI_GWIKI_ADPREFIX ,
    'link'	=> 'admin/prefixes.php' ,
    'icon'	=> 'assets/images/admin/namespaces.png'
) ;

$adminmenu[] = array(
    'title'	=> _MI_GWIKI_ADFILES ,
    'link'	=> 'admin/attachments.php' ,
    'icon'	=> 'assets/images/admin/attachments.png'
) ;

$adminmenu[] = array(
    'title'	=> _MI_GWIKI_ADRECENT ,
    'link'	=> 'admin/recent.php' ,
    'icon'	=> 'assets/images/admin/recent.png'
) ;

$adminmenu[] = array(
    'title'	=> _MI_GWIKI_ABOUT ,
    'link'	=> 'admin/about.php' ,
    'icon'	=> 'assets/images/admin/about.png'
) ;
}
