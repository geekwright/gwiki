<?php
/**
* importhtml.php - convert html text to wiki page
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
global $wikiPage;
global $xoopsDB;

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

	if (isset($_GET['page'])) {
		$page = cleaner($_GET['page']);
	}
	if (isset($_POST['page'])) {
		$page = cleaner($_POST['page']);
	}
	// namespace id (prefix_id) is set by newpage block, turn it into a full page name
	if (isset($_GET['nsid'])) {
		$nsid=intval($_GET['nsid']);
		if($nsid>=0) {
			$pfx=getPrefixFromId($nsid);
			if(empty($page)) {
				if($pfx['prefix_auto_name']) $page=date('Y-m-d-His'); // TODO should this be a config item?
				else $page=$pfx['prefix_home'];
			}
			$page=$pfx['prefix'].':'.$page;
		}
	}
	if(empty($page)) $page=$wikiPage->wikiHomePage;

	$import_html = '';
	if (isset($_POST['import_html'])) {
		$import_html = cleaner($_POST['import_html']);
	}

	$pageX = $wikiPage->getPage($page);
	$mayEdit = $wikiPage->checkEdit();
	if (!$mayEdit) {
		$err_message=_MD_GWIKI_NO_PAGE_PERMISSION;
		redirect_header("index.php?page=$page", 2, $err_message);
		exit();
	}	if($pageX) {
		$pageX['author'] = $wikiPage->getUserName($wikiPage->uid);
		$pageX['revisiontime']=date($wikiPage->dateFormat,$pageX['lastmodified']);
		$pageX['mayEdit'] = $mayEdit;
		$pageX['pageFound'] = true;
		if(!empty($highlight)) $pageX['body'] = $wikiPage->highlightWords($highlight);
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
	$pageX['ineditor']  = true;
	$pageX['imglib'] = $wikiPage->getImageLib($page);

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
				$err_message=_AD_GWREPORTS_AD_IMPORT_ERROR;
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

	$token=0;

	$form = new XoopsThemeForm(_MD_GWIKI_IMPORTHTML_TITLE, 'form1', '', 'POST', $token);
	$form->setExtra(' enctype="multipart/form-data" ');

	$caption = _MD_GWIKI_IMPORTHTML_FILE;
	$form->addElement(new XoopsFormFile($caption, 'import_file',100000),false);
	$form->addElement(new XoopsFormLabel('', _MD_GWIKI_IMPORTHTML_FORM_DESC, 'instructions')); // edit buttons added in template

	$form->addElement(new XoopsFormTextArea(_MD_GWIKI_IMPORTHTML_TEXT, 'import_html', htmlspecialchars($import_html), 20, 80));
	$form->addElement(new XoopsFormButton('', 'submit', _MD_GWIKI_IMPORTHTML_SUBMIT, 'submit'));
	//$form->display();
	$body=$form->render();
	$title='Testing';
	
	$xoopsTpl->assign('gwiki', $pageX);
	$xoopsTpl->assign('body', $body);
	$xoopsTpl->assign('title', $title);

	if(!empty($err_message)) $xoopsTpl->assign('err_message',$err_message);
	if(!empty($message)) $xoopsTpl->assign('message',$message);

include XOOPS_ROOT_PATH."/footer.php";
?>
