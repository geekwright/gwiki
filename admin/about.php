<?php
/**
 * admin/about.php
 *
 * @copyright  Copyright © 2013 geekwright, LLC. All rights reserved.
 * @license    gwiki/docs/license.txt  GNU General Public License (GPL)
 * @since      1.0
 * @author     Richard Griffith <richard@geekwright.com>
 * @package    gwiki
 */

include __DIR__ . '/header.php';

$moduleAdmin->displayNavigation(basename(__FILE__));
$moduleAdmin->displayAbout('', false);

include __DIR__ . '/footer.php';
