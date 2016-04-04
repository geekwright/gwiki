<?php
/**
 * include/search.inc.php - search gwiki pages
 *
 * This file is part of gwiki - geekwright wiki
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 * @param        $queryarray
 * @param        $andor
 * @param        $limit
 * @param        $offset
 * @param        $userid
 * @param  null  $prefix
 * @return array
 */
function gwiki_search($queryarray, $andor, $limit, $offset, $userid, $prefix = null)
{
    global $xoopsDB;

    $dir = basename(dirname(__DIR__));

    $module_handler = xoops_getHandler('module');
    $module         = $module_handler->getByDirname($dir);
    $module_id      = $module->getVar('mid');
    $config_handler = xoops_getHandler('config');
    $moduleConfig   = $config_handler->getConfigsByCat(0, $module->getVar('mid'));

    $baseurl = $moduleConfig['searchlink_template'];

    if ($queryarray === '') {
        $args = '';
    } else {
        $args = implode('+', $queryarray);// template should include '&query='
    }

    $pagesetq = '';
    if (is_array($queryarray) && (count($queryarray) > 1) && substr_compare($queryarray[count($queryarray) - 1], '{pageset=', 0, 9) === 0) {
        $pageset = array_pop($queryarray);
        $pageset = substr($pageset, 9, -1);
        trigger_error($pageset);
        $pagesetq = " AND page_set_home = '{$pageset}' ";
    }

    $sql = 'SELECT DISTINCT * FROM ' . $xoopsDB->prefix('gwiki_pages') . ' WHERE active=1 ' . $pagesetq;
    if (is_array($queryarray) && ($count = count($queryarray))) {
        $sql .= " AND (title LIKE '%$queryarray[0]%' OR search_body LIKE '%$queryarray[0]%' OR meta_keywords LIKE '%$queryarray[0]%' OR meta_description LIKE '%$queryarray[0]%')";
        for ($i = 1; $i < $count; ++$i) {
            $sql .= " $andor (title LIKE '%$queryarray[$i]%' OR search_body LIKE '%$queryarray[$i]%' OR meta_keywords LIKE '%$queryarray[$i]%' OR meta_description LIKE '%$queryarray[$i]%')";
        }
    } else {
        $sql .= " AND uid='$userid'";
    }
    $sql .= ' ORDER BY lastmodified DESC';

    $items  = array();
    $result = $xoopsDB->query($sql, $limit, $offset);
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $items[] = array(
            'title' => $myrow['title'],
            'link'  => sprintf($baseurl, strtolower($myrow['keyword']), $args),
            'time'  => $myrow['lastmodified'],
            'uid'   => $myrow['uid'],
            'image' => 'assets/images/search-result-icon.png'
        );
    }

    return $items;
}
