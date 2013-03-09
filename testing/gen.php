<?php
include_once '../../../mainfile.php';
$xoopsOption['template_main'] = 'gwiki_view.html';
include XOOPS_ROOT_PATH."/header.php";
$dir = basename( dirname ( dirname( __FILE__ ) ) ) ;
include_once XOOPS_ROOT_PATH.'/modules/'.$dir.'/classes/gwikiPage.php';
global $wikiPage;
$wikiPage = new gwikiPage;

include "LoremIpsumGenerator.php";
$LIGen = new LoremIpsumGenerator;

$limit=100;
$bodylimit=1000;

if(!empty($_POST['op'])) {
	for ($i = 1; $i <= $limit; $i++) {
//		$keyword=trim($LIGen->getContent( mt_rand ( 1, 2), 'txt', $loremipsum = false));
		$keyword=trim($LIGen->getContent( 2, 'txt', $loremipsum = false));
		$keyword = str_replace(array(' ', '.',',',"\t"), array('-', '','',''), $keyword);
		//echo $keyword . "\n";
		$title=$LIGen->getContent( mt_rand ( 3, 6), 'txt', $loremipsum = false);
		$title = str_replace(array('.',',',"\t"), '', $title);
		//echo $title . "\n";
		$body=$LIGen->getContent( mt_rand ( 60, $bodylimit), 'txt', $loremipsum = true);
		//echo $body . "\n";

		$linklimit=mt_rand(3,8);
		for($j=1;$j<$linklimit;$j++) {
			$text=trim($LIGen->getContent( 1, 'txt', $loremipsum = false));
			$text = str_replace('.','', $text);
			$link = str_replace(array(' ', '.',',',"\t"), array('-', '','',''), $text);
			$body = str_replace(' '.$text.' ',' [['.$link.'|'.$text.']] ', $body);
			//echo $text.':';
		}
		$linklimit=mt_rand(100,300);
		for($j=1;$j<$linklimit;$j++) {
			$text=trim($LIGen->getContent( 2, 'txt', $loremipsum = false));
			$text = str_replace('.','', $text);
			$link = str_replace(array(' ', '.',',',"\t"), array('-', '','',''), $text);
			$body = str_replace(' '.$text.' ',' [['.$link.'|'.$text.']] ', $body);
			//echo $text.':';
		}
		
		$wikiPage->keyword=$keyword;
		$wikiPage->title=$title;
		$wikiPage->display_keyword=$keyword;
		$wikiPage->body=$body;
		$wikiPage->uid=($xoopsUser)?$xoopsUser->getVar('uid'):0;

		$wikiPage->parent_page='';
		$wikiPage->page_set_home='';
		$wikiPage->page_set_order='';
		$wikiPage->meta_description='';
		$wikiPage->meta_keywords='';
		$wikiPage->show_in_index=true;

		$success = $wikiPage->addRevision();

		echo $success.' - '.$keyword.'<br />';
	}
}
echo '<br /><br/><form method="post"><input type="hidden" name="op" value="doit"><input type="submit" value="Run"></form>';

include XOOPS_ROOT_PATH."/footer.php";
?>
