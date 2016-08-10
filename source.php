<?php
/**
 * source.php - return source of page as text file
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 */
include dirname(dirname(__DIR__)) . '/mainfile.php';
$xoopsLogger->activated = false;
include_once __DIR__ . '/include/functions.php';
global $wikiPage;

if (isset($_GET['page'])) {
    $page  = $wikiPage->normalizeKeyword(cleaner($_GET['page']));
    $pageX = $wikiPage->getPage($page);
} else {
    $page  = false;
    $pageX = false;
}

if ($page && $pageX) {
    header('Content-type: text/plain');
    header('Content-Disposition: inline; filename="' . $page . '.txt"');
    echo $wikiPage->body;
    echo "\r\n";
} else {
    redirect_header(sprintf($wikiPage->getWikiLinkURL(), $wikiPage->wikiHomePage), 2, _MD_GWIKI_PAGENOTFOUND_ERR);
}

exit;
