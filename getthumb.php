<?php
/*
 * blocks
 *
 * @copyright	Geekwright, LLC http://geekwright.com
 * @license	GNU General Public License (GPL)
 * @since	1.0
 * @author	Richard Griffith richard@geekwright.com
 * @package	gwiki
 * @version	$Id$
 * 
 * Manage thumbnail cache. Expects gwiki_page_images keyword as page and
 * image_name as name, also optional maximal pixel dimension as size.
 * 
 * Thumbnails are generated for requested size on use, and then served
 * from cache until source image is changed.
 * 
 * Images which are smaller than requested size, or of an unsupported
 * format (currently only jpeg, png and gif are supported,) are served
 * as original source.
 * 
 */

include_once '../../mainfile.php';
$xoopsLogger->activated = false;
// provide error logging for our sanity in debugging (won't see xoops logger)
restore_error_handler();
error_reporting(-1);

//$xoopsOption['template_main'] = 'gwiki_view.html';
//include XOOPS_ROOT_PATH."/header.php";

$dir = basename( dirname( __FILE__ ) ) ;
include_once XOOPS_ROOT_PATH.'/modules/'.$dir.'/classes/gwikiPage.php';
$wikiPage = new gwikiPage;

$default_thumb_size=$xoopsModuleConfig['default_thumb_size'];

global $xoopsDB;

function errorExit($msg) {
	header("Status: 500 Internal Error - ".$msg);
	echo $msg;
	exit;
}

function cleaner($string) {
	$string=stripcslashes($string);
	$string=html_entity_decode($string);
	$string=strip_tags($string); // DANGER -- kills wiki text
	$string=trim($string);
	$string=stripslashes($string);
	return $string;
}

function serveFile($name,$mime) {
	$fp = fopen($name, 'rb');

	header('Content-Type: ' . $mime);
	header('Content-Disposition: inline; filename='. urlencode(basename($name)) );
	header('Content-Length: ' . filesize($name) );

	fpassthru($fp);
	fclose($fp);
	exit;
}

unset($page,$name,$size);
if (isset($_GET['page'])) $page = cleaner($_GET['page']);
if (isset($_GET['name'])) $name = cleaner($_GET['name']);
if (isset($_GET['size'])) $size = intval($_GET['size']);
if (empty($page) || empty($name)) errorExit("parameter missing");
if (empty($size) || $size==0) $size=$default_thumb_size;

$strategy=0;
$strategy_no_thumb=1; // no thumb possible or needed - pass original image
$strategy_old_thumb=2; // send existing thumbnail image
$strategy_new_thumb=3; // generate and pass new thumbnail

$image = $wikiPage->getPageImage($page,$name);
if(!$image) errorExit("invalid parameters");

$file=$image['image_file'];
$i=strrpos($file,'/');
if($i===false) errorExit("malformed path");
$file_pre=substr($file,0,$i);
$file_post=substr($file,$i);

$filename=XOOPS_ROOT_PATH.'/uploads/'.$dir.'/'.$file;
$thumbpath=XOOPS_ROOT_PATH.'/uploads/'.$dir.'/'.$file_pre.'/'.$size;
$thumbname=$thumbpath.$file_post;
//echo $filename.'<br />'.$thumbpath.'<br />'.$thumbname;

if (file_exists($thumbname) && (filemtime($thumbname) > filemtime($filename))) {
	$strategy=$strategy_old_thumb;
	$info = getimagesize($thumbname);
	$img_width=$info[0];
	$img_height=$info[1];
	$img_mime=$info['mime'];
}
else { // (!file_exists($thumbname) || (file_exists($thumbname) && (filemtime($filename) > filemtime($thumbname))))
	$info = getimagesize($filename);
	$img_width=$info[0];
	$img_height=$info[1];
	$img_mime=$info['mime'];
	
	if(($size >= $img_width) && ($size >= $img_height)) {
		$thumb_width  = $img_width;
		$thumb_height = $img_height;
		$strategy=$strategy_no_thumb;
	}
	else {
		$ratio=max($img_width,$img_height) / $size;
		$thumb_width  = ceil($img_width / $ratio);
		$thumb_height = ceil($img_height / $ratio);
		$strategy=$strategy_new_thumb;
	}
	
	switch($info[2]) {
		case IMAGETYPE_JPEG:
			$img_type='jpg';
			break;
		case IMAGETYPE_PNG:
			$img_type='png';
			break;
		case IMAGETYPE_GIF:
			$img_type='gif';
			break;
		default:
			$img_type='unsupported';
			$strategy=$strategy_no_thumb;
			break;
	}
/*
	echo '<br />Image Width: '.$img_width;
	echo '<br />Image Height: '.$img_height;
	echo '<br />Type: '.$info[2].' '.$img_type.' '.$img_mime;

	echo '<br />Thumb Width: '.$thumb_width;
	echo '<br />Thumb Height: '.$thumb_height;
*/
}

switch($strategy) {
	case $strategy_new_thumb:
		@mkdir($thumbpath,0755,true);
		$data = file_get_contents($filename);
	    $im = imagecreatefromstring($data);
	    unset($data);
	    $ti = ImageCreateTrueColor($thumb_width, $thumb_height);
	    ImageCopyResampled($ti, $im, 0, 0, 0, 0, $thumb_width, $thumb_height, $img_width, $img_height);
		imagedestroy($im);
		if($img_type=='jpg') imagejpeg($ti, $thumbname, 80);
		if($img_type=='png') imagepng($ti, $thumbname);
		if($img_type=='git') imagegif($ti, $thumbname);
		imagedestroy($ti);
		serveFile($thumbname,$img_mime);
		break;
	case $strategy_old_thumb:
		serveFile($thumbname,$img_mime);
		break;
	default:
		serveFile($filename,$img_mime);
		break;
}

errorExit('unknown condition');
//include XOOPS_ROOT_PATH."/footer.php";
?>