<?php
/**
* ajaximgedit.php - backend upload images and update image info
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
// provide error logging for our sanity in debugging ajax use (won't see xoops logger)
restore_error_handler();
error_reporting(-1);

$dir = basename(dirname( __FILE__ )) ;
require_once XOOPS_ROOT_PATH.'/modules/'.$dir.'/class/gwikiPage.php';
global $wikiPage;
$wikiPage = new gwikiPage;

$uploadpath=XOOPS_ROOT_PATH."/uploads/{$dir}/";
$uploadurl=XOOPS_URL."/uploads/{$dir}/";

$newimage = (isset($_SERVER['HTTP_GW_FILENAME']) ? $_SERVER['HTTP_GW_FILENAME'] : false);
$jsondata = (isset($_SERVER['HTTP_GW_JSONDATA']) ? $_SERVER['HTTP_GW_JSONDATA'] : false);

//if (function_exists('xdebug_disable')) { xdebug_disable(); }
//foreach ($_SERVER as $k => $v) {
//    trigger_error($k.':'.$v);
//}

/**
 * @param $string
 *
 * @return string
 */
function cleaner($string)
{
    $string=stripcslashes($string);
    $string=html_entity_decode($string);
    $string=strip_tags($string); // DANGER -- kills wiki text
    $string=trim($string);
    $string=stripslashes($string);

    return $string;
}

/**
 * @param $input
 *
 * @return mixed
 */
function deleteData(&$input) {
global $xoopsDB, $uploadpath, $wikiPage;

    $q_image_id=intval($input['image_id']);
    $q_keyword=$wikiPage->escapeForDB($input['page']); // use keyword in delete so we know id and edit authority are connected

    // look up the name and delete the image file
    $sql = "SELECT image_file FROM ".$xoopsDB->prefix('gwiki_page_images').
        " where image_id='{$q_image_id}' AND keyword = '{$q_keyword}' ";

    $result = $xoopsDB->query($sql);
    if ($result) {
        $rows=$xoopsDB->getRowsNum($result);
        if ($rows) {
            $myrow=$xoopsDB->fetchArray($result);
            if (!empty($myrow['image_file'])) {
                $oldfilename=$uploadpath.$myrow['image_file'];
                unlink($oldfilename);
            }
        }
    }

    // delete the row
    $sql = "DELETE FROM ".$xoopsDB->prefix('gwiki_page_images').
        " where image_id='{$q_image_id}' AND keyword = '{$q_keyword}' ";

    $result = $xoopsDB->queryF($sql);
    $cnt=$xoopsDB->getAffectedRows();
    if($cnt) $input['message']=_MD_GWIKI_AJAX_IMGEDIT_DEL_OK;

    return $result;
}

/**
 * @param $input
 *
 * @return mixed
 */
function updateData(&$input) {
global $xoopsDB, $wikiPage;

    $q_image_id=intval($input['image_id']);
    $q_keyword=$wikiPage->escapeForDB($input['page']);
    $q_image_name=$wikiPage->escapeForDB($input['image_name']);
    $q_image_alt_text=$wikiPage->escapeForDB($input['image_alt_text']);
    //  image_file only changed by image upload
    $q_use_to_represent=intval($input['use_to_represent']);
    $q_image_file=empty($input['image_file'])?'':$wikiPage->escapeForDB($input['image_file']);

//  if(!$q_image_id) return false; // only updates

    // if we are setting this, clear it on all other images
    if ($q_use_to_represent) {
        $sql = "UPDATE ".$xoopsDB->prefix('gwiki_page_images').
            " set use_to_represent = 0 where keyword = '{$q_keyword}' ";

        $result = $xoopsDB->queryF($sql);
    }

    $sql = "UPDATE ".$xoopsDB->prefix('gwiki_page_images');
    $sql.= " set image_name = '{$q_image_name}' ";
    $sql.= " , image_alt_text = '{$q_image_alt_text}' ";
    $sql.= " , use_to_represent = '{$q_use_to_represent}' ";
    if(!empty($q_image_file)) $sql.= " , image_file = '{$q_image_file}' ";
    $sql.= " where image_id = '{$q_image_id}' ";

    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        $input['message']=$xoopsDB->error();

        return(0);
    }
    $cnt=$xoopsDB->getAffectedRows();
    if(!$cnt) $input['message']=_MD_GWIKI_AJAX_IMGEDIT_NOT_DEFINED;
    else $input['message']=_MD_GWIKI_AJAX_IMGEDIT_UPD_OK;

    if ($result && !$cnt && !empty($q_image_file)) { // database is OK but nothing to update - require image_file
        $sql = "insert into ".$xoopsDB->prefix('gwiki_page_images');
        $sql.= " (keyword, image_name, image_alt_text, use_to_represent, image_file) ";
        $sql.= " values ('{$q_keyword}', '{$q_image_name}', '{$q_image_alt_text}', '{$q_use_to_represent}', '{$q_image_file}' )";
        $result = $xoopsDB->queryF($sql);
        $input['image_id']=$xoopsDB->getInsertId();
        $input['message']=_MD_GWIKI_AJAX_IMGEDIT_ADD_OK;
    }

    return $input['image_id'];

}

/**
 * @param $newimage
 * @param $input
 *
 * @return mixed
 */
function updateImage($newimage, &$input) {
global $uploadpath,$xoopsDB;
    // For now, images are stored in individual directories for each page.
    // We can change the directory distribution later, as the entire path
    // relative to /uploads/gwiki/ ($relpath) is stored in the database.

    // We get rid of any colons in the page name in case the filesystem has
    // issues with them. (undescore is illegal in page name, so it stays unique.)
    $relpath='pages/'.str_replace ( ':', '_', $input['page']) .'/img/';
    $ourpath=$uploadpath.$relpath;
    $oldUmask = umask(0);
    @mkdir($ourpath,0755,true);
    umask($oldUmask);
    $tempfn = tempnam ( $ourpath, 'WIKIIMG_');
    $image=file_get_contents('php://input');
    file_put_contents($tempfn,$image);

    $ogimage_parts = pathinfo($newimage);

    // we are intentionally ignoring $ogimage_parts['dirname']
    // get rid of extra dots, commas and spaces
    $ogimage =  str_replace(array('.',' ',','), '_', $ogimage_parts['basename']) . '.' . strtolower($ogimage_parts['extension']);
    $filename = $tempfn.'_'.$ogimage;
    $justfn = basename($filename);
    if(empty($input['image_name'])) $input['image_name']=$justfn;
    $input['image_file']=$relpath . $justfn;

    rename($tempfn,$filename);
    chmod($filename, 0644);
    $q_image_id=intval($input['image_id']);
    $sql = "SELECT image_file FROM ".$xoopsDB->prefix('gwiki_page_images').
        " where image_id='{$q_image_id}' ";

    $result = $xoopsDB->query($sql);
    if ($result) {
        $rows=$xoopsDB->getRowsNum($result);
        if ($rows) {
            $myrow=$xoopsDB->fetchArray($result);
            if (!empty($myrow['image_file'])) {
                $oldfilename=$uploadpath . $myrow['image_file'];
                unlink($oldfilename);
            }
            // update
        } else {
            // new row
        }
    }
// $result=$xoopsDB->getInsertId();
//$rows=$xoopsDB->getRowsNum($result);
    return updateData($input);
}

    if ($jsondata===false) { header("Status: 500 Internal Error - No Data Passed"); exit; }
    $input=json_decode($jsondata, true);
    //file_put_contents ( XOOPS_ROOT_PATH.'/uploads/debug.txt', print_r($input,true));

    if (!empty($input['image_id'])) {
        $q_image_id=intval($input['image_id']);
        $sql = "SELECT keyword FROM ".$xoopsDB->prefix('gwiki_page_images')." where image_id = '{$q_image_id}' ";
        $result = $xoopsDB->query($sql);
        if ($row = $xoopsDB->fetcharray($result)) {
            $input['page']=$row['keyword'];
        }
    }

    if (empty($input['page'])) { header("Status: 500 Internal Error - No Page"); exit; }
    $input['page']=strtolower($wikiPage->normalizeKeyword($input['page']));
    $pageX = $wikiPage->getPage($input['page']);
    $mayEdit = $wikiPage->checkEdit();

    if (!$mayEdit) {
        header("Status: 403 Forbidden - No Permission");
        if(!$mayEdit) $out['message']=_MD_GWIKI_AJAX_IMGEDIT_NO_AUTH;
        echo json_encode($out);
        exit;
    }

/*
 * This creates issues if page being edited has not been saved yet, so let's not be anal about it
    if (!$pageX) {
        header("Status: 403 Forbidden - No Page");
        if(!$pageX) $out['message']='Page does not exist';
        echo json_encode($out);
        exit;
    }
*/

    if ($newimage) {
        $input['image_id']=updateImage($newimage,$input);
        if ($input['image_id']) {
            $input['message']='Image Saved';
            $input['link']=$uploadurl.$input['image_file'];
        }
    }
    else {
        if (!empty($input['op']) && $input['op']=='delete') { deleteData($input); }
        else updateData($input);
    }
    echo json_encode($input);
    exit;
