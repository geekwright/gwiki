<?php
/**
* ajaxfilelist.php - supply list of file attachments for a page
*
* @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
* @license    gwiki/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/
include '../../mainfile.php';
$xoopsLogger->activated = false;

header("Pragma: public");
header("Cache-Control: no-cache");

/**
 * @param $string
 *
 * @return string
 */
function cleaner($string) {
    $string=stripcslashes($string);
    $string=html_entity_decode($string);
    $string=strip_tags($string); // DANGER -- kills wiki text
    $string=trim($string);
    $string=stripslashes($string);

    return $string;
}

/**
 * @param $uid
 *
 * @return string
 */
function getUserName($uid)
{
    global $xoopsConfig;

    $uid = intval($uid);

     if ($uid > 0) {
        $member_handler =& xoops_gethandler('member');
        $user = $member_handler->getUser($uid);
        if (is_object($user)) {
            return "<a href=\"".XOOPS_URL."/userinfo.php?uid=$uid\">".htmlspecialchars($user->getVar('uname'),ENT_QUOTES)."</a>";
        }
    }

      return $xoopsConfig['anonymous'];
}

// $_GET variables we use
unset($page,$bid,$id);
$page = isset($_GET['page'])?cleaner($_GET['page']):'';

    $dir = basename( dirname( __FILE__ ) ) ;

    $sql = 'SELECT * FROM '.$xoopsDB->prefix('gwiki_page_files') .
        ' WHERE keyword = \''.$page.'\' '.
        ' ORDER BY file_name ';
    $result = $xoopsDB->query($sql);

    $filess=array();

    for ($i = 0; $i < $xoopsDB->getRowsNum($result); $i++) {
        $row = $xoopsDB->fetchArray($result);
        $row['iconlink']=XOOPS_URL . '/modules/' . $dir . '/assets/icons/48px/' . $row['file_icon'] . '.png';
        $row['userlink']=getUserName($row['file_uid']);
        $row['size']=number_format($row['file_size']);
        $row['date']=date($xoopsModuleConfig['date_format'],$row['file_upload_date']);

        $files[]=$row;
    }

    $jsonimages=json_encode($files);
    echo $jsonimages;
    exit;
