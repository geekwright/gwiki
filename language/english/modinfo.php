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
define('_MI_GWIKI_LINK_TEMPLATE','Format of Internal Wiki Links');
define('_MI_GWIKI_LINK_TEMPLATE_DESC','Format string for sprintf, will be passed page keyword.');
define('_MI_GWIKI_SEARCH_LINK_TEMPLATE','Format of Wiki Search Links');
define('_MI_GWIKI_SEARCH_LINK_TEMPLATE_DESC','Format string for sprintf, will be passed page keyword and search terms.');
define('_MI_GWIKI_IMAGE_LIBRARY_PAGES','Image Libary Page(s)');
define('_MI_GWIKI_IMAGE_LIBRARY_PAGES_DESC','Comma separated list of Wiki pages from which images will be made globally available.');
define('_MI_GWIKI_RETAIN_DAYS','Minimum History Retention Days');
define('_MI_GWIKI_RETAIN_DAYS_DESC','Number of days of change history to retain when database is cleaned. Enter 0 to disable.');
define('_MI_GWIKI_ATTACH_EXT_WHITELIST','Attachment Extension Whitelist');
define('_MI_GWIKI_ATTACH_EXT_WHITELIST_DESC','Comma separated list of file extensions to be allowed for attachments. (Leave blank to disable file uploads.)');
define('_MI_GWIKI_AJAX_ALLOW_ORIGIN','AJAX Wiki Allowed Origin');
define('_MI_GWIKI_AJAX_ALLOW_ORIGIN_DESC','String to include in Access-Control-Allow-Origin: header for AJAX Wiki. Leave blank to disable, * to allow all, or specific host(s)');
define('_MI_GWIKI_ALLOW_CAMELCASE','Treat CamelCase as Link');
define('_MI_GWIKI_ALLOW_CAMELCASE_DESC','Treat CamelCase text in Wiki pages as a link to a same named page.');
define('_MI_GWIKI_DEFAULT_THUMB_SIZE','Default Thumbnail Size');
define('_MI_GWIKI_DEFAULT_THUMB_SIZE_DESC','Default maximal pixel dimension for thumbnail cache.');
define('_MI_GWIKI_WIZARD_TEMPLATES','Wizard Templates Namespace');
define('_MI_GWIKI_WIZARD_TEMPLATES_DESC','Pages in this namespace will be used as templates in the Create Page Wizard. Leave blank to disable templates in the wizard.');
define('_MI_GWIKI_AUTO_NAME_FORMAT','Format for Automatic Names');
define('_MI_GWIKI_AUTO_NAME_FORMAT_DESC','Format string passed to PHP date() function to generate page names for namespaces configured for automatic naming.');

// Wiki special pages
// Change these names, if you want a different homepage and error page
// for this language - just make sure that they are legal WikiLink names.
define('_MI_GWIKI_WIKIHOME','WikiHome'); // this is used only as a default for config option and blocks
define('_MI_GWIKI_WIKI404','IllegalName');

// Admin menu
define('_MI_GWIKI_ADMAIN','Home');
define('_MI_GWIKI_ABOUT','About');
define('_MI_GWIKI_PAGES','Mange Revisions');
define('_MI_GWIKI_ADPERM','Permissions');
define('_MI_GWIKI_ADPREFIX','Namespaces');
define('_MI_GWIKI_ADFILES','Attachments');
define('_MI_GWIKI_ADRECENT','Recent Activity');
define('_MI_GWIKI_AD_PERM_TITLE','Wiki Page Authority');
define('_MI_GWIKI_AD_PERM_DESC',"These permissions control the user's authority to create and edit wiki pages. Permissions can be granted for ALL pages, or only pages in specifc namespaces assigned to the group.");

// Blocks
define('_MI_GWIKI_BL_CLONE_WARN','<span style="color:red;">Please save this cloned block, and then edit and save again!</span>.');
define('_MI_GWIKI_BL_WIKIBLOCK','Wiki in a Block');
define('_MI_GWIKI_BL_WIKIBLOCK_DESC','Display a Wiki Page in a Block with AJAX Loading of links.');
define('_MI_GWIKI_BL_NEWPAGE','New Page');
define('_MI_GWIKI_BL_NEWPAGE_DESC','Shortcut to create a new wiki page.');
define('_MI_GWIKI_BL_TEASERBLOCK','Page Display Block');
define('_MI_GWIKI_BL_TEASERBLOCK_DESC','Display Full or Teaser only view of a Wiki Page');
define('_MI_GWIKI_BL_RECENTBLOCK','Recent Pages');
define('_MI_GWIKI_BL_RECENTBLOCK_DESC','Display Teaser view of recently changed Wiki pages');
define('_MI_GWIKI_BL_PAGESET_TOC','Page Set TOC');
define('_MI_GWIKI_BL_PAGESET_TOC_DESC','Display Table of Contents for a Page Set');
define('_MI_GWIKI_BL_RELATED','Related Pages');
define('_MI_GWIKI_BL_RELATED_DESC','Display Related Pages');
define('_MI_GWIKI_BL_LINKSHERE','What Links Here');
define('_MI_GWIKI_BL_LINKSHERE_DESC','Display wiki vpages that link to this page.');

// notification categories
define ('_MI_GWIKI_NOTIFY_GLOBAL','Entire Wiki');
define ('_MI_GWIKI_NOTIFY_GLOBAL_DESC','Notification options that apply to the entire wiki.');

define ('_MI_GWIKI_NOTIFY_PAGE_CAT','Individual Page');
define ('_MI_GWIKI_NOTIFY_PAGE_CAT_DESC','Notification options that apply to a single wiki page.');

define ('_MI_GWIKI_NOTIFY_NS_CAT','Namespace');
define ('_MI_GWIKI_NOTIFY_NS_CAT_DESC','Notification options that apply to a whole namespace.');

// notification events
define ('_MI_GWIKI_NOTIFY_GLOBAL_NEW_PAGE','New Wiki Page');
define ('_MI_GWIKI_NOTIFY_GLOBAL_NEW_PAGE_CAPTION','Notify me whenever anyone creates a new page in the wiki.');
define ('_MI_GWIKI_NOTIFY_GLOBAL_NEW_PAGE_DESC','Receive notification when a new page is created.');
define ('_MI_GWIKI_NOTIFY_GLOBAL_NEW_PAGE_SUBJECT','[{X_SITENAME}] auto-notify : new wiki page created');

define ('_MI_GWIKI_NOTIFY_GLOBAL_UPD_PAGE','Updated Wiki Page');
define ('_MI_GWIKI_NOTIFY_GLOBAL_UPD_PAGE_CAPTION','Notify me whenever anyone updates a page in the wiki.');
define ('_MI_GWIKI_NOTIFY_GLOBAL_UPD_PAGE_DESC','Receive notification when a page is updated.');
define ('_MI_GWIKI_NOTIFY_GLOBAL_UPD_PAGE_SUBJECT','[{X_SITENAME}] auto-notify : wiki page updated');

define ('_MI_GWIKI_NOTIFY_PAGE_UPD_PAGE','Watch Wiki Page');
define ('_MI_GWIKI_NOTIFY_PAGE_UPD_PAGE_CAPTION','Notify me whenever anyone updates this wiki page.');
define ('_MI_GWIKI_NOTIFY_PAGE_UPD_PAGE_DESC','Receive notification when this page is updated.');
define ('_MI_GWIKI_NOTIFY_PAGE_UPD_PAGE_SUBJECT','[{X_SITENAME}] auto-notify : watched page updated');

define ('_MI_GWIKI_NOTIFY_NS_NEW_PAGE','New Page in Namespace');
define ('_MI_GWIKI_NOTIFY_NS_NEW_PAGE_CAPTION','Notify me whenever anyone creates a new page in this namespace.');
define ('_MI_GWIKI_NOTIFY_NS_NEW_PAGE_DESC','Receive notification when a new page is created in this namespace.');
define ('_MI_GWIKI_NOTIFY_NS_NEW_PAGE_SUBJECT','[{X_SITENAME}] auto-notify : new wiki page created in {NAMESPACE}');

define ('_MI_GWIKI_NOTIFY_NS_UPD_PAGE','Updated Page in Namespace');
define ('_MI_GWIKI_NOTIFY_NS_UPD_PAGE_CAPTION','Notify me whenever anyone updates a page in this namespace.');
define ('_MI_GWIKI_NOTIFY_NS_UPD_PAGE_DESC','Receive notification when a page is updated.');
define ('_MI_GWIKI_NOTIFY_NS_UPD_PAGE_SUBJECT','[{X_SITENAME}] auto-notify : page updated in {NAMESPACE}');

}
