<?php
/**
* gwikiPage.php - class to access wiki page data
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

if (!defined("XOOPS_ROOT_PATH")) die("Root path not defined");

define ('_WIKI_CAMELCASE_REGEX','(([A-Z]{1,}[a-z\x80-\xff0-9\:]+){2,}\d*)');
define ('_WIKI_KEYWORD_REGEX','([A-Za-z\x80-\xff0-9.\:-]{1,})');

class gwikiPage {
	//------------------------------------------------------------
	// Properties -  public, protected, and private
	//------------------------------------------------------------

	protected $currentid;
	protected $currentkeyword;
	public $gwiki_id;
	public $keyword;
	public $display_keyword;
	public $title;
	public $body;
	public $parent_page;
	public $page_set_home;
	public $page_set_order;
	public $meta_description;
	public $meta_keywords;
	public $lastmodified;
	public $uid;
	public $admin_lock;
	public $active;
	public $search_body;
	public $toc_cache;
	public $show_in_index;
	public $gwiki_version;

	public $page_id; // an integer id for the keyword
	public $wikiHomePage; // the home page
	public $currentprefix; // Prefix of current keyword, if any
	public $currentprefixid; // id of current Prefix
	public $currenttemplateid; // template for current Prefix (0=use default)
	public $attachments;
	
	public $renderedPage;
	
	private $numberOfRecentItems=10;
	private $wikiLinkURL='index.php?page=%s';	// keyword will be inserted with sprintf. better link establised in __construct()
	public $dateFormat;
	public $defaultThumbSize;
	private $tocIdPrefix='toc';
	private $tocAnchorFmt='#%s';
	private $imageLib=array();
	private $useCamelCase;

	private $module_id;

	private $wikiDir;				// dirname of the gwiki module
	private $gwikiVersion=1;		// wiki syntax version for future backward compatability
	
	private $highlightArg;
	
	private $noWikiQueue = array();		// hold no wiki content during rendering
	private $noWikiIndex = 0;
	
	private $tocQueue = array();                   // track headers for toc
	private $tocIndex = 0;
	
	private $refQueue = array();                   // track reference
	private $refIndex = 0;
	private $refShown = false;
	
	// Out Of Bounds data - not cleared with resetPage
	private $pageIndexPrefix='';	// current prefix for the pageindex

	//------------------------------------------------------------
	// Methods
	//------------------------------------------------------------

	/**
	* class constructor
	*
	* @param ? what and why?
	* @access private
	* @since 1.0.0
	*/
	public function __construct()
	{
		global $xoopsDB;
		$this->resetPage();
		$dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
		$this->wikiDir=$dir;

		$module_handler =& xoops_gethandler('module');
		$module         =& $module_handler->getByDirname($dir);
		$module_id      =  $module->getVar('mid');
		$config_handler =& xoops_gethandler('config');
		$moduleConfig   =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));

		$this->wikiLinkURL = $moduleConfig['wikilink_template'];
		$this->wikiHomePage = $moduleConfig['wiki_home_page'];
		$this->dateFormat = $moduleConfig['date_format'];
		$this->imageLib = explode(',',$moduleConfig['imagelib_pages']);
		$this->useCamelCase = $moduleConfig['allow_camelcase'];
		$this->defaultThumbSize = $moduleConfig['default_thumb_size'];
		$this->module_id = $module_id;

		if(!defined('_MI_GWIKI_WIKIHOME')) $this->loadLanguage('modinfo',$dir);
		if(!defined('_MD_GWIKI_PAGE_PERM_EDIT_ANY_NUM')) $this->loadLanguage('main',$dir);
	}
	
	private function loadLanguage($name, $domain = '',$language = null)
	{
	global $xoopsConfig;
		if ( !@include_once XOOPS_ROOT_PATH . "/modules/{$domain}/language/" . $xoopsConfig['language'] . "/{$name}.php") {
			include_once XOOPS_ROOT_PATH . "/modules/{$domain}/language/english/{$name}.php" ;
		}
	}

	protected function resetPage()
	{
		$this->gwiki_id = NULL;
		$this->keyword = '';
		$this->display_keyword = '';
		$this->title = '';
		$this->body = '';
		$this->parent_page = '';
		$this->page_set_home = '';
		$this->page_set_order = '';
		$this->meta_description = '';
		$this->meta_keywords = '';
		$this->lastmodified = 0;
		$this->uid = 0;
		$this->admin_lock = 0;
		$this->active = 0;
		$this->search_body = '';
		$this->toc_cache = '';
		$this->show_in_index = 1;
		$this->gwiki_version = $this->gwikiVersion;
		
		$this->page_id = 0;
		$this->created = 0;
		$this->renderedPage = '';
		$this->currentprefix = '';
		$this->currentprefixid = '';
		$this->currenttemplateid=0;
		$this->attachments=array();
		$this->tocQueue = array();
		$this->tocIndex = 0;
		$this->refQueue = array();
		$this->refIndex = 0;
		$this->refShown=false;
	}

	public function escapeForDB($value)
	{
		return mysql_real_escape_string($value);
	}

	public function setRecentCount($count)
	{
		$count=intval($count);
		if($count>1 and $count<1000) $this->numberOfRecentItems = $count;
	}
	
	public function setWikiLinkURL($url)
	{
		$this->wikiLinkURL=$url;
	}

	public function getWikiLinkURL()
	{
		return $this->wikiLinkURL;
	}

	public function getWikiDir()
	{
		return $this->wikiDir;
	}

	public function setTocFormat($prefix,$linkformat)
	{
		$this->tocIdPrefix=$prefix;
		$this->tocAnchorFmt=$linkformat;
	}


	/**
	* Make sure that keyword obeys formatting rules or switch to illegal name
	* @param mixed $keyword - wiki page name
	* @access public
	* @since 1.0
	*/
	public function makeKeyword($keyword)
	{
		if (!preg_match("#^"._WIKI_KEYWORD_REGEX."$#", $keyword)) {
			$keyword = _MI_GWIKI_WIKI404;
		}
		else {  // check for undefined prefix
			$prefix=$this->getPrefix($keyword);
			if($prefix && !$prefix['defined']) $keyword = _MI_GWIKI_WIKI404; 
		}
		return $keyword;
	}
	
	/**
	* Capture out of bounds data traveling with keyword. Such data is sent
	* in keyword(oob) construct. This function processes any oob data and
	* returns a clean keyword.
	* oob data is used this way to pass page specific data in any url
	* 
	* Only call this if you will NOT be calling normalizeKeyword or the
	* OOB data will be lost.
	* 
	* @param mixed $keyword - wiki page name possibily containing OOB data
	* @access public
	* @since 1.0
	*/
	public function getOOBFromKeyword($keyword)
	{
		$oob=null;
		if(substr($keyword,-1)==')') {
			$lparen=strpos($keyword,'(');
			if($lparen!==false) {
				$inparen=substr($keyword,$lparen);
				$inparen=substr($inparen,1,strlen($inparen)-2);
				$keyword=substr($keyword,0,$lparen);
				$oob=$inparen;
			}
		}
		// currently this is the only use
		$this->pageIndexPrefix=strtolower($oob);
		return $keyword;
	}
	
	/**
	* If page exists, fix case of page name to that specified in database
	* @param mixed $keyword - wiki page name
	* @access public
	* @since 1.0
	*/
	public function normalizeKeyword($keyword)
	{
		global $xoopsDB;
		
		$keyword=$this->getOOBFromKeyword($keyword);
		$keyword=$this->escapeForDB($keyword);
		$sql = "SELECT keyword FROM ".$xoopsDB->prefix('gwiki_pages')." WHERE keyword='{$keyword}' AND active=1 ";
		$result = $xoopsDB->query($sql);
		if ($content = $xoopsDB->fetchArray($result)) {
			$keyword=$content['keyword'];
		} else {
			$keyword=$this->makeKeyword($keyword);
		}
		return $keyword;
	}
	
	/**
	* Get the gwiki_id of the active page for the keyword
	* @param mixed $keyword - wiki page name
	* @access public
	* @since 1.0
	*/
	public function getCurrentId($keyword)
	{
		global $xoopsDB;
    
		$sql = 'SELECT gwiki_id FROM '.$xoopsDB->prefix('gwiki_pages');
		$sql.= " WHERE keyword='{$keyword}' AND active = 1 ORDER BY gwiki_id DESC LIMIT 1";
		$result = $xoopsDB->query($sql);
		list($id) = $xoopsDB->fetchRow($result);
    
		return intval($id);
	}

	public function addRevision($leave_inactive=false)
	{
		global $xoopsDB;
		
		$page=$this->escapeForDB($this->keyword);
		$display_keyword=$this->display_keyword;
		if(empty($this->display_keyword)) $this->display_keyword=$page;
		$this->tocQueue = array();
		$this->tocIndex = 0;
		$this->refQueue = array();
		$this->refIndex = 0;

		// eliminate things we don't want in search page because they
		// are misleading and/or change outside of the page itself
		$search[]  = "#{(PageIndex|RecentChanges)([^\"<\n]+?)?}#si";
		$replace[] = '';
		$search[]  = "#\{toc\}#i";
		$replace[] = '';
		$search[]  = "#\{pagesettoc\}#i";
		$replace[] = '';
		$tempbody=preg_replace($search, $replace, $this->body)."\n\n";

		$this->search_body=strip_tags($this->renderPage($tempbody));
		$this->toc_cache=serialize($this->tocQueue);
		$this->gwiki_version = $this->gwikiVersion;		// new revisions always for current engine
		
		// if we are adding to a page set, auto increment the order if none specified
		if(!empty($this->page_set_home) && $this->page_set_order==0) {
			$this->page_set_order = $this->getNextPageSetOrder($this->page_set_home);
		}

		// this will usually fail (duplicate)
		$sql = 'INSERT INTO '.$xoopsDB->prefix('gwiki_pageids')." (keyword, created) VALUES('{$page}', UNIX_TIMESTAMP())";
		$result=$xoopsDB->query($sql);
		if($result) {
			$page_id=$xoopsDB->getInsertId();
			$this->page_id=$page_id;
		}		
		if($leave_inactive) {
			// allow a save that is not activated (for conflict management, and maybe more)
			$this->active=0;
			$sql = 'INSERT INTO '.$xoopsDB->prefix('gwiki_pages');
			$sql .= ' (keyword, display_keyword, title, body, parent_page, page_set_home, page_set_order, meta_description, meta_keywords';
			$sql .= ', lastmodified, uid, admin_lock, active, search_body, toc_cache, show_in_index, gwiki_version)';
			$sql .= ' VALUES (';
			$sql .= '\''.$page.'\' ,';
			$sql .= '\''.$this->escapeForDB($this->display_keyword).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->title).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->body).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->parent_page).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->page_set_home).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->page_set_order).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->meta_description).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->meta_keywords).'\' ,';
			$sql .= 'UNIX_TIMESTAMP() ,';
			$sql .= '\''.$this->escapeForDB($this->uid).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->admin_lock).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->active).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->search_body).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->toc_cache).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->show_in_index).'\' ,';
			$sql .= '\''.$this->escapeForDB($this->gwiki_version).'\' )';
			$result=$xoopsDB->query($sql);
			if($result) {
				$result=$xoopsDB->getInsertId();
				$this->gwiki_id=$result;
			}
		}
		else {
			$sql = 'UPDATE '.$xoopsDB->prefix('gwiki_pages')." SET active = 0 WHERE keyword='{$page}' and active = 1 ";
			$result=$xoopsDB->query($sql);
			if($result) {
				$previous_rows=$xoopsDB->getAffectedRows();
				$this->active=1;
				$sql = 'INSERT INTO '.$xoopsDB->prefix('gwiki_pages');
				$sql .= ' (keyword, display_keyword, title, body, parent_page, page_set_home, page_set_order, meta_description, meta_keywords';
				$sql .= ', lastmodified, uid, admin_lock, active, search_body, toc_cache, show_in_index, gwiki_version)';
				$sql .= ' VALUES (';
				$sql .= '\''.$page.'\' ,';
				$sql .= '\''.$this->escapeForDB($this->display_keyword).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->title).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->body).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->parent_page).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->page_set_home).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->page_set_order).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->meta_description).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->meta_keywords).'\' ,';
				$sql .= 'UNIX_TIMESTAMP() ,';
				$sql .= '\''.$this->escapeForDB($this->uid).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->admin_lock).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->active).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->search_body).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->toc_cache).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->show_in_index).'\' ,';
				$sql .= '\''.$this->escapeForDB($this->gwiki_version).'\' )';
				$result=$xoopsDB->query($sql);
				if($result) {
					$result=$xoopsDB->getInsertId();
					$this->gwiki_id=$result;
					$notification_handler =& xoops_gethandler('notification');
					$tags['PAGE_NAME']=$page;
					$tags['PAGE_TITLE']=$this->title;
					if(empty($tags['PAGE_TITLE'])) $tags['PAGE_TITLE']=$this->display_keyword;
					if(empty($tags['PAGE_TITLE'])) $tags['PAGE_TITLE']=$this->page;
					$tags['PAGE_LINK']=sprintf($this->wikiLinkURL,$page);
					$tags['NAMESPACE']=$this->currentprefix;

					if($previous_rows<1) {
						// only for new
						$notification_handler->triggerEvent ('global',0,'new_page' , $tags, array(), $this->module_id);
						if($this->currentprefixid) { // have namespace
							$notification_handler->triggerEvent ('namespace', $this->currentprefixid, 'new_ns_page' , $tags, array(), $this->module_id);
						}
					}
					// for all cases (new is also an update)
					$notification_handler->triggerEvent ('page',$this->page_id,'page_watch' , $tags, array(), $this->module_id);
					$notification_handler->triggerEvent ('global',0,'upd_page', $tags, array(), $this->module_id);
					if($this->currentprefixid) { // have namespace
						$notification_handler->triggerEvent ('namespace', $this->currentprefixid, 'upd_ns_page' , $tags, array(), $this->module_id);
					}
				}
			}
		}

		return $result;
	}

	// get the next higher unused page_set_order for a given page_set_home
	private function getNextPageSetOrder($page_set_home) {
		global $xoopsDB;

		$page_set_order=1;
		
		$keyword=$this->escapeForDB($page_set_home);
		
		$sql = 'SELECT MAX(page_set_order) FROM '.$xoopsDB->prefix('gwiki_pages')." WHERE active = 1 and page_set_home = '{$keyword}' ";
		$result=$xoopsDB->query($sql);
		if ($result) {
			$myrow=$xoopsDB->fetchRow($result);
			$page_set_order=$myrow[0]+1;
		}
		return $page_set_order;
	}
	
	/**
	* Check if the current user may edit the current page
	* Since the class can be used outside the module where permissions are assigned, we have to work at this a bit
	*
	* @param mixed $keyword - wiki page name
	* @access public
	* @since 1.0
	*/
	public function checkEdit()
	{
		global $xoopsUser, $xoopsDB;
		
		$mayEdit=false;
		$keyword=$this->keyword;

		$dir = $this->wikiDir;
		$module_handler =& xoops_gethandler('module');
		$module         =& $module_handler->getByDirname($dir);
		$module_id = $module->getVar('mid');
		// $config_handler =& xoops_gethandler('config');
		// $moduleConfig   =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));

		if (is_object($xoopsUser)) {
			$groups = $xoopsUser->getGroups();
		} else {
			$groups = XOOPS_GROUP_ANONYMOUS;
		}

		$gperm_handler =& xoops_gethandler('groupperm');

		$edit_any = $gperm_handler->checkRight('wiki_authority', _MD_GWIKI_PAGE_PERM_EDIT_ANY_NUM, $groups, $module_id);
		$edit_pfx = $gperm_handler->checkRight('wiki_authority', _MD_GWIKI_PAGE_PERM_EDIT_PFX_NUM, $groups, $module_id);
		$create_any = $gperm_handler->checkRight('wiki_authority', _MD_GWIKI_PAGE_PERM_CREATE_ANY_NUM, $groups, $module_id);
		$create_pfx = $gperm_handler->checkRight('wiki_authority', _MD_GWIKI_PAGE_PERM_CREATE_PFX_NUM, $groups, $module_id);
		
		// check for namespace prefix
		$prefix = $this->getPrefix($keyword);
		if($prefix) {
			if($prefix['defined']) {
				if(is_array($groups)) $groupwhere=' IN ('.implode(', ',$groups).') ';
				else $groupwhere=" = '".$groups."'";
				$sql='SELECT group_prefix_id FROM '.$xoopsDB->prefix('gwiki_group_prefix').' WHERE prefix_id = \''.$prefix['prefix_id'].'\' AND group_id '.$groupwhere;
				$result = $xoopsDB->query($sql);
				$rows=$xoopsDB->getRowsNum($result);
				$xoopsDB->freeRecordSet($result);
				if($rows) { // prefix is assigned to one or more of user's groups
					if(($edit_pfx || $create_pfx) && $this->gwiki_id) $mayEdit=true;
					if($create_pfx && !($this->gwiki_id)) $mayEdit=true;
				}
				if(($edit_any || $create_any) && $this->gwiki_id) $mayEdit=true;
				if($create_any && !($this->gwiki_id)) $mayEdit=true;
			}
			else {  // allow edit, but no create if prefix is undefined
				if($edit_any && $this->gwiki_id) $mayEdit=true;
			}
		}
		else {
			if(($edit_any || $create_any) && $this->gwiki_id) $mayEdit=true;
			if($create_any && !($this->gwiki_id)) $mayEdit=true;
		}

		return $mayEdit;

	}
	
	public function getUserName($uid)
	{
		global $xoopsConfig;
    
		$uid = intval($uid);
    
		 if ($uid > 0) {
			$member_handler =& xoops_gethandler('member');
			$user =& $member_handler->getUser($uid);
			if (is_object($user)) {
				return "<a href=\"".XOOPS_URL."/userinfo.php?uid=$uid\">".htmlspecialchars($user->getVar('uname'),ENT_QUOTES)."</a>";
			}
		}
    
	      return $xoopsConfig['anonymous'];
	}

	public function getKeywordById($page_id)
	{
		global $xoopsDB;

		$keyword=null;
		
		$sql = 'SELECT keyword FROM '.$xoopsDB->prefix('gwiki_pageids')." WHERE page_id = '{$page_id}' ";
		$result=$xoopsDB->query($sql);
		if ($result) {
			$myrow=$xoopsDB->fetchRow($result);
			$keyword=$myrow[0];
		}
		return $keyword;
	}

	public function getPageId($keyword)
	{
		global $xoopsDB;

		$page_id=0;
		
		$keyword=$this->escapeForDB($keyword);
		
		$sql = 'SELECT page_id FROM '.$xoopsDB->prefix('gwiki_pageids')." WHERE keyword = '{$keyword}' ";
		$result=$xoopsDB->query($sql);
		if ($result) {
			$myrow=$xoopsDB->fetchRow($result);
			$page_id=$myrow[0];
		}
		return $page_id;
	}

	public function registerHit($keyword)
	{
		global $xoopsDB;

		$keyword=$this->escapeForDB($keyword);
		
		$sql = 'UPDATE '.$xoopsDB->prefix('gwiki_pageids')." SET hit_count = hit_count + 1 WHERE keyword = '{$keyword}' ";
		$result=$xoopsDB->queryF($sql);
		// nothing to do if it fails
		return $xoopsDB->getAffectedRows();
	}

	public function getPage($keyword,$id=NULL)
	{
		global $xoopsDB;

		$this->resetPage();
		$this->keyword= $keyword;
		$prefix = $this->getPrefix($keyword);
		if($prefix) {
			$this->currentprefix = $prefix['prefix'];
			$this->currentprefixid = $prefix['prefix_id'];
			$this->currenttemplateid = $prefix['prefix_template_id'];
		}

		$keyword=$this->escapeForDB($keyword);
		
		$this->page_id = $this->getPageId($keyword);

		if(empty($id)) $sql = "SELECT * FROM ".$xoopsDB->prefix('gwiki_pages').' natural left join ' .$xoopsDB->prefix('gwiki_pageids')." WHERE keyword='{$keyword}' and active = 1 ";
		else {
			$id=intval($id);
			$sql = "SELECT * FROM ".$xoopsDB->prefix('gwiki_pages').' natural left join ' .$xoopsDB->prefix('gwiki_pageids')." WHERE keyword='{$keyword}' and gwiki_id = {$id} ";
		}
		$result = $xoopsDB->query($sql);
		$page=false;
		$rows=$xoopsDB->getRowsNum($result);
		if($rows>0) {
			$page=$xoopsDB->fetchArray($result);

			$this->gwiki_id = $page['gwiki_id'];
			$this->keyword= $page['keyword'];
			$this->display_keyword= $page['display_keyword'];
			$this->title = $page['title'];
			$this->body = $page['body'];
			$this->parent_page = $page['parent_page'];

			$this->page_set_home = $page['page_set_home'];
			$this->page_set_order = $page['page_set_order'];

			$this->meta_description = $page['meta_description'];
			$this->meta_keywords= $page['meta_keywords'];
			$this->lastmodified = $page['lastmodified'];
			$this->uid = $page['uid'];
			$this->admin_lock = $page['admin_lock'];
			$this->active = $page['active'];
			$this->search_body = $page['search_body'];
			$this->toc_cache = $page['toc_cache'];
			$this->show_in_index = $page['show_in_index'];
			
			$this->gwiki_version = $page['gwiki_version'];
			$this->page_id = $page['page_id'];
			$this->created = $page['created'];
			
			$page['author'] = $this->getUserName($page['uid']);
			$page['revisiontime']=date($this->dateFormat,$page['lastmodified']);
			$page['createdtime']=date($this->dateFormat,$page['created']);
			$page['createdmonth']=date('M',$page['created']);
			$page['createdday']=date('d',$page['created']);
			$page['createdyear']=date('Y',$page['created']);

			$temp = $this->renderPageSetNav($keyword);
			if($temp) $page['pageset'] = $temp;
		}
		return $page;
	}

	/**
	* Check for a prefix (namespace)
	* @param mixed $keyword - wiki page name
	* @access public
	* @since 1.0
	*/
	public function getPrefix($keyword)
	{
/*
 gwiki_prefix columns
  prefix_id
  prefix
  prefix_home
  prefix_template_id
  prefix_is_external
  prefix_external_url < sprintf template for page in external namespace
*/
		global $xoopsDB;

		$prefix=false;
		$keyword=$this->escapeForDB($keyword);

		$pos=strpos($keyword,':');
		// split namespace and page reference on first colon
		if($pos!==false && $pos>0) {
			$pre=substr($keyword,0,$pos);
			$page=substr($keyword,$pos+1);
			$q_pre=$this->escapeForDB($pre);
			$sql = "SELECT * FROM ".$xoopsDB->prefix('gwiki_prefix')." WHERE prefix='{$q_pre}' ";
			$result = $xoopsDB->query($sql);
			$rows=$xoopsDB->getRowsNum($result);
			if($rows>0) {
				$prefix=$xoopsDB->fetchArray($result);
				if($page=='') $page=$prefix['prefix_home']; // supply home page if empty
				$prefix['defined']=true;
				// external namespace
				if($prefix['prefix_is_external']) {
					$prefix['actual_page']=sprintf($prefix['prefix_external_url'],$page);
				}
				// local namespace
				else {
					$prefix['actual_page']=$prefix['prefix'].':'.$page;
				}
			}
			else { // we have an undefined prefix
				$prefix['defined']=false;
			}
		}
		return $prefix;
	}
	
	public function getTemplateName()
	{
		if($this->currenttemplateid) {
			$template=$this->wikiDir.'_prefix_'.$this->currentprefixid.'.html';
		}
		else {
			$template='gwiki_view.html';
		}
		return $template;
	}

	public function getAttachments($page)
	{
	global $xoopsDB;

		$this->attachments=array();
		$q_keyword=$this->escapeForDB($page);
		$sql = "SELECT * FROM ".$xoopsDB->prefix('gwiki_page_files')." WHERE keyword='{$q_keyword}' ";
		$result = $xoopsDB->query($sql);
		$rows=$xoopsDB->getRowsNum($result);
		while($row=$xoopsDB->fetchArray($result)) {
			$row['iconlink']=XOOPS_URL . '/modules/' . $this->wikiDir . '/icons/48px/' . $row['file_icon'] . '.png';
			$row['userlink']=$this->getUserName($row['file_uid']);
			$row['size']=number_format($row['file_size']);
			$row['date']=date($this->dateFormat,$row['file_upload_date']);
			$this->attachments[]=$row;
		}
		return $this->attachments;
	}
	
	/**
	* Make a link from a wiki keyword
	* @param mixed $keyword - wiki page name
	* @param mixed $altkey - alternate text for link. If empty, display_keyword will be used.
	* @access public
	* @since 1.0
	*/
	public function wikiLink($keyword,$altkey=NULL)
	{
		global $xoopsDB;

		// HACK - get rid of spaces in page
		// WikiCreole site is filled with page references such as [[Creole 1.0 Poll]] which resolve as
		// hrefs like http://wikicreole.org/wiki/Creole1.0Poll
		// 
		// will assume this is considered normal wikiish behavior, and try to emulate.
		// Also seems to capitalize each portion, ie 'Ab and Cd' yields 'AbAndCd' - emulate this, too.
		$org_keyword=$keyword;
		if(strpos(trim($keyword),' ')) {
			$keys=explode(' ',$keyword);
			foreach($keys as $i=>$k) $keys[$i]=ucfirst($k);
			$keyword=implode('',$keys);
		}
		// $keyword=str_replace (' ', '', $keyword);

		// check for namespace prefix
		$prefix = $this->getPrefix($keyword);
		if($prefix && $prefix['defined']) {
			$link=$prefix['actual_page'];
			// external namespace
			if($prefix['prefix_is_external']) {
				if($altkey) $linktext = $altkey;
				else $linktext = $org_keyword;
				$linktext=stripslashes($linktext);
				//$linktext=$this->noWikiHold('inline',$linktext);
				$ret='<a href="'.$link.'" target="_blank" title="'._MD_GWIKI_PAGE_EXT_LINK_TT.'">'.$linktext.'<span class="wikiextlink"> </span></a>';
				return $ret;
			}
			// interal namespace
			else {
				$keyword=$link; // we may have modified the keyword
			}
		}

		$sql = "SELECT keyword, display_keyword, title FROM ".$xoopsDB->prefix('gwiki_pages')." WHERE keyword='{$keyword}' and active = 1 ";
		$result = $xoopsDB->query($sql);
		$rows=$xoopsDB->getRowsNum($result);
		if($rows) {	// existing page
			list($keyword, $display_keyword, $title) = $xoopsDB->fetchRow($result);
			$display_keyword=htmlentities($display_keyword,ENT_QUOTES);
			if(empty($display_keyword)) $display_keyword=$org_keyword;
			$keyword=strtolower($keyword);
			$newpage='';
		}
		else {		// new page link
			$display_keyword=$org_keyword;
			$newpage='<span class="wikinewpage"> </span>';
			$title=sprintf(_MD_GWIKI_PAGE_CREATE_TT,$keyword);
		}
		if(!empty($altkey)) $display_keyword=$altkey;
		$title=htmlspecialchars($title);
		$display_keyword=stripslashes($display_keyword);
		//$display_keyword=htmlspecialchars($display_keyword);
		//$display_keyword=$this->noWikiHold('inline',$display_keyword);

		$url=sprintf($this->wikiLinkURL,$keyword);
		return sprintf('<a href="%s" title="%s">%s%s</a>', $url, $title, $display_keyword, $newpage);
	}

	private function getIndexTabs()
	{
		global $xoopsDB ,$wikiPage;

		$related=false;

		$tabs=array();

		$sql  = 'SELECT SUBSTRING(display_keyword,1,1) as letter, count(*) as count ';
		$sql .= ' FROM '.$xoopsDB->prefix('gwiki_pages');
		$sql .= ' WHERE active=1 AND show_in_index=1 ';
		$sql .= ' GROUP BY letter ';
	
		$result = $xoopsDB->query($sql);

		$currentset=false;
		$rows=$xoopsDB->getRowsNum($result);
		if($rows) {
			while($row = $xoopsDB->fetchArray($result)) {
				$row['letter']=strtolower($row['letter']);
				if($this->pageIndexPrefix==$row['letter']) { $row['current']=true; $currentset=true; }
				else $row['current']=false;
				$tabs[]=$row;
			}
		}
		$xoopsDB->freeRecordSet($result);
		
		if(!$currentset) {
			$this->pageIndexPrefix=$tabs[0]['letter'];
			$tabs[0]['current']=true;
		}
		return $tabs;
	}
	
	private function pageIndex($prefix=null)
	{
		global $xoopsDB;
		$simplelayout=false;
		$tablayout=false;
		
		$body = "";
		    
		$pageselect='active=1 AND show_in_index=1 ';
		
		if(!empty($prefix)) $pageselect .= ' AND keyword LIKE "' . $prefix . '%" ';
		else {
			$sql  = 'SELECT count(*) as count  FROM '.$xoopsDB->prefix('gwiki_pages');
			$sql .= ' WHERE '.$pageselect;
			$result = $xoopsDB->query($sql);
			$row = $xoopsDB->fetchArray($result);
			$cnt=$row['count'];
			$xoopsDB->freeRecordSet($result);
			if($cnt>500) {
				$tablayout=true; 
				$simplelayout=true; // tablayout is already grouped by first character
				$tabs=$this->getIndexTabs();
				$pageselect .= ' AND display_keyword LIKE "' . $this->pageIndexPrefix . '%" ';
			}
		}

		$sql = 'SELECT keyword, display_keyword, title';
		$sql.= ' FROM '.$xoopsDB->prefix('gwiki_pages');
		$sql.= ' WHERE '.$pageselect;
		$sql.=' ORDER BY display_keyword ';
//		$sql.=' ORDER BY active, show_in_index, display_keyword ';
    
		$result = $xoopsDB->query($sql);
		$rowcnt = $xoopsDB->getRowsNum($result);

		if($rowcnt<50) $simplelayout=true; // skip the fancy by letter breakout if this is a small index

		if($tablayout) {
			$body.='<div class="wikiindex"><div class="wikiindextabs"><ul>';

			foreach($tabs as $tab) {
				if($tab['current']) $class=' id="wikiindextabactive"';
				else $class='';
				$url=sprintf($this->wikiLinkURL,strtolower($this->keyword.'(\\'.$tab['letter'].')'));
				$letter=strtoupper($tab['letter']);
				$body.="\n<li{$class}><a href=\"{$url}\">{$letter}</a></li>";
			}
			$body.='</ul></div><div class="wikiindexbody">';
		}
    
		$lastletter='';
		if($simplelayout) $body .= '<ul>';
		while ($content = $xoopsDB->fetchArray($result)) {
			$display_keyword=$content['display_keyword'];
			if(empty($display_keyword)) $display_keyword=$content['keyword'];
			if(!$simplelayout) {
				if(function_exists ('mb_substr')) $testletter=mb_strtoupper(mb_substr($display_keyword,0,1,'UTF-8'),'UTF-8');
				else $testletter=strtoupper(substr($display_keyword,0,1));
				if($lastletter=='') {
					$lastletter=$testletter;
					$body.="<h3>{$lastletter}</h3><ul>";
				}
				if($lastletter!=$testletter) {
					$lastletter=$testletter;
					$body.="</ul><h3>{$lastletter}</h3><ul>";
				}
			}
			$title=htmlspecialchars($content['title']);
			$display_keyword=htmlspecialchars($display_keyword);
			$url=sprintf($this->wikiLinkURL,strtolower($content['keyword']));
			$link=sprintf('<a href="%s" title="%s">%s%s</a>', $url, $title, $display_keyword, '');
			$body.='<li>'.$link.' : '.$title.'</li>';
		}
		$xoopsDB->freeRecordSet($result);
		if($tablayout) {
			$body.='</ul></div></div>';
		}
		else if($body!='') $body.='</ul>';
		return $body."\n\n";
	}

	private function recentIndex($prefix=null)
	{
		global $xoopsDB;

		// only show active pages
		$pageselect='active=1  AND show_in_index=1 ';
		if(!empty($prefix)) $pageselect .= ' AND keyword LIKE "' . $prefix . '%" ';
    
		$body = "";

		$sql = 'SELECT keyword, display_keyword, title, lastmodified';
		$sql.= ', FROM_UNIXTIME(lastmodified) as fmtlastmodified, uid';
		$sql.= ' FROM '.$xoopsDB->prefix('gwiki_pages');
		$sql.=' WHERE '.$pageselect;
		$sql.=' ORDER BY lastmodified DESC LIMIT '.$this->numberOfRecentItems;
    
		$result = $xoopsDB->query($sql);
    
		$lastdate='';
		while ($content = $xoopsDB->fetchArray($result)) {
			$testdate=substr($content['fmtlastmodified'],0,10);
			if($lastdate=='') {
				$lastdate=$testdate;
				$body.="<h3>{$lastdate}</h3><ul>";
			}
			if($lastdate!=$testdate) {
				$lastdate=$testdate;
				$body.="</ul><h3>{$lastdate}</h3><ul>";
			}

			$title=htmlspecialchars($content['title']);
			$display_keyword=htmlspecialchars($content['display_keyword']);
			$url=sprintf($this->wikiLinkURL,strtolower($content['keyword']));
			$link=sprintf('<a href="%s" title="%s">%s%s</a>', $url, $title, $display_keyword, '');
			$body.='<li>'.$link.' : '.$title.'</li>';

		}
		$xoopsDB->freeRecordSet($result);
		if($body!='') $body.='</ul>';
		return $body."\n\n";
	}

	private function renderIndex($type,$parms)
	{
		$parms=trim($parms);
		if(strcasecmp($type,'RecentChanges')===0) return $this->recentIndex($parms);
		if(strcasecmp($type,'PageIndex')==0) return $this->pageIndex($parms);
		return false;
	}

	// highlight search terms
	// adapted from: http://stack:overflow.com/questions/2591046/highlight-text-except-html-tags

	private function mon_rplc_callback($capture)
	{
		$haystack=$capture[1];
		$p1=stripos($haystack,$this->highlightArg['needle']);
		$l1=strlen($this->highlightArg['needle']);
		$ret='';
		while($p1!==false) {
			$ret.=substr($haystack,0,$p1).$this->highlightArg['pre'].substr($haystack,$p1,$l1).$this->highlightArg['post'];
			$haystack=substr($haystack,$p1+$l1);
			$p1=stripos($haystack,$this->highlightArg['needle']);
		}
		$ret.=$haystack.$capture[2];

		return $ret;
	}

	private function split_on_tag($needle, $pre, $post, $txt)
	{
		$this->highlightArg = compact('needle', 'pre', 'post');
		return preg_replace_callback('#((?:(?!<[/a-z]).)*)([^>]*>|$)#si', array($this, 'mon_rplc_callback'), $txt);
	}

	public function highlightWords($words)
	{
		$words=str_replace ('  ', ' ', $words);
		$words=explode(' ',$words);
		$body=$this->renderedPage;
		foreach($words as $word) {
			$body=$this->split_on_tag($word, '<span class="wiki_search_term">', '</span>', $body);
		}
		return $body;
	}

	private function noWikiHold($type,$source)
	{

		$this->noWikiIndex += 1;
		switch ($type) {
			case 'block':
				$this->noWikiQueue[$this->noWikiIndex]="<pre>\n{$source}\n</pre>";
				break;
			case 'wcinline':
				$this->noWikiQueue[$this->noWikiIndex]='<span class="wikinoinline">'.$source.'</span>';
				break;
			case 'inline':
			default:
				$this->noWikiQueue[$this->noWikiIndex]=$source;
				break;
		}
		
		$ret="{PdNlNw:{$this->noWikiIndex}}";

		return $ret;
	}

	private function noWikiEmit($index)
	{
		return $this->noWikiQueue[$index];
	}

	private function renderTables($source)
	{
		$rowcnt=0;
		$table="<table class=\"wikitable\">\n";
		$rows=explode("\n",$source);
		foreach($rows as $i => $row) {
			$row=trim($row);
			if(!empty($row)) {
				if($row[0]=='|') $row=substr($row,1);
				if(substr($row,-1)=='|') $row=substr($row,0,-1);
				$cols=explode('|',$row);
				$table.='<tr'.(($rowcnt % 2)?' class="even"':' class="odd"').'>';
				++$rowcnt;
				foreach($cols as $col) {
					if(empty($col)) $table .= '<td>&nbsp;</td>';
					elseif($col[0]=='=') $table .= '<th>'.substr($col,1).'</th>';
					elseif($col[0]=='>') $table .= '<td class="right">'.substr($col,1).'</td>';
					elseif($col[0]=='+') $table .= '<td class="center">'.substr($col,1).'</td>';
					elseif(substr($col,0,4)=='&lt;') $table .= '<td class="left">'.substr($col,4).'</td>';
					elseif(preg_match('/^\s*[0-9.$+\-]+\s*$/',$col)) {
						if(floatval(preg_replace("/[^-0-9\.]/","",$col))<0) $class='number negative';
						else $class="number";
						$table .= '<td class="'.$class.'">'.trim($col).'</td>';
						}
					else $table .= '<td>'.$col.'</td>';
				}
				$table.="</tr>\n";
			}
		}
		$table .= "</table>\n";
		return $table;
	}

	private function renderLink($source)
	{
		$source=trim($source);
		$pos=strpos($source,'|');
		//if($pos===false) $pos=strpos($source,' ');
		if($pos===false) { // no delimter - whole thing is the link
			$link=$source;
			$linktext='';
		}
		else {
			$link=trim(substr($source,0,$pos));
			$linktext=trim(substr($source,$pos+1));
		}

		if(preg_match('/^([A-Za-z\x80-\xff0-9.:\- ]){2,}$/',$link)) { 
			//$link=str_replace (' ', '', $link);
			if(empty($linktext)) $ret=$this->wikiLink($link);
//			else $ret=$this->wikiLink($link,$linktext);
			else $ret=$this->wikiLink($link,stripslashes($linktext));
		}
		else {
			$ext=true;
			if (strncasecmp($link,XOOPS_URL,strlen(XOOPS_URL))==0) $ext=false; // matches our site
			if (strcasecmp('siteurl:', substr($link,0,8)) == 0) { // explicit reference to our site
				$link=XOOPS_URL.substr($link,8);
				$ext=false;
			}
			if(strpos($link,':')===false) $ext=false; // no protocol, assume relative url
			if($linktext=='') $linktext=$link;
			$linktext=stripslashes($linktext);
			// $linktext=$this->noWikiHold('inline',stripslashes($linktext));
			if($ext)
				$ret='<a href="'.$link.'" target="_blank" title="'._MD_GWIKI_PAGE_EXT_LINK_TT.'">'.$linktext.'<span class="wikiextlink"> </span></a>';
			else
				$ret="<a href=\"{$link}\" title=\"{$linktext}\">{$linktext}</a>";
		}

		return $ret;
	}

	private function renderHeader($source,$level)
	{
		$level=strlen($level)+1;
		$this->tocQueue[$this->tocIndex] = array('level'=>$level, 'name'=>$source);
		$toc="\n<h".$level.' id="'.$this->tocIdPrefix.$this->tocIndex.'" >'.$source.'</h'.$level.">\n";
		$this->tocIndex += 1;
		return $toc; 
	}

	private function renderIndent($source,$level)
	{
		$level=strlen($level);
		$ret="\n<div class=\"wikiindent{$level}\">\n{$source}\n</div>";
		return $ret; 
	}

	private function renderToc()
	{
		$tocq=$this->tocQueue;
		$toc='';
		foreach($tocq as $i=>$v) {
			$toc.='<li class="wikitoclevel' . $v['level'] . '"><a href="'.sprintf($this->tocAnchorFmt,$this->tocIdPrefix.$i).'">'.strip_tags($v['name']).'</a></li>';
		}
		if(!empty($toc)) {
			$toc='<div class="wikitoc"><div class="wikitocheader">'._MD_GWIKI_TOC.'</div><ul class="wikitoclist">'.$toc.'</ul></div>';
		}
		return $toc; 
	}

	public function fetchPageSetToc(&$page)
	{
		global $xoopsDB;
		$toc=false;

		$q_page=$this->escapeForDB($page);

		$sql  = 'SELECT gwiki_id, keyword, display_keyword, page_set_home, page_set_order, toc_cache ';
		$sql .= ' FROM '.$xoopsDB->prefix('gwiki_pages');
		$sql .= " WHERE active=1 and keyword='{$q_page}' ";
	
		$result = $xoopsDB->query($sql);
    
		$rows=$xoopsDB->getRowsNum($result);
		if($rows) {
			$row = $xoopsDB->fetchArray($result);
			if(!empty($row['page_set_home'])) {
				$page=$row['page_set_home']; // this is passed back up to caller!
				$q_page=$this->escapeForDB($row['page_set_home']);
				$xoopsDB->freeRecordSet($result);
				$sql  = 'SELECT gwiki_id, keyword, display_keyword, page_set_home, page_set_order, toc_cache ';
				$sql .= ' FROM '.$xoopsDB->prefix('gwiki_pages');
				$sql .= " WHERE active=1 and page_set_home='{$q_page}' ";
				$sql .= " ORDER BY page_set_order, keyword ";
	
				$result = $xoopsDB->query($sql);
				while($row = $xoopsDB->fetchArray($result)) {
					$row['display_keyword']=strip_tags($row['display_keyword']);
					if(!empty($row['toc_cache'])) {
						$tmp=unserialize($row['toc_cache']);
						foreach($tmp as $i=>$v) {
							$tmp[$i]['name']=strip_tags($tmp[$i]['name']);
						}
						$row['toc']=$tmp;
					
						$toc[]=$row;
					}
				}
    
			}
		}
		$xoopsDB->freeRecordSet($result);
		return($toc);
	}

	public function renderPageSetToc(&$page,$level,$tocclass='wikitocpage')
	{
		$toc=$this->fetchPageSetToc($page);
		if(!$toc) return false;
		$tocout='';
		foreach($toc as $ti=>$tv) {
			//$link=sprintf($this->getWikiLinkURL(),$tv['keyword']);
			foreach($tv['toc'] as $i=>$v) {
				if(intval($v['level']) <= $level) {
					$tocout .= '<li class="wikitoclevel' . $v['level'] . '"><a href="' . sprintf($this->getWikiLinkURL(),$tv['keyword'].sprintf($this->tocAnchorFmt,$this->tocIdPrefix.$i)).'">'.$v['name'].'</a></li>';
				}
			}
		}
		if(!empty($tocout)) {
			$tocout='<div class="'.$tocclass.'"><ul class="wikitoclist">'.$tocout.'</ul></div>';
		}
		return($tocout);
	}

	public function renderPageSetNav($page)
	{
		$sethome=$page;
		$toc=$this->fetchPageSetToc($sethome); // this will set home
		if(!$toc) return false;
		$home = -1; $current = -1; $prev = -1; $next= -1;
		foreach($toc as $i=>$v) {
			if(strcasecmp($toc[$i]['keyword'],$page)===0) $current=$i; 
			if(strcasecmp($toc[$i]['keyword'],$sethome)===0) $home=$i; 
		}

		if($current>-1) { $prev=$current-1; $next=$current+1; }

		$first=0;
		$last=count($toc)-1;

		// should these wrap instead?
		if($next>$last) $next = $last;
		if($prev<0) $prev = 0;
		if($home<0) $home = 0;
		
		$pageset['first']=array('link' => sprintf($this->getWikiLinkURL(),$toc[$first]['keyword']),
								'text' => htmlentities($toc[$first]['display_keyword'],ENT_QUOTES),
								'desc' => _MD_GWIKI_PAGENAV_FIRST);

		$pageset['prev']=array(	'link' => sprintf($this->getWikiLinkURL(),$toc[$prev]['keyword']),
								'text' => htmlentities($toc[$prev]['display_keyword'],ENT_QUOTES),
								'desc' => _MD_GWIKI_PAGENAV_PREV);

		$pageset['home']=array( 'link' => sprintf($this->getWikiLinkURL(),$toc[$home]['keyword']),
								'text' => htmlentities($toc[$home]['display_keyword'],ENT_QUOTES),
								'desc' => _MD_GWIKI_PAGENAV_TOP);

		$pageset['next']=array( 'link' => sprintf($this->getWikiLinkURL(),$toc[$next]['keyword']),
								'text' => htmlentities($toc[$next]['display_keyword'],ENT_QUOTES),
								'desc' => _MD_GWIKI_PAGENAV_NEXT);

		$pageset['last']=array( 'link' => sprintf($this->getWikiLinkURL(),$toc[$last]['keyword']),
								'text' => htmlentities($toc[$last]['display_keyword'],ENT_QUOTES),
								'desc' => _MD_GWIKI_PAGENAV_LAST);

		return($pageset);
	}

	public function getImageLib($keyword)
	{
		global $xoopsDB;
		
		$lib=$this->imageLib;
		array_unshift($lib,$keyword);
		
		return array_unique($lib);;
	}

	public function getPageImage($keyword,$name)
	{
		global $xoopsDB;
		
		if(strncasecmp($name,'http://',7)==0 || strncasecmp($name,'https://',8)==0 ) return false;
		$lib=$this->imageLib;
		array_unshift($lib,$keyword);
		foreach ($lib as $page) {
			$sql  = 'SELECT * FROM '.$xoopsDB->prefix('gwiki_page_images').' WHERE keyword=\''.$this->escapeForDB($page).'\' ';
			$sql .= ' AND image_name=\''.$this->escapeForDB($name).'\' ';
			$result = $xoopsDB->query($sql);
			if ($image = $xoopsDB->fetchArray($result)) {
				return $image;
			}
		}
		return false;
		
		// return array includes:
		//   image_id
		//   keyword
		//   image_name
		//   image_alt_text
		//   image_file
	}

	private function renderImage($source)
	{
		$source=trim($source);
		$pos=strpos($source,'|');
		//if($pos===false) $pos=strpos($source,' ');
		if($pos===false) { // no delimter - whole thing is the image url
			$link=$source;
			$parms=array();
		}
		else {
			$link=trim(substr($source,0,$pos));
			$parms=explode('|',trim(substr($source,$pos+1)));
			foreach($parms as $i => $parm) {
				$parms[$i]=trim($parm);
			}
		}
		if (strcasecmp('siteurl:', substr($link,0,8)) == 0) { // explicit reference to our site
			$link=XOOPS_URL.substr($link,8);
		}
		$showthumb=false;
		$showthumblink=false;
		if (strcasecmp('thumb:', substr($link,0,6)) == 0) { // explicit request for thumbnail, links to full image
			$revertlink=$link;
			$link=substr($link,6);
			$showthumblink=true;
		}
		$alttext  = empty($parms[0])?'':$parms[0];
		$align    = empty($parms[1])?'':$parms[1];
		$maxpx    = empty($parms[2])?'':intval($parms[2]);
		
		// align must be left, right, center or empty
		if     (strcasecmp($align,'left'  )===0) $align='left';
		elseif (strcasecmp($align,'right' )===0) $align='right';
		elseif (strcasecmp($align,'center')===0) $align='center';
		else $align='';
		
		$alignparm='';
		if($align=='left' || $align=='right' || $align=='center') $alignparm=', '.$align;
		
		// look up link in page_images table, if found use that, otherwise just pass on link as is
		$image=$this->getPageImage($this->keyword,$link);
		if($image) {
			// image array includes:
			//   image_id
			//   keyword
			//   image_name
			//   image_alt_text
			//   image_file
			//   use_to_represent
			$link = XOOPS_URL . '/uploads/' . $this->wikiDir . '/' . $image['image_file'];
			if(empty($alttext)) $alttext = $image['image_alt_text'];
		}
		else {
			// thumbs don't apply, so put everything back the way it was
			if($showthumblink) { $link=$revertlink; $showthumblink=false; }
		}

		$alt='';
//		$alttext=htmlspecialchars($alttext);
		if(!empty($alttext)) $alt=" alt=\"{$alttext}\"  title=\"{$alttext}\" ";
		
		$maxpxstyle='';
		if(!empty($maxpx)) {
			$maxpxstyle=" style=\"max-width:{$maxpx}px; max-height:{$maxpx}px; width:auto; height:auto;\" ";
			$showthumb=true; // trigger automatic thumbnail use
		}
		
		if($showthumb) {
			$thumbsize=$this->defaultThumbSize;
			if(!empty($maxpx)) $thumbsize=$maxpx;
			$link=XOOPS_URL.'/modules/'.$this->wikiDir.'/getthumb.php?page='.$image['keyword'].'&name='.$image['image_name'].'&size='.$thumbsize;
		}
		
		if($showthumblink) {
			$thumbsize=$this->defaultThumbSize;
			if(!empty($maxpx)) $thumbsize=$maxpx;
			$thumb=XOOPS_URL.'/modules/'.$this->wikiDir.'/getthumb.php?page='.$image['keyword'].'&name='.$image['image_name'].'&size='.$thumbsize;
			$img=XOOPS_URL . '/uploads/' . $this->wikiDir . '/' . $image['image_file'];
			$ret.='<a href="'.$img.'" '.$alt.'><img src="'.$thumb.'"'.$alt.$maxpxstyle.'/></a>';
		}
		else {
			$ret="<img class=\"wikiimage{$alignparm}\" src=\"{$link}\" {$alt}{$maxpxstyle} />"; 
		}

		if($align=='center') $ret='<div style="margin: 0 auto; text-align: center;">'.$ret.'</div>';

		return $ret;
	}

	private function renderGallery($source)
	{
		global $xoopsDB;

		$maxpx=intval(trim($source));
		if($maxpx<10) $maxpx=$this->defaultThumbSize;
		$page=$this->keyword;
		
		$sql = 'SELECT * FROM '.$xoopsDB->prefix('gwiki_page_images') .
			' WHERE keyword = \''.$page.'\' '.
			' ORDER BY image_name ';
		$result = $xoopsDB->query($sql);

		$dir=$this->wikiDir;
		$body='<div class="wikigallery"><ul class="wikigalleryimg">';
	
		for ($i = 0; $i < $xoopsDB->getRowsNum($result); $i++) {
			$image = $xoopsDB->fetchArray($result);
			$img=XOOPS_URL . '/uploads/' . $dir . '/' . $image['image_file'];
			$thumb=XOOPS_URL . '/modules/' . $dir . '/getthumb.php?page='.$image['keyword'].'&name='.$image['image_name'].'&size='.$maxpx;
			$alt=htmlentities($image['image_alt_text'],ENT_QUOTES);
			$name=htmlentities($image['image_name'],ENT_QUOTES);
			if(empty($alt)) $alt=$name;
			$body.='<li><a href="'.$img.'" title="'.$name.'"><img src="'.$thumb.'" alt="'.$alt.'" title="'.$alt.'" /></a></li>'."\n";
		}
		
		$body.='</ul><br style="clear:both;" /></div>';
		
		return $body;
	}

	private function renderLists($source)
	{
		$lines=explode("\n",$source);
		$last='';
		foreach($lines as $i => $line) {
			$line=ltrim($line);
			if(!empty($line)) {
				$list='';
				$p = strpos($line,' ');
				$current=substr($line,0,$p);
				$x=0;
				while (!empty($last[$x]) && !empty($current[$x]) && $last[$x]==$current[$x]) ++$x;
				// $x is where the last and current list prefixes differ
				// close anything from $x to end in last
				$close=strrev(substr($last,$x));
				$y=0;
				while(!empty($close[$y])) {
					if($close[$y]=='*') $list.='</li></ul>'; //.($x>0?'</li>':'');
					if($close[$y]=='#') $list.='</li></ol>'; //.($x>0?'</li>':'');
					++$y;
				}
				// open anything from $x to end in 
				$open=substr($current,$x);
				$y=0;
				while(!empty($open[$y])) {
					if($open[$y]=='*') $list.='<ul class="wikiulist">';
					if($open[$y]=='#') $list.='<ol class="wikiolist">';
					++$y;
				}
				$endli=($last==$current)?'</li>':'';
				$last=$current;
				$lines[$i]=$list.$endli."\n<li> ".substr($line,$p+1);
			}
		}

		// put list back together
		$list="\n";
		foreach($lines as $line) {
			if(!empty($line)) $list.=$line;
		}
		// close anything left open
		$close=strrev($last);
		$y=0;
		while(!empty($close[$y])) {
			if($close[$y]=='*') $list.="</li></ul>\n";
			if($close[$y]=='#') $list.="</li></ol>\n";
			++$y;
		}

		return $list;
	}
	
	private function renderRef($refinfo,$source)
	{
		$first_ref=false;
		$refs=explode('|',trim($refinfo).'|||');
		$rq['id']=$refs[0];
		$rq['first']=$refs[1];
		$rq['repeat']=$refs[2];
		$rq['source']=$source;
		$refid=(-1);
		if(!empty($rq['id'])) {
			foreach($this->refQueue as $i=>$v) {
				if($v['id']==$rq['id']) $refid=$i;
			}
		}
		if($refid==(-1)) {		
			$refid=$this->refIndex;
			$first_ref=true;
			$this->refQueue[$this->refIndex] = $rq;
			$this->refIndex += 1;
		}
		$paren_ref=false;
		if(!empty($this->refQueue[$refid]['first'])) $paren_ref=true;
		if($paren_ref) {
			$ref_text=$this->refQueue[$refid]['first'];
			if(!$first_ref) {
				if(!empty($this->refQueue[$refid]['repeat'])) {
					$ref_text=$this->refQueue[$refid]['repeat'];
				}
			}
			$r='<span class="wikiparenref"><a href="#ref'.$refid.'">('.$ref_text.')</a></span>';
		} else {
			$r='<span class="wikinumref"><a href="#ref'.$refid.'">'.($refid+1).'</a></span>';			
		}
		return $r;
	}
	
	private function renderRefList()
	{
		$this->refShown=true;
		$r='<div class="wikicitelist">';

		foreach($this->refQueue as $i=>$v) {
			$refid=$i;
			if(empty($v['first'])) {
				$r .= '<div class="wikicitenumitem" id="ref'.$refid.'"><span class="wikicitenum">'.($refid+1).'. </span>'.$v['source']."</div>\n";
			}
			else {
				$r .= '<div class="wikiciteparenitem" id="ref'.$refid.'">'.$v['source']."</div>\n";
			}
		}

		$r .= '</div>';

		return $r;
	}
	
	private function renderBox($type,$title,$body)
	{
		// make sure we have a valid type
		$type=strtolower($type);
		if(!($type=='code' || $type=='info' || $type=='info' || $type=='note' || $type=='tip' || $type=='warn' || $type=='folded' )) $type='info';

		// $title may include options ( title | align ) :
		//   align: adds additonal class 'left' or 'right' to box so css can alter float, size, etc.
		$title=trim($title);
		$eclass=''; $ejs=''; $etooltip='';
		$pos=strpos($title,'|');
		if($pos===false) { // no delimter - whole thing is the title
			// $title=$title;
			$parms=array();
		}
		else {
			//$title=trim(substr($title,0,$pos));
//			$parms=explode('|',trim(substr($title,$pos+1)));
			$parms=explode('|',$title);
			$title=$parms[0];
			if(!empty($parms[1]) && ($parms[1]=='left' || $parms[1]=='right')) $eclass=' '.$parms[1];
		}
		if($type=='folded') {
			$foldclass='wikifolded'.$eclass;
			$unfoldclass='wikiunfolded'.$eclass;
			$ejs=' onclick="var c=this.className; if(c==\''.$foldclass.'\') this.className=\''.$unfoldclass.'\'; else this.className=\''.$foldclass.'\';"';
			$etooltip='<span>'._MD_GWIKI_FOLDED_TT.'</span>';
		}

		$ret = '<div class="wiki'.$type.$eclass.'"'.$ejs.'><div class="wiki'.$type.'icon"></div><div class="wiki'.$type.'title">'.$title.$etooltip.'</div><div class="wiki'.$type.'inner">'.$body.'<br clear="all" /></div></div>'."\n\n";
		return $ret;
	}
	
	private function convertEntities($body)
	{
		// convert some entites
		$sym=array();		$ent=array();
		$sym[]='{cent}'; 	$ent[]='&cent;';
		$sym[]='{pound}'; 	$ent[]='&pound;';
		$sym[]='{yen}'; 	$ent[]='&yen;';
		$sym[]='{euro}'; 	$ent[]='&euro;';
		$sym[]='{c}'; 		$ent[]='&copy;';
		$sym[]='(c)'; 		$ent[]='&copy;'; // too common to ignore
		$sym[]='{r}'; 		$ent[]='&reg;';
		$sym[]='(r)'; 		$ent[]='&reg;';
		$sym[]='{tm}'; 		$ent[]='&trade;';
		$sym[]='(tm)'; 		$ent[]='&trade;';
		$sym[]='{sm}';		$ent[]='<span style="font-size: 50%; vertical-align: super;">SM</span>'; // very poor font support for unicode code point for service mark
		$sym[]='{nbsp}';	$ent[]='&nbsp;';

		$body=str_ireplace ($sym , $ent, $body);
		return $body;
	}

	public function renderTeaser($body=NULL,$title=NULL)
	{
		// chop body at more tag if it is set
		// TODO - need a fallback if no {more} tag and length is above some threshold
		if(empty($body)) $body=$this->body;
		$pos=stripos($body,'{more}');
		if($pos!==false) {
			$body=substr($body,0,$pos);
			$url=sprintf($this->wikiLinkURL,$this->keyword);
		}
		$body=str_ireplace ('{toc}', '', $body);
		$body=$this->renderPage($body,$title);
		if($pos!==false) $body.='<a href="'.$url.'#more"><span class="wikimore">'._MD_GWIKI_MORE.'</span></a>';
		return $body;
	}

	public function renderBlockquote($source)
	{
		$src = str_replace("\n", ' ', preg_replace("#^> #m", "", $source));
		return '<blockquote class=\"wikiquote\">'.$src."</blockquote>\n";
	}

	public function renderPreformat($source)
	{
		$src = preg_replace("#^. #m", "", $source);
		return '<pre>'.$src."</pre>\n";
	}
	
	public function renderPage($body=NULL,$title=NULL)
	{
		if(empty($body)) $body=$this->body;
		$this->renderedPage='';
		$this->noWikiQueue=array();
		$this->noWikiIndex=0;
		$this->refShown=false;
		
		if(empty($title)) $title=$this->title;
		$this->renderHeader($title,''); // do first because title should always be #toc0 - set in template
		
		$search = array();
		$replace = array();

		// eliminate double line endings
		$search[]  = "#\r\n?#";
		$replace[] = "\n";

		// neuter html tags
		$search[]  = "#<#";
		$replace[] = "&lt;";

		// neuter single quotes
		$search[]  = "#'#";
		$replace[] = "\\'";

		// nowiki - tilde escape
		$search[]  = "#~([^ \t\r\n\v\f])#Ue";
		$replace[] = '$this->noWikiHold("inline",\'\\1\')';
		
		// nowiki content gwiki style
		$search[]  = "#{nowiki}(.*){endnowiki}#Umsie";
		$replace[] = '$this->noWikiHold("inline",\'\\1\')';

		// nowiki content block creole style (a nowiki that forces a style, how odd.)
		$search[]  = "#^{{{\n(.*)^}}}\n#Umsie";
		$replace[] = '$this->noWikiHold("block",\'\\1\')';
		
		// nowiki content inline creole style
		$search[]  = "#{{{(.*)}}}#Ue";
		$replace[] = '$this->noWikiHold("wcinline",\'\\1\')';
		
		// automatically nowiki content of code box - {code title}xxx{endcode}
		$search[]  = "#({code [^\"<\n]+?})(.*?)({endcode})#sie";
		$replace[] = '\'\\1\'.$this->noWikiHold("inline",\'\\2\').\'\\3\'';

		// center ++ xxx
		$search[]  = "#^(\+{2})(.*)(?=\n\n|\Z)#Usm";
		$replace[] = "<center class=\"wikicenter\">\n\\2\n</center>\n";

		// : indent up to 5 levels
		$search[]  = "#^(\:{1,5})\s(.*)(?=\n\n|\Z)#Usme";
		$replace[] = '$this->renderIndent(\'\\2\',\'\\1\')';

		// lists
		$search[]  = "#^( *[*\#]{1,} (.*)\n)+#me";
		$replace[] = '$this->renderLists(\'\\0\')';
		
		// bold **xxx**
		$search[]  = "#\*{2}(.*?)(\*{2}|(?=\n\n))#s";
		$replace[] = "<strong class=\"wikistrong\">\\1</strong>";

		// italic //xxx//
		$search[]  = "#(?<![:])/{2}(.*?[^:])(/{2}|(?=\n\n))#s";
		$replace[] = "<em class=\"wikiem\">\\1</em>";

		// horizontal rule ---- (not an empty strikethru; creole says 4 or more so this needs to go first)
		$search[]  = "#^-{4,}$#m";
		$replace[] = "\n<hr  class=\"wikihr\"/>\n";

		// strikethru --xxx-- (this does NOT cross lines, as '--' is a common typographic convention
		$search[]  = "#-{2}(.*?)(-{2})#";
		$replace[] = "<del class=\"wikidel\">\\1</del>";

		// underline __xxx__
		$search[]  = "#_{2}(.*?)(_{2}|(?=\n\n))#s";
		$replace[] = "<span class=\"wikiu\">\\1</span>";

		// superscript ^^xxx^^
		$search[]  = "#\^{2}(.*?)(\^{2}|(?=\n\n))#s";
		$replace[] = "<sup class=\"wikisup\">\\1</sup>";

		// subscript ,,xxx,,
		$search[]  = "#,{2}(.*?)(,{2}|(?=\n\n))#s";
		$replace[] = "<sub class=\"wikisub\">\\1</sub>";

		// monospace ##xxx##
		$search[]  = "#\#{2}(.*?)(\#{2}|(?=\n\n))#s";
		$replace[] = "<span class=\"wikitt\">\\1</span>";

		// color !!color:xxx!!
		$search[]  = "#!{2}(\#{0,1}[0-9A-Za-z]*):(.*?)(!{2}|(?=\n\n))#s";
		$replace[] = "<span style=\"color:\\1;\">\\2</span>";

		// color !!color,background:xxx!!
		$search[]  = "#!{2}(\#{0,1}[0-9A-Za-z]*),(\#{0,1}[0-9A-Za-z]*):(.*?)(!{2}|(?=\n\n))#s";
		$replace[] = "<span style=\"color:\\1; background-color:\\2;\">\\3</span>";

		// forced line break creole style \\, just a bare break tag
		$search[]  = "#(\\\{2})#i";
		$replace[] = '<br />';

		// forced line break blog [[br]] or gwiki {break} styles, themed - by default clear all
		$search[]  = "#(\[\[BR\]\]|{break})#i";
		$replace[] = '<br class="wikibreak" />';

		// image {{image url|alt text|align|max width in pixels}}
		$search[]  = "#\{{2}(.*)\}{2}#Ume";
		$replace[] = '$this->renderImage(\'\\1\')';

		// info box {info title}xxx{endinfo}
		$search[]  = "#{info ([^\"<\n]+?)?}(.*?){endinfo}#sie";
		$replace[] = '$this->renderBox(\'info\',\'\\1\',\'\\2\')';

		// note box {note title}xxx{endnote}
		$search[]  = "#{note ([^\"<\n]+?)?}(.*?){endnote}#sie";
		$replace[] = '$this->renderBox(\'note\',\'\\1\',\'\\2\')';

		// tip box {tip title}xxx{endtip}
		$search[]  = "#{tip ([^\"<\n]+?)?}(.*?){endtip}#sie";
		$replace[] = '$this->renderBox(\'tip\',\'\\1\',\'\\2\')';

		// warning box {warning title}xxx{endwarning}
		$search[]  = "#{warning ([^\"<\n]+?)?}(.*?){endwarning}#sie";
		$replace[] = '$this->renderBox(\'warn\',\'\\1\',\'\\2\')';

		// code (preformatted) box {code title}xxx{endcode}
		$search[]  = "#{code ([^\"<\n]+?)?}(.*?){endcode}#sie";
		$replace[] = '$this->renderBox(\'code\',\'\\1\',\'<pre>\\2</pre>\')';

		// folded box {folded title}xxx{endfolded}
		$search[]  = "#{folded ([^\"<\n]+?)?}(.*?){endfolded}#sie";
		$replace[] = '$this->renderBox(\'folded\',\'\\1\',\'\\2\')';

		// urls - smells like a link
//		$search[]  = "#(?<![\"\[])((http|https|ftp|ftps)://.{2,}\..*)([,.?!:;]?\s)#Uie";
		$search[]  = "#(?<=\s)((http|https|ftp|ftps)://.{2,}\..*)(?=[,.?!:;]{0,1}\s)#Uie";
		$replace[] = '$this->renderLink(\'\\1\')';

		// link [[link|linktext]]
		$search[]  = "#\[{2}(.*)\]{2}#Ume";
		$replace[] = '$this->renderLink(\'\\1\')';

		// email xxx@example.com
		$search[]="#(?<=\s)([A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4})(?=\s)#i";
//		$search[]  = "#([\w.-]+@[\w.-]+)(?![\w.]*(\">|<))#";
		$replace[] = '<a href="mailto:\\1">\\1</a>';

		// CamelCase wiki link
		//                "#^([A-Z][a-z\:]+){2,}\d*$#" - Could be between whitespace on either end or between > on start and/or < on end
		if($this->useCamelCase) {
			$search[]  = "#(?<=\s|>)"._WIKI_CAMELCASE_REGEX."(?=\s|</l|</t)#e";
			$replace[] = '$this->wikiLink(\'\\1\')';
		}

		// =====headings up to 5 levels
		$search[]  = "#(^\s{0,})(={1,5})([^=].*[^=])(={0,5})\s*$#Ume";
		$replace[] = '$this->renderHeader(\'\\3\',\'\\2\')';
		//$replace[] = "\n<h6>\\2</h6>\n";

		// blockquote > xxx
		$search[]  = "#^(> .*\n)+#me";
		$replace[] = '$this->renderBlockquote(\'\\0\')';

		// preformated  .xxx
		$search[]  = "#^(\. .*\n)+#me";
		$replace[] = '$this->renderPreformat(\'\\0\')';

		// reference {ref id|first-ref}source{endref}
		$search[]  = "#{ref( [^\"<\n]+?)?}(.*?){endref}#sie";
		$replace[] = '$this->renderRef(\'\\1\',\'\\2\')';
		
		// forced line break blog [[br]] or gwiki {break} styles, themed - by default clear all
		$search[]  = "#({reflist})#ie";
		$replace[] = '$this->renderRefList()';


		// index or change list {pageindex prefix}
		$search[]  = "#{(PageIndex|RecentChanges)([^\"<\n]+?)?}#sie";
		$replace[] = '$this->renderIndex(\'\\1\',\'\\2\')';

		// table of contents
		$search[]  = "#\{toc\}#ie";
		$replace[] = '$this->renderToc()';
		
		// page set table of contents
		$search[]  = "#\{pagesettoc\}#ie";
		$replace[] = '$this->renderPageSetToc($this->keyword,6)';

		// image gallery {gallery size}
		$search[]  = "#{gallery([^\"<\n]+?)?}#sie";
		$replace[] = '$this->renderGallery(\'\\1\')';

		// page set navigation
		//$search[]  = "#\{pagesetnav\}#ie";
		//$replace[] = '$this->renderPageSetNav($this->keyword)';

		// more anchor - indicates end of teaser/summary
		$search[]  = "#\{more\}#i";
		$replace[] = '<span id="more"></span>';
		
		// tables
		$search[]  = "#^( *\|((.*)\|){1,}\s*\n)+#me";
		$replace[] = '$this->renderTables(\'\\0\')';
		
		// paragraph on 2 consecutive newlines
		$search[]  = "#\n{2}#";
		$replace[] = "\n<p>";
		
		// restore cached nowiki content, all styles (if you need to use {PdNlNw:#} in your page, put it in a nowiki tag) 
		$search[] = "#{PdNlNw:([0-9]{1,})}#e";
		$replace[] = '$this->noWikiEmit(\'\\1\')';

		$body=preg_replace($search, $replace, trim($body)."\n");
		if($this->refShown==false && $this->refIndex>0) $body.=$this->renderRefList();
		$body=stripslashes($this->convertEntities($body));

		$this->renderedPage=$body;
		
		return $this->renderedPage;
	}


}

?>
