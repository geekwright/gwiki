<{if isset($title)}>
    <div class="content_body">
        <h1><{$title}></h1>
    </div>
<{/if}>
<{if isset($page_message)}>
    <div class="content_body"><{$page_message}><br><br></div>
<{/if}>
<{if isset($err_message)}>
    <hr/>
    <div class="errorMsg"><{$err_message}></div>
    <hr/>
<{/if}>
<{if isset($message)}>
    <hr/>
    <div class="resultMsg"><{$message}></div>
    <hr/>
<{/if}>

<{if is_array($history)}>
    <table width="100%" border="0" cellspacing="1" class="outer">
        <tr>
            <th colspan="4"><{$smarty.const._MD_GWIKI_HISTORY_TITLE}></th>
        </tr>
        <tr class="<{cycle values="odd,even"}>">
            <th><{$smarty.const._MD_GWIKI_TITLE}></th>
            <th><{$smarty.const._AD_GWIKI_MODIFIED}><{if $revision.active}>*<{/if}></th>
            <th><{$smarty.const._AD_GWIKI_AUTHOR}></th>
            <th><{$smarty.const._AD_GWIKI_ACTION}></th>
        </tr>
        <{if count($history) > 0 }>
            <{foreach key=rid item=revision from=$history }>
                <tr class="<{cycle values="odd,even"}>">
                    <td><a href="javascript:ajaxGwikiView('<{$revision.keyword}>','<{$revision.gwiki_id}>');"
                           title="<{$smarty.const._MD_GWIKI_HISTORY_VIEW_TT}>"><{$revision.title}></a></td>

                    <td><{$revision.revisiontime}><{if $revision.active}>*<{/if}></td>
                    <td><{$revision.username}></td>
                    <td><a href="javascript:ajaxGwikiCompare('<{$revision.keyword}>','<{$revision.gwiki_id}>');"
                           title="<{$smarty.const._MD_GWIKI_HISTORY_COMPARE_TT}>"><{$smarty.const._MD_GWIKI_HISTORY_COMPARE}></a>
                        <{if $gwiki.mayEdit}> |
                            <a href="javascript:restoreGwikiRevision('<{$revision.keyword}>','<{$revision.gwiki_id}>');"
                               title="<{$smarty.const._MD_GWIKI_HISTORY_RESTORE_TT}>"><{$smarty.const._AD_GWIKI_RESTORE}></a>
                            |
                            <a href="<{$gwiki.modurl}>/edit.php?op=edit&page=<{$revision.keyword}>&id=<{$revision.gwiki_id}>"
                               title="<{$smarty.const._EDIT}>"><{$smarty.const._EDIT}></a>
                        <{/if}>
                        | <a href="<{$gwiki.modurl}>/showdiff.php?page=<{$revision.keyword}>&id=<{$revision.gwiki_id}>"
                             title="<{$smarty.const._MD_GWIKI_HISTORY_DIFF_TT}>"><{$smarty.const._MD_GWIKI_HISTORY_DIFF}></a>
                    </td>
                </tr>
            <{/foreach}>
            </ul>
        <{else}>
            <tr>
                <td><{$smarty.const._MD_GWIKI_HISTORY_EMPTY}></td>
            </tr>
        <{/if}>
    </table>
    <br>
<{/if}>
<{include file="db:gwiki_page_info.tpl"}>
<script>
    window.ajaxGwikiPageView = function (keyword, id, elid) {
        // alert(keyword+bid);
        var xmlhttp;
        var txt, x, _y, i;
        if (window.XMLHttpRequest) { // code for browsers
            xmlhttp = new XMLHttpRequest();
        } else {  // code for historical curiosities which should die (IE6, IE5) and will probably choke on something else
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.onreadystatechange = function () {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                txt = xmlhttp.responseText;
                el = document.getElementById(elid);
                el.innerHTML = txt;

            }
        };
        xmlhttp.open("GET", "<{$gwiki.modurl}>/ajaxgwiki.php?page=" + encodeURIComponent(keyword) + "&id=" + id, true);
        xmlhttp.send();
    };
    window.ajaxGwikiView = function (keyword, id) {
        var fid = document.getElementById('diffnid');
        fid.value = id;

        ajaxGwikiPageView(keyword, id, 'wikibase');

        // try to make sure the top of the just changed content is visible
        // find the top of our div
        el = document.getElementById('wikipage');
        for (var topPos = 0;
             el != null;
             topPos += el.offsetTop, el = el.offsetParent);

        // figure out where we are viewing now
        var scrOfY = 0;
        if (typeof( window.pageYOffset ) == 'number') { //Netscape compliant
            scrOfY = window.pageYOffset;
        } else if (document.body && ( document.body.scrollLeft || document.body.scrollTop )) { //DOM compliant
            scrOfY = document.body.scrollTop;
        } else if (document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop )) { //IE6 standards compliant mode
            scrOfY = document.documentElement.scrollTop;
        }
        // alert(topPos-scrOfY);
        // try to make the browser reposition if content is not in view
        if ((topPos - scrOfY) > 250) {
            document.body.scrollTop = topPos - 16;
            document.documentElement.scrollTop = topPos - 16;
        }
    };
    window.ajaxGwikiCompare = function (keyword, id) {
        var el = document.getElementById('wikibase');
        if (el.innerHTML == '') ajaxGwikiView(keyword, 0);

        var fid = document.getElementById('diffid');
        fid.value = id;

        ajaxGwikiPageView(keyword, id, 'wikicomp');
        // try to make sure the top of the just changed content is visible
        // find the top of our div
        el = document.getElementById('wikipage');
        for (var topPos = 0;
             el != null;
             topPos += el.offsetTop, el = el.offsetParent);

        // figure out where we are viewing now
        var scrOfY = 0;
        if (typeof( window.pageYOffset ) == 'number') { //Netscape compliant
            scrOfY = window.pageYOffset;
        } else if (document.body && ( document.body.scrollLeft || document.body.scrollTop )) { //DOM compliant
            scrOfY = document.body.scrollTop;
        } else if (document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop )) { //IE6 standards compliant mode
            scrOfY = document.documentElement.scrollTop;
        }
        // alert(scrOfY+' : '+topPos);
        // try to make the browser reposition if content is not in view
        // if(scrOfY>topPos) { document.body.scrollTop=topPos-16; document.documentElement.scrollTop=topPos-16; }
        document.body.scrollTop = topPos - 16;
        document.documentElement.scrollTop = topPos - 16;
    };
    <{if $gwiki.mayEdit}>
    window.restoreGwikiRevision = function (keyword, id) {
        var r = confirm("<{$smarty.const._MD_GWIKI_RESTORE_CONFIRM}>");
        if (r) {
            document.restoreform.page.value = keyword;
            document.restoreform.id.value = id;
            document.restoreform.submit();
        }
    };
    <{/if}>

</script>
<div id="wikipage" class="wikipage">
    <h3><{$smarty.const._MD_GWIKI_HISTORY_VIEW}></h3>
    <div id="wikibase"></div>
    <h3><{$smarty.const._MD_GWIKI_HISTORY_COMPARE}></h3>
    <div id="wikicomp"></div>
</div>
<form id="difform" name="diffform" action="showdiff.php" method="get">
    <input type="hidden" id="diffpage" name="page" value="<{$gwiki.keyword}>"/>
    <input type="hidden" id="diffnid" name="nid" value=""/>
    <input type="hidden" id="diffid" name="id" value=""/>
    <input type="submit" value="<{$smarty.const._MD_GWIKI_HISTORY_DIFF}>"/>
</form>
<{if $gwiki.mayEdit}>
    <form id="restoreform" name="restoreform" action="history.php" method="post">
        <input type="hidden" id="op" name="op" value="restore"/>
        <input type="hidden" id="page" name="page" value=""/>
        <input type="hidden" id="id" name="id" value=""/>
    </form>
<{/if}>
<{if isset($debug)}>
    <div><{$debug}></div>
<{/if}>
<{include file='db:system_notification_select.tpl'}>
