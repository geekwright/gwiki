<?php
/**
* cleanlitterbox.php - keep a sandbox clean
*
* @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
* @license    gwiki/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/

/*
  This is a script you can adapt to keep a portion of your wiki
  clean. It is intended for tidying up a sandbox where people can
  practice editing. It will delete all page revisions older than a
  certain age specified in hours where the page name matches an
  SQL LIKE pattern.

  You must enable this by declaring a sandbox policy by setting
  the two variables below:
    $keywordpattern pattern to identify sandbox pages (i.e. 'Sandbox:%')
    $retainhours is the minimum number of hours to retain (i.e. 24 for one day)

  Move this file from the extras folder, up one level to the main gwiki
  folder to execute it. You can call it manually in a web browser or set
  it up to be automatically called, for example, by wget in a cron job.
*/

$keywordpattern='';
$retainhours=0;
$dir='gwiki';

include '../../mainfile.php';
// if check variable is set, show like a regular module page (with debug if on)
// otherwise, turn off logging and just get busy cleaning
    if (!empty($_REQUEST['check'])) {
        $xoopsOption['template_main'] = 'gwiki_view.tpl';
        include XOOPS_ROOT_PATH."/header.php";
        do_clean();
        include XOOPS_ROOT_PATH."/footer.php";
    }
    else {
        $xoopsLogger->activated = false;
        do_clean();
        exit;
    }

function do_clean() {
global $xoopsDB;

global $keywordpattern, $retainhours, $dir;

    if($retainhours<=0 || $keywordpattern=='') return;

    $lastmodifiedbefore=time()-($retainhours * 3600);

    $sql = 'DELETE FROM '.$xoopsDB->prefix('gwiki_pages')." WHERE keyword like '{$keywordpattern}' AND lastmodified< $lastmodifiedbefore";
    $result = $xoopsDB->queryF($sql);
    $cnt=$xoopsDB->getAffectedRows();
    if ($cnt>0) {
        $sql  = 'SELECT image_file FROM '.$xoopsDB->prefix('gwiki_page_images');
        $sql .= ' WHERE keyword NOT IN (SELECT keyword from '.$xoopsDB->prefix('gwiki_pages').')';
        $result = $xoopsDB->query($sql);
        while ($f = $xoopsDB->fetchArray($result)) {
            unlink(XOOPS_ROOT_PATH.'/uploads/'.$dir.'/'.$f['image_file']);
        }
        $sql  = 'DELETE FROM '.$xoopsDB->prefix('gwiki_page_images');
        $sql .= ' WHERE keyword NOT IN (SELECT keyword from '.$xoopsDB->prefix('gwiki_pages').')';
        $result = $xoopsDB->queryF($sql);

        $sql  = 'SELECT file_path FROM '.$xoopsDB->prefix('gwiki_page_files');
        $sql .= ' WHERE keyword NOT IN (SELECT keyword from '.$xoopsDB->prefix('gwiki_pages').')';
        $result = $xoopsDB->query($sql);
        while ($f = $xoopsDB->fetchArray($result)) {
            unlink(XOOPS_ROOT_PATH.'/uploads/'.$dir.'/'.$f['file_path']);
        }
        $sql  = 'DELETE FROM '.$xoopsDB->prefix('gwiki_page_files');
        $sql .= ' WHERE keyword NOT IN (SELECT keyword from '.$xoopsDB->prefix('gwiki_pages').')';
        $result = $xoopsDB->queryF($sql);

        $sql = 'DELETE FROM '.$xoopsDB->prefix('gwiki_pageids').' WHERE keyword NOT IN (SELECT keyword from '.$xoopsDB->prefix('gwiki_pages').')';
        $result = $xoopsDB->queryF($sql);
        $sql = 'OPTIMIZE TABLE '.$xoopsDB->prefix('gwiki_pages');
        $result = $xoopsDB->queryF($sql);
    }
}
