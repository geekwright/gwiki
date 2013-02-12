<?php
function wikimod_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB;
    
    $sql = "SELECT w1.* FROM ".$xoopsDB->prefix("wikimod")." AS w1 LEFT JOIN ".$xoopsDB->prefix("wikimod")." AS w2 ON w1.keyword=w2.keyword AND w1.id<w2.id WHERE w2.id IS NULL";
    if (is_array($queryarray) && ($count = count($queryarray))) {
        $sql .= " AND (w1.title LIKE '%$queryarray[0]%' OR w1.body LIKE '%$queryarray[0]%')";
        for($i = 1; $i < $count; $i++) {
            $sql .= " $andor (w1.title LIKE '%$queryarray[$i]%' OR w1.body LIKE '%$queryarray[$i]%')";
        }
    } else {
        $sql .= " AND w1.u_id='$userid'";
    }
    $sql .= " ORDER BY w1.lastmodified DESC";
    
    $items = array();
    $result = $xoopsDB->query($sql, $limit, $offset);
     while($myrow = $xoopsDB->fetchArray($result)) {
         $items[] = array(
             'title' => $myrow['title'],
             'link' => "index.php?page=".$myrow['keyword'],
             'time' => strtotime($myrow['lastmodified']),
             'uid' => $myrow['u_id'],
             'image' => "../../images/quote.gif"
         );
    }
    
    return $items;
}
?>