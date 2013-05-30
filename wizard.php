<?php
/**
* wizard.php - wiki page creation wizard
*
* @copyright  Copyright © 2013 geekwright, LLC. All rights reserved. 
* @license    gwiki/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/
include_once '../../mainfile.php';
$xoopsOption['template_main'] = 'gwiki_utility.html';
include XOOPS_ROOT_PATH."/header.php";
include_once 'include/functions.php';
include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
global $wikiPage, $xoopsDB;

$token=0;

function redirect_by_post_request($url, $params)
{
    
    $cookie_string='';
    foreach($_COOKIE as $key => $val) {
		if(empty($cookie_string)) $cookie_string='Cookie:';
		$cookie_string.=' '.$key.'='.urlencode($val).';';
	}
    $parts=parse_url($url);

	$opts = array(
		'http'=>array(
			'method'=>"POST",
			'content' => http_build_query($params),
			'header'=> $cookie_string."\r\n"
		)
	);

	$context = stream_context_create($opts);

	$fp = fopen($url, 'r', false, $context);
	fpassthru($fp);
	fclose($fp);
    exit;
}

function obtainPage()
{
global $wikiPage, $xoopsTpl, $token;

	$wikiPage = new gwikiPage;
	$prefixes=$wikiPage->getUserNamespaces();
	if($prefixes) {
		$options=array();
		foreach($prefixes as $p) {
			$options[$p['prefix_id']]=$p['prefix'];
		}
	}
	else {
		$err_message=_MD_GWIKI_NO_PAGE_PERMISSION;
		redirect_header('index.php', 2, $err_message);
		exit();
	}

	$page='';
	
	$form = new XoopsThemeForm(_MD_GWIKI_WIZARD_NEWPAGE_PROMPT, 'gwizardform', 'wizard.php', 'POST', $token);

	$form_ns_select=new XoopsFormSelect(_MD_GWIKI_WIZARD_PICK_NAMESPACE, 'nsid'); //, [mixed $value = null], [int $size = 1], [bool $multiple = false]  )
	$form_ns_select->addOptionArray($options);
	$form->addElement($form_ns_select);
	
	$form->addElement(new XoopsFormText(_MD_GWIKI_WIZARD_PAGE_NAME, 'page', 20, 120, $page));
	
	$btn_tray = new XoopsFormElementTray('', ' ','gwizardformtray');
	$submit_btn = new XoopsFormButton("", "wikiwizard_submit", _MD_GWIKI_WIZARD_NEWPAGE_SUBMIT, "submit");
//	$submit_btn->setExtra("onclick='prepForSubmit();'");
	$btn_tray->addElement($submit_btn);
    
	$cancel_btn = new XoopsFormButton("", "wikiwizard_cancel", _CANCEL, "button");
	$cancel_btn->setExtra(' onclick="document.location.href=\'index.php\';"');
	$btn_tray->addElement($cancel_btn);

	$form->addElement($btn_tray);

    $form->assign($xoopsTpl);

	return true;
}

function obtainImportHTML($import_html='')
{
global $wikiPage, $xoopsTpl, $token;

	$form = new XoopsThemeForm(_MD_GWIKI_IMPORTHTML_TITLE, 'gwizardform', 'wizard.php', 'POST', $token);
	$form->setExtra(' enctype="multipart/form-data" ');

	$caption = _MD_GWIKI_IMPORTHTML_FILE;
	$form->addElement(new XoopsFormFile($caption, 'import_file',100000),false);
	$form->addElement(new XoopsFormLabel('', _MD_GWIKI_IMPORTHTML_FORM_DESC, 'instructions'));

	$form->addElement(new XoopsFormTextArea(_MD_GWIKI_IMPORTHTML_TEXT, 'import_html', htmlspecialchars($import_html), 10, 40));
	$btn_tray = new XoopsFormElementTray('', ' ','gwizardformtray');
	$submit_btn = new XoopsFormButton("", "wikiwizard_submit", _MD_GWIKI_WIZARD_NEWPAGE_SUBMIT, "submit");
//	$submit_btn->setExtra("onclick='prepForSubmit();'");
	$btn_tray->addElement($submit_btn);
    
	$cancel_btn = new XoopsFormButton("", "wikiwizard_cancel", _CANCEL, "button");
	$cancel_btn->setExtra(" onclick='history.back();'");
	$btn_tray->addElement($cancel_btn);

	$form->addElement($btn_tray);
	$form->addElement(new XoopsFormHidden('page', $wikiPage->keyword));
	$form->addElement(new XoopsFormHidden('op', 'doimporthtml'));

    $form->assign($xoopsTpl);

}

function showDOMNode(&$out, DOMNode $domNode,$nest,$lt,$ld,$nop) {
    foreach ($domNode->childNodes as $node)
    {
        switch ($node->nodeName) {
			case 'a':
				$h=$node->getAttribute('href');
				if(!empty($h)) {
					$out.='[['.$h.'|';
					if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,1); }
					$out.=' ]]';
				}
				break;
			case 'img':
				$out.='{{'.$node->getAttribute('src');
				$alt=trim($node->getAttribute('alt'));
				if(!empty($alt)) $out.= '|'.$alt;
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,$nop); }
				$out.='}}';
				break;
			case 'p':
				if($ld<1) $out.="\n\n";
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,$nop); }
				break;
			case 'div':
				$out.="\n\n";
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,$nop); }
				$out.="\n\n";
				break;
			case 'blockquote':
				$out.="\n> ";
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,$nop); }
				break;
			case 'pre':
				$out.="\n{{{\n";
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,0); }
				$out.="\n}}}\n";
				break;
			case 'ul':
				$out.="\n";
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,'*',$ld+1,$nop); }
				$out.="\n";
				break;
			case 'ol':
				$out.="\n";
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,'#',$ld+1,$nop); }
				$out.="\n";
				break;
			case 'li':
				$out.="\n";
				if($ld==0) $ld=1;
				if($lt=='#') {
					for($i = 1; $i <= $ld; $i++) $out.="#";
				}
				else {
					for($i = 1; $i <= $ld; $i++) $out.="*";
				}
				$out.=" ";
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,1); }
				break;
			case 'h1':
				$out.="\n= ".$node->getAttribute('href');
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,1); }
				$out.="\n";
				break;
			case 'h2':
				$out.="\n== ".$node->getAttribute('href');
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,1); }
				$out.="\n";
				break;
			case 'h3':
				$out.="\n=== ".$node->getAttribute('href');
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,$nop); }
				$out.="\n";
				break;
			case 'h4':
				$out.="\n=== ".$node->getAttribute('href');
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,$nop); }
				$out.="\n";
				break;
			case 'h5':
				$out.="\n===== ".$node->getAttribute('href');
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,$nop); }
				$out.="\n";
				break;
			case 'b':
			case 'strong':
				$out.='**';
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,$nop); }
				$out.='**';
				break;
			case 'i':
			case 'em':
				$out.='//';
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,$nop); }
				$out.='//';
				break;
			case 'u':
				$out.='__';
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,$nop); }
				$out.='__';
				break;
			case 'br':
				$out.='\\\\';
				break;
			case 'hr':
				$out.="\n----\n";
				break;
			case 'tr':
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,$nop); }
				$out.="|\n";
				break;
			case 'td':
				$out.="|";
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,1); }
				break;
			case 'th':
				$out.="|=";
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,1); }
				break;
			case '#text':
				if($nop) $out.=str_replace (array("\n","\r",'  '),' ', $node->nodeValue);
				else $out.=$node->nodeValue;
				break;
			default:
				if($node->hasChildNodes()) { showDOMNode($out, $node,$nest+1,$lt,$ld,$nop); }
				break;
		}
    }    
}

function doImportHTML($page,$import_html,$dir)
{
	$import='';
	$pathname=XOOPS_ROOT_PATH.'/uploads/'.$dir.'/';
	if(isset($_POST['xoops_upload_file'][0])) {
		$filekey=$_POST['xoops_upload_file'][0];
		if(isset($_FILES[$filekey]) && !$_FILES[$filekey]['error']) {
			$zapus = array(' ', '/', '\\');
			$filename = tempnam($pathname, 'IMPORTHTML_');
			if (move_uploaded_file($_FILES[$filekey]['tmp_name'], $filename)) {
				$import = file_get_contents ($filename);
				unlink($filename);
			}
			else {
				return false;
			}
		}
	}
	if(empty($import) && !empty($import_html)) {
		$import=$import_html;
	}

	if(!empty($import)) {
		$doc = new DOMDocument();
		$doc->loadHTML($import);
		$domlist=$doc->getElementsByTagName('body');
		$out='';
		foreach($domlist as $node) showDOMNode($out,$node,0,'',0,1);

		$url=XOOPS_URL.'/modules/'.$dir.'/edit.php';

		$params=array(
			'page' => $page,
			'op' => 'preview',
			'body' => $out );

		redirect_by_post_request($url, $params);
		exit;
	}
	return false;
}

function doTemplate($page,$templatename)
{
global $wikiPage, $xoopsDB;

	$p=$wikiPage->getPage($templatename);
	if($p) {
		$url=XOOPS_URL.'/modules/'.$wikiPage->getWikiDir().'/edit.php';

		$params=array(
			'page' => $page,
			'op' => 'preview',
			'body' => $p['body'] );

		redirect_by_post_request($url, $params);
	}
	redirect_header(XOOPS_URL."/modules/{$wikiPage->getWikiDir()}/wizard.php?page={$page}", 2, _MD_GWIKI_PAGENOTFOUND);
	return false;
}

function doCopy($page,$templatename)
{
global $wikiPage, $xoopsDB;

	$p=$wikiPage->getPage($templatename);
	if($p) {
		$url=XOOPS_URL.'/modules/'.$wikiPage->getWikiDir().'/edit.php';

		$params=array(
			'page' => $page,
			'op' => 'preview',
			'body' => $p['body'],
			'title' => $p['title'],
			'display_keyword' => $page,
			'parent_page' => $p['parent_page'],
			'page_set_home' => $p['page_set_home'],
			'page_set_order' => '',
			'meta_description' => $p['meta_description'],
			'meta_keywords' => $p['meta_keywords'],
			'show_in_index' => '1',
			'leave_inactive' => '0' );

		redirect_by_post_request($url, $params);
	}
	redirect_header(XOOPS_URL."/modules/{$wikiPage->getWikiDir()}/wizard.php?page={$page}", 2, _MD_GWIKI_PAGENOTFOUND);
	return false;
}

function getPagesLike($keyword_like)
{
global $wikiPage, $xoopsDB;

	$pages=false;

	if(!empty($keyword_like)) {
		$q_keyword=$wikiPage->escapeForDB($keyword_like.'%');

		$sql = 'SELECT keyword, display_keyword FROM '. $xoopsDB->prefix('gwiki_pages');
		$sql.= ' WHERE keyword like \''.$q_keyword.'\'';
		$sql.= ' AND active = 1';
		$sql.= ' ORDER BY display_keyword ';
		$pages=array();
		$result = $xoopsDB->query($sql);
		while($myrow = $xoopsDB->fetchArray($result)) {
			$pages[$myrow['keyword']]=$myrow['display_keyword'];
		}
	}

	return $pages;
}

function chooseWizard()
{
global $wikiPage, $xoopsTpl, $xoopsModuleConfig;

	$wizopts=array();

	$template_namespace=$xoopsModuleConfig['template_namespace'];
	if(!empty($template_namespace)) {
		$templates=getPagesLike($template_namespace);
		if($templates) {
			$wizopts[]=array(
				'name' => 'template',
				'title'=> 'Create from Template',
				'description'=>'Template verbage',
				'options'=> array(
						array('type'=>'select', 'prompt'=>'', 'name'=>'templatename', 'values'=>$templates)
					)
			);
		}
	}

	$wizopts[]=array(
		'name' => 'copy',
		'title'=> 'Copy an existing page',
		'description'=>'Copy verbage',
		'options'=> array(
			array('type'=>'text', 'prompt'=>'Page to Copy', 'name'=>'copykeyword', 'values'=>'')
		)
	);

	$wizopts[]=array(
		'name' => 'importhtml',
		'title'=> 'Import from HTML',
		'description'=>'HTML verbage',
		'options'=>null
	);

	$wizopts[]=array(
		'name' => 'gallery',
		'title'=> 'Create and Image Gallery',
		'description'=>'Gallery verbage',
		'options'=>null
	);

	$page=$wikiPage->keyword;
	$title='How do you want to build your page?';
	$body=array();
	$body[] = '<form id="gwizardform" name="gwizardform" action="wizard.php" method="POST">';
	$body[] = '<table class="wikiwizard_table">';
	foreach($wizopts as $i=>$opt) {
		$rid='radio_id_'.$opt['name'];
		$body[] = '<tr><td> </td><td><span class="wikiwizard_formcaption">'.$opt['title'].'</span></td></tr>';
		$body[] = '<tr><td> <input type="radio" name="op" id="'.$rid.'" value="'.$opt['name'].'"></td><td>'.$opt['description'].'</td></tr>';
		if(!empty($opt['options'])) {
			foreach ($opt['options'] as $value) {
				switch ($value['type']) {
					case 'select':
						$body[] = '<tr><td>'.$value['prompt'].'</td><td><select name="'.$value['name'].'" id="'.$value['name'].'" onchange="setRadioButton(\''.$rid.'\');">';
						foreach($value['values'] as $n=>$v) {
							$body[] = '<option value="'.$n.'">'.$v.'</option>';
						}
						$body[] = '</select></td></tr>';
						break;
					case 'text':
						$body[] = '<tr><td> </td><td>'.$value['prompt'].' <input name="'.$value['name'].'" id="'.$value['name'].'" value="'.$value['values'].'" onchange="setRadioButton(\''.$rid.'\');"></td></tr>';
						break;
					default:
						break;
				}
			}
		}
		$body[] = '<tr><td></td><td><hr /></td></tr>';
	}
	$body[] = '<tr><td> </td><td>';
	$body[] = '<input type="hidden" name="page" value="'.$page.'">';
	$body[] = '<input type="submit" class="formButton" name="wikiwizard_submit" id="wikiwizard_submit" value="Continue" />';
	$body[] = '<input type="button" class="formButton" name="wikiwizard_cancel" id="wikiwizard_cancel" value="Cancel" onclick="document.location.href=\'wizard.php\';" />';
	$body[] = '</td></tr>';
	$body[] = '</table>';
	$body[] = '</form>';

	$xoopsTpl->assign('body', implode("\n",$body));
	$xoopsTpl->assign('title', $title);
	return true;
}

	if (isset($_GET['page'])) {
		$page = cleaner($_GET['page']);
	}
	if (isset($_POST['page'])) {
		$page = cleaner($_POST['page']);
	}
	// namespace id (prefix_id) is set by newpage block, turn it into a full page name
	if (isset($_REQUEST['nsid'])) {
		$nsid=intval($_REQUEST['nsid']);
		if($nsid>=0) {
			$pfx=getPrefixFromId($nsid);
			if(empty($page)) {
				if($pfx['prefix_auto_name']) $page=date('Y-m-d-His'); // TODO should this be a config item?
				else $page=$pfx['prefix_home'];
			}
			$page=$pfx['prefix'].':'.$page;
		}
	}
	$op='';
	if (isset($_POST['op'])) {
		$op=cleaner($_POST['op']);
	}
	$import_html = '';
	if (isset($_POST['import_html'])) {
		$import_html = cleaner($_POST['import_html']);
	}
	$templatename = '';
	if (isset($_POST['templatename'])) {
		$templatename = cleaner($_POST['templatename']);
	}
	$copykeyword = '';
	if (isset($_POST['copykeyword'])) {
		$copykeyword = cleaner($_POST['copykeyword']);
	}
	if(empty($page)) {
		$pageX=false;
		$op="page";
	}
	else {
		$pageX = $wikiPage->getPage($page);
		$mayEdit = $wikiPage->checkEdit();
		if (!$mayEdit) {
			$err_message=_MD_GWIKI_NO_PAGE_PERMISSION;
			redirect_header("index.php?page=$page", 2, $err_message);
			exit();
		}
	}

	if($pageX) {
		$pageX['author'] = $wikiPage->getUserName($wikiPage->uid);
		$pageX['revisiontime']=date($wikiPage->dateFormat,$pageX['lastmodified']);
		$pageX['mayEdit'] = $mayEdit;
		$pageX['pageFound'] = true;
	}
	else {
		$pageX=array();
		$uid = ($xoopsUser)?$xoopsUser->getVar('uid'):0;
		$pageX['uid']=$uid;
		$pageX['author']=$wikiPage->getUserName($uid);
		$pageX['revisiontime']=date($wikiPage->dateFormat);
		$pageX['mayEdit'] = $mayEdit;
		$pageX['keyword'] = $page;
		$pageX['pageFound'] = false;
	}

	$dir = basename( dirname( __FILE__ ) ) ;
	$pageX['moddir']  = $dir;
	$pageX['modpath'] = XOOPS_ROOT_PATH .'/modules/' . $dir;
	$pageX['modurl']  = XOOPS_URL .'/modules/' . $dir;
	$pageX['ineditor']  = false;

	switch ($op) {
		case 'page':
			obtainPage();
			break;
		case 'importhtml':
			obtainImportHTML($import_html);
			break;
		case 'doimporthtml':
			doImportHTML($page,$import_html,$dir);
			obtainImportHTML($import_html); // if we come back, we failed so try again
			break;
		case 'template':
			doTemplate($page,$templatename);
			chooseWizard();
			break;
		case 'copy':
			doCopy($page,$copykeyword);
			chooseWizard();
			break;
		default:
			chooseWizard();
			break;
	}


	$title=_MD_GWIKI_WIZARD;
	$xoopsTpl->assign('xoops_pagetitle', $title);
	$xoopsTpl->assign('icms_pagetitle', $title);
	
	$xoopsTpl->assign('gwiki', $pageX);

	if(!empty($err_message)) $xoopsTpl->assign('err_message',$err_message);
	if(!empty($message)) $xoopsTpl->assign('message',$message);

	$xoTheme->addStylesheet(XOOPS_URL.'/modules/gwiki/module.css');


include XOOPS_ROOT_PATH."/footer.php";
?>
