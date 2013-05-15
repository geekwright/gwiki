<?php
/**
* admin/help.php - admin help for non Xoops 2.5+ systems
*
* @copyright  Copyright © 2013 geekwright, LLC. All rights reserved. 
* @license    gwiki/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/

include 'header.php';

if(!$xoop25plus) adminmenu(0);

$help = XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar("dirname") . '/language/' . $xoopsConfig['language'] . '/help/help.html';
if(!file_exists($help)) $help = XOOPS_ROOT_PATH . "/modules/" . $xoopsModule->getVar("dirname") . "/language/english/help/help.html" ;

adminTableStart(_AD_GW_ADMENU_HELP,1);
echo '<tr><td width="100%" >';
$helptext=utf8_encode(implode("\n", file($help)));
$helptext=str_replace ( '<{$xoops_url}>', XOOPS_URL, $helptext);
echo $helptext.'<br /></td></tr>';

adminTableEnd(array(_AD_GW_ADMENU_TOADMIN=>'index.php'));

include "footer.php";
?>
