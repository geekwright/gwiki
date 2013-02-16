<?php
if (!defined('_AD_GWIKI_ADMINTITLE')) {
define('_AD_GWIKI_ADMINTITLE','Wiki Administration');
define('_AD_GWIKI_KEYWORD','Page ID');
define('_AD_GWIKI_REVISIONS','Revs.');
define('_AD_GWIKI_MODIFIED','Modification date');
define('_AD_GWIKI_AUTHOR','Author');
define('_AD_GWIKI_ACTION','Action');

define('_AD_GWIKI_VIEW','View');
define('_AD_GWIKI_HISTORY','History');
define('_AD_GWIKI_RESTORE','Restore');
define('_AD_GWIKI_FIX','Fix');
define('_AD_GWIKI_LOCK', 'Lock');
define('_AD_GWIKI_UNLOCK', 'Unlock');
define('_AD_GWIKI_DELETE', 'Delete');
define('_AD_GWIKI_PARTITION', 'Partition');
define('_AD_GWIKI_PAGETOOLS', 'Tools');

define('_AD_GWIKI_EMPTYWIKI','No pages have been created, so far.');
define('_AD_GWIKI_CLEANUPDB','Clean up the database');
define('_AD_GWIKI_PARTITION_ALREADY','The table is already partitioned.');
define('_AD_GWIKI_PARTITION_OK','Partitioning completed.');
define('_AD_GWIKI_PARTITION_FAILED','Partitioning failed.');

define('_AD_GWIKI_CONFIRM_DEL','Do you really want to delete this Wiki page: %s?');
define('_AD_GWIKI_CONFIRM_FIX','Do you really want to fix this Wiki page: %s?');
define('_AD_GWIKI_CONFIRM_CLEAN','Do you really want to clean up the database?');
define('_AD_GWIKI_CONFIRM_LOCK','Do you really want to lock this Wiki page: %s?');
define('_AD_GWIKI_CONFIRM_UNLOCK','Do you really want to unlock this Wiki page: %s?');
define('_AD_GWIKI_CONFIRM_PARTITION','Do you really want to partition the gwiki_pages table?');

define('_AD_GWIKI_ADMENU_PREF', 'Preferences');
define('_AD_GWIKI_ADMENU_GOMOD', 'Go To Module');
define('_AD_GWIKI_CONFIRM', 'Please Confirm');
define('_AD_GWIKI_SHOWPAGE', 'Wiki Page');
define('_AD_GWIKI_NO_ACTIVE_PAGE','*deleted*');
define('_AD_GWIKI_PAGENAV', 'Page: ');

// about and menu strings
define('_AD_GW_ABOUT_ABOUT', 'About');
define('_AD_GW_ABOUT_AUTHOR', 'By');
define('_AD_GW_ABOUT_CREDITS', 'Credits');
define('_AD_GW_ABOUT_LICENSE', 'License:');
define('_AD_GW_ADMENU_PREF', 'Preferences');
define('_AD_GW_ADMENU_GOMOD', 'Go To Module');
define('_AD_GW_ADMENU_HELP', 'Help');
define('_AD_GW_ADMENU_TOADMIN', 'Back to Module Administration');
define('_AD_GW_ADMENU_WELCOME', 'Welcome to GWiki!');
define('_AD_GW_ADMENU_MESSAGE', '<img src="../images/icon_big.png" alt="Logo" style="float:left; margin-right:2em;" /> A flexible wiki for your site.');

// namespace
define('_AD_GWIKI_NAMESPACE_PREFIX', 'Namespace');
define('_AD_GWIKI_NAMESPACE_HOME', 'Home Page');
define('_AD_GWIKI_NAMESPACE_AUTONAME', 'Enable Automatic Names');
define('_AD_GWIKI_NAMESPACE_AUTONAME_SHORT', 'Auto Name');
define('_AD_GWIKI_NAMESPACE_TEMPLATE', 'Template');
define('_AD_GWIKI_NAMESPACE_EXTERN', 'External');
define('_AD_GWIKI_NAMESPACE_EXTERN_SHORT', 'External');
define('_AD_GWIKI_NAMESPACE_EXTERN_URL', 'External URL Format');
define('_AD_GWIKI_NAMESPACE_LIST', 'Namepsaces');
define('_AD_GWIKI_NAMESPACE_NEW', 'New Namepsace');
define('_AD_GWIKI_NAMESPACE_EDIT', 'Edit Namepsace');
define('_AD_GWIKI_NAMESPACE_EMPTY', 'No namepsaces defined');
define('_AD_GWIKI_NAMESPACE_GROUPS', 'Assigned Groups');
define('_AD_GWIKI_NAMESPACE_SUBMIT', 'Update');
define('_AD_GWIKI_NAMESPACE_NOT_FOUND', 'Namespace definition not found');
define('_AD_GWIKI_NAMESPACE_CONFIRM_DEL', 'Do you really want to delete this namespace: %s?');

define('_AD_GWIKI_TEMPLATE_ADD', '<em>(Add Template)</em>');
define('_AD_GWIKI_TEMPLATE_NEW', 'New Template');
define('_AD_GWIKI_TEMPLATE_EDIT', 'Edit Template');
define('_AD_GWIKI_TEMPLATE_NOT_FOUND', 'Template definition not found');
define('_AD_GWIKI_TEMPLATE_NAME', 'Name');
define('_AD_GWIKI_TEMPLATE_BODY', 'Template Code');
define('_AD_GWIKI_TEMPLATE_NOTES', 'Notes');
define('_AD_GWIKI_TEMPLATE_CONFIRM_DEL', 'Do you really want to delete this template: %s?');

}
?>