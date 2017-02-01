<?php
/**
 * functions.php - admin area functions
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 */

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');
/**
 * @param $langdir
 *
 * @return bool
 */
function loadmodinfo($langdir)
{
    global $xoopsModule;
    if (file_exists(XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/' . $langdir . '/modinfo.php')) {
        include_once XOOPS_ROOT_PATH . '/modules/' . $xoopsModule->getVar('dirname') . '/language/' . $langdir . '/modinfo.php';

        return true;
    }

    return false;
}

/**
 * @param $title
 * @param $cols
 */
function adminTableStart($title, $cols)
{
    echo '<table width="100%" border="0" cellspacing="1" class="outer">';
    echo '<tr><th colspan="' . $cols . '">' . $title . '</th></tr>';
}

/**
 * @param $links
 */
function adminTableEnd($links)
{
    echo '</table>';

    if (!empty($links)) {
        $linkline = '';
        foreach ($links as $legend => $link) {
            if ($linkline !== '') {
                $linkline .= ' | ';
            }
            if ($legend === '!PREFORMATTED!') {
                $linkline .= $link;
            } else {
                $linkline .= '<a href="' . $link . '">' . $legend . '</a>';
            }
        }

        echo '<div style="text-align: right; padding-top: 2px; border-top: 1px solid #000000;">' . $linkline . '</div>';
    }
}

$dir = basename(dirname(__DIR__));
include_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/GwikiPage.php';
global $wikiPage;
$wikiPage = new GwikiPage;
