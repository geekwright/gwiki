<?php
include 'header.php';

	if($xoop25plus) {
		echo $moduleAdmin->addNavigation('index.php') ;
		$welcome=_AD_GW_ADMENU_WELCOME;
		$moduleAdmin->addInfoBox($welcome);
		$moduleAdmin->addInfoBoxLine($welcome, _AD_GW_ADMENU_MESSAGE, '', '', 'information');
		echo $moduleAdmin->renderIndex();
	}
	else {
		adminmenu(1);

		echo '<table width="100%" border="0" cellspacing="1" class="outer">';
		echo '<tr><th>'._AD_GW_ADMENU_WELCOME.'</th></tr>';
		echo '<tr><td width="100%" ><div style="margin:2em;">'._AD_GW_ADMENU_MESSAGE . '</td></tr>';
		echo '</table>';
}

include 'footer.php';
?>