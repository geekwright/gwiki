<!-- block block contains keyword,moddir,modpath,modurl,linkshere (an array with entry for each match - keyword, display_keyword, title, lastmodified, uid, page_id, created, hit_count, pageurl, pagelink) -->
<div class="wikilinkshere">
<{foreach key=id item=gwiki from=$block.linkshere}>
<{$gwiki.pagelink}><br />
<{/foreach}>
</div>
