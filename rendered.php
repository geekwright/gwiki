<?php
/**
 * rendered.php - return rendered page as text file
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
include_once "include/functions.php";
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
    echo '<div class="wikipage">' . "\n";
    echo '<h1 class="wikititle" id="toc0">' . $wikiPage->title . "</h1>\n";
    echo $wikiPage->renderPage();
    echo "\n</div>\n";
} else {
    redirect_header(sprintf($wikiPage->getWikiLinkURL(), $wikiPage->wikiHomePage), 2, _MD_GWIKI_PAGENOTFOUND_ERR);
}

exit;
