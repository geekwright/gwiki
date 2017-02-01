<?php
/**
 * admin/recent.php - recent wiki changes list
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 */
include __DIR__ . '/header.php';

$moduleAdmin->displayNavigation(basename(__FILE__));

global $xoopsModule, $xoopsConfig;

$wikiPage->setRecentCount(100);
$wikiPage->setWikiLinkURL('pages.php?page=%s&op=history');

adminTableStart(_MI_GWIKI_ADRECENT, 1);
echo '<tr><td width="100%" >';
echo '<div style="margin:2em;">';
echo $wikiPage->renderPage('{RecentChanges}');
echo '</div>';
echo '</td></tr>';

adminTableEnd(null);

include __DIR__ . '/footer.php';
