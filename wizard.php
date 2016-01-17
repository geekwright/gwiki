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
include dirname(dirname(__DIR__)) . '/mainfile.php';
$xoopsOption['template_main'] = 'gwiki_wizard.tpl';
include XOOPS_ROOT_PATH . "/header.php";
include_once 'include/functions.php';
include_once XOOPS_ROOT_PATH . "/class/xoopsformloader.php";
global $wikiPage, $xoopsDB;

$token = 0;

/**
 * @param $params
 */
function redirect_to_edit($params)
{
    global $xoopsLogger, $wikiPage;

    $url = XOOPS_URL . '/modules/' . $wikiPage->getWikiDir() . '/edit.php#wikipage';

    $_SESSION['gwikiwizard'] = serialize($params);

    redirect_header($url, 1, _MD_GWIKI_WIZARD_FORWARDING);
    exit;
}

/**
 * @return bool
 */
function obtainPage()
{
    global $wikiPage, $xoopsTpl, $token;

    $wikiPage = new gwikiPage;
    $prefixes = $wikiPage->getUserNamespaces(true);
    if ($prefixes) {
        $options = array();
        foreach ($prefixes as $p) {
            $options[$p['prefix_id']] = $p['prefix'];
        }
    } else {
        $err_message = _MD_GWIKI_NO_PAGE_PERMISSION;
        redirect_header('index.php', 2, $err_message);
    }

    $page = '';

    $form = new XoopsThemeForm(_MD_GWIKI_WIZARD_NEWPAGE_PROMPT, 'gwizardform', 'wizard.php', 'POST', $token);

    $form_ns_select = new XoopsFormSelect(_MD_GWIKI_WIZARD_PICK_NAMESPACE, 'nsid'); //, [mixed $value = null], [int $size = 1], [bool $multiple = false]  )
    $form_ns_select->addOptionArray($options);
    $form->addElement($form_ns_select);

    $form->addElement(new XoopsFormText(_MD_GWIKI_WIZARD_PAGE_NAME, 'page', 20, 120, $page));

    $btn_tray   = new XoopsFormElementTray('', ' ', 'gwizardformtray');
    $submit_btn = new XoopsFormButton("", "wikiwizard_submit", _MD_GWIKI_WIZARD_CONTINUE, "submit");
    //  $submit_btn->setExtra("onclick='prepForSubmit();'");
    $btn_tray->addElement($submit_btn);

    $cancel_btn = new XoopsFormButton("", "wikiwizard_cancel", _MD_GWIKI_WIZARD_CANCEL, "button");
    $cancel_btn->setExtra(' onclick="document.location.href=\'index.php\';"');
    $btn_tray->addElement($cancel_btn);

    $form->addElement($btn_tray);

    $form->assign($xoopsTpl);

    return true;
}

function obtainImportText()
{
    global $wikiPage, $xoopsTpl, $token;

    $form = new XoopsThemeForm(_MD_GWIKI_IMPORT_TEXT_TITLE, 'gwizardform', 'wizard.php', 'POST', $token);
    $form->setExtra(' enctype="multipart/form-data" ');

    $caption = _MD_GWIKI_IMPORT_TEXT_FILE;
    $form->addElement(new XoopsFormFile($caption, 'import_file', $wikiPage->getMaxUploadSize()), false);
    $form->addElement(new XoopsFormLabel('', _MD_GWIKI_IMPORT_TEXT_FORM_DESC, 'instructions'));

    $btn_tray   = new XoopsFormElementTray('', ' ', 'gwizardformtray');
    $submit_btn = new XoopsFormButton("", "wikiwizard_submit", _MD_GWIKI_WIZARD_CONTINUE, "submit");
    //  $submit_btn->setExtra("onclick='prepForSubmit();'");
    $btn_tray->addElement($submit_btn);

    $cancel_btn = new XoopsFormButton("", "wikiwizard_cancel", _MD_GWIKI_WIZARD_CANCEL, "button");
    $cancel_btn->setExtra(" onclick='history.back();'");
    $btn_tray->addElement($cancel_btn);

    $form->addElement($btn_tray);
    $form->addElement(new XoopsFormHidden('page', $wikiPage->keyword));
    $form->addElement(new XoopsFormHidden('op', 'doimporttext'));

    $form->assign($xoopsTpl);
}

/**
 * @param $page
 * @param $dir
 *
 * @return bool
 */
function doImportText($page, $dir)
{
    $import   = '';
    $pathname = XOOPS_ROOT_PATH . '/uploads/' . $dir . '/';
    if (isset($_POST['xoops_upload_file'][0])) {
        $filekey = $_POST['xoops_upload_file'][0];
        if (isset($_FILES[$filekey]) && !$_FILES[$filekey]['error']) {
            $zapus    = array(' ', '/', '\\');
            $filename = tempnam($pathname, 'IMPORTTEXT_');
            if (move_uploaded_file($_FILES[$filekey]['tmp_name'], $filename)) {
                $import = file_get_contents($filename);
                unlink($filename);
            } else {
                return false;
            }
        }
    }
    if (empty($import)) {
        return false;
    }

    if (!empty($import)) {
        $params = array(
            'page' => $page,
            'op'   => 'preview',
            'body' => $import);

        redirect_to_edit($params);
        exit;
    }

    return false;
}

/**
 * @param string $import_html
 */
function obtainImportHTML($import_html = '')
{
    global $wikiPage, $xoopsTpl, $token;

    $form = new XoopsThemeForm(_MD_GWIKI_IMPORT_HTML_TITLE, 'gwizardform', 'wizard.php', 'POST', $token);
    $form->setExtra(' enctype="multipart/form-data" ');

    $caption = _MD_GWIKI_IMPORT_HTML_FILE;
    $form->addElement(new XoopsFormFile($caption, 'import_file', $wikiPage->getMaxUploadSize()), false);
    $form->addElement(new XoopsFormLabel('', _MD_GWIKI_IMPORT_HTML_FORM_DESC, 'instructions'));

    $form->addElement(new XoopsFormTextArea(_MD_GWIKI_IMPORT_HTML_TEXT, 'import_html', htmlspecialchars($import_html), 10, 40));
    $btn_tray   = new XoopsFormElementTray('', ' ', 'gwizardformtray');
    $submit_btn = new XoopsFormButton("", "wikiwizard_submit", _MD_GWIKI_WIZARD_CONTINUE, "submit");
    //  $submit_btn->setExtra("onclick='prepForSubmit();'");
    $btn_tray->addElement($submit_btn);

    $cancel_btn = new XoopsFormButton("", "wikiwizard_cancel", _MD_GWIKI_WIZARD_CANCEL, "button");
    $cancel_btn->setExtra(" onclick='history.back();'");
    $btn_tray->addElement($cancel_btn);

    $form->addElement($btn_tray);
    $form->addElement(new XoopsFormHidden('page', $wikiPage->keyword));
    $form->addElement(new XoopsFormHidden('op', 'doimporthtml'));

    $form->assign($xoopsTpl);
}

/**
 * @param         $out
 * @param DOMNode $domNode
 * @param         $nest
 * @param         $lt
 * @param         $ld
 * @param         $nop
 */
function showDOMNode(&$out, DOMNode $domNode, $nest, $lt, $ld, $nop)
{
    foreach ($domNode->childNodes as $node) {
        switch ($node->nodeName) {
            case 'a':
                $h = $node->getAttribute('href');
                $h = str_replace(array("\n", "\r"), '', $h);
                if (!empty($h)) {
                    $out .= '[[' . $h . '|';
                    if ($node->hasChildNodes()) {
                        showDOMNode($out, $node, $nest + 1, $lt, $ld, 1);
                    }
                    $out .= ' ]]';
                }
                break;
            case 'img':
                $out .= '{{' . $node->getAttribute('src');
                $alt = trim($node->getAttribute('alt'));
                if (!empty($alt)) {
                    $out .= '|' . $alt;
                }
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, $nop);
                }
                $out .= '}}';
                break;
            case 'p':
                if ($ld < 1) {
                    $out .= "\n\n";
                }
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, $nop);
                }
                break;
            case 'div':
                $out .= "\n\n";
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, $nop);
                }
                $out .= "\n\n";
                break;
            case 'blockquote':
                $out .= "\n> ";
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, $nop);
                }
                break;
            case 'pre':
                $out .= "\n{{{\n";
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, 0);
                }
                $out .= "\n}}}\n";
                break;
            case 'ul':
                $out .= "\n";
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, '*', $ld + 1, $nop);
                }
                $out .= "\n";
                break;
            case 'ol':
                $out .= "\n";
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, '#', $ld + 1, $nop);
                }
                $out .= "\n";
                break;
            case 'li':
                $out .= "\n";
                if ($ld === 0) {
                    $ld = 1;
                }
                if ($lt === '#') {
                    for ($i = 1; $i <= $ld; ++$i) {
                        $out .= "#";
                    }
                } else {
                    for ($i = 1; $i <= $ld; ++$i) {
                        $out .= "*";
                    }
                }
                $out .= " ";
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, 1);
                }
                break;
            case 'h1':
                $out .= "\n= " . $node->getAttribute('href');
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, 1);
                }
                $out .= "\n";
                break;
            case 'h2':
                $out .= "\n== " . $node->getAttribute('href');
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, 1);
                }
                $out .= "\n";
                break;
            case 'h3':
                $out .= "\n=== " . $node->getAttribute('href');
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, $nop);
                }
                $out .= "\n";
                break;
            case 'h4':
                $out .= "\n=== " . $node->getAttribute('href');
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, $nop);
                }
                $out .= "\n";
                break;
            case 'h5':
                $out .= "\n===== " . $node->getAttribute('href');
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, $nop);
                }
                $out .= "\n";
                break;
            case 'b':
            case 'strong':
                $out .= '**';
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, $nop);
                }
                $out .= '**';
                break;
            case 'i':
            case 'em':
                $out .= '//';
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, $nop);
                }
                $out .= '//';
                break;
            case 'u':
                $out .= '__';
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, $nop);
                }
                $out .= '__';
                break;
            case 'br':
                $out .= '\\\\';
                break;
            case 'hr':
                $out .= "\n----\n";
                break;
            case 'tr':
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, $nop);
                }
                $out .= "|\n";
                break;
            case 'td':
                $out .= "|";
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, 1);
                }
                break;
            case 'th':
                $out .= "|=";
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, 1);
                }
                break;
            case '#text':
                if ($nop) {
                    $out .= str_replace(array("\n", "\r", '  '), ' ', $node->nodeValue);
                } else {
                    $out .= $node->nodeValue;
                }
                break;
            default:
                if ($node->hasChildNodes()) {
                    showDOMNode($out, $node, $nest + 1, $lt, $ld, $nop);
                }
                break;
        }
    }
}

/**
 * @param $page
 * @param $import_html
 * @param $dir
 *
 * @return bool
 */
function doImportHTML($page, $import_html, $dir)
{
    $import   = '';
    $pathname = XOOPS_ROOT_PATH . '/uploads/' . $dir . '/';
    if (isset($_POST['xoops_upload_file'][0])) {
        $filekey = $_POST['xoops_upload_file'][0];
        if (isset($_FILES[$filekey]) && !$_FILES[$filekey]['error']) {
            $zapus    = array(' ', '/', '\\');
            $filename = tempnam($pathname, 'IMPORTHTML_');
            if (move_uploaded_file($_FILES[$filekey]['tmp_name'], $filename)) {
                $import = file_get_contents($filename);
                unlink($filename);
            } else {
                return false;
            }
        }
    }
    if (empty($import) && !empty($import_html)) {
        $import = $import_html;
    }

    if (!empty($import)) {
        // the "--" mark is common in text, but gets interpreted as strike
        //$search  = "#(?<=\s)(-{2})(?=\s)#";
        //$replace = "~\\1";
        //$import=preg_replace($search, $replace, $import);

        $doc = new DOMDocument();
        $doc->loadHTML($import);
        $domlist = $doc->getElementsByTagName('body');
        $out     = '';
        foreach ($domlist as $node) {
            showDOMNode($out, $node, 0, '', 0, 1);
        }

        $params = array(
            'page' => $page,
            'op'   => 'preview',
            'body' => $out);

        redirect_to_edit($params);
        exit;
    }

    return false;
}

/**
 * @param $page
 * @param $templatename
 *
 * @return bool
 */
function doTemplate($page, $templatename)
{
    global $wikiPage, $xoopsDB;

    $p = $wikiPage->getPage($templatename);
    if ($p) {
        $params = array(
            'page' => $page,
            'op'   => 'preview',
            'body' => $p['body']);

        redirect_to_edit($params);
    }
    redirect_header(XOOPS_URL . "/modules/{$wikiPage->getWikiDir()}/wizard.php?page={$page}", 2, _MD_GWIKI_PAGENOTFOUND);

    return false;
}

function doGallery()
{
    global $wikiPage, $xoopsDB;

    $page = $wikiPage->keyword;

    $params = array(
        'page' => $page,
        'op'   => 'preview',
        'body' => '{gallery}');

    redirect_to_edit($params);
}

/**
 * @param $page
 * @param $templatename
 *
 * @return bool
 */
function doCopy($page, $templatename)
{
    global $wikiPage, $xoopsDB;

    $p = $wikiPage->getPage($templatename);
    if ($p) {
        $params = array(
            'page'             => $page,
            'op'               => 'preview',
            'body'             => $p['body'],
            'title'            => $p['title'],
            'display_keyword'  => $page,
            'parent_page'      => $p['parent_page'],
            'page_set_home'    => $p['page_set_home'],
            'page_set_order'   => '',
            'meta_description' => $p['meta_description'],
            'meta_keywords'    => $p['meta_keywords'],
            'show_in_index'    => '1',
            'leave_inactive'   => '0');

        redirect_to_edit($params);
    }
    redirect_header(XOOPS_URL . "/modules/{$wikiPage->getWikiDir()}/wizard.php?page={$page}", 2, _MD_GWIKI_PAGENOTFOUND);

    return false;
}

/**
 * @param $keyword_like
 *
 * @return array|bool
 */
function getPagesLike($keyword_like)
{
    global $wikiPage, $xoopsDB;

    $pages = false;

    if (!empty($keyword_like)) {
        $q_keyword = $wikiPage->escapeForDB($keyword_like . '%');

        $sql = 'SELECT keyword, display_keyword FROM ' . $xoopsDB->prefix('gwiki_pages');
        $sql .= " WHERE keyword like '{$q_keyword}'";
        $sql .= ' AND active = 1';
        $sql .= ' ORDER BY display_keyword ';
        $pages  = array();
        $result = $xoopsDB->query($sql);
        while ($myrow = $xoopsDB->fetchArray($result)) {
            $pages[$myrow['keyword']] = $myrow['display_keyword'];
        }
    }

    return $pages;
}

/**
 * @return bool
 */
function galleryForm()
{
    global $wikiPage, $xoopsTpl, $xoopsModuleConfig;

    $page   = $wikiPage->keyword;
    $title  = _MD_GWIKI_WIZARD_GALLERY_SELECT;
    $body   = array();
    $body[] = '<div class="wikiimagedetail">';
    $body[] = '<form id="wikieditimg_form" action="ajaximgedit.php" method="POST" enctype="multipart/form-data">';
    $body[] = '<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="' . $wikiPage->getMaxUploadSize() . '" />';
    $body[] = '<input type="hidden" id="page" name="page" value="' . $page . '" />';
    $body[] = '<div id="wikieditimg_dd">';
    //  $body[] = '<img name="wikieditimg_img" id="wikieditimg_img" class="wikieditimg" src="assets/images/blank.png" /><br />';
    $body[] = '<span id="wikieditimg_dd_msg">' . _MD_GWIKI_IMAGES_DROPHERE . '</span>';
    $body[] = '<div id="gwikiimgform_nofiledrag">' . _MD_GWIKI_IMAGES_PICKFILE . '<input type="file" id="wikieditimg_fileselect" name="fileselect[]"  multiple="multiple"/></div>';
    $body[] = '<div id="wikieditimg_progress"></div>';
    $body[] = '</div>';
    $body[] = '</form>';
    $body[] = '</div>';
    $body[] = '<form id="gwizardform" name="gwizardform" action="wizard.php" method="POST">';
    $body[] = '<table class="wikiwizard_table">';
    $body[] = '<tr><td></td><td><hr /></td></tr>';
    $body[] = '<tr><td> </td><td>';
    $body[] = '<input type="hidden" name="page" value="' . $page . '">';
    $body[] = '<input type="hidden" name="op" value="addgallery">';
    $body[] = '<input type="submit" class="formButton" name="wikiwizard_submit" id="wikiwizard_submit" value="' . _MD_GWIKI_WIZARD_CONTINUE . '" />';
    $body[] = '<input type="button" class="formButton" name="wikiwizard_cancel" id="wikiwizard_cancel" value="' . _MD_GWIKI_WIZARD_CANCEL . '" onclick="document.location.href=\'wizard.php\';" />';
    $body[] = '</td></tr>';
    $body[] = '</table>';
    $body[] = '</form>';

    $xoopsTpl->assign('body', implode("\n", $body));
    $xoopsTpl->assign('title', $title);

    return true;
}

/**
 * @return bool
 */
function chooseWizard()
{
    global $wikiPage, $xoopsTpl, $xoopsModuleConfig;

    $wizopts = array();

    $template_namespace = $xoopsModuleConfig['template_namespace'];
    if (!empty($template_namespace)) {
        $templates = getPagesLike($template_namespace);
        if ($templates) {
            $wizopts[] = array(
                'name'        => 'template',
                'title'       => _MD_GWIKI_WIZARD_TEMPLATE_TITLE,
                'description' => _MD_GWIKI_WIZARD_TEMPLATE_DESC,
                'options'     => array(
                    array('type' => 'select', 'prompt' => '', 'name' => 'templatename', 'values' => $templates)));
        }
    }

    $wizopts[] = array(
        'name'        => 'copy',
        'title'       => _MD_GWIKI_WIZARD_COPY_TITLE,
        'description' => _MD_GWIKI_WIZARD_COPY_DESC,
        'options'     => array(
            array('type' => 'text', 'prompt' => _MD_GWIKI_WIZARD_COPY_PAGE, 'name' => 'copykeyword', 'values' => '')));

    $wizopts[] = array(
        'name'        => 'importhtml',
        'title'       => _MD_GWIKI_WIZARD_HTML_TITLE,
        'description' => _MD_GWIKI_WIZARD_HTML_DESC,
        'options'     => null);

    $wizopts[] = array(
        'name'        => 'importtext',
        'title'       => _MD_GWIKI_WIZARD_TEXT_TITLE,
        'description' => _MD_GWIKI_WIZARD_TEXT_DESC,
        'options'     => null);

    $wizopts[] = array(
        'name'        => 'gallery',
        'title'       => _MD_GWIKI_WIZARD_GALLERY_TITLE,
        'description' => _MD_GWIKI_WIZARD_GALLERY_DESC,
        'options'     => null);

    $page   = $wikiPage->keyword;
    $title  = _MD_GWIKI_WIZARD_OPTIONS_TITLE;
    $body   = array();
    $body[] = '<form id="gwizardform" name="gwizardform" action="wizard.php" method="POST">';
    $body[] = '<table class="wikiwizard_table">';
    foreach ($wizopts as $i => $opt) {
        $rid    = 'radio_id_' . $opt['name'];
        $body[] = '<tr><td> </td><td><span class="wikiwizard_formcaption">' . $opt['title'] . '</span></td></tr>';
        $body[] = '<tr><td> <input type="radio" name="op" id="' . $rid . '" value="' . $opt['name'] . '"></td><td>' . $opt['description'] . '</td></tr>';
        if (!empty($opt['options'])) {
            foreach ($opt['options'] as $value) {
                switch ($value['type']) {
                    case 'select':
                        $body[] = '<tr><td>' . $value['prompt'] . '</td><td><select name="' . $value['name'] . '" id="' . $value['name'] . '" onchange="setRadioButton(\'' . $rid . '\');">';
                        foreach ($value['values'] as $n => $v) {
                            $body[] = '<option value="' . $n . '">' . $v . '</option>';
                        }
                        $body[] = '</select></td></tr>';
                        break;
                    case 'text':
                        $body[] = '<tr><td> </td><td>' . $value['prompt'] . ' <input name="' . $value['name'] . '" id="' . $value['name'] . '" value="' . $value['values'] . '" onchange="setRadioButton(\'' . $rid . '\');"></td></tr>';
                        break;
                    default:
                        break;
                }
            }
        }
        $body[] = '<tr><td></td><td><hr /></td></tr>';
    }
    $body[] = '<tr><td> </td><td>';
    $body[] = '<input type="hidden" name="page" value="' . $page . '">';
    $body[] = '<input type="submit" class="formButton" name="wikiwizard_submit" id="wikiwizard_submit" value="' . _MD_GWIKI_WIZARD_CONTINUE . '" />';
    $body[] = '<input type="button" class="formButton" name="wikiwizard_cancel" id="wikiwizard_cancel" value="' . _MD_GWIKI_WIZARD_CANCEL . '" onclick="document.location.href=\'wizard.php\';" />';
    $body[] = '</td></tr>';
    $body[] = '</table>';
    $body[] = '</form>';

    $xoopsTpl->assign('body', implode("\n", $body));
    $xoopsTpl->assign('title', $title);

    return true;
}

$page = '';
if (isset($_GET['page'])) {
    $page = cleaner($_GET['page']);
}
if (isset($_POST['page'])) {
    $page = cleaner($_POST['page']);
}
// namespace id (prefix_id) is set by newpage block, turn it into a full page name
if (isset($_REQUEST['nsid'])) {
    $page = $wikiPage->makeKeywordFromPrefix((int)($_REQUEST['nsid']), $page);
}

$op = '';
if (isset($_POST['op'])) {
    $op = cleaner($_POST['op']);
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
if (empty($page)) {
    $pageX   = false;
    $op      = "page";
    $mayEdit = false;
} else {
    $pageX   = $wikiPage->getPage($page);
    $mayEdit = $wikiPage->checkEdit();
    if (!$mayEdit) {
        $err_message = _MD_GWIKI_NO_PAGE_PERMISSION;
        redirect_header("index.php?page=$page", 2, $err_message);
    }
}

if ($pageX) {
    $pageX['author']       = $wikiPage->getUserName($wikiPage->uid);
    $pageX['revisiontime'] = date($wikiPage->dateFormat, $pageX['lastmodified']);
    $pageX['mayEdit']      = $mayEdit;
    $pageX['pageFound']    = true;
} else {
    $pageX                 = array();
    $uid                   = ($xoopsUser) ? $xoopsUser->getVar('uid') : 0;
    $pageX['uid']          = $uid;
    $pageX['author']       = $wikiPage->getUserName($uid);
    $pageX['revisiontime'] = date($wikiPage->dateFormat);
    $pageX['mayEdit']      = $mayEdit;
    $pageX['keyword']      = $page;
    $pageX['pageFound']    = false;
}

$dir               = basename(__DIR__);
$pageX['moddir']   = $dir;
$pageX['modpath']  = XOOPS_ROOT_PATH . '/modules/' . $dir;
$pageX['modurl']   = XOOPS_URL . '/modules/' . $dir;
$pageX['ineditor'] = false;

switch ($op) {
    case 'page':
        obtainPage();
        break;
    case 'importtext':
        obtainImportText();
        break;
    case 'doimporttext':
        doImportText($page, $dir);
        obtainImportText(); // if we come back, we failed so try again
        break;
    case 'importhtml':
        obtainImportHTML($import_html);
        break;
    case 'doimporthtml':
        doImportHTML($page, $import_html, $dir);
        obtainImportHTML($import_html); // if we come back, we failed so try again
        break;
    case 'template':
        doTemplate($page, $templatename);
        chooseWizard();
        break;
    case 'copy':
        doCopy($page, $copykeyword);
        chooseWizard();
        break;
    case 'gallery':
        galleryForm();
        break;
    case 'addgallery':
        doGallery();
        break;
    default:
        chooseWizard();
        break;
}

$title = _MD_GWIKI_WIZARD;
$xoopsTpl->assign('xoops_pagetitle', $title);
$xoopsTpl->assign('icms_pagetitle', $title);

$xoopsTpl->assign('gwiki', $pageX);

if (!empty($err_message)) {
    $xoopsTpl->assign('err_message', $err_message);
}
if (!empty($message)) {
    $xoopsTpl->assign('message', $message);
}

$xoTheme->addStylesheet(XOOPS_URL . '/modules/gwiki/assets/css/module.css');

include XOOPS_ROOT_PATH . "/footer.php";
