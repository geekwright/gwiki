<?php
/**
* update.php - initializations on module update
*
* This file is part of gwiki - geekwright wiki
*
* @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
* @license    gwiki/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/

// defined("XOOPS_ROOT_PATH") || die("XOOPS root path not defined");

/**
 * @param $module
 * @param $old_version
 *
 * @return bool
 */
function xoops_module_update_gwiki(&$module, $old_version) {
global $xoopsDB;

    $error=false;

    // recompile namespace templates
    $tplfile_handler =& xoops_gethandler( 'tplfile' ) ;

    $dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
    $mid = $module->getVar('mid');

    $sql  = 'SELECT * FROM '.$xoopsDB->prefix('gwiki_prefix').', '.$xoopsDB->prefix('gwiki_template');
    $sql .= ' WHERE prefix_template_id = template_id ';

    $result = $xoopsDB->query($sql);

    //$rows=$xoopsDB->getRowsNum($result);
    while ($template = $xoopsDB->fetchArray($result)) {
        $pid=$template['prefix_id'];
        $file=$dir.'_prefix_'.$pid.'.tpl';

        $tplfiles=$tplfile_handler->find('default','module',$mid,$dir,$file,false);
        if (count($tplfiles)) { $tplfile=$tplfiles[0]; $isnew=false; }
        else { $tplfile =& $tplfile_handler->create() ; $isnew=true; }

        $tplfile->setVar( 'tpl_source' , $template['template_body'] , true ) ;
        $tplfile->setVar( 'tpl_refid' , $mid ) ;
        $tplfile->setVar( 'tpl_tplset' , 'default' ) ;
        $tplfile->setVar( 'tpl_file' , $file ) ;
        $tplfile->setVar( 'tpl_desc' , $template['template'] , true ) ;
        $tplfile->setVar( 'tpl_module' , $dir ) ;
        $tplfile->setVar( 'tpl_lastmodified' , time() ) ;
        $tplfile->setVar( 'tpl_lastimported' , 0 ) ;
        $tplfile->setVar( 'tpl_type' , 'module' ) ;

        if ($isnew) {
            if ( ! $tplfile_handler->insert( $tplfile ) ) {
                $module->setErrors('ERROR: Could not insert template '.htmlspecialchars($file).' to the database.');
                $error=true;
            }
        } else {
            if ( ! $tplfile_handler->forceUpdate( $tplfile ) ) {
                $module->setErrors('ERROR: Could not update template '.htmlspecialchars($file).' in the database.');
                $error=true;
            }
        }
    }

    // table alterations - these will quietly fail if already done
    // these are all to bring development versions to current
if ($old_version<100) {
//	trigger_error($old_version);
    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_pages').' ADD COLUMN toc_cache TEXT NOT NULL AFTER search_body';
    $xoopsDB->queryF($sql);
    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_pages').' ADD COLUMN show_in_index TINYINT NOT NULL DEFAULT 1 AFTER toc_cache';
    $xoopsDB->queryF($sql);
    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_pages').' DROP PRIMARY KEY, ADD PRIMARY KEY(gwiki_id, active)';
    $xoopsDB->queryF($sql);
    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_prefix').' ADD COLUMN prefix_auto_name TINYINT NOT NULL DEFAULT 0 AFTER prefix_home';
    $xoopsDB->queryF($sql);

    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_pageids')." CHANGE keyword  keyword VARCHAR(128) NOT NULL DEFAULT ''";
    $xoopsDB->queryF($sql);
    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_pages')." CHANGE keyword  keyword VARCHAR(128) NOT NULL DEFAULT ''";
    $xoopsDB->queryF($sql);
    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_prefix')." CHANGE prefix  prefix VARCHAR(128) NOT NULL DEFAULT '', " .
    " CHANGE prefix_home  prefix_home VARCHAR(128) NOT NULL DEFAULT ''";
    $xoopsDB->queryF($sql);
    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_template')." CHANGE template  template VARCHAR(128) NOT NULL DEFAULT ''";
    $xoopsDB->queryF($sql);
    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_page_images')." CHANGE keyword  keyword VARCHAR(128) NOT NULL DEFAULT '', " .
    " CHANGE image_name  image_name VARCHAR(128) NOT NULL DEFAULT ''";
    $xoopsDB->queryF($sql);

    $sql="CREATE TABLE ".$xoopsDB->prefix('gwiki_page_files').
    " (file_id int(10) NOT NULL AUTO_INCREMENT, keyword varchar(128) NOT NULL DEFAULT ''," .
    " file_name varchar(128) NOT NULL DEFAULT '', file_path varchar(255) NOT NULL DEFAULT '', " .
    " file_type varchar(128) NOT NULL DEFAULT '', file_icon varchar(64) NOT NULL DEFAULT '', " .
    " file_size int(10) NOT NULL DEFAULT '0', file_upload_date int(10) NOT NULL DEFAULT '0'," .
    " file_description text, file_uid int(10) NOT NULL DEFAULT '0', " .
    " PRIMARY KEY (file_id), UNIQUE KEY (keyword, file_name) ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";
    $xoopsDB->queryF($sql);

    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_page_files')." ADD COLUMN file_type varchar(128) NOT NULL DEFAULT ''" .
    ", ADD COLUMN file_icon varchar(64) NOT NULL DEFAULT '', " . " ADD COLUMN file_size int(10) NOT NULL DEFAULT '0'" .
    ", ADD COLUMN file_upload_date int(10) NOT NULL DEFAULT '0', ADD COLUMN file_description text" .
    ", ADD COLUMN file_uid int(10) NOT NULL DEFAULT '0' ";
    $xoopsDB->queryF($sql);

    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_page_files')." ADD COLUMN file_uid int(10) NOT NULL DEFAULT '0' ";
    $xoopsDB->queryF($sql);

    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_pageids')." ADD COLUMN hit_count int(10) NOT NULL DEFAULT '0' ";
    $xoopsDB->queryF($sql);

    // shift all tables to MyISAM
    $tabs=array('gwiki_pages','gwiki_pageids','gwiki_group_prefix',
            'gwiki_prefix','gwiki_template','gwiki_page_images','gwiki_page_files');
    foreach ($tabs as $v) {
        $sql='ALTER TABLE '.$xoopsDB->prefix($v).' ENGINE = MyISAM';
        $xoopsDB->queryF($sql);
    }

    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_pageids').' ENGINE = MyISAM';
    $xoopsDB->queryF($sql);

    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_pages')." CHANGE parent_page  parent_page VARCHAR(128) NOT NULL DEFAULT ''";
    $xoopsDB->queryF($sql);

    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_pages')." CHANGE page_set_home  page_set_home VARCHAR(128) NOT NULL DEFAULT ''";
    $xoopsDB->queryF($sql);

    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_pages')." CHANGE active active tinyint NOT NULL DEFAULT '0'";
    $xoopsDB->queryF($sql);

    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_pages')." CHANGE admin_lock admin_lock tinyint NOT NULL DEFAULT '0'";
    $xoopsDB->queryF($sql);

    $sql='ALTER TABLE '.$xoopsDB->prefix('gwiki_pages')." CHANGE display_keyword  display_keyword VARCHAR(128) NOT NULL DEFAULT ''";
    $xoopsDB->queryF($sql);

    // drop all indexes except PRIMARY
    $tabs=array();
    $sql  = 'SHOW INDEX FROM '.$xoopsDB->prefix('gwiki_pages');
    $result = $xoopsDB->queryF($sql);
    while ($row = $xoopsDB->fetchArray($result)) {
        if($row['Key_name']!='PRIMARY') $tabs[$row['Key_name']]=$row['Non_unique'];
    }
    $xoopsDB->freeRecordSet($result);
    if (!empty($tabs)) {
        $sql='';
        foreach ($tabs as $i => $v) {
            if(empty($sql)) $sql = 'ALTER TABLE '.$xoopsDB->prefix('gwiki_pages') . ' DROP KEY '.$i;
            else $sql .= ' , DROP KEY '.$i;
        }
        $xoopsDB->queryF($sql);
    }

    $sql  = 'ALTER TABLE '.$xoopsDB->prefix('gwiki_pages');
    $sql .=	' ADD KEY activekey (active,keyword), ADD KEY keyword (keyword), ' .
            ' ADD KEY parent (active,parent_page), ADD KEY pageset (active,page_set_home), ' .
            ' ADD KEY lastmod (active,lastmodified), ADD KEY pageindex (active,show_in_index,display_keyword) ';
    $xoopsDB->queryF($sql);
}
if ($old_version<101) {
    $sql  = 'CREATE TABLE IF NOT EXISTS '.$xoopsDB->prefix('gwiki_pagelinks').' (';
    $sql .=	' from_keyword varchar(128) NOT NULL DEFAULT \'\',';
    $sql .=	' to_keyword varchar(128) NOT NULL DEFAULT \'\',';
    $sql .=	' PRIMARY KEY (from_keyword, to_keyword),';
    $sql .=	' KEY (to_keyword),';
    $sql .=	' KEY (from_keyword)';
    $sql .=	') ENGINE=MyISAM  DEFAULT CHARSET=utf8;';
    $xoopsDB->queryF($sql);
}

    return !$error;
}
