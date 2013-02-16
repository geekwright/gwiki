<?php
include 'header.php';
if(!$xoop25plus) adminmenu(6);

global $xoopsModule, $xoopsConfig;

$wikiPage->setRecentCount(100);
$wikiPage->setWikiLinkURL('pages.php?page=%s&op=history');

adminTableStart(_MI_GWIKI_ADRECENT,1);
echo '<tr><td width="100%" >';
echo '<div style="margin:2em;">';
echo $wikiPage->renderPage('{RecentChanges}');
echo '</div>';
echo '</td></tr>';

adminTableEnd(NULL);

include 'footer.php';
?>
