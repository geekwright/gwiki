<?php
function makeKeyWord($keyword)
{
    if (!preg_match("#^([A-Z][a-z]+){2,}\d*$#", $keyword)) {
        $keyword = _MI_WIKIMOD_WIKI404;
    }
    
    return $keyword;
}

function getCurrentId($page)
{
    global $xoopsDB;
    
    $sql = "SELECT id FROM ".$xoopsDB->prefix(_TAB_WIKIMOD)." WHERE keyword='$page' ORDER BY id DESC LIMIT 1";
    $result = $xoopsDB->query($sql);
    list($id) = $xoopsDB->fetchRow($result);
    
    return intval($id);
}

function addRevision($page, $title, $body, $uid)
{
    global $xoopsDB, $myts;
    
    $sql = "INSERT INTO ".$xoopsDB->prefix(_TAB_WIKIMOD)." (keyword, title, body, lastmodified, u_id) VALUES('$page', '".$myts->addSlashes($title)."', '".$myts->addSlashes($body)."', '".date("Y-m-d H:i:s")."', '$uid')";
    
    return $xoopsDB->query($sql);
}

function getPage($page)
{
    global $xoopsDB;
    
    $sql = "SELECT title, body, lastmodified, u_id FROM ".$xoopsDB->prefix(_TAB_WIKIMOD)." WHERE keyword='$page' ORDER BY id DESC LIMIT 1";
    $result = $xoopsDB->query($sql);
    
    return ($xoopsDB->getRowsNum($result) > 0)?$result:false;
}

function wikiDisplay($body)
{
    $search = array("#\r\n?#", "#<#",
        "#(&lt;){2}(.*?)>{2}#s",
        "#\{{2}(.*?)\}{2}#s",
        "#^-{4,}$#m", "#\[\[BR\]\]#i",
        
        "#\[\[IMG ([^\s\"\[>{}]+)( ([^\"<\n]+?))?\]\]#i",
        "#\[\[([^\s\"\[\]>{}]+) (.+?)\]\]#", "#\[\[([^\s\"\[\]>{}]+)\]\]#",
        "#([\w.-]+@[\w.-]+)(?![\w.]*(\">|<))#",
        "#(^|\s)(([A-Z][a-z]+){2,}\d*)\b#e",
        
        "#^= (.*) =$#m",
        "#^(> .*\n)+#me",
        "#^\* (.*)#m", "#^(<li>.*</li>\n)+#m",
        "#^(  .*\n)+#me",
        "#&lt;\[(PageIndex|RecentChanges)\]>#e",
        "#^(?!\n|<h2>|<blockquote>|<hr />)(.*?)\n$#sm", "#\n+#");
    $replace = array("\n", "&lt;",
        "<strong>\\2</strong>",
        "<em>\\1</em>",
        "\n<hr />\n", "<br />",
        
        '<img src="\\1" alt="\\3" />',
        '<a href="\\1">\\2</a>', '<a href="\\1">\\1</a>',
        '<a href="mailto:\\1">\\1</a>',
        '"$1".wikiLink("$2")',
        
        "\n<h2>\\1</h2>\n",
        '"<blockquote>".str_replace("\n", " ", preg_replace("#^> #m", "", "$0"))."</blockquote>\n"',
        "<li>\\1</li>", "<ul>\n\\0</ul>\n",
        '"<pre>".preg_replace("#^  #m", "", "$0")."</pre>\n"',
        'createIndex("$1")',
        "<p>\\1</p>", "\n");
    
    return preg_replace($search, $replace, trim($body)."\n");
}

function wikiLink($keyword)
{
    return sprintf('<a href="%s">%s%s</a>', "index.php?page=$keyword", $keyword, (getPage($keyword))?"":"(?)");
}

function createIndex($type)
{
    global $xoopsDB, $xoopsModuleConfig;
    
    $settings = array(
        "PageIndex" => array("ORDER BY w1.keyword ASC", "keyword", 1, '"<strong>$counter</strong><br />"', 'wikiLink($content["keyword"]).": ".$content["title"]."<br />"', ""),
        "RecentChanges" => array("ORDER BY w1.lastmodified DESC LIMIT ".$xoopsModuleConfig['number_recent'], "lastmodified", 10, '"<strong>".date("'.$xoopsModuleConfig['date_format'].'", strtotime($counter))."</strong><ul>"', '"<li>(".date("H:i", strtotime($content["lastmodified"])).") ".wikiLink($content["keyword"]).": ".$content["title"]." . . . . . . <span class=\"itemPoster\">".getUserName($content["u_id"])."</span></li>"', "</ul>")
    );
    $cfg = $settings[$type];
    
    $sql = "SELECT w1.keyword, w1.title, w1.lastmodified, w1.u_id FROM ".$xoopsDB->prefix(_TAB_WIKIMOD)." AS w1 LEFT JOIN ".$xoopsDB->prefix(_TAB_WIKIMOD)." AS w2 ON w1.keyword=w2.keyword AND w1.id<w2.id WHERE w2.id IS NULL ".$cfg[0];
    $result = $xoopsDB->query($sql);
    
    $body = $counter = "";
    while ($content = $xoopsDB->fetcharray($result)) {
        if ($counter != substr($content[$cfg[1]], 0, $cfg[2])) {
            $counter = substr($content[$cfg[1]], 0, $cfg[2]);
            eval('$body .= (($body)?"'.$cfg[5].'":"")."\n\n".'.$cfg[3].';');
        }
        eval('$body .= '.$cfg[4].'."\n";');
    }
    
    return $body.(($body)?$cfg[5]:"")."\n\n";
}

function getUserName($uid)
{
    global $myts, $xoopsConfig;
    
    $uid = intval($uid);
    
    if ($uid > 0) {
        $member_handler =& xoops_gethandler('member');
        $user =& $member_handler->getUser($uid);
        if (is_object($user)) {
            return "<a href=\"".XOOPS_URL."/userinfo.php?uid=$uid\">".$myts->htmlSpecialChars($user->getVar('uname'))."</a>";
        }
    }
    
    return $xoopsConfig['anonymous'];
}
?>