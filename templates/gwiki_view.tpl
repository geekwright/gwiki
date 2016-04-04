<{if isset($err_message)}>
<hr />
<div class="errorMsg"><{$err_message}></div>
<hr />
<{/if}>
<{if isset($message)}>
<hr />
<div class="resultMsg"><{$message}></div>
<hr />
<{/if}>
<div class="wikipage">
<h1 class="wikititle" id="toc0"><{$gwiki.title}></h1>
<{$gwiki.body}>
<{include file="db:gwiki_page_info.tpl"}>
</div>
<{if empty($hideInfoBar)}>
<div style="margin: 3px; padding: 3px;">
<{$commentsnav}> <{$lang_notice}>
<!-- start comments loop -->
<{if $comment_mode == "flat"}>
  <{include file="db:system_comments_flat.tpl"}>
<{elseif $comment_mode == "thread"}>
  <{include file="db:system_comments_thread.tpl"}>
<{elseif $comment_mode == "nest"}>
  <{include file="db:system_comments_nest.tpl"}>
<{/if}>
<!-- end comments loop -->
<{include file='db:system_notification_select.tpl'}>
</div>
<{/if}>
