<?php
/**
 * ajaxfileedit.php - backend upload attachments and update file info
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 * @version    $Id$
 */
include dirname(dirname(__DIR__)) . '/mainfile.php';
$xoopsLogger->activated = false;
// provide error logging for our sanity in debugging ajax use (won't see xoops logger)
restore_error_handler();
error_reporting(-1);

$dir = basename(__DIR__);
require_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/gwikiPage.php';
global $wikiPage;
$wikiPage = new gwikiPage;

$uploadpath = XOOPS_ROOT_PATH . "/uploads/{$dir}/";
$uploadurl  = XOOPS_URL . "/uploads/{$dir}/";

$newfile  = (isset($_SERVER['HTTP_GW_FILENAME']) ? $_SERVER['HTTP_GW_FILENAME'] : false);
$jsondata = (isset($_SERVER['HTTP_GW_JSONDATA']) ? $_SERVER['HTTP_GW_JSONDATA'] : false);

// initialize whitelist
$whitelist = array();
$wlconfig  = $xoopsModuleConfig['attach_ext_whitelist'];
//$wlconfig='txt,pdf,doc,docx,xls,rtf,zip';

// populate whitelist
if (!empty($wlconfig)) {
    $whitelist = explode(',', $wlconfig);
}

/**
 * @param $filename
 *
 * @return array
 */
function getExtensionInfo($filename)
{
    global $whitelist;

    $fi = array();

    // these choices are just from our icon set - nothing magic, just ext => filename - .png
    $icons = array(
        'aac'  => 'aac',
        'aiff' => 'aiff',
        'ai'   => 'ai',
        'avi'  => 'avi',
        'bmp'  => 'bmp',
        'c'    => 'c',
        'cpp'  => 'cpp',
        'css'  => 'css',
        'dat'  => 'dat',
        'dmg'  => 'dmg',
        'doc'  => 'doc',
        'docx' => 'doc',
        'dot'  => 'dotx',
        'dotx' => 'dotx',
        'dwg'  => 'dwg',
        'dxf'  => 'dxf',
        'eps'  => 'eps',
        'exe'  => 'exe',
        'flv'  => 'flv',
        'gif'  => 'gif',
        'h'    => 'h',
        'hpp'  => 'hpp',
        'htm'  => 'html',
        'html' => 'html',
        'ics'  => 'ics',
        'iso'  => 'iso',
        'java' => 'java',
        'jpe'  => 'jpg',
        'jpeg' => 'jpg',
        'jpg'  => 'jpg',
        'key'  => 'key',
        'mid'  => 'mid',
        'mp3'  => 'mp3',
        'mp4'  => 'mp4',
        'mpg'  => 'mpg',
        'odf'  => 'odf',
        'ods'  => 'ods',
        'odt'  => 'odt',
        'otp'  => 'otp',
        'ots'  => 'ots',
        'ott'  => 'ott',
        'pdf'  => 'pdf',
        'php'  => 'php',
        'png'  => 'png',
        'ppt'  => 'ppt',
        'psd'  => 'psd',
        'py'   => 'py',
        'qt'   => 'qt',
        'rar'  => 'rar',
        'rb'   => 'rb',
        'rtf'  => 'rtf',
        'sql'  => 'sql',
        'tga'  => 'tga',
        'tgz'  => 'tgz',
        'tif'  => 'tiff',
        'tiff' => 'tiff',
        'txt'  => 'txt',
        'wav'  => 'wav',
        'xls'  => 'xls',
        'xlsx' => 'xlsx',
        'xml'  => 'xml',
        'yml'  => 'yml',
        'zip'  => 'zip');
    // Also have files '_blank' '_page'

    $path_parts = pathinfo($filename);
    // =$path_parts['dirname'], "\n";
    $fi['file_name'] = $path_parts['basename'];
    if (!isset($path_parts['extension'])) {
        $ext = '';
    } else {
        $ext = strtolower($path_parts['extension']);
    }
    //=$path_parts['filename'];

    // if no name, or not on whitelist reject
    if (empty($path_parts['filename']) || array_search($ext, $whitelist) === false) {
        return false;
    }

    if (empty($ext)) {
        $fi['file_icon'] = '_blank';
    } else {
        if (empty($icons[$ext])) {
            $fi['file_icon'] = '_blank';
        } else {
            $fi['file_icon'] = $icons[$ext];
        }
    }

    return $fi;
}

/**
 * @param $filename
 *
 * @return array
 */
function getFileInfo($filename)
{
    $fi = array();

    if (function_exists('finfo_open')) {
        $finfo           = finfo_open(FILEINFO_MIME_TYPE); // return mime type ala mimetype extension
        $fi['file_type'] = finfo_file($finfo, $filename);
        finfo_close($finfo);
    } else {
        $fi['file_type'] = mime_content_type($filename);
    }
    $fi['file_size'] = filesize($filename);

    return $fi;
}

/**
 * @param $string
 *
 * @return string
 */
function cleaner($string)
{
    $string = stripcslashes($string);
    $string = html_entity_decode($string);
    $string = strip_tags($string); // DANGER -- kills wiki text
    $string = trim($string);
    $string = stripslashes($string);

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

    $uid = (int)($uid);

    if ($uid > 0) {
        $member_handler =& xoops_gethandler('member');
        $user           =& $member_handler->getUser($uid);
        if (is_object($user)) {
            return "<a href=\"" . XOOPS_URL . "/userinfo.php?uid=$uid\">" . htmlspecialchars($user->getVar('uname'), ENT_QUOTES) . "</a>";
        }
    }

    return $xoopsConfig['anonymous'];
}

/**
 * @param $input
 *
 * @return mixed
 */
function deleteData(&$input)
{
    global $xoopsDB, $uploadpath, $wikiPage;

    $q_file_id = (int)($input['file_id']);
    // use keyword in delete so we know id and edit authority are connected
    $q_keyword = $wikiPage->escapeForDB($input['page']);

    // look up the name and delete the image file
    $sql = "SELECT file_path FROM " . $xoopsDB->prefix('gwiki_page_files') . " where file_id='{$q_file_id}' AND keyword = '{$q_keyword}' ";

    $result = $xoopsDB->query($sql);
    if ($result) {
        $rows = $xoopsDB->getRowsNum($result);
        if ($rows) {
            $myrow = $xoopsDB->fetchArray($result);
            if (!empty($myrow['file_path'])) {
                $oldfilename = $uploadpath . '/' . $myrow['file_path'];
                unlink($oldfilename);
            }
        }
    }

    // delete the row
    $sql = 'DELETE FROM ' . $xoopsDB->prefix('gwiki_page_files') . " where file_id='{$q_file_id}' AND keyword = '{$q_keyword}' ";

    $result = $xoopsDB->queryF($sql);
    $cnt    = $xoopsDB->getAffectedRows();
    if ($cnt) {
        $input['message'] = _MD_GWIKI_AJAX_FILEEDIT_DEL_OK;
    }

    return $result;
}

/**
 * @param $input
 *
 * @return mixed
 */
function updateData(&$input)
{
    global $xoopsDB, $xoopsUser, $wikiPage;

    $q_file_id          = (int)($input['file_id']);
    $q_keyword          = $wikiPage->escapeForDB($input['page']);
    $q_file_name        = $wikiPage->escapeForDB($input['file_name']);
    $q_file_icon        = $wikiPage->escapeForDB($input['file_icon']);
    $q_file_type        = $wikiPage->escapeForDB($input['file_type']);
    $q_file_description = $wikiPage->escapeForDB($input['file_description']);
    //  file_path only changed by image upload
    $q_file_size       = (int)($input['file_size']);
    $q_file_path       = empty($input['file_path']) ? '' : $wikiPage->escapeForDB($input['file_path']);
    $q_file_uid        = ($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
    $input['file_uid'] = $q_file_uid;
    if ((int)($input['file_upload_date']) === 0) {
        $input['file_upload_date'] = time();
    }
    $q_file_upload_date = $input['file_upload_date'];

    $sql = "UPDATE " . $xoopsDB->prefix('gwiki_page_files') . ' SET ';
    $sql .= " file_name = '{$q_file_name}', ";
    $sql .= " file_icon = '{$q_file_icon}', ";
    $sql .= " file_type = '{$q_file_type}', ";
    $sql .= " file_size = '{$q_file_size}', ";
    $sql .= " file_path = '{$q_file_path}', ";
    $sql .= " file_uid  = '{$q_file_uid}', ";
    //  $sql.= " file_uid  = '{$q_file_upload_date}', ";
    $sql .= " file_description = '{$q_file_description}' ";
    $sql .= " where file_id = '{$q_file_id}' AND keyword = '{$q_keyword}' ";

    $result = $xoopsDB->queryF($sql);
    if (!$result) {
        header("Status: 500 Internal Error - Database Error");
        $out['message'] === $xoopsDB->error();
        echo json_encode($out);
        exit;
    }
    $cnt = $xoopsDB->getAffectedRows();
    if (!$cnt) {
        $input['message'] = _MD_GWIKI_AJAX_FILEEDIT_NOT_DEFINED;
    } else {
        $input['message'] = _MD_GWIKI_AJAX_FILEEDIT_UPD_OK;
    }

    //file_id, keyword, file_name, file_path, file_type, file_icon, file_size, file_upload_date, file_description, file_uid

    if ($result && !$cnt && !empty($q_file_path)) { // database is OK but nothing to update - require file_path
        $sql = "insert into " . $xoopsDB->prefix('gwiki_page_files');
        $sql .= " (keyword, file_name, file_path, file_type, file_icon, file_size, file_upload_date, file_description, file_uid) ";
        $sql .= " values ('{$q_keyword}', '{$q_file_name}', '{$q_file_path}', '{$q_file_type}', '{$q_file_icon}', '{$q_file_size}', $q_file_upload_date, '{$q_file_description}', '{$q_file_uid}' )";
        $result           = $xoopsDB->queryF($sql);
        $input['file_id'] = $xoopsDB->getInsertId();
        $input['message'] = _MD_GWIKI_AJAX_FILEEDIT_ADD_OK;
    }

    return $input['file_id'];
}

/**
 * @param $newfile
 * @param $input
 *
 * @return mixed
 */
function updateFile($newfile, &$input)
{
    global $uploadpath, $xoopsDB;
    // For now, images are stored in individual directories for each page.
    // We can change the directory distribution later, as the entire path
    // relative to /uploads/gwiki/ ($relpath) is stored in the database.

    // We get rid of any colons in the page name in case the filesystem has
    // issues with them. (undescore is illegal in page name, so it stays unique.)
    $relpath  = 'pages/' . str_replace(':', '_', $input['page']) . '/file/';
    $ourpath  = $uploadpath . $relpath;
    $oldUmask = umask(0);
    @mkdir($ourpath, 0755, true);
    umask($oldUmask);
    $tempfn = tempnam($ourpath, 'WIKIFILE_');
    $image  = file_get_contents('php://input');
    file_put_contents($tempfn, $image);

    $filename = $ourpath . $newfile;
    if (empty($input['file_name'])) {
        $input['file_name'] = $justfn;
    }

    $fi = getFileInfo($tempfn);
    foreach ($fi as $k => $v) {
        $input[$k] = $v;
    }

    $input['file_name'] = $newfile;
    $input['file_path'] = $relpath . $newfile;

    rename($tempfn, $filename);
    chmod($filename, 0644);
    $q_file_id = (int)($input['file_id']);
    $sql       = "SELECT file_path FROM " . $xoopsDB->prefix('gwiki_page_files') . " where file_id='{$q_file_id}' ";

    $result = $xoopsDB->query($sql);
    if ($result) {
        $rows = $xoopsDB->getRowsNum($result);
        if ($rows) {
            $myrow = $xoopsDB->fetchArray($result);
            if (!empty($myrow['file_path'])) {
                $oldfilename = $uploadpath . '/' . $myrow['file_path'];
                unlink($oldfilename);
            }
            // update
        } else {
            // new row
        }
    }
    $input['file_upload_date'] = time();

    return updateData($input);
}

if (!$jsondata) {
    header('Status: 500 Internal Error - No Data Passed');
    exit;
}
$input = json_decode($jsondata, true);
//file_put_contents ( XOOPS_ROOT_PATH.'/uploads/debug.txt', print_r($input,true));

if (!empty($input['file_id'])) {
    $q_file_id = (int)($input['file_id']);
    $sql       = 'SELECT * FROM ' . $xoopsDB->prefix('gwiki_page_files') . " where file_id = '{$q_file_id}' ";
    $result    = $xoopsDB->query($sql);
    if ($row = $xoopsDB->fetcharray($result)) {
        $input['page'] = $row['keyword'];
        foreach ($row as $k => $v) {
            if (!isset($input[$k])) {
                $input[$k] = $v;
            }
        }
    }
}

if (empty($input['page'])) {
    header('Status: 500 Internal Error - No Page');
    exit;
}
$input['page'] = strtolower($wikiPage->normalizeKeyword($input['page']));
$pageX         = $wikiPage->getPage($input['page']);
$mayEdit       = $wikiPage->checkEdit();

if (!$mayEdit) {
    header('Status: 403 Forbidden - No Permission');
    if (!$mayEdit) {
        $out['message'] = _MD_GWIKI_AJAX_FILEEDIT_NO_AUTH;
    }
    echo json_encode($out);
    exit;
}

$q_newfile = $wikiPage->escapeForDB($newfile);
$q_keyword = $wikiPage->escapeForDB($input['page']);

$sql = 'SELECT file_id FROM ' . $xoopsDB->prefix('gwiki_page_files') . " where file_name = '{$q_newfile}' AND keyword='{$q_keyword}' ";

$result = $xoopsDB->query($sql);
if ($row = $xoopsDB->fetcharray($result)) {
    if ($input['file_id'] !== $row['file_id']) {
        header('Status: 500 Internal Error - Duplicate File Name');
        $out['message'] = _MD_GWIKI_AJAX_FILEEDIT_DUPLICATE;
        echo json_encode($out);
        exit;
    }
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

if ($newfile) {
    $fi = getExtensionInfo($newfile);
    if ($fi === false) {
        header('Status: 403 Forbidden - Bad File Type');
        $out['message'] = _MD_GWIKI_AJAX_FILEEDIT_BAD_TYPE;
        echo json_encode($out);
        exit;
    }
    foreach ($fi as $k => $v) {
        $input[$k] = $v;
    }
    $input['file_id'] = updateFile($newfile, $input);
    if ($input['file_id']) {
        $input['message']  = 'Attachment Saved';
        $input['link']     = $uploadurl . $input['file_path'];
        $input['iconlink'] = XOOPS_URL . '/modules/' . $dir . '/assets/icons/48px/' . $input['file_icon'] . '.png';
        $input['userlink'] = getUserName($input['file_uid']);
        $input['size']     = number_format($input['file_size']);
        $input['date']     = date($wikiPage->dateFormat, $input['file_upload_date']);
    }
} else {
    if (!empty($input['op']) && $input['op'] === 'delete') {
        deleteData($input);
    } else {
        updateData($input);
    }
}
echo json_encode($input);
exit;
