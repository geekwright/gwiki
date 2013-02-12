<?php
$modversion['name']        = _MI_WIKIMOD_NAME;
$modversion['version']     = '0.97';
$modversion['description'] = _MI_WIKIMOD_DESC;
$modversion['author']      = 'Simon B&uuml;nzli &lt;<a href="mailto:zeniko@gmx.ch">zeniko@gmx.ch</a>&gt;';
$modversion['credits']     = '<a href="http://go.to/zeniko">zeniko\'s webcreations</a>';
$modversion['license']     = "GNU General Public License";
$modversion['help']        = "";
$modversion['official']    = 0;
$modversion['image']       = "wikimod.png"; // Credits due to Sebastian Loh
$modversion['dirname']     = _MI_WIKIMOD_DIRNAME;

// Tables created by the SQL file (without prefix!)
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][0] = _TAB_WIKIMOD;

// Administration tools
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Main menu
$modversion['hasMain'] = 1;

// Templates
$modversion['templates'][1]['file'] = 'wikimod_view.html';
$modversion['templates'][1]['description'] = 'WikiMod - View Wiki Page';
$modversion['templates'][2]['file'] = 'wikimod_edit.html';
$modversion['templates'][2]['description'] = 'WikiMod - Edit/Preview Wiki Page';

// Search
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = "common/search.php";
$modversion['search']['func'] = "wikimod_search";

// Configuration settings
$modversion['config'][1]['name'] = 'anonymous_edit';
$modversion['config'][1]['title'] = '_MI_WIKIMOD_ANONYMOUS_EDIT';
$modversion['config'][1]['description'] = '';
$modversion['config'][1]['formtype'] = 'yesno';
$modversion['config'][1]['valuetype'] = 'int';
$modversion['config'][1]['default'] = 1;

$modversion['config'][2]['name'] = 'date_format';
$modversion['config'][2]['title'] = '_MI_WIKIMOD_DATEFORMAT';
$modversion['config'][2]['description'] = '';
$modversion['config'][2]['formtype'] = 'select';
$modversion['config'][2]['valuetype'] = 'text';
$modversion['config'][2]['default'] = 'd.m.y';
$modversion['config'][2]['options'] = array('dd.mm.yy' => 'd.m.y', 'mm/dd/yy' => 'm/d/y', 'yyyy-mm-dd' => 'Y-m-d');

$modversion['config'][3]['name'] = 'number_recent';
$modversion['config'][3]['title'] = '_MI_WIKIMOD_NUMBERRECENT';
$modversion['config'][3]['description'] = '';
$modversion['config'][3]['formtype'] = 'select';
$modversion['config'][3]['valuetype'] = 'int';
$modversion['config'][3]['default'] = 10;
$modversion['config'][3]['options'] = array('5' => 5, '10' => 10, '20' => 20, '50' => 50);
?>