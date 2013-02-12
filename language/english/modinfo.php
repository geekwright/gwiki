<?php
// Module info WikiMod

// The name of this module
define('_MI_WIKIMOD_NAME','Wiki');

// A brief description of this module
define('_MI_WIKIMOD_DESC','A light-weight Wiki implementation.');

// Admin menu
define('_MI_WIKIMOD_ADMENU1','Manage Revisions');

// Config page
define('_MI_WIKIMOD_ANONYMOUS_EDIT','Allow anonymous users to create and modify pages');
define('_MI_WIKIMOD_DATEFORMAT',"Date format of a page's last modification");
define('_MI_WIKIMOD_NUMBERRECENT','Number of pages to display for &lt;[RecentChanges]&gt;');

// Wiki special pages
// Change these names, if you want a different homepage and error page
// for this language - just make sure that they are legal WikiLink names.
define('_MI_WIKIMOD_WIKIHOME','WikiHome');
define('_MI_WIKIMOD_WIKI404','IllegalName');

/***********************************/
/*  Language independent settings  */
/***********************************/
define('_TAB_WIKIMOD','wikimod');
define('_MI_WIKIMOD_DIRNAME','wikimod');
?>