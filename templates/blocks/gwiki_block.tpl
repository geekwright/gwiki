<!-- block contains keyword,moddir,modpath,modurl,title,pageurl,mayEdit,template,body,image_file,image_alt_text -->
<{assign var=gwiki value=$block}>
<{assign var=hideInfoBar value=true}>
<{if isset($gwiki.image_file)}>
<a href="<{$gwiki.pageurl}>"><img class="wikirepresentimage" src="<{$gwiki.image_file}>" alt="<{$gwiki.image_alt_text}>" /></a>
<{/if}>
<{include file=$gwiki.template}>
<div class="wikiblocknav">
<{if $block.mayEdit}>
 <a href="<{$gwiki.modurl}>/edit.php?page=<{$gwiki.keyword}>#gwikiform"><img src="<{$gwiki.modurl}>/assets/images/editicon.png" alt="<{$smarty.const._EDIT}>" title="<{$smarty.const._EDIT}>" /></a>
 <a href="<{$gwiki.modurl}>/history.php?page=<{$gwiki.keyword}>"><img src="<{$gwiki.modurl}>/assets/images/historyicon.png" alt="<{$smarty.const._MD_GWIKI_HISTORY}>" title="<{$smarty.const._MD_GWIKI_HISTORY}>" /></a>
 <a href="<{$gwiki.modurl}>/source.php?page=<{$gwiki.keyword}>" target="_blank"><img src="<{$gwiki.modurl}>/assets/images/texticon.png" alt="<{$smarty.const._MD_GWIKI_SOURCE}>" title="<{$smarty.const._MD_GWIKI_SOURCE}>" /></a>
<{/if}>
</div>
