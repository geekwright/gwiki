<?php
/**
 * include/notification.inc.php - notification lookup
 *
 * This file is part of gwiki - geekwright wiki
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 */
if (!defined('GWIKI_NOTIFY_ITEMINFO')) {
    define('GWIKI_NOTIFY_ITEMINFO', 1);

    /**
     * @param $category
     * @param $item_id
     *
     * @return mixed
     */
    function gwiki_notify_iteminfo($category, $item_id)
    {
        global $xoopsDB;

        $dir = basename(dirname(__DIR__));
        //include_once XOOPS_ROOT_PATH.'/modules/'.$dir.'/class/gwikiPage.php';
        //$wikiPage = new GwikiPage;
        /** @var XoopsModuleHandler $moduleHandler */
        $moduleHandler = xoops_getHandler('module');
        $module        = $moduleHandler->getByDirname($dir);
        $module_id     = $module->getVar('mid');
        $configHandler = xoops_getHandler('config');
        $moduleConfig  = $configHandler->getConfigsByCat(0, $module->getVar('mid'));

        switch ($category) {
            case 'page':
                $item_id = (int)$item_id;
                $sql     = 'SELECT i.keyword as keyword, display_keyword, title FROM ';
                $sql .= $xoopsDB->prefix('gwiki_pageids') . ' i, ' . $xoopsDB->prefix('gwiki_pages') . ' p ';
                //            $sql .= ' WHERE i.keyword = p.keyword AND active = 1 AND page_id = '.$item_id;
                $sql .= " WHERE i.keyword = p.keyword AND active = 1 AND page_id = {$item_id}";

                $result = $xoopsDB->query($sql);
                $row    = $xoopsDB->fetchArray($result);

                $item['name'] = $row['display_keyword'];
                if (empty($item['name'])) {
                    $item['name'] = $row['title'];
                }
                if (empty($item['name'])) {
                    $item['name'] = $row['keyword'];
                }

                $item['url'] = sprintf($moduleConfig['wikilink_template'], $row['keyword']);
                break;
            case 'namespace':
                $item_id = (int)$item_id;
                $sql     = 'SELECT prefix, prefix_home FROM ' . $xoopsDB->prefix('gwiki_prefix');
                //            $sql .= ' WHERE prefix_id = '.$item_id;
                $sql .= " WHERE prefix_id = {$item_id}";

                $result = $xoopsDB->query($sql);
                $row    = $xoopsDB->fetchArray($result);

                $item['name'] = $row['prefix'];
                $item['url']  = sprintf($moduleConfig['wikilink_template'], $row['prefix'] . ':' . $row['prefix_home']);
                break;
            default:
                $item['name'] = $category;
                $item['url']  = XOOPS_URL . '/modules/' . $dir . '/';
                break;
        }

        return $item;
    }
}
