<?php
if (!defined('_MD_GWIKI_PAGE')) {
define('_MD_GWIKI_PAGE','Page');
define('_MD_GWIKI_LASTMODIFIED','Last modified:');
define('_MD_GWIKI_BY','by');
define('_MD_GWIKI_SUBMIT','Save');
define('_MD_GWIKI_PREVIEW','Preview');

define('_MD_GWIKI_EDITPAGE','Edit Wiki Page');
define('_MD_GWIKI_TITLE','Title');
define('_MD_GWIKI_BODY','Content');
define('_MD_GWIKI_DISPLAY_KEYWORD','Display Page Name');
define('_MD_GWIKI_PAGE_SET_HOME','Page Set Home');
define('_MD_GWIKI_PAGE_SET_ORDER','Page Set Order');
define('_MD_GWIKI_PARENT_PAGE','Parent Page');
define('_MD_GWIKI_META_KEYWORDS','Meta Keywords');
define('_MD_GWIKI_META_DESCRIPTION','Meta Description');
define('_MD_GWIKI_SHOW_IN_INDEX','Show in Index Listings');
define('_MD_GWIKI_LEAVE_INACTIVE','Leave Inactive on Save');
define('_MD_GWIKI_AUTHOR','Author');
define('_MD_GWIKI_ATTACHMENT_LIST','Attachments');

define('_MD_GWIKI_PAGENOTFOUND','This page doesn\'t exist yet.');
define('_MD_GWIKI_PAGENOTFOUND_ERR','That page doesn\'t exist.');
define('_MD_GWIKI_NO_PAGE_PERMISSION','You do not have the authority to edit that page.');
define('_MD_GWIKI_PAGENOTSAVED','This page has not been saved yet.');
define('_MD_GWIKI_DBUPDATED','Database successfully updated!');
define('_MD_GWIKI_ERRORINSERT','Error while updating database!');
define('_MD_GWIKI_EDITCONFLICT','Conflicting modifications! - Changes were saved but not made the active revision. Review page history and resolve any conflicts.');
define('_MD_GWIKI_SAVED_INACTIVE','Database successfully updated. Changes were saved but not made the active revision.');
// Permissions - do not translate *_NUM
// edit all
define('_MD_GWIKI_PAGE_PERM_EDIT_ANY_NUM','11');
define('_MD_GWIKI_PAGE_PERM_EDIT_ANY','Edit Any');
// edit prefix
define('_MD_GWIKI_PAGE_PERM_EDIT_PFX_NUM','12');
define('_MD_GWIKI_PAGE_PERM_EDIT_PFX','Edit Namespace');
// create any
define('_MD_GWIKI_PAGE_PERM_CREATE_ANY_NUM','21');
define('_MD_GWIKI_PAGE_PERM_CREATE_ANY','Create Any');
// create prefix
define('_MD_GWIKI_PAGE_PERM_CREATE_PFX_NUM','22');
define('_MD_GWIKI_PAGE_PERM_CREATE_PFX','Create Namespace');

define('_MD_GWIKI_NOEDIT_NOTFOUND_TITLE','No Such Page');
define('_MD_GWIKI_NOEDIT_NOTFOUND_BODY','<div class="wikinote"><div class="wikinoteicon"></div><div class="wikinotetitle">That Page Was Not Found</div><div class="wikiwarninner">The requested page has not been created yet.</div></div>');

// history
define('_MD_GWIKI_HISTORY','Review page history');
define('_MD_GWIKI_SOURCE','View Source');

define('_MD_GWIKI_HISTORY_TITLE','Page History');
define('_MD_GWIKI_HISTORY_EMPTY','No history for this page');
define('_MD_GWIKI_RESTORE_CONFIRM','Continue with page restore?');
define('_MD_GWIKI_RESTORED','Page Restore Completed.');
define('_MD_GWIKI_HISTORY_COMPARE','Compare');
define('_MD_GWIKI_HISTORY_COMPARE_TT','Show in Compare pane.');
define('_MD_GWIKI_HISTORY_VIEW','View');
define('_MD_GWIKI_HISTORY_VIEW_TT','Show in View pane.');
define('_MD_GWIKI_HISTORY_RESTORE_TT','Make this page revision the active version.');
define('_MD_GWIKI_EMPTY_TITLE','*empty*');
define('_MD_GWIKI_HISTORY_DIFF','Diff');
define('_MD_GWIKI_HISTORY_DIFF_TT','Display diff from this revision.');

define('_MD_GWIKI_PAGE_CREATE_TT','Create page %s');
define('_MD_GWIKI_PAGE_EXT_LINK_TT','This off-site link opens in new window.');
define('_MD_GWIKI_FOLDED_TT','Click to toggle the display of this section.');

define('_MD_GWIKI_BACK_TO_TOP','Back to Top');
define('_MD_GWIKI_TOC','Contents');
define('_MD_GWIKI_MORE','Read On');

define('_MD_GWIKI_IMAGES','Add or Update Images');
define('_MD_GWIKI_IMAGES_TITLE','Page Images');
define('_MD_GWIKI_IMAGES_LIBRARY','Library');
define('_MD_GWIKI_IMAGES_LIST','Images');
define('_MD_GWIKI_IMAGES_DETAIL','Image Detail');
define('_MD_GWIKI_IMAGES_EMPTY','There are no images for this page.');
define('_MD_GWIKI_IMAGES_DROPHERE','(Drop File Here)');
define('_MD_GWIKI_IMAGES_PICKFILE','Image File: ');
define('_MD_GWIKI_IMAGES_NAME','Name');
define('_MD_GWIKI_IMAGES_ALTTEXT','Alt Text');
define('_MD_GWIKI_IMAGES_UPDATE','Update Image');
define('_MD_GWIKI_IMAGES_DELETE','Delete Image');
define('_MD_GWIKI_IMAGES_DELETE_CONFIRM','Delete this Image?');
define('_MD_GWIKI_IMAGES_NEW','Start New Image');
define('_MD_GWIKI_IMAGES_REPRESENT','Use this image to represent the page');
define('_MD_GWIKI_IMAGES_MAX_WIDTH','Max Size in Pixels:');
define('_MD_GWIKI_IMAGES_ALIGN','Align:');
define('_MD_GWIKI_IMAGES_ALIGN_NONE','None');
define('_MD_GWIKI_IMAGES_ALIGN_LEFT','Left');
define('_MD_GWIKI_IMAGES_ALIGN_CENTER','Center');
define('_MD_GWIKI_IMAGES_ALIGN_RIGHT','Right');
define('_MD_GWIKI_IMAGES_INSERT_TITLE','Insert into page using: ');
define('_MD_GWIKI_IMAGES_INSERT_TIP','Insert Image into Page');
define('_MD_GWIKI_IMAGES_CLOSE','Close');
define('_MD_GWIKI_IMAGES_NO_SELECTION','Please select an image first.');

define('_MD_GWIKI_EDIT_SHOW_BODY','Page Edit');
define('_MD_GWIKI_EDIT_SHOW_META','Meta Edit');
define('_MD_GWIKI_FULLSCREEN_EDIT','Full Screen Edit');
define('_MD_GWIKI_FULLSCREEN_EXIT','Exit Full Screen Edit');
define('_MD_GWIKI_ATTACHMENT_EDIT','Edit Attachments for this page.');
define('_MD_GWIKI_WIKI_EDIT_HELP','Wiki Editing Help');
define('_MD_GWIKI_WIKI_HELP_DIR',basename( dirname( __FILE__ ) ) ) ;

define('_MD_GWIKI_CLEAN_DISABLED','Clean is disabled in module config.');
define('_MD_GWIKI_CLEAN_STARTED','Clean script was launched.');

define('_MD_GWIKI_PAGE_IS_LOCKED','This page is locked and may not be edited.');

define('_MD_GWIKI_FILES','Add or Update Attachments');
define('_MD_GWIKI_FILES_TITLE','Attachments');
define('_MD_GWIKI_FILES_LIST','Files');
define('_MD_GWIKI_FILES_DETAIL','File Details');
define('_MD_GWIKI_FILES_EMPTY','There are no attachments for this page.');
define('_MD_GWIKI_FILES_DROPHERE','(Drop File Here)');
define('_MD_GWIKI_FILES_PICKFILE','File to Attach: ');
define('_MD_GWIKI_FILES_NAME','Name');
define('_MD_GWIKI_FILES_TYPE','Type');
define('_MD_GWIKI_FILES_SIZE','Size');
define('_MD_GWIKI_FILES_DATE','Upload Date');
define('_MD_GWIKI_FILES_DESCRIPTION','Description');
define('_MD_GWIKI_FILES_USER','Uploaded by');
define('_MD_GWIKI_FILES_UPDATE','Update File');
define('_MD_GWIKI_FILES_DELETE','Delete File');
define('_MD_GWIKI_FILES_DELETE_CONFIRM','Delete this File?');
define('_MD_GWIKI_FILES_NEW','Start New File');
define('_MD_GWIKI_FILES_CLOSE','Close');
define('_MD_GWIKI_FILES_NO_SELECTION','Please select a file first.');

define('_MD_GWIKI_AJAX_FILEEDIT_DEL_OK','File Deleted');
define('_MD_GWIKI_AJAX_FILEEDIT_NOT_DEFINED','File not defined');
define('_MD_GWIKI_AJAX_FILEEDIT_UPD_OK','Updated');
define('_MD_GWIKI_AJAX_FILEEDIT_ADD_OK','Attachment Added');
define('_MD_GWIKI_AJAX_FILEEDIT_NO_AUTH','No Edit Permission for Page');
define('_MD_GWIKI_AJAX_FILEEDIT_BAD_TYPE','File type is not accepted');
define('_MD_GWIKI_AJAX_FILEEDIT_DUPLICATE','Duplicate File Name');

define('_MD_GWIKI_AJAX_IMGEDIT_DEL_OK','Image Deleted');
define('_MD_GWIKI_AJAX_IMGEDIT_NOT_DEFINED','Image not defined');
define('_MD_GWIKI_AJAX_IMGEDIT_UPD_OK','Image Updated');
define('_MD_GWIKI_AJAX_IMGEDIT_ADD_OK','Image Added');
define('_MD_GWIKI_AJAX_IMGEDIT_NO_AUTH','No Edit Permission for Page');

define('_MD_GWIKI_PAGENAV_TOP','Top');
define('_MD_GWIKI_PAGENAV_NEXT','Next');
define('_MD_GWIKI_PAGENAV_PREV','Previous');
define('_MD_GWIKI_PAGENAV_FIRST','First');
define('_MD_GWIKI_PAGENAV_LAST','Last');

// sort
define('_MD_GWIKI_SORT_UP','Move Up');
define('_MD_GWIKI_SORT_DOWN','Move Down');
define('_MD_GWIKI_SORT_REVERSE','Reverse');
define('_MD_GWIKI_SORT_SAVE','Save');
define('_MD_GWIKI_SORT_ACTIONS','Actions');
define('_MD_GWIKI_SORT_EMPTY','Nothing to Sort');

define('_MD_GWIKI_SORT_PAGE_SELECT','Select a Page to Move');
define('_MD_GWIKI_SORT_PAGE_FORM','Reorder Pages in a Page Set');
define('_MD_GWIKI_SORT_PAGES','Pages');

define('_MD_GWIKI_DIFF_TITLE','Diff: %s');

// editor toolbar buttons
define('_MD_GWIKI_EDIT_BTN_BOLD','Bold');
define('_MD_GWIKI_EDIT_BTN_BOXES','Boxes');
define('_MD_GWIKI_EDIT_BTN_BULLETLIST','Bullet List');
define('_MD_GWIKI_EDIT_BTN_CENTER','Center');
define('_MD_GWIKI_EDIT_BTN_CODE','Monospace');
define('_MD_GWIKI_EDIT_BTN_COLOR','Color');
define('_MD_GWIKI_EDIT_BTN_HEADER','Header');
define('_MD_GWIKI_EDIT_BTN_HORIZONTALRULE','Horizontal Rule');
define('_MD_GWIKI_EDIT_BTN_INDENT','Indent');
define('_MD_GWIKI_EDIT_BTN_IMAGE','Image URL');
define('_MD_GWIKI_EDIT_BTN_ITALIC','Italic');
define('_MD_GWIKI_EDIT_BTN_LINEBREAK','Line Break');
define('_MD_GWIKI_EDIT_BTN_LINK','Link');
define('_MD_GWIKI_EDIT_BTN_NOWIKI','No Wiki');
define('_MD_GWIKI_EDIT_BTN_ORDEREDLIST','Numbered List');
define('_MD_GWIKI_EDIT_BTN_QUOTE','Quote');
define('_MD_GWIKI_EDIT_BTN_REFERENCE','Reference');
define('_MD_GWIKI_EDIT_BTN_STRIKE','Strike through');
define('_MD_GWIKI_EDIT_BTN_SUBSCRIPT','Subscript');
define('_MD_GWIKI_EDIT_BTN_SUPERSCRIPT','Superscript');
define('_MD_GWIKI_EDIT_BTN_UNDERLINE','Underline');

define('_MD_GWIKI_EDPOP_PRMT_COLOR','Color:');

define('_MD_GWIKI_EDPOP_CLR_DEFAULT','(default)');
define('_MD_GWIKI_EDPOP_CLR_AQUA','Aqua');
define('_MD_GWIKI_EDPOP_CLR_BLACK','Black');
define('_MD_GWIKI_EDPOP_CLR_BLUE','Blue');
define('_MD_GWIKI_EDPOP_CLR_FUCHSIA','Fuchsia');
define('_MD_GWIKI_EDPOP_CLR_GRAY','Gray');
define('_MD_GWIKI_EDPOP_CLR_GREEN','Green');
define('_MD_GWIKI_EDPOP_CLR_LIME','Lime');
define('_MD_GWIKI_EDPOP_CLR_MAROON','Maroon');
define('_MD_GWIKI_EDPOP_CLR_NAVY','Navy');
define('_MD_GWIKI_EDPOP_CLR_OLIVE','Olive');
define('_MD_GWIKI_EDPOP_CLR_ORANGE','Orange');
define('_MD_GWIKI_EDPOP_CLR_PURPLE','Purple');
define('_MD_GWIKI_EDPOP_CLR_RED','Red');
define('_MD_GWIKI_EDPOP_CLR_SILVER','Silver');
define('_MD_GWIKI_EDPOP_CLR_TEAL','Teal');
define('_MD_GWIKI_EDPOP_CLR_WHITE','White');
define('_MD_GWIKI_EDPOP_CLR_YELLOW','Yellow');

define('_MD_GWIKI_EDPOP_PRMT_BGCOLOR','Background:');

define('MD_GWIKI_EDIT_BTN_INSERT','Insert');
define('MD_GWIKI_EDIT_BTN_CANCEL','Cancel');

define('_MD_GWIKI_EDPOP_PRMT_HEADER','Header:');
define('_MD_GWIKI_EDPOP_HEAD_1','Level 1');
define('_MD_GWIKI_EDPOP_HEAD_2','Level 2');
define('_MD_GWIKI_EDPOP_HEAD_3','Level 3');
define('_MD_GWIKI_EDPOP_HEAD_4','Level 4');
define('_MD_GWIKI_EDPOP_HEAD_5','Level 5');

define('_MD_GWIKI_EDPOP_PRMT_INDENT','Indent:');

define('_MD_GWIKI_EDPOP_INDENT_1','Indent 1x');
define('_MD_GWIKI_EDPOP_INDENT_2','Indent 2x');
define('_MD_GWIKI_EDPOP_INDENT_3','Indent 3x');
define('_MD_GWIKI_EDPOP_INDENT_4','Indent 4x');
define('_MD_GWIKI_EDPOP_INDENT_5','Indent 5x');

define('_MD_GWIKI_EDPOP_PRMT_BXTYPE','Box Type:');

define('_MD_GWIKI_EDPOP_BXTYPE_CODE','Code');
define('_MD_GWIKI_EDPOP_BXTYPE_FOLD','Folded');
define('_MD_GWIKI_EDPOP_BXTYPE_INFO','Information');
define('_MD_GWIKI_EDPOP_BXTYPE_NOTE','Note');
define('_MD_GWIKI_EDPOP_BXTYPE_TIP','Tip');
define('_MD_GWIKI_EDPOP_BXTYPE_WARN','Warning');

define('_MD_GWIKI_EDPOP_PRMT_REF_ID','Id:');

define('_MD_GWIKI_EDPOP_PRMT_REF_FIRST','First:');
define('_MD_GWIKI_EDPOP_PRMT_REF_REPEAT','Repeat:');
define('_MD_GWIKI_EDPOP_XPRMT_REF1','Specify <i>Id</i> to use a reference more than once.');
define('_MD_GWIKI_EDPOP_XPRMT_REF2','Use <i>First</i> for a parenthetical reference. Use <i>Repeat</i> for shortened version when used again.');

define('_MD_GWIKI_WIZARD','Create Page Wizard');

define('_MD_GWIKI_WIZARD_NEWPAGE_PROMPT','Enter a Name for your new page.');
define('_MD_GWIKI_WIZARD_PICK_NAMESPACE','Select Namespace');
define('_MD_GWIKI_WIZARD_PAGE_NAME','Page Name');
define('_MD_GWIKI_WIZARD_CONTINUE','Continue');
define('_MD_GWIKI_WIZARD_CANCEL','Cancel');

define('_MD_GWIKI_IMPORT_HTML_TITLE','Import HTML to Wiki Page');
define('_MD_GWIKI_IMPORT_HTML_FILE','HTML File');
define('_MD_GWIKI_IMPORT_HTML_TEXT','HTML Text');
define('_MD_GWIKI_IMPORT_HTML_FORM_DESC','Choose file above, or enter HTML text below.');
define('_MD_GWIKI_IMPORT_TEXT_TITLE','Import HTML to Wiki Page');
define('_MD_GWIKI_IMPORT_TEXT_FILE','Text File');
define('_MD_GWIKI_IMPORT_TEXT_FORM_DESC','Choose file above to import as a wiki page. No reformatting or translation will be permormed.');

define('_MD_GWIKI_WIZARD_OPTIONS_TITLE','How do you want to build your page?');
define('_MD_GWIKI_WIZARD_TEMPLATE_TITLE','Create from Template');
define('_MD_GWIKI_WIZARD_TEMPLATE_DESC','Initialize a new page with a copy of a predefined template.');
define('_MD_GWIKI_WIZARD_COPY_TITLE','Copy an existing page');
define('_MD_GWIKI_WIZARD_COPY_DESC','Duplicate an existing page into a new page.');
define('_MD_GWIKI_WIZARD_COPY_PAGE','Page to Copy');
define('_MD_GWIKI_WIZARD_TEXT_TITLE','Import a Text file');
define('_MD_GWIKI_WIZARD_TEXT_DESC','Copy a text file into a wiki page.');
define('_MD_GWIKI_WIZARD_HTML_TITLE','Import from HTML');
define('_MD_GWIKI_WIZARD_HTML_DESC','Convert a simple HTML document into wiki page.');
define('_MD_GWIKI_WIZARD_GALLERY_TITLE','Create an Image Gallery');
define('_MD_GWIKI_WIZARD_GALLERY_DESC','Select pictures to include in a new page.');
define('_MD_GWIKI_WIZARD_GALLERY_SELECT','Select Images for Your Gallery Page');

define('_MD_GWIKI_WIZARD_FORWARDING','Transfering to Editor');
}
