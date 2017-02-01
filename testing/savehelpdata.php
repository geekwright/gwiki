<?php

use Xmf\Database\TableLoad;

include dirname(dirname(dirname(__DIR__))) . '/mainfile.php';

$criteria = new CriteriaCompo(new Criteria('page_set_home', 'Help:Index', '='));
$criteria->add(new Criteria('active', '1', '='), 'AND');
$skipColumns = array('gwiki_id');
$status = TableLoad::saveTableToYamlFile('gwiki_pages', '../sql/helppages.yml', $criteria, $skipColumns);

if ($status) {
    $criteria = new CriteriaCompo(new Criteria('from_keyword', 'help:%', 'LIKE'));
    $criteria->add(new Criteria('to_keyword', 'help:%', 'LIKE'), 'AND');
    $status = TableLoad::saveTableToYamlFile('gwiki_pagelinks', '../sql/helplinks.yml', $criteria);
}

echo $status ? 'Data saved.' : 'Failed';
