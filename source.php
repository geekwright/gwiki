<?php
include '../../mainfile.php';
$xoopsLogger->activated = false;
include_once "include/functions.php";
global $wikiPage;


	if (isset($_GET['page'])) {
		$page = $wikiPage->normalizeKeyword(cleaner($_GET['page']));
		$pageX = $wikiPage->getPage($page);
	}
	else {
		$page=false; $pageX=false;
	}

	if($page && $pageX) {
		header('Content-type: text/plain');
		header('Content-Disposition: inline; filename="'.$page.'.txt"');
		echo $wikiPage->body;
		echo "\r\n";
	}
	else {
		redirect_header(sprintf($wikiPage->getWikiLinkURL(),$wikiPage->wikiHomePage), 2, _MD_GWIKI_PAGENOTFOUND_ERR);
	}

	exit;

?>
