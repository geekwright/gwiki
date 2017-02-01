<?php
/**
 * admin/index.php - index, splash and config checks
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 */
include __DIR__ . '/header.php';

$moduleAdmin->displayNavigation(basename(__FILE__));
$welcome = _AD_GW_ADMENU_WELCOME;
$moduleAdmin->addInfoBox($welcome);
$moduleAdmin->addInfoBoxLine($welcome, _AD_GW_ADMENU_MESSAGE, '', '', 'information');
echo $moduleAdmin->renderIndex();

include __DIR__ . '/footer.php';
