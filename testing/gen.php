<?php
/**
 * testing/gen.php - rough tool to generate pages for testing
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 */
include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';
$xoopsOption['template_main'] = 'gwiki_view.tpl';
include XOOPS_ROOT_PATH . '/header.php';
$dir = basename(dirname(__DIR__));
include_once XOOPS_ROOT_PATH . '/modules/' . $dir . '/class/gwikiPage.php';
global $wikiPage;
$wikiPage = new GwikiPage;

include __DIR__ . '/LoremIpsumGenerator.php';
$LIGen = new LoremIpsumGenerator;

$limit     = 100;            // how many pages per run
$bodylimit = 1000;    // max length of a page body in words

$pageset = '';
$pscnt   = 0;

if (!empty($_POST['op'])) {
    for ($i = 1; $i <= $limit; ++$i) {
        $r         = mt_rand(1, 1000);
        $keylength = 1;
        if ($r < 980) {
            $keylength = 2;
        }
        if ($r < 780) {
            $keylength = 3;
        }

        $keyword = trim($LIGen->getContent($keylength, 'txt', $loremipsum = false));
        $keyword = str_replace(array(' ', '.', ',', "\t"), array('-', '', '', ''), $keyword);
        //echo $keyword . "\n";
        $title = $LIGen->getContent(mt_rand(3, 6), 'txt', $loremipsum = false);
        $title = str_replace(array('.', ',', "\t"), '', $title);
        //echo $title . "\n";
        $body = $LIGen->getContent(mt_rand(60, $bodylimit), 'txt', $loremipsum = true);
        //echo $body . "\n";

        // convert a few single words in body to links
        $linklimit = mt_rand(3, 8);
        for ($j = 1; $j < $linklimit; ++$j) {
            $text = trim($LIGen->getContent(1, 'txt', $loremipsum = false));
            $text = str_replace('.', '', $text);
            $link = str_replace(array(' ', '.', ',', "\t"), array('-', '', '', ''), $text);
            $body = str_replace(' ' . $text . ' ', ' [[' . $link . '|' . $text . ']] ', $body);
            //echo $text.':';
        }

        // convert 2 word phrases in body to links - do lots since most won't be found
        $linklimit = mt_rand(100, 300);
        for ($j = 1; $j < $linklimit; ++$j) {
            $text = trim($LIGen->getContent(2, 'txt', $loremipsum = false));
            $text = str_replace('.', '', $text);
            $link = str_replace(array(' ', '.', ',', "\t"), array('-', '', '', ''), $text);
            $body = str_replace(' ' . $text . ' ', ' [[' . $link . '|' . $text . ']] ', $body);
            //echo $text.':';
        }

        $wikiPage->keyword         = $keyword;
        $wikiPage->title           = $title;
        $wikiPage->display_keyword = $keyword;
        $wikiPage->body            = $body;
        $wikiPage->uid             = $xoopsUser ? $xoopsUser->getVar('uid') : 0;

        // randomly pick a random parent page
        $parent = '';
        if (mt_rand(0, 1000) > 700) {
            $sql = 'SELECT keyword FROM ' . $xoopsDB->prefix('gwiki_pageids') . ' AS r1 ';
            $sql .= 'JOIN (SELECT (RAND() * (SELECT MAX(page_id) FROM ' . $xoopsDB->prefix('gwiki_pageids') . ')) AS id) AS r2 ';
            $sql .= 'WHERE r1.page_id >= r2.id ORDER BY r1.page_id ASC LIMIT 1 ';
            $result = $xoopsDB->query($sql);
            if ($result) {
                $myrow  = $xoopsDB->fetchRow($result);
                $parent = $myrow[0];
            }
        }

        $wikiPage->parent_page = $parent;

        // randomly construct a page set
        if ($pageset === '' && mt_rand(0, 1000) > 950) {
            $pageset = $keyword;
            $pscnt   = mt_rand(3, 20);
        } else {
            if ((--$pscnt) < 1) {
                $pageset = '';
            }
        }

        $wikiPage->page_set_home    = $pageset;
        $wikiPage->page_set_order   = '';
        $wikiPage->meta_description = '';
        $wikiPage->meta_keywords    = '';
        $wikiPage->show_in_index    = true;

        $success = $wikiPage->addRevision();

        echo $success . ' - ' . $keyword . ' (' . $pageset . '-' . $parent . ')<br />';
    }
}
echo '<br /><br/><form method="post"><input type="hidden" name="op" value="doit"><input type="submit" value="Run"></form>';

include XOOPS_ROOT_PATH . '/footer.php';
