<?php
if (!defined('_MI_GWIKI_NAME')) {
// Module info WikiMod

// The name of this module
define('_MI_GWIKI_NAME','Wiki');

// A brief description of this module
define('_MI_GWIKI_DESC','A flexible Wiki for XOOPS.');

// Config page
define('_MI_GWIKI_WIKI_HOME','Home Page');
define('_MI_GWIKI_WIKI_HOME_DESC','The name of the Wiki home page (displayed if no other page is specified.)');
define('_MI_GWIKI_DATEFORMAT','Date Format');
define('_MI_GWIKI_DATEFORMAT_DESC',"Date format of a page's last modification");
define('_MI_GWIKI_NUMBERRECENT','Recent Changes Limit');
define('_MI_GWIKI_NUMBERRECENT_DESC','Number of pages to display for {RecentChanges}');
define('_MI_GWIKI_LINK_TEMPLATE', 'Format of Internal Wiki Links');
define('_MI_GWIKI_LINK_TEMPLATE_DESC', 'Format string for sprintf, will be passed page keyword.');
define('_MI_GWIKI_SEARCH_LINK_TEMPLATE','Format of Wiki Search Links');
define('_MI_GWIKI_SEARCH_LINK_TEMPLATE_DESC','Format string for sprintf, will be passed page keyword and search terms.');
define('_MI_GWIKI_IMAGE_LIBRARY_PAGES', 'Image Libary Page(s)');
define('_MI_GWIKI_IMAGE_LIBRARY_PAGES_DESC', 'Comma separated list of Wiki pages from which images will be made globally available.');
define('_MI_GWIKI_RETAIN_DAYS', 'Minimum History Retention Days');
define('_MI_GWIKI_RETAIN_DAYS_DESC', 'Number of days of change history to retain when database is cleaned. Enter 0 to disable.');
define('_MI_GWIKI_ATTACH_EXT_BLACKLIST', 'Attachment Extension Blacklist');
define('_MI_GWIKI_ATTACH_EXT_BLACKLIST_DESC', 'Commas separated list of file extensions NOT to be allowed for attachments. (Do not specify if Whitelist is specified.)');
define('_MI_GWIKI_ATTACH_EXT_WHITELIST', 'Attachment Extension Whitelist');
define('_MI_GWIKI_ATTACH_EXT_WHITELIST_DESC', 'Commas separated list of file extensions to be allowed for attachments. (Do not specify if Blacklist is specified.)');

// Wiki special pages
// Change these names, if you want a different homepage and error page
// for this language - just make sure that they are legal WikiLink names.
define('_MI_GWIKI_WIKIHOME','WikiHome'); // this is used only as a default for config option and blocks
define('_MI_GWIKI_WIKI404','IllegalName');

// Admin menu
define('_MI_GWIKI_ADMAIN','Home');
define('_MI_GWIKI_ABOUT','About');
define('_MI_GWIKI_PAGES', 'Mange Revisions');
define('_MI_GWIKI_ADPERM','Permissions');
define('_MI_GWIKI_ADPREFIX','Namespaces');
define('_MI_GWIKI_ADRECENT', 'Recent Activity');
define('_MI_GWIKI_AD_PERM_TITLE','Wiki Page Authority');
define('_MI_GWIKI_AD_PERM_DESC',"These permissions control the user's authority to create and edit wiki pages. Permissions can be granted for ALL pages, or only pages with specifc name prefixes assigned to the group.");

// Blocks
define('_MI_GWIKI_BL_CLONE_WARN','<span style="color:red;">Please save this cloned block, and then edit and save again!</span>.');
define('_MI_GWIKI_BL_WIKIBLOCK','Wiki in a Block');
define('_MI_GWIKI_BL_WIKIBLOCK_DESC','Display a Wiki Page in a Block with AJAX Loading of links.');
define('_MI_GWIKI_BL_NEWPAGE', 'New Page');
define('_MI_GWIKI_BL_NEWPAGE_DESC', 'Shortcut to create a new wiki page.');
define('_MI_GWIKI_BL_TEASERBLOCK', 'Page Display Block');
define('_MI_GWIKI_BL_TEASERBLOCK_DESC', 'Display Full or Teaser only view of a Wiki Page');
define('_MI_GWIKI_BL_RECENTBLOCK', 'Recent Pages');
define('_MI_GWIKI_BL_RECENTBLOCK_DESC', 'Display Teaser view of recently changed Wiki pages');

}
?>