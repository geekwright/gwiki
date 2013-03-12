<?php
/**
* update.php - initializations on module update
*
* @copyright  Copyright © 2012 geekwright, LLC. All rights reserved. 
* @license    qr/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/

if (!defined("XOOPS_ROOT_PATH"))  die("Root path not defined");

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
	while($template = $xoopsDB->fetchArray($result)) {
		$pid=$template['prefix_id'];
		$file=$dir.'_prefix_'.$pid.'.html';

		$tplfiles=$tplfile_handler->find('default','module',$mid,$dir,$file,false);
		if(count($tplfiles)) { $tplfile=$tplfiles[0]; $isnew=false; }
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

		if($isnew) {
			if( ! $tplfile_handler->insert( $tplfile ) ) {
				$module->setErrors('ERROR: Could not insert template '.htmlspecialchars($file).' to the database.');
				$error=true;
			}
		} else {
			if( ! $tplfile_handler->forceUpdate( $tplfile ) ) {
				$module->setErrors('ERROR: Could not update template '.htmlspecialchars($file).' in the database.');
				$error=true;
			}
		}
	}

	// table alterations - these will quietly fail if already done
	// these are all to bring development versions to current
if($old_version<100) {
	trigger_error($old_version);
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
}
	
	return !$error;
}

?>