<?php
/**
* sortpageset.php - change order of pages within a page set
*
* @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
* @license    gwiki/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/
include "header.php";
global $xoTheme, $xoopsTpl;
global $wikiPage;
$GLOBALS['xoopsOption']['template_main'] = 'gwiki_view.tpl';
include(XOOPS_ROOT_PATH.'/header.php');
$currentscript=basename( __FILE__ ) ;
include_once XOOPS_ROOT_PATH.'/class/xoopsformloader.php';

$page_set_home='';
$display_keyword='';

function fetchPageSet($page)
{
    global $xoopsDB, $wikiPage, $page_set_home, $display_keyword;

    $q_page=$wikiPage->escapeForDB($page);

    $pageset=false;

    $sql  = 'SELECT gwiki_id, keyword, display_keyword, page_set_home, page_set_order';
    $sql .= ' FROM '.$xoopsDB->prefix('gwiki_pages');
    $sql .= " WHERE active=1 and keyword='{$q_page}' ";

    $result = $xoopsDB->query($sql);

    $rows=$xoopsDB->getRowsNum($result);
    if ($rows) {
        $row = $xoopsDB->fetchArray($result);
        if (!empty($row['page_set_home'])) {
            $page=$row['page_set_home']; // this is passed back up to caller!
            $page_set_home=$row['page_set_home'];
            $display_keyword=$row['display_keyword'];
            $q_page=$wikiPage->escapeForDB($row['page_set_home']);
            $xoopsDB->freeRecordSet($result);
            $sql  = 'SELECT gwiki_id, keyword, display_keyword, page_set_home, page_set_order ';
            $sql .= ' FROM '.$xoopsDB->prefix('gwiki_pages');
            $sql .= " WHERE active=1 and page_set_home='{$q_page}' ";
            $sql .= " ORDER BY page_set_order, keyword ";

            $result = $xoopsDB->query($sql);
            while ($row = $xoopsDB->fetchArray($result)) {
                $row['display_keyword']=strip_tags($row['display_keyword']);
                if ($row['page_set_home']==$row['keyword']) {
                    $display_keyword=$row['display_keyword'];
                }
                $pageset[($row['gwiki_id'].'')]=$row;
            }

        }
    }
    $xoopsDB->freeRecordSet($result);

    return($pageset);
}

// $_GET variables we use
$page='';
if(isset($_POST['page'])) $page=cleaner($_POST['page']);
elseif (isset($_GET['page'])) $page=cleaner($_GET['page']);
$page = $wikiPage->normalizeKeyword((!empty($page))?$page:$wikiPage->wikiHomePage);

$selectalert=_MD_GWIKI_SORT_PAGE_SELECT;
$sortelement='sortelement';
$sort_js = <<<ENDJSCODE
function move(f,bDir) {
  var el = f.elements["$sortelement"]
  var idx = el.selectedIndex
  if (idx==-1)
    alert("$selectalert")
  else {
    var nxidx = idx+( bDir? -1 : 1)
    if (nxidx<0) return; // nxidx=el.length-1
    if (nxidx>=el.length) return; // nxidx=0
    var oldVal = el[idx].value
    var oldText = el[idx].text
    el[idx].value = el[nxidx].value
    el[idx].text = el[nxidx].text
    el[nxidx].value = oldVal
    el[nxidx].text = oldText
    el.selectedIndex = nxidx
  }
}

function reverseorder(f) {
  var el = f.elements["$sortelement"];
  var b = 0;
  var t = el.length;
  t = t-1;
  while (b<t) {
    var oldVal = el[t].value;
    var oldText = el[t].text;
    el[t].value = el[b].value;
    el[t].text = el[b].text;
    el[b].value = oldVal;
    el[b].text = oldText;
    b = b+1;
    t = t-1;
  }
}

function processForm(f) {
  for (var i=0;i<f.length;i++) {
    var el = f[i]
    // If reorder listbox, then generate value for hidden field
    if (el.name=="$sortelement") {
      var strIDs = ""
      for (var j=0;j<f[i].options.length;j++)
        strIDs += f[i].options[j].value + ","
        f.elements['neworder'].value = strIDs.substring(0,strIDs.length-1)
    }
  }
}
ENDJSCODE;

$xoTheme->addScript( null, array( 'type' => 'text/javascript' ), $sort_js );

$pageX = $wikiPage->getPage($page);
if(!$pageX) redirect_header("index.php?page=$page", 3, _MD_GWIKI_PAGENOTFOUND);

// leave if we don't have admin authority
$mayEdit = $wikiPage->checkEdit();
if (!$mayEdit) {
    redirect_header("index.php?page=$page", 3, _NOPERM);
}

$pageset=$page;
$pages=fetchPageSet($pageset);

// leave if there is nothing to sort
if ($pages===false || count($pages)<2) {
    redirect_header("index.php?page=$page", 3, _MD_GWIKI_SORT_EMPTY);
}

$op='display';
if (isset($_POST['submit'])) {
    $op='update';
}

if ($op=='update') {
    if (isset($_POST['neworder'])) {
        $neworder=array();
        $neworder=explode(',',$_POST['neworder']);
    }
    else $op='display';
}

if ($op=='update') {
    foreach ($neworder as $i => $p) {
        if (isset($pages[$p])) {
            $pages[$p]['page_set_order'] = $i+1;
        }
        else $op='display';
    }
}

if ($op=='update') {
    $q_page=$wikiPage->escapeForDB($page);
    foreach ($pages as $i => $v) {
        $sql ='UPDATE '.$xoopsDB->prefix('gwiki_pages');
        $sql.=' SET page_set_order = '.$v['page_set_order'];
        $sql.=' WHERE gwiki_id = '. $v['gwiki_id']. " and active=1 and page_set_home='{$q_page}' ";;
        $result = $xoopsDB->queryF($sql);
        }
    $pages=array();
    $pages=fetchPageSet($pageset);
    $op='display';
    $pageX = $wikiPage->getPage($page); // reset current to clean up
    if(!$pageX) redirect_header("index.php?page=$page", 3, _MD_GWIKI_PAGENOTFOUND); // better not happen, but ...

}


$token=0;

$caption = _MD_GWIKI_SORT_PAGE_FORM;
$form = new XoopsThemeForm($caption, 'form1', 'sortpageset.php', 'POST', $token);

$caption = _MD_GWIKI_SORT_PAGE_FORM;
$form->addElement(new XoopsFormLabel($caption, '<a href="edit.php?page='.$page_set_home.'">'.$display_keyword.'</a>', 'page_set_home'),false);

$form->addElement(new XoopsFormHidden('page', $page_set_home));

$caption = _MD_GWIKI_SORT_ACTIONS;
$buttontray=new XoopsFormElementTray($caption, '');

$button_moveup=new XoopsFormButton('', 'moveup', _MD_GWIKI_SORT_UP, 'button');
$button_moveup->setExtra('onClick="move(this.form,true)" ');
$buttontray->addElement($button_moveup);

$button_movedown=new XoopsFormButton('', 'movedown', _MD_GWIKI_SORT_DOWN, 'button');
$button_movedown->setExtra('onClick="move(this.form,false)" ');
$buttontray->addElement($button_movedown);

$button_reverse=new XoopsFormButton('', 'reverse', _MD_GWIKI_SORT_REVERSE, 'button');
$button_reverse->setExtra('onClick="reverseorder(this.form)" ');
$buttontray->addElement($button_reverse);

$button_submit=new XoopsFormButton('', 'submit', _MD_GWIKI_SORT_SAVE, 'submit');
$button_submit->setExtra('onClick="processForm(this.form)" ');
$buttontray->addElement($button_submit);

$form->addElement($buttontray);

// XoopsFormSelect( string $caption, string $name, [mixed $value = null], [int $size = 1], [bool $multiple = false])
$listbox = new XoopsFormSelect(_MD_GWIKI_SORT_PAGES, 'sortelement', null, count($pages), false);
foreach ($pages as $i => $v) {
    $listbox->addOption($i, $v['display_keyword']);
}
$form->addElement($listbox);

$form->addElement($buttontray);

$form->addElement(new XoopsFormHidden('neworder', ''));
$body=$form->render();

//$debug='<pre>$_POST='.print_r($_POST,true).'</pre>';
//$debug.='<pre>$places='.print_r($places,true).'</pre>';
//if(isset($neworder)) $debug.='<pre>$neworder='.print_r($neworder,true).'</pre>';
//$debug.='<pre>$topics='.print_r($topics,true).'</pre>';

$title=_MD_GWIKI_SORT_PAGE_FORM;
$xoopsTpl->assign('xoops_pagetitle', $title);
$xoopsTpl->assign('icms_pagetitle', $title);

$dir = basename( dirname( __FILE__ ) ) ;
$pageX['moddir']  = $dir;
$pageX['modpath'] = XOOPS_ROOT_PATH .'/modules/' . $dir;
$pageX['modurl']  = XOOPS_URL .'/modules/' . $dir;
$pageX['mayEdit'] = $mayEdit;
$pageX['pageFound'] = true;

$pageX['body']=$body;

$pageX['title']=$title;
$xoopsTpl->assign('gwiki', $pageX);
$xoTheme->addStylesheet(XOOPS_URL.'/modules/gwiki/assets/css/module.css');

if(isset($message)) $xoopsTpl->assign('message', $message);
if(isset($err_message)) $xoopsTpl->assign('err_message', $err_message);
if(isset($debug)) $xoopsTpl->assign('debug', $debug);

include(XOOPS_ROOT_PATH.'/footer.php');
