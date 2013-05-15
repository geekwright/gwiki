<?php
/**
* admin/footer.php - wrapup for all admin pages
*
* @copyright  Copyright © 2013 geekwright, LLC. All rights reserved. 
* @license    gwiki/docs/license.txt  GNU General Public License (GPL)
* @since      1.0
* @author     Richard Griffith <richard@geekwright.com>
* @package    gwiki
* @version    $Id$
*/

//echo '<div align="right"><small>'._AM_FBCOM_ADMENU_TRADEMARK.'</small><br /></div>';
echo "<br /><br /><div align=\"center\"><a href=\"http://geekwright.com/\" target=\"_blank\"><img src=\"../images/admin/gwlogo-small.png\" alt=\"geekwright\" title=\"geekwright\"></a></div>";
echo "<div align=\"center\"><small><strong>GWiki</strong> is maintained by <a class='tooltip' rel='external' href='http://geekwright.com/'>geekwright, LLC</a></small></div>";

xoops_cp_footer();
?>
