<?php
/**
 * ajaximglist.php - supply list of images defined for a page
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 */
include dirname(dirname(__DIR__)) . '/mainfile.php';
$xoopsLogger->activated = false;

header('Pragma: public');
header('Cache-Control: no-cache');

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

// $_GET variables we use
unset($page, $bid, $id);
$page = isset($_GET['page']) ? cleaner($_GET['page']) : '';

$dir = basename(__DIR__);

$sql    = 'SELECT * FROM ' . $xoopsDB->prefix('gwiki_page_images') . ' WHERE keyword = \'' . $page . '\' ' . ' ORDER BY image_name ';
$result = $xoopsDB->query($sql);

$images = array();

for ($i = 0, $iMax = $xoopsDB->getRowsNum($result); $i < $iMax; ++$i) {
    $image = $xoopsDB->fetchArray($result);
    //      $image['link']=XOOPS_URL . '/uploads/' . $dir . '/' . $image['image_file'];
    $image['link'] = XOOPS_URL . '/modules/' . $dir . '/getthumb.php?page=' . $image['keyword'] . '&name=' . $image['image_name'];
    $images[]      = $image;
}

$jsonimages = json_encode($images);
echo $jsonimages;
exit;
